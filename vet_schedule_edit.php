<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $termin = get_schedule_by_id($pdo, $id);
    if (!$termin || $termin['veterinarian_id'] != $_SESSION['vet_id']) {
        die("Nevažeći zahtev.");
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_time'], $_POST['end_time'], $_POST['termin_id'])) {
    $termin_id = (int)$_POST['termin_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    update_schedule($pdo, $termin_id, $start_time, $end_time);
    header("Location: vet_profile.php");
    exit;
} else {
    die("Nevažeći pristup.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Izmeni termin</title>
</head>
<body>
<h2>Izmena termina</h2>
<form method="POST" action="vet_schedule_edit.php">
    <input type="hidden" name="termin_id" value="<?= $termin['id'] ?>">
    <label for="start_time">Početak:</label>
    <input type="datetime-local" name="start_time" value="<?= date('Y-m-d\TH:i', strtotime($termin['start_time'])) ?>" required><br>
    <label for="end_time">Kraj:</label>
    <input type="datetime-local" name="end_time" value="<?= date('Y-m-d\TH:i', strtotime($termin['end_time'])) ?>" required><br>
    <button type="submit">Sačuvaj izmene</button>
</form>
</body>
</html>
