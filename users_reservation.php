<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Učitavanje korisnika (email i ime za slanje mejla)
$stmt = $pdo->prepare("SELECT email, first_name FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Veterinari i ljubimci za formu
$veterinari = get_all_vets($pdo);
$selectedVetId = $_GET['vet_id'] ?? ($_POST['vet_id'] ?? '');
$selectedDate = $_GET['date'] ?? date('Y-m-d');
$pets = get_pets_by_user($pdo, $user_id);
$selectedPetId = $_POST['pet_id'] ?? '';
$availableSlots = $selectedVetId ? get_available_terms_by_vet_and_date($pdo, (int)$selectedVetId, $selectedDate) : [];

// Obrađuje rezervaciju i otkazivanje termina
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $schedule_id = isset($_POST['reservation']) ? (int)$_POST['reservation'] : 0;
        $pet_id = isset($_POST['pet_id']) ? (int)$_POST['pet_id'] : null;

        if ($schedule_id && $pet_id) {
            $schedule = get_schedule_by_id($pdo, $schedule_id, $selectedVetId);
            if (!$schedule) {
                $error = "Neispravan termin.";
            } else {
                $date = $selectedDate;
                $start_time = $schedule['start_time'];
                $end_time = $schedule['end_time'];
                if (is_schedule_slot_taken($pdo, $schedule_id, $date)) {
                    $error = "Termin je već zauzet.";
                } elseif (pet_has_overlapping_appointment($pdo, $pet_id, $date, $start_time, $end_time)) {
                    $error = "Ljubimac već ima zakazan termin koji se preklapa sa ovim.";
                } else {
                    $code = create_appointment(
                        $pdo,
                        $pet_id,
                        $schedule['veterinarian_id'],
                        1, // ovde ubaci pravi service_id ako treba
                        $date,
                        $start_time,
                        $end_time,
                        'scheduled',
                        $user_id,
                        $schedule_id
                    );

                    send_appointment_email($user['email'], $user['first_name'], $code, $date, $start_time);

                    $success = "Uspešno ste rezervisali termin. Kod rezervacije je poslat na vaš email.";
                    header("Location: users_reservation.php?vet_id=$selectedVetId&date=$selectedDate&success=1");
                    exit;
                }
            }
        } else {
            $error = "Morate izabrati ljubimca i termin.";
        }
    } elseif ($action === 'cancel') {
        $appointment_id = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
        if ($appointment_id) {
            $stmt = $pdo->prepare("SELECT appointment_date, start_time FROM appointments WHERE id = :id AND user_id = :uid AND status = 'scheduled'");
            $stmt->execute([':id' => $appointment_id, ':uid' => $user_id]);
            $app = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($app) {
                $datetime_termin = strtotime($app['appointment_date'] . ' ' . $app['start_time']);
                $datetime_sada = time();
                if (($datetime_termin - $datetime_sada) >= 4 * 3600) {
                    $stmt = $pdo->prepare("UPDATE appointments SET status = 'canceled' WHERE id = :id");
                    $stmt->execute([':id' => $appointment_id]);
                    $success = "Termin je uspešno otkazan.";
                } else {
                    $error = "Termin se može otkazati samo najmanje 4 sata pre početka.";
                }
            } else {
                $error = "Termin nije pronađen ili ne možete da ga otkažete.";
            }
        } else {
            $error = "Niste izabrali termin za otkazivanje.";
        }
    }
}

// Dohvati aktivne termine korisnika za prikaz
$stmt = $pdo->prepare("
    SELECT a.id, a.appointment_date, a.start_time, a.end_time, a.reservation_code,
           u.first_name AS vet_first_name, u.last_name AS vet_last_name
    FROM appointments a
    JOIN veterinarians v ON a.veterinarian_id = v.id
    JOIN users u ON v.user_id = u.id
    WHERE a.user_id = :uid AND a.status = 'scheduled'
    ORDER BY a.appointment_date, a.start_time
");
$stmt->execute([':uid' => $user_id]);
$activeAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Za prikaz dana u nedelji na srpskom
$daniUSrpskom = [
    'Monday' => 'Ponedeljak',
    'Tuesday' => 'Utorak',
    'Wednesday' => 'Sreda',
    'Thursday' => 'Četvrtak',
    'Friday' => 'Petak',
    'Saturday' => 'Subota',
    'Sunday' => 'Nedelja'
];
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8" />
    <title>Slobodni termini i moje rezervacije</title>
    <link rel="stylesheet" href="css/css.css" />
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="pet_information.php">Moj ljubimac</a></li>
            <li><a href="pet_treatments.php">Tretman ljubimca</a></li>
            <li><a href="users_reservation.php" class="active">Rezervacije</a></li>
            <li><a href="change_reservation.php">Promeni rezervaciju</a></li>
            <li><a href="user_information.php">Moj nalog</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<h2>Slobodni termini</h2>

<?php if (!empty($error)): ?>
    <div style="color: red; font-weight: bold;"><?= htmlspecialchars($error) ?></div>
<?php elseif (!empty($success)): ?>
    <div style="color: green; font-weight: bold;"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="GET" action="">
    <label for="vet_id">Izaberi veterinara:</label>
    <select name="vet_id" id="vet_id" onchange="this.form.submit()">
        <option value="">-- svi veterinari --</option>
        <?php foreach ($veterinari as $vet): ?>
            <option value="<?= $vet['vet_id'] ?>" <?= $vet['vet_id'] == $selectedVetId ? 'selected' : '' ?>>
                <?= htmlspecialchars($vet['first_name'] . ' ' . $vet['last_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="date">Izaberi datum:</label>
    <input type="date" name="date" id="date" value="<?= htmlspecialchars($selectedDate) ?>" onchange="this.form.submit()" />
</form>

<form method="POST">
    <label for="pet_id">Izaberi ljubimca:</label>
    <select name="pet_id" id="pet_id" required>
        <option value="">-- ljubimac --</option>
        <?php foreach ($pets as $pet): ?>
            <option value="<?= $pet['id'] ?>" <?= $pet['id'] == $selectedPetId ? 'selected' : '' ?>>
                <?= htmlspecialchars($pet['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <table border="1" cellpadding="5" cellspacing="0" style="margin-top:10px;">
        <thead>
        <tr>
            <th>Izaberi</th>
            <th>Datum</th>
            <th>Dan</th>
            <th>Vreme</th>
            <th>Veterinar</th>
            <th>Zakazi</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($availableSlots) > 0): ?>
            <?php foreach ($availableSlots as $slot): ?>
                <?php
                $datum = $selectedDate;
                $danUNedeljiEng = date('l', strtotime($datum));
                $danUNedelji = $daniUSrpskom[$danUNedeljiEng] ?? $danUNedeljiEng;
                ?>
                <tr>
                    <td><input type="radio" name="reservation" value="<?= $slot['schedule_id'] ?>"></td>
                    <td><?= htmlspecialchars($datum) ?></td>
                    <td><?= $danUNedelji ?></td>
                    <td><?= htmlspecialchars($slot['start_time']) ?> - <?= htmlspecialchars($slot['end_time']) ?></td>
                    <td><?= htmlspecialchars($slot['first_name'] . ' ' . $slot['last_name']) ?></td>
                    <td>
                        <button type="submit" name="action" value="create" onclick="document.getElementsByName('reservation')[0].value='<?= $slot['schedule_id'] ?>'">
                            Zakazi
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">Trenutno nema slobodnih termina.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</form>

<h2>Moje aktivne rezervacije</h2>
<?php if (count($activeAppointments) > 0): ?>
    <table border="1" cellpadding="5" cellspacing="0" style="margin-top:10px;">
        <thead>
        <tr>
            <th>Datum</th>
            <th>Dan</th>
            <th>Vreme</th>
            <th>Veterinar</th>
            <th>Kod rezervacije</th>
            <th>Opcija</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($activeAppointments as $app): ?>
            <?php
            $datum = $app['appointment_date'];
            $danUNedeljiEng = date('l', strtotime($datum));
            $danUNedelji = $daniUSrpskom[$danUNedeljiEng] ?? $danUNedeljiEng;
            $datetime_termin = strtotime($datum . ' ' . $app['start_time']);
            $datetime_sada = time();
            $moze_otkazati = ($datetime_termin - $datetime_sada) >= 4 * 3600;
            ?>
            <tr>
                <td><?= htmlspecialchars($datum) ?></td>
                <td><?= $danUNedelji ?></td>
                <td><?= htmlspecialchars($app['start_time']) ?> - <?= htmlspecialchars($app['end_time']) ?></td>
                <td><?= htmlspecialchars($app['vet_first_name'] . ' ' . $app['vet_last_name']) ?></td>
                <td><?= htmlspecialchars($app['reservation_code']) ?></td>
                <td>
                    <?php if ($moze_otkazati): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="appointment_id" value="<?= $app['id'] ?>">
                            <button type="submit" name="action" value="cancel" onclick="return confirm('Da li ste sigurni da želite da otkažete ovaj termin?')">Otkaži</button>
                        </form>
                    <?php else: ?>
                        <span style="color: gray;">Ne može da otkaže</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Nemate aktivnih rezervacija.</p>
<?php endif; ?>

</body>
</html>
