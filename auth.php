<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


const ROLE_ADMIN = 1;
const ROLE_VETERINARIAN = 2;
const ROLE_USER = 3;


function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}


function isAdmin(): bool {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] === ROLE_ADMIN;
}


function isVeterinarian(): bool {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] === ROLE_VETERINARIAN;
}


function isRegularUser(): bool {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] === ROLE_USER;
}


function requireAdmin(): void {
    if (!isAdmin()) {
        header("Location: login.php");
        exit;
    }
}


function requireVeterinarian(): void {
    if (!isVeterinarian()) {
        header("Location: login.php");
        exit;
    }
}


function requireRegularUser(): void {
    if (!isRegularUser()) {
        header("Location: login.php");
        exit;
    }
}
