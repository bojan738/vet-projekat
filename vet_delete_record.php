<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

$record_id = (int)($_GET['id'] ?? 0);
$appointment_id = (int)($_GET['appointment_id'] ?? 0);
$vet_id = $_SESSION['vet_id'];

$stmt = $pdo->prepare("DELETE FROM medical_records WHERE id = ? AND veterinarian_id = ?");
$stmt->execute([$record_id, $vet_id]);

header("Location: vet_treatments_details.php?appointment_id=" . $appointment_id);
exit;
