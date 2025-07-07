<?php
session_start();
require_once 'auth.php';
requireRegularUser();
require_once 'db_config.php';
require_once 'functions.php';

$message = '';
$ordinacija = new VeterinarskaOrdinacija();

$user_id = $_SESSION['user_id'];
$breeds_by_type = $ordinacija->getBreedsGroupedByType();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['pet_name'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = (int)$_POST['age'];
    $gender = $_POST['gender'];
    $birth_date = $_POST['birth_date'];

    $birth = new DateTime($birth_date);
    $today = new DateTime();

    if ($birth > $today) {
        $message = "⚠️ Datum rođenja ne može biti u budućnosti.";
    } else {
        $calculated_age = $birth->diff($today)->y;
        if ($calculated_age !== $age) {
            $message = "⚠️ Starost i datum rođenja se ne poklapaju (računato: $calculated_age god.).";
        } else {
            $image_path = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'images/pets/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $filename = basename($_FILES['image']['name']);
                $target_path = $upload_dir . $filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    $image_path = $filename;
                }
            }

            // Inset pet type and pet breed

            $type_id = $ordinacija->insertPetType($species);
            $breed_id = $ordinacija->insertPetBreed($breed, $type_id);

            $owner_id = $ordinacija->getOwnerIdByUserId($user_id);
            if (!$owner_id) {
                $owner_id = $ordinacija->create_owner_for_user($user_id);
            }

            // Input pet
            $ordinacija->add_pet($owner_id, $name, $type_id, $age, $gender, $breed_id, $image_path, $birth_date);

            header("Location: pet_information.php");
            exit;
        }
    }
}
?>



<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dodaj ljubimca</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>

<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="users_reservation.php">Rezervacije</a></li>
            <li><a href="user_information.php">Korisnik</a></li>
            <li><a href="pet_information.php">Ljubimci</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="form-section">
        <h2 class="form-title">Dodaj ljubimca</h2>

        <?php if ($message): ?>
            <p style="color: green; font-weight: bold;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Ime ljubimca</label>
                <input type="text" name="pet_name" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">Vrsta</label>
                <select name="species" class="form-input" id="speciesSelect" required>
                    <option value="">-- Izaberi vrstu --</option>
                    <?php foreach ($breeds_by_type as $type => $rases): ?>
                        <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Rasa</label>
                <select name="breed" class="form-input" id="breedSelect" required>
                    <option value="">-- Prvo izaberi vrstu --</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Starost (u godinama)</label>
                <input type="number" name="age" min="0" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">Datum rođenja</label>
                <input type="date" name="birth_date" class="form-input" required max="<?= date('Y-m-d') ?>">

            </div>

            <div class="form-group">
                <label class="form-label">Pol</label>
                <select name="gender" class="form-input" required>
                    <option value="">-- Izaberi pol --</option>
                    <option value="muski">Mužjak</option>
                    <option value="zenski">Ženka</option>
                    <option value="nepoznat">Nepoznat</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Slika ljubimca</label>
                <input type="file" name="image" accept="image/*" class="form-input">
            </div>

            <button type="submit" class="cta-button">Dodaj ljubimca</button>
        </form>
    </section>
</main>

<footer>
    <p>&copy; 2025 PetCare Ordinacija</p>
</footer>

<script id="breedsByTypeData" type="application/json"><?= json_encode($breeds_by_type, JSON_UNESCAPED_UNICODE) ?></script>
<script src="js/pet_add.js"></script>



</body>
</html>
