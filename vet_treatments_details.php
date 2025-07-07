<?php
session_start();
require_once 'auth.php';
requireVeterinarian();
require_once 'db_config.php';
require_once 'functions.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

$vet_id = $_SESSION['vet_id'];
$appointment_id = (int)($_GET['appointment_id'] ?? 0);
$error = '';
$details = null;
$code_input = '';

$db = new DBConfig();
$pdo = $db->getConnection();
$ordinacija = new VeterinarskaOrdinacija($pdo);

if ($appointment_id <= 0) {
    die("❌ Nevažeći ID termina.");
}

if (isset($_SESSION['allowed_appointments'][$appointment_id])) {
    $access_granted = true;
} else {
    $access_granted = false;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $code_input = trim($_GET['code_input'] ?? '');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code_input = trim($_POST['code_input'] ?? '');
}

if (!$access_granted) {
    if ($code_input === '') {
        $show_form = true;
    } else {
        $stmt = $pdo->prepare("SELECT reservation_code FROM appointments WHERE id = ? AND veterinarian_id = ?");
        $stmt->execute([$appointment_id, $vet_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            die("❌ Termin nije pronađen ili nemate pristup.");
        }

        if ($row['reservation_code'] !== $code_input) {
            $error = "❌ Nije tačan kod za ovaj termin.";
            $show_form = true;
        } else {
            $_SESSION['allowed_appointments'][$appointment_id] = true;
            $access_granted = true;
            $show_form = false;
        }
    }
} else {
    $show_form = false;
}

if ($access_granted) {
    $details = $ordinacija->getTreatmentDetails($appointment_id, $vet_id);
    if (!$details) {
        die("❌ Nema dostupnih podataka za ovaj termin.");
    }

    $stmt = $pdo->prepare("SELECT pet_id FROM appointments WHERE id = ?");
    $stmt->execute([$appointment_id]);
    $appointmentData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$appointmentData) {
        die("❌ Termin nije pronađen.");
    }

    $pet_id = $appointmentData['pet_id'];

    $treatments = $ordinacija->getAllTreatments();
    $records = $ordinacija->getTreatmentRecords($appointment_id);

    if (isset($_GET['delete_id'])) {
        $delete_id = (int)$_GET['delete_id'];
        $ordinacija->deleteTreatmentRecord($delete_id);
        header("Location: vet_treatments_details.php?appointment_id=$appointment_id&code_input=" . urlencode($code_input));
        exit;
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['no_show']) && $_POST['no_show'] == '1') {
            $ordinacija->addPenaltyToOwner((int)$details['user_id']);
            header("Location: vet_treatments_details.php?appointment_id=$appointment_id&code_input=" . urlencode($code_input));
            exit;
        }

        $diagnosis = trim($_POST['diagnosis'] ?? '');
        $treatment_id = (int)($_POST['treatment_id'] ?? 0);

        $selected_treatment = array_filter($treatments, function($t) use ($treatment_id) {
            return $t['id'] == $treatment_id;
        });
        $selected_treatment = reset($selected_treatment);

        if ($diagnosis && $selected_treatment) {
            $ordinacija->saveTreatmentDetails(
                $appointment_id,
                $vet_id,
                $pet_id,
                $diagnosis,
                $selected_treatment['name'],
                (float)$selected_treatment['price']
            );
            header("Location: vet_treatments_details.php?appointment_id=$appointment_id&code_input=" . urlencode($code_input));
            exit;
        } else {
            $error = "Molimo unesite sve potrebne podatke.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8" />
    <title>Detalji tretmana</title>
    <link rel="stylesheet" href="css/css.css" />
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="vet_treatments_info.php">Tretmani</a></li>
            <li><a href="vet_electronic_card.php" class="active">Karton</a></li>
            <li><a href="vet_profile.php">Vet profil</a></li>
            <li><a href="vet_schedule.php">Radno vreme</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<?php if ($show_form): ?>
    <h2>Unesite kod za pristup kartonu</h2>
    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="get" action="">
        <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment_id) ?>" />
        <input type="text" name="code_input" required autocomplete="off" placeholder="Kod potvrde" style="width: 300px;" />
        <button type="submit">Potvrdi</button>
    </form>
<?php else: ?>
    <h2>Detalji tretmana</h2>

    <div class="container" style="display: flex; gap: 20px; padding: 20px;">
        <div style="width: 25%;">
            <img src="images/pets/<?= htmlspecialchars($details['photo']) ?>" alt="Slika ljubimca" style="max-width: 100%; border-radius: 10px;" />
        </div>
        <div style="width: 75%;">
            <p><strong>Ljubimac:</strong> <?= htmlspecialchars($details['pet_name']) ?></p>
            <p><strong>Vlasnik:</strong> <?= htmlspecialchars($details['owner_name']) ?></p>
            <p><strong>Datum:</strong> <?= date('Y-m-d', strtotime($details['appointment_date'])) ?></p>
            <p><strong>Početak:</strong> <?= date('H:i', strtotime($details['start_time'])) ?></p>
            <p><strong>Kraj:</strong> <?= date('H:i', strtotime($details['end_time'])) ?></p>

            <?php if ($error): ?>
                <p style="color:red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <!-- Form to submit new note -->
            <form method="post" id="noteForm">
                <input type="hidden" name="code_input" value="<?= htmlspecialchars($code_input) ?>" />
                <label for="diagnosis">Beleška (dijagnoza):</label><br />
                <textarea name="diagnosis" id="diagnosis" rows="4" cols="50" required><?= htmlspecialchars($_POST['diagnosis'] ?? '') ?></textarea><br /><br />

                <label for="treatment_id">Tretman:</label>
                <select name="treatment_id" id="treatment_id" class="form-input" required>
                    <option value="">-- Izaberite tretman --</option>
                    <?php foreach ($treatments as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= (isset($_POST['treatment_id']) && $_POST['treatment_id'] == $t['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['name']) ?> (<?= number_format($t['price'], 2) ?> RSD)
                        </option>
                    <?php endforeach; ?>
                </select><br /><br />

                <button type="submit" id="submitBtn" class="cta-button" style="height: 40px; width: 200px;">Unesi napomenu</button>
            </form>

            <!-- Display previous notes -->
            <h3>Prethodne beleške</h3>
            <?php if ($records): ?>
                <table border="1" cellpadding="8" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Veterinar</th>
                        <th>Dijagnoza</th>
                        <th>Tretman</th>
                        <th>Cena</th>
                        <th>Akcije</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['created_at']) ?></td>
                            <td><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
                            <td><?= htmlspecialchars($r['diagnosis']) ?></td>
                            <td><?= htmlspecialchars($r['treatment']) ?></td>
                            <td><?= number_format($r['price'], 2) ?> RSD</td>
                            <td>


                                <form method="get" style="display:inline-block;">
                                    <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment_id) ?>" />
                                    <input type="hidden" name="delete_id" value="<?= $r['id'] ?>" />
                                    <button type="submit" class="cta-button" style="height: 40px; width: 100px;" onclick="return confirm('Da li ste sigurni?')">Obriši</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Još nema unetih beleški.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<script src="js/vet_treatments_details.js"></script>
</body>
</html>
