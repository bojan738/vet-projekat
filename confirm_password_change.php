<?php
require_once 'db.php';

$token = $_GET['token'] ?? '';

if (!$token) {
    die("Nevažeći zahtev.");
}

// Provera tokena
$stmt = $pdo->prepare("SELECT user_id, new_password FROM password_resets WHERE token = ?");
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset) {
    die("Nevažeći ili istekao token.");
}

// Menjanje lozinke
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->execute([$reset['new_password'], $reset['user_id']]);

// Brisanje tokena iz password_resets
$stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
$stmt->execute([$token]);

echo "Uspešno ste promenili lozinku. <a href='login.php'>Prijavite se</a>";