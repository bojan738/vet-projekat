<?php
session_start();
require_once 'functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['vet_id'])) {
    echo json_encode(['error' => 'Niste prijavljeni.']);
    exit;
}

$vet_id = $_SESSION['vet_id'];
$date = $_POST['date'] ?? '';

if (!$date) {
    echo json_encode(['error' => 'Nije prosleđen datum']);
    exit;
}

try {
    // Pretpostavimo da postoji funkcija u functions.php koja vraća termine za veterinara za dati dan
    $schedule = get_schedule_for_vet_by_date($pdo, $vet_id, $date);

    echo json_encode(['schedule' => $schedule]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
