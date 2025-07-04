<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vet_id = $_SESSION['vet_id'];
    $pet_id = (int)$_POST['pet_id'];
    $diagnosis = trim($_POST['diagnosis']);
    $treatment = trim($_POST['treatment']);
    $price = floatval($_POST['price']);

    $stmt = $pdo->prepare("
        INSERT INTO medical_records (appointment_id, veterinarian_id, pet_id, diagnosis, treatment, price)
        VALUES (NULL, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$vet_id, $pet_id, $diagnosis, $treatment, $price]);

    header("Location: vet_medical_record.php?pet_id=$pet_id");
    exit;
}
