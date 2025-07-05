<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_POST['id'])) {
    die("Nije prosleđen ID termina za brisanje.");
}

$id = $_POST['id'];
$vetId = $_SESSION['vet_id'];

try {
    $stmt = $pdo->prepare("DELETE FROM veterinarian_schedule WHERE id = :id AND veterinarian_id = :vetId");
    $stmt->execute(['id' => $id, 'vetId' => $vetId]);

    $_SESSION['msg'] = "Termin uspešno obrisan.";
    header("Location: vet_schedule.php");
    exit;
} catch (PDOException $e) {
    die("Greška prilikom brisanja termina: " . $e->getMessage());
}
?>
