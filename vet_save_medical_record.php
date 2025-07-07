<?php
session_start();
require_once 'db_config.php';
require_once 'functions.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vet_id = $_SESSION['vet_id'];
    $pet_id = (int)($_POST['pet_id'] ?? 0);
    $diagnosis = trim($_POST['diagnosis'] ?? '');
    $treatment = trim($_POST['treatment'] ?? '');
    $price = floatval($_POST['price'] ?? 0);

    if ($pet_id > 0 && $diagnosis && $treatment && $price >= 0) {
        $db = new DBConfig();
        $pdo = $db->getConnection();
        $ordinacija = new VeterinarskaOrdinacija($pdo);

        $ordinacija->saveMedicalRecord(null, $vet_id, $pet_id, $diagnosis, $treatment, $price);
    }

    header("Location: vet_medical_record.php?pet_id=$pet_id");
    exit;
}
