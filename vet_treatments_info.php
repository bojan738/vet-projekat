<?php
session_start();
require_once 'auth.php';
requireVeterinarian();
require_once 'db_config.php';
require_once 'functions.php';

$db = new DBConfig();
$pdo = $db->getConnection();
$ordinacija = new VeterinarskaOrdinacija($pdo);

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_penalty']) && isset($_POST['appointment_id'])) {
    $appointment_id = (int)$_POST['appointment_id'];

    $appointment = $ordinacija->getAppointmentById3($appointment_id);

    if ($appointment) {
        $owner_id = $appointment['user_id'];

        $ordinacija->addPenaltyToOwner1($owner_id);

        $ordinacija->deleteAppointment($appointment_id);

        $_SESSION['msg'] = "✅ Negativan poen dodat i termin obrisan.";
    } else {
        $_SESSION['msg'] = "⚠️ Termin nije pronađen.";
    }

    header("Location: vet_treatments_info.php");
    exit;
}


$vetId = $_SESSION['vet_id'];
$tretmani = $ordinacija->getAppointmentsForVet($vetId);


?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Zakazani Tretmani</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="vet_treatments_info.php" class="active">Tretmani</a></li>
            <li><a href="vet_electronic_card.php">Kartoni</a></li>
            <li><a href="vet_profile.php">Profil</a></li>
            <li><a href="vet_schedule.php">Radno vreme</a></li>
            <li><a href="logout.php">Odjava</a></li>
        </ul>
    </nav>
</header>

<main class="container">
    <h2 class="my-4">Zakazani tretmani</h2>

    <?php if (empty($tretmani)): ?>
        <p>Nema zakazanih termina.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Datum</th>
                <th>Vreme</th>
                <th>Vlasnik</th>
                <th>Ljubimac</th>
                <th>Kod potvrde</th>
            </tr>
            <?php foreach ($tretmani as $t): ?>
                <tr>
                    <td><?= date('Y-m-d', strtotime($t['appointment_date'])) ?></td>
                    <td><?= date('H:i', strtotime($t['start_time'])) ?></td>
                    <td><?= htmlspecialchars($t['owner_name']) ?></td>
                    <td><?= htmlspecialchars($t['pet_name']) ?></td>
                    <td>

                            <form method="get" action="vet_treatments_details.php">
                                <input type="hidden" name="appointment_id" value="<?= $t['appointment_id'] ?>">
                                <input type="text" name="code_input"  class="form-input" style="width: 200px" placeholder="Enter reservation code" required>
                                <button type="submit" class="cta-button" style="height: 40px; width: 150px;" >Detalji</button>
                            </form>


                        <form method="post" style="margin-top:10px;">
                            <input type="hidden" name="appointment_id" value="<?= $t['appointment_id'] ?>">
                            <input type="hidden" name="add_penalty" value="1">
                            <button type="submit" class="cta-button" style="height: 40px; width: 150px;" onclick="return confirm('Potvrđujete da se korisnik nije pojavio?')">Negativan poen</button>
                        </form>
                    </td>

                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</main>

<script src="js/vet_treatments_info.js"></script>


</body>
</html>
