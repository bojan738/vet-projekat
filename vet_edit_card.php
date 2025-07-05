<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

$vet_id = $_SESSION['vet_id'];
$record_id = (int)($_GET['id'] ?? 0);

// Učitavanje stare beleške
$stmt = $pdo->prepare("SELECT * FROM medical_records WHERE id = ? AND veterinarian_id = ?");
$stmt->execute([$record_id, $vet_id]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    die("Nemate pravo pristupa ovoj belešci.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diagnosis = trim($_POST['diagnosis']);
    $treatment = trim($_POST['treatment']);
    $price = floatval($_POST['price']);

    $stmt = $pdo->prepare("UPDATE medical_records SET diagnosis = ?, treatment = ?, price = ? WHERE id = ? AND veterinarian_id = ?");
    $stmt->execute([$diagnosis, $treatment, $price, $record_id, $vet_id]);

    header("Location: vet_treatments_details.php?appointment_id=" . $record['appointment_id']);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Izmena beleške</title>
</head>
<body>
<h2>Izmeni belešku</h2>
<form method="post">
    <label>Dijagnoza:</label><br>
    <textarea name="diagnosis" rows="3" required><?= htmlspecialchars($record['diagnosis']) ?></textarea><br>

    <label>Tretman:</label><br>
    <textarea name="treatment" rows="3" required><?= htmlspecialchars($record['treatment']) ?></textarea><br>

    <label>Cena (RSD):</label><br>
    <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($record['price']) ?>" required><br><br>

    <button type="submit">Sačuvaj izmene</button>
</form>
</body>
</html>
