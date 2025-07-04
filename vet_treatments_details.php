<?php
session_start();
require_once 'functions.php';

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
$treatments = get_services_for_vet($pdo, $_SESSION['vet_id']);
$diagnoses = ['Povreda', 'Infekcija', 'Letargija', 'Alergija', 'Zubni kamenac'];

$history = get_all_medical_records_for_appointment($pdo, $appointment_id);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detalji tretmana</title>
    <link rel="stylesheet" href="css/css.css">
    <style>
        .container { display: flex; gap: 20px; padding: 20px; }
        .photo-box { width: 25%; }
        .photo-box img { max-width: 100%; border-radius: 10px; }
        .info-box { width: 75%; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
    </style>
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="vet_treatments_info.php">Tretmani</a></li>
            <li><a href="vet_electronic_card.php" class="active">Karton</a></li>
            <li><a href="vet_profile.php">Vet profil</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<h2>Detalji tretmana</h2>

<div class="container">
    <div class="photo-box">
        <img src="images/pets/<?= htmlspecialchars($details['photo']) ?>">
    </div>
    <div class="info-box">
        <p><strong>Životinja:</strong> <?= htmlspecialchars($details['pet_name']) ?></p>
        <p><strong>Vlasnik:</strong> <?= htmlspecialchars($details['owner_name']) ?></p>
        <p><strong>Datum:</strong> <?= date('Y-m-d H:i', strtotime($details['appointment_date'])) ?></p>
        <p><strong>Napomene o životinji:</strong> <?= htmlspecialchars($details['notes'] ?? 'Nema posebnih napomena.') ?></p>

        <form method="post" action="vet_save_note.php">
            <label>Dijagnoza:</label>
            <select name="diagnosis" required>
                <option value="">-- Izaberite dijagnozu --</option>
                <?php foreach ($diagnoses as $d): ?>
                    <option value="<?= htmlspecialchars($d) ?>"><?= htmlspecialchars($d) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Tretman:</label>
            <select name="treatment_id" required>
                <option value="">-- Izaberite tretman --</option>
                <?php foreach ($treatments as $t): ?>
                    <option value="<?= $t['id'] ?>">
                        <?= htmlspecialchars($t['name']) ?> (<?= number_format($t['price'], 2) ?> RSD)
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="hidden" name="appointment_id" value="<?= $appointment_id ?>">
            <button type="submit">Sačuvaj napomene</button>
        </form>

        <h3>Prethodne beleške</h3>
        <?php if (count($history) > 0): ?>
            <table>
                <thead>
                <tr>
                    <th>Datum</th>
                    <th>Veterinar</th>
                    <th>Dijagnoza</th>
                    <th>Tretman</th>
                    <th>Cena</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($history as $r): ?>
                    <tr>
                        <td><?= date('Y-m-d H:i', strtotime($r['created_at'])) ?></td>
                        <td><?= htmlspecialchars($r['vet_name']) ?></td>
                        <td><?= htmlspecialchars($r['diagnosis']) ?></td>
                        <td><?= htmlspecialchars($r['treatment']) ?></td>
                        <td><?= number_format($r['price'], 2) ?> RSD</td>
                        <td>
                            <?php if ($r['vet_id'] == $_SESSION['vet_id']): ?>
                                <a href="vet_edit_record.php?id=<?= $r['id'] ?>">Izmeni</a> |
                                <a href="vet_delete_record.php?id=<?= $r['id'] ?>&appointment_id=<?= $appointment_id ?>" onclick="return confirm('Da li ste sigurni da želite da obrišete ovu belešku?')">Obriši</a>
                            <?php else: ?>
                                <span style="color: gray;">Nije vaša beleška</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

            </table>
        <?php else: ?>
            <p>Još uvek nema unetih beleški.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
