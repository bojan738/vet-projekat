<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['datum'], $_POST['vreme_start'], $_POST['vreme_end'])) {
    $datum = $_POST['datum'];
    $vreme_start = $_POST['vreme_start'];
    $vreme_end = $_POST['vreme_end'];

    $start_datetime = "$datum $vreme_start";
    $end_datetime = "$datum $vreme_end";

    if (strtotime($end_datetime) <= strtotime($start_datetime)) {
        echo "<script>alert('❌ Greška: Kraj mora biti posle početka.'); window.location.href='vet_profile.php';</script>";
        exit;
    }

    try {
        add_schedule($pdo, $_SESSION['vet_id'], $start_datetime, $end_datetime);
        header("Location: vet_profile.php");
        exit;
    } catch (Exception $e) {
        $msg = addslashes($e->getMessage()); // escape za JS
        echo "<script>alert('Greška: $msg'); window.location.href='vet_profile.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Nevažeći zahtev.'); window.location.href='vet_profile.php';</script>";
    exit;
}
