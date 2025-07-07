<?php
session_start();
require_once 'auth.php';
requireAdmin();
require_once 'db_config.php';
require_once 'functions.php';

$db = new DBConfig();
$pdo = $db->getConnection();
$ordinacija = new VeterinarskaOrdinacija($pdo);

// AJAX data processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    $action = $_POST['action'];
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $license_number = trim($_POST['license_number'] ?? '');
    $password = trim($_POST['password'] ?? '');


    if ($action !== 'delete') {
        if (!$first_name || !$last_name || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Molimo unesite ispravno ime, prezime i email.']);
            exit;
        }
        if ($phone_number !== '' && !preg_match('/^\d+$/', $phone_number)) {
            echo json_encode(['success' => false, 'message' => 'Telefon mora sadržati samo brojeve.']);
            exit;
        }
    }
    $photoFileName = null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'images/vets/';  // folder gde će slike biti smeštene
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $photoFileName = basename($_FILES['photo']['name']);
        $targetFilePath = $uploadDir . $photoFileName;

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $targetFilePath)) {
            echo json_encode(['success' => false, 'message' => 'Greška pri uploadu slike.']);
            exit;
        }
    }

    if ($action === 'add') {
        $result = $ordinacija->insertFullVeterinarian(
            $first_name,
            $last_name,
            $email,
            $phone_number,
            $address,
            $specialization,
            $license_number,
            $password,
            $photoFileName
        );


        if ($result['success']) {
            echo json_encode(['success' => true, 'message' => 'Veterinar je uspešno dodat.']);
        } else {
            echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Greška pri dodavanju veterinara.']);
        }
        exit;
    } elseif ($action === 'update') {
        $vet_id = $id;
        $vet = $ordinacija->getVeterinarianById($vet_id);
        if (!$vet) {
            echo json_encode(['success' => false, 'message' => 'Veterinar nije pronađen.']);
            exit;
        }
        $user_id = $vet['user_id'];

        $ordinacija->updateVeterinarianFull(
            $vet_id,
            $user_id,
            $first_name,
            $last_name,
            $email,
            $phone_number,
            $address,
            $specialization,
            $license_number
        );
        echo json_encode(['success' => true, 'message' => 'Veterinar je uspešno izmenjen.']);
        exit;
    } elseif ($action === 'delete') {
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Nevažeći ID veterinara.']);
            exit;
        }
        $ordinacija->deleteVeterinarian($id);
        echo json_encode(['success' => true, 'message' => 'Veterinar je uspešno obrisan.']);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Nepoznata akcija.']);
    exit;
}


$vets = $ordinacija->getAllVeterinarians2();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Veterinari</title>
    <link rel="stylesheet" href="css/css.css" />
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px; border: 1px solid #ccc; }
        th { background-color: #eee; }
        input, select, button { padding: 6px; margin: 4px 0; }
        .error-msg { color: #f44336; font-weight: bold; margin-bottom: 10px; }
    </style>
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="manage_services.php">Upravljanje servisa</a></li>
            <li><a href="manage_vets.php"  class="active" >Upravljanje veterinarima</a></li>
            <li><a href="manage_term.php" >Upravljanje terminima</a></li>
            <li><a href="manage_users.php">Upravljanje korisnicima</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<main style="padding: 20px;">
    <h1>Upravljanje veterinarima</h1>
    <form id="vetForm" novalidate>
        <input type="hidden" id="vetId" name="id" value="">
        <div class="error-msg" id="formError"></div>
        <input type="text" id="firstName" class="form-input" style="width: 150px;" name="first_name" placeholder="Ime" required>
        <input type="text" id="lastName" class="form-input" style="width: 150px;" name="last_name" placeholder="Prezime" required>
        <input type="file" id="photo" name="photo" accept="Image/*" >
        <input type="email" id="email" class="form-input" style="width: 150px;" name="email" placeholder="Email" required>
        <input type="password" id="password" class="form-input" style="width: 150px;" name="password" placeholder="Lozinka" required>
        <input type="text" id="phoneNumber" class="form-input" style="width: 150px;" name="phone_number" placeholder="Telefon">
        <input type="text" id="address" class="form-input" style="width: 150px;" name="address" placeholder="Adresa">
        <input type="text" id="specialization" class="form-input" style="width: 150px;" name="specialization" placeholder="Specijalizacija">
        <input type="text" id="licenseNumber" class="form-input" style="width: 150px;" name="license_number" placeholder="Licenca">
        <button type="submit" id="submitBtn" class="cta-button" style="height: 40px; width: 160px;">Dodaj</button>
        <button type="button" id="cancelBtn" class="cta-button" style="height: 40px; width: 100px; display: none;">Otkaži</button>
    </form>


    <div id="messageBox" style="font-weight:bold; margin-top:10px;"></div>

    <h2>Lista veterinara</h2>
    <table id="vetsTable">
        <thead>
        <tr>
            <th>Ime</th>
            <th>Prezime</th>
            <th>Email</th>
            <th>Telefon</th>
            <th>Adresa</th>
            <th>Specijalizacija</th>
            <th>Licenca</th>
            <th>Akcije</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($vets as $v): ?>
            <tr data-id="<?= $v['id'] ?>">
                <td><?= htmlspecialchars($v['first_name']) ?></td>
                <td><?= htmlspecialchars($v['last_name']) ?></td>
                <td><?= htmlspecialchars($v['email']) ?></td>
                <td><?= htmlspecialchars($v['phone_number']) ?></td>
                <td><?= htmlspecialchars($v['address']) ?></td>
                <td><?= htmlspecialchars($v['specialization']) ?></td>
                <td><?= htmlspecialchars($v['license_number']) ?></td>
                <td>
                    <button class="btn btn-edit cta-button" style="height: 40px; width: 100px;">Izmeni</button>
                    <button class="btn btn-delete cta-button" style="height: 40px; width: 100px;">Obriši</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>

<script src="js/manage_vets.js"></script>

</body>
</html>
