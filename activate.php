<?php
session_start();
require_once 'db_config.php';
require_once 'functions.php';

$ordinacija = new VeterinarskaOrdinacija();

$poruka = '';
$uspesno = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $uspesno = $ordinacija->activateUserByToken($token);

    if ($uspesno) {
        $poruka = "✅ Vaš nalog je uspešno aktiviran. Sada se možete prijaviti.";
    } else {
        $poruka = "❌ Aktivacioni link je nevažeći ili je već iskorišćen.";
    }
} else {
    $poruka = "⚠️ Nedostaje aktivacioni token.";
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Aktivacija naloga - PetCare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow p-4">
                <h3 class="text-center mb-4">Aktivacija naloga</h3>
                <div class="alert <?= $uspesno ? 'alert-success' : 'alert-danger' ?>">
                    <?= htmlspecialchars($poruka) ?>
                </div>
                <div class="text-center">
                    <a href="<?= $uspesno ? 'login.php' : 'index.php' ?>" class="btn <?= $uspesno ? 'btn-primary' : 'btn-secondary' ?>">
                        <?= $uspesno ? 'Prijavi se' : 'Početna' ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

