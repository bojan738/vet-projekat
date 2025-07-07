<?php
session_start();
require_once 'auth.php';
requireVeterinarian();
require_once 'db_config.php';
require_once 'functions.php';


if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_POST['id'])) {
    die("⛔ Nije prosleđen ID termina za brisanje.");
}

$id = (int)$_POST['id'];
$vetId = (int)$_SESSION['vet_id'];

$db = new DBConfig();
$pdo = $db->getConnection();
$ordinacija = new VeterinarskaOrdinacija($pdo);

try {
    if ($ordinacija->deleteScheduleById($id, $vetId)) {
        $_SESSION['msg'] = "✅ Termin uspešno obrisan.";
    } else {
        $_SESSION['msg'] = "⚠️ Termin nije pronađen ili nije vaš.";
    }
    header("Location: vet_schedule.php");
    exit;
} catch (PDOException $e) {
    die("❌ Greška prilikom brisanja termina: " . $e->getMessage());
}
