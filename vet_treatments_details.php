<?php
session_start();
require_once 'functions.php';
require_once 'db.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

$appointment_id = (int)($_GET['appointment_id'] ?? 0);
$vet_id = $_SESSION['vet_id'];
$details = get_treatment_details($pdo, $appointment_id, $vet_id);

if (!$details) {
    die("Neispravan zahtev ili nemate pristup ovom terminu.");
}

$treatments = get_all_treatments($pdo);

// Dodavanje nove beleške ili označavanje da se korisnik nije pojavio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['no_show']) && $_POST['no_show'] == '1') {
        add_penalty_to_owner($pdo, (int)$details['user_id']);
        header("Location: vet_treatments_details.php?appointment_id=$appointment_id");
        exit;
    }

    $note = trim($_POST['note'] ?? '');
    $treatment_id = (int)($_POST['treatment_id'] ?? 0);

    $selected_treatment = array_filter($treatments, fn($t) => $t['id'] == $treatment_id);
    $selected_treatment = reset($selected_treatment);

    if ($note && $selected_treatment) {
        save_medical_note(
            $pdo,
            $appointment_id,
            $vet_id,
            $details['pet_id'],
            $note,
            $selected_treatment['name'],
            (float)$selected_treatment['price']
        );
    }
    header("Location: vet_treatments_details.php?appointment_id=$appointment_id");
    exit;
}

// Brisanje beleške
if (isset($_GET['delete_id'])) {
    delete_medical_note($pdo, (int)$_GET['delete_id']);
    header("Location: vet_treatments_details.php?appointment_id=$appointment_id");
    exit;
}

$records = get_medical_records_by_appointment($pdo, $appointment_id);
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Detalji tretmana</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="vet_treatments_info.php">Tretmani</a></li>
            <li><a href="vet_electronic_card.php">Karton</a></li>
            <li><a href="vet_profile.php">Vet profil</a></li>
            <li><a href="vet_schedule.php">Radno vreme</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<h2>Detalji tretmana</h2>
<div class="container" style="display: flex; gap: 20px; padding: 20px;">
    <div style="width: 25%;">
        <img src="images/pets/<?= htmlspecialchars($details['photo']) ?>" alt="Slika ljubimca" style="max-width: 100%; border-radius: 10px;">
    </div>
    <div style="width: 75%;">
        <p><strong>Životinja:</strong> <?= htmlspecialchars($details['pet_name']) ?></p>
        <p><strong>Vlasnik:</strong> <?= htmlspecialchars($details['owner_name']) ?></p>
        <p><strong>Datum:</strong> <?= htmlspecialchars($details['appointment_date']) ?></p>

        <form method="post" id="noteForm">
            <label for="note">Beleška (dijagnoza):</label><br>
            <textarea name="note" id="note" rows="4" cols="50" required></textarea><br><br>

            <label for="treatment_id">Tretman:</label>
            <select name="treatment_id" id="treatment_id" required>
                <option value="">-- Izaberite tretman --</option>
                <?php foreach ($treatments as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?> (<?= number_format($t['price'], 2) ?> RSD)</option>
                <?php endforeach; ?>
            </select><br><br>

            <label>
                <input type="checkbox" name="no_show" id="no_show"> Korisnik se nije pojavio
            </label><br><br>

            <button type="submit" id="submitBtn">Unesi napomenu</button>
        </form>

        <form method="post" style="margin-top:10px;">
            <input type="hidden" name="no_show" value="1">
            <button type="submit" onclick="return confirm('Potvrđujete da se korisnik nije pojavio?')">Označi da se nije pojavio</button>
        </form>

        <script>
            const noShowCheckbox = document.getElementById('no_show');
            const noteField = document.getElementById('note');
            const treatmentField = document.getElementById('treatment_id');
            const submitBtn = document.getElementById('submitBtn');

            noShowCheckbox.addEventListener('change', () => {
                const disabled = noShowCheckbox.checked;
                noteField.disabled = disabled;
                treatmentField.disabled = disabled;
                submitBtn.disabled = disabled;
                noteField.required = !disabled;
                treatmentField.required = !disabled;
            });
        </script>

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
                            <form method="get" action="vet_edit_record.php" style="display:inline-block;">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <input type="hidden" name="appointment_id" value="<?= $appointment_id ?>">
                                <button type="submit">Izmeni</button>
                            </form>
                            <form method="get" style="display:inline-block;">
                                <input type="hidden" name="appointment_id" value="<?= $appointment_id ?>">
                                <input type="hidden" name="delete_id" value="<?= $r['id'] ?>">
                                <button type="submit" onclick="return confirm('Da li ste sigurni?')">Obriši</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Još nema beleški.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
