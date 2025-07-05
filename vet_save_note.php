<?php
session_start();
require_once 'functions.php';
header('Content-Type: application/json');

if (!isset($_SESSION['vet_id'])) {
    echo json_encode(['success' => false, 'message' => 'Niste prijavljeni.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = (int)($_POST['appointment_id'] ?? 0);
    $vet_id = $_SESSION['vet_id'];
    $note = trim($_POST['note'] ?? '');
    $treatment_id = isset($_POST['treatment_id']) && $_POST['treatment_id'] !== '' ? (int)$_POST['treatment_id'] : null;
    $no_show = isset($_POST['no_show']) && $_POST['no_show'] == '1';

    if ($appointment_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Neispravan ID termina.']);
        exit;
    }

    if ($no_show) {
        $owner_id = get_owner_id_by_appointment($pdo, $appointment_id);
        if ($owner_id) {
            add_negative_point($pdo, $owner_id);
        }
        save_medical_note($pdo, $appointment_id, $vet_id, $note, null, null, true);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($treatment_id === null) {
        echo json_encode(['success' => false, 'message' => 'Tretman je obavezan.']);
        exit;
    }

    $treatment = get_service_by_id($pdo, $treatment_id);
    if (!$treatment) {
        echo json_encode(['success' => false, 'message' => 'Nepostojeći tretman.']);
        exit;
    }

    save_medical_note($pdo, $appointment_id, $vet_id, $note, $treatment_id, $treatment['price'], false);
    echo json_encode(['success' => true]);
}
