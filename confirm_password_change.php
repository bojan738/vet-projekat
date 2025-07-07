<?php
require_once 'db_config.php';
require_once 'functions.php';

$db = new DBConfig();
$pdo = $db->getConnection();
$ordinacija = new VeterinarskaOrdinacija($pdo);

$token = $_GET['token'] ?? '';

if (!$token) {
    exit('<p style="color:red; text-align:center; margin-top:30px;">❌ Nevažeći zahtev.</p>');
}

$reset = $ordinacija->getPasswordResetByToken($token);

if (!$reset) {
    exit('<p style="color:red; text-align:center; margin-top:30px;">❌ Token je nevažeći ili je istekao.</p>');
}

$ordinacija->confirmNewPassword($reset['user_id'], $reset['new_password'], $token);
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Promena lozinke uspešna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="alert alert-success text-center" role="alert" style="font-size: 18px;">
        ✅ Vaša lozinka je uspešno promenjena. Sada se možete prijaviti.
    </div>
    <div class="text-center">
        <a href="login.php" class="btn btn-primary">Prijavi se</a>
    </div>
</div>
</body>
</html>
