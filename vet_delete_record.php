<?php
session_start();
require_once 'db_config.php';
require_once 'VeterinarskaOrdinacija.php';

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
if ($record) {
    $ordinacija->deleteMedicalNote($record_id);
}

header("Location: vet_treatments_details.php?appointment_id=$appointment_id");
exit;
