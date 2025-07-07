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
$record_id = (int)($_GET['id'] ?? 0);
$appointment_id = (int)($_GET['appointment_id'] ?? 0);

$db = new DBConfig();
$pdo = $db->getConnection();
$ordinacija = new VeterinarskaOrdinacija($pdo);

$record = $ordinacija->getMedicalRecordById($record_id, $vet_id);
if (!$record) {
    die("Nemate pravo pristupa ovoj belešci ili ne postoji.");
}

$treatments = $ordinacija->getAllTreatments();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diagnosis = trim($_POST['diagnosis']);
    $treatment_id = (int)$_POST['treatment_id'];

    $selected_treatment = $ordinacija->getServiceById($treatment_id);

    if ($diagnosis && $selected_treatment) {
        $ordinacija->updateMedicalNote(
            $record_id,
            $diagnosis,
            $selected_treatment['name'],
            (float)$selected_treatment['price']
        );
        header("Location: vet_treatments_details.php?appointment_id=$appointment_id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Izmeni belešku</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eefbf1;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .edit-container {
            width: 100%;
            max-width: 600px;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #4caf50;
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }
        textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #4caf50;
            border: none;
            border-radius: 6px;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<div class="edit-container">
    <h2>Izmeni belešku</h2>
    <form method="post">
        <label for="diagnosis">Dijagnoza:</label>
        <textarea name="diagnosis" id="diagnosis" rows="3" required><?= htmlspecialchars($record['diagnosis']) ?></textarea>

        <label for="treatment_id">Tretman:</label>
        <select name="treatment_id" id="treatment_id" required>
            <option value="">-- Izaberite tretman --</option>
            <?php foreach ($treatments as $t): ?>
                <option value="<?= $t['id'] ?>" <?= ($t['name'] == $record['treatment']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['name']) ?> (<?= number_format($t['price'], 2) ?> RSD)
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Sačuvaj izmene</button>
    </form>
</div>
</body>
</html>
