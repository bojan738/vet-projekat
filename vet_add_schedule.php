<?php
session_start();
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

    if (!$dayOfWeek || !$start || !$end) {
        $_SESSION['msg'] = "Sva polja su obavezna.";
        header("Location: vet_schedule.php");
        exit;
    }

    if ($start >= $end) {
        $_SESSION['msg'] = "Početak termina mora biti pre kraja.";
        header("Location: vet_schedule.php");
        exit;
    }

    global $pdo;

    if (schedule_exists($pdo, $vetId, $dayOfWeek, $start, $end)) {
        $_SESSION['msg'] = "Termin za odabrani dan i vreme već postoji.";
        header("Location: vet_schedule.php");
        exit;
    }

    $success = add_vet_schedule($pdo, $vetId, $dayOfWeek, $start, $end);
    if ($success) {
        $_SESSION['msg'] = "Termin je uspešno dodat.";
    } else {
        $_SESSION['msg'] = "Došlo je do greške prilikom dodavanja termina.";
    }

    header("Location: vet_schedule.php");
    exit;
}
?>
