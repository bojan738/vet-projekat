<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $termin_id = (int)$_POST['id'];

    $termin = get_schedule_by_id($pdo, $termin_id);
    if (!$termin || $termin['veterinarian_id'] != $_SESSION['vet_id']) {
        die("Nevažeći zahtev.");
    }

    delete_schedule($pdo, $termin_id);
    header("Location: vet_profile.php");
    exit;
} else {
    die("Nevažeći pristup.");
}
