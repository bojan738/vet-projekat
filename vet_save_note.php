<?php
session_start();
require_once 'db_config.php';
require_once 'functions.php';
header('Content-Type: application/json');

if (!isset($_SESSION['vet_id'])) {
    echo json_encode(['success' => false, 'message' => 'Niste prijavljeni.']);
    exit;
}

$db = new DBConfig();
$pdo = $db->getConnection();
$ordinacija = new VeterinarskaOrdinacija($pdo);

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
        $owner_id = $ordinacija->getOwnerIdByAppointment($appointment_id);
        if ($owner_id) {
            $ordinacija->addNegativePoint($appointment_id);
        }
        $ordinacija->saveMedicalNote($appointment_id, $vet_id, null, $note, null, null, true);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($treatment_id === null) {
        echo json_encode(['success' => false, 'message' => 'Tretman je obavezan.']);
        exit;
    }

    $treatment = $ordinacija->getServiceById($treatment_id);
    if (!$treatment) {
        echo json_encode(['success' => false, 'message' => 'Nepostojeći tretman.']);
        exit;
    }

    $details = $ordinacija->getAppointmentById($appointment_id);
    $pet_id = $details['pet_id'] ?? null;

    $ordinacija->saveMedicalNote($appointment_id, $vet_id, $pet_id, $note, $treatment['name'], (float)$treatment['price'], false);
    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Pogrešan zahtev.']);
