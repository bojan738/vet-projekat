<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = (int)$_POST['appointment_id'];
    $vet_id = $_SESSION['vet_id'];
    $diagnosis = trim($_POST['diagnosis']);
    $treatment_id = (int)$_POST['treatment_id'];

    $treatment = get_service_name($pdo, $treatment_id);
    $price = get_service_price($pdo, $treatment_id);

    $stmt = $pdo->prepare("
        INSERT INTO medical_records (appointment_id, veterinarian_id, pet_id, diagnosis, treatment, price)
        SELECT a.id, a.veterinarian_id, a.pet_id, ?, ?, ?
        FROM appointments a
        WHERE a.id = ?
    ");
    $stmt->execute([$diagnosis, $treatment, $price, $appointment_id]);

    header("Location: vet_treatments_details.php?appointment_id=" . $appointment_id);
    exit;
}
?>