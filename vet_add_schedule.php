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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vetId = $_SESSION['vet_id'];
    $dayOfWeek = $_POST['day_of_week'] ?? '';
    $start = $_POST['start_time'] ?? '';
    $end = $_POST['end_time'] ?? '';

    if (empty($dayOfWeek) || empty($start) || empty($end)) {
        $_SESSION['msg'] = "Sva polja su obavezna.";
        header("Location: vet_schedule.php");
        exit;
    }

    if (strtotime($start) >= strtotime($end)) {
        $_SESSION['msg'] = "Početak termina mora biti pre kraja.";
        header("Location: vet_schedule.php");
        exit;
    }

    $db = new DBConfig();
    $pdo = $db->getConnection();
    $ordinacija = new VeterinarskaOrdinacija($pdo);

    if ($ordinacija->scheduleExists($vetId, $dayOfWeek, $start, $end)) {
        $_SESSION['msg'] = "Termin za odabrani dan i vreme već postoji.";
        header("Location: vet_schedule.php");
        exit;
    }

    if ($ordinacija->addVetSchedule($vetId, $dayOfWeek, $start, $end)) {
        $_SESSION['msg'] = "Termin je uspešno dodat.";
    } else {
        $_SESSION['msg'] = "Greška pri dodavanju termina.";
    }

    header("Location: vet_schedule.php");
    exit;
}
