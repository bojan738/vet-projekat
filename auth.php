<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
/*Provera da li je korisnik ulogovan (bilo koja uloga).
@return bool*/
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/*Zahtjeva da korisnik bude ulogovan (bilo koja uloga),
inače redirekcija na login.php*/
function requireLogin(): void {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

/*Provera da li je korisnik admin.
@return bool*/
function isAdmin(): bool {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] === 1;
}

/*Zahtjeva da korisnik bude admin,
inače redirekcija na login.php*/
function requireAdmin(): void {
    if (!isAdmin()) {
        header("Location: login.php");
        exit;
    }
}

/*Provera da li je korisnik veterinar.
@return bool*/
function isVeterinarian(): bool {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] === 2;
}

/*Zahtjeva da korisnik bude veterinar,
inače redirekcija na login.php*/
function requireVeterinarian(): void {
    if (!isVeterinarian()) {
        header("Location: login.php");
        exit;
    }
}

/**

Provera da li je korisnik običan korisnik.
@return bool*/
function isRegularUser(): bool {
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == 3;  // koristimo == a ne ===
}

function requireRegularUser(): void {
    if (!isRegularUser()) {
        header("Location: login.php");
        exit;
    }
}
