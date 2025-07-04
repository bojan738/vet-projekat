<?php
session_start();
require_once 'db.php';

$poruka = '';
$uspesno = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $pdo->prepare("UPDATE users SET is_active = 1, activation_token = NULL WHERE activation_token = ?");
    $stmt->execute([$token]);

    if ($stmt->rowCount()) {
        $poruka = "✅ Vaš nalog je uspešno aktiviran. Sada se možete prijaviti.";
        $uspesno = true;
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
                    <?= $poruka ?>
                </div>
                <?php if ($uspesno): ?>
                    <div class="text-center">
                        <a href="login.php" class="btn btn-primary">Prijavi se</a>
                    </div>
                <?php else: ?>
                    <div class="text-center">
                        <a href="index.php" class="btn btn-secondary">Početna</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
