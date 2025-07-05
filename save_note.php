<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vet_id = $_SESSION['vet_id'];
    $appointment_id = (int)$_POST['appointment_id'];
    $diagnosis = trim($_POST['diagnosis']);
    $treatment = trim($_POST['treatment']);
    $price = floatval($_POST['price']);

    save_treatment_note($pdo, $vet_id, $appointment_id, $diagnosis, $treatment, $price);

    header("Location: vet_treatments_details.php?appointment_id=$appointment_id");
    exit;
}
