<?php
session_start();
require_once 'db_config.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = new DBConfig();
$pdo = $db->getConnection();
$ord = new VeterinarskaOrdinacija($pdo);

$user_id = $_SESSION['user_id'];
$veterinari = $ord->getAllVets();
$pets = $ord->getPetsByUser($user_id);
$services = $ord->getAllServices();

// AJAX: Get free terms
if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'slots') {
    $vet_id = (int)($_GET['vet_id'] ?? 0);
    $date = $_GET['date'] ?? '';
    $slots = $ord->getAvailableSlots($vet_id, $date);
    echo json_encode($slots);
    exit;
}

// AJAX: Create new term
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    $slot_id = (int)($_POST['slot_id'] ?? 0);
    $pet_id = (int)($_POST['pet_id'] ?? 0);
    $date = $_POST['date'] ?? '';
    $service_id = (int)($_POST['service_id'] ?? 0);

    $result = $ord->createAppointment($user_id, $pet_id, $slot_id, $date, $service_id);
    echo json_encode($result);
    exit;
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8" />
    <title>Rezervacije</title>
    <link rel="stylesheet" href="css/css.css" />
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="pet_information.php">Moj ljubimac</a></li>
            <li><a href="pet_treatments.php">Tretman ljubimca</a></li>
            <li><a href="users_reservation.php" class="active">Rezervacije</a></li>
            <li><a href="change_reservation.php">Promeni rezervaciju</a></li>
            <li><a href="user_information.php">Moj nalog</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<main style="padding: 20px;">
    <h2>Rezerviši termin</h2>

    <form id="reservationForm" class="form-section vet-schedule-form">
        <label for="vet_id" class="form-label vet-form-label">Veterinar:</label>
        <select id="vet_id" name="vet_id" required class="form-input vet-form-input">
            <option value="" disabled selected>-- Izaberi --</option>
            <?php foreach ($veterinari as $v): ?>
                <option value="<?= $v['vet_id'] ?>"><?= htmlspecialchars($v['first_name'] . ' ' . $v['last_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="date" class="form-label vet-form-labell">Datum:</label>
        <input type="date" id="date" name="date" required min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+30 days')) ?>">

        <button type="button" onclick="fetchSlots()" class="cta-button vet-cta-button">Prikaži termine</button>

        <div id="slotsContainer" style="margin-top:20px;"></div>


        <div id="slotsContainer" style="margin-top:20px;"></div>

        <label for="pet_id">Ljubimac:</label>
        <select id="pet_id" name="pet_id" required class="form-input vet-form-input">
            <option value="">-- Izaberi --</option>
            <?php foreach ($pets as $p): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="service_id">Usluga:</label>
        <select id="service_id" name="service_id" required class="form-input vet-form-input" >
            <option value="">-- Izaberi --</option>
            <?php foreach ($services as $s): ?>
                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="cta-button vet-cta-button">Rezerviši</button>
    </form>

    <div id="msgBox" style="margin-top:20px; font-weight: bold;"></div>
</main>
<footer class="custom-footer" >
    <div class="footer-content">
        &copy; 2025 PetCare Ordinacija. Sva prava zadržana.
    </div>
</footer>
<script src="js/users_reservation.js"></script>
</body>
</html>
