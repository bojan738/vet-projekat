<?php
session_start();
require_once 'auth.php';
requireRegularUser();
require_once 'db_config.php';
require_once 'functions.php';

$ordinacija = new VeterinarskaOrdinacija();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Delete pet
if (isset($_GET['delete_pet_id']) && is_numeric($_GET['delete_pet_id'])) {
    $pet_id = (int)$_GET['delete_pet_id'];

    if ($ordinacija->petBelongsToUser($pet_id, $user_id)) {
        $ordinacija->petDelete($pet_id);
        $_SESSION['msg'] = "✅ Ljubimac obrisan.";
    } else {
        $_SESSION['msg'] = "⚠️ Nemate pravo da obrišete ovog ljubimca.";
    }
    header("Location: pet_information.php");
    exit;
}

// Load data
$pets = $ordinacija->getPetsByOwner1($user_id);
$breeds_by_type = $ordinacija->getBreedsGroupedByType();

// Update pet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pet_id'])) {
    $pet_id = $_POST['pet_id'];
    $name = $_POST['pet_name'];
    $type_name = $_POST['species'];
    $age = (int)$_POST['age'];
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $breed_name = $_POST['breed'];

    $today = new DateTime();
    $birth = new DateTime($birth_date);
    $interval = $birth->diff($today);
    $calculated_age = $interval->y;

    if ($calculated_age !== $age) {
        $_SESSION['msg'] = "⚠️ Starost i datum rođenja se ne poklapaju (računato: $calculated_age god.).";
        header("Location: pet_information.php");
        exit;
    }

    // Images
    $image_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'images/pets/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $filename = basename($_FILES['photo']['name']);
        $target_path = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_path)) {
            $image_path = $filename;
        }
    }

    $ordinacija->updatePet($pet_id, $name, $type_name, $age, $birth_date, $gender, $breed_name, $image_path);
    $_SESSION['msg'] = "✅ Ljubimac je uspešno ažuriran.";
    header("Location: pet_information.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Moj ljubimac</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/css.css">
    <script>
        const breedsByType = <?= json_encode($breeds_by_type, JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <style>
        .pet-card, .pet-form-horizontal {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .pet-card {
            cursor: pointer;
            text-align: center;
            width: 180px;
            margin: 10px;
        }
        .pet-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .hidden {
            display: none;
        }
        .pet-form-horizontal {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin: 30px auto;
            max-width: 900px;
            position: relative;
        }
        .form-left {
            flex: 1 1 250px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .pet-form-image {
            width: 150px;
            height: 150px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .form-right {
            flex: 2 1 500px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        input, select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .cta-button {
            align-self: flex-start;
            padding: 12px 25px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }

    </style>
</head>
<body>

<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="pet_information.php" class="active">Moj ljubimac</a></li>
            <li><a href="pet_treatments.php">Tretman ljubimca</a></li>
            <li><a href="users_reservation.php">Rezervacije</a></li>
            <li><a href="change_reservation.php">Promeni rezervaciju</a></li>
            <li><a href="user_information.php">Moj nalog</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<main class="container my-5">
    <h2 class="text-center mb-4">Moji ljubimci</h2>

    <?php if (isset($_SESSION['msg'])): ?>
        <div class="alert alert-info text-center"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

    <div class="text-center mb-4">
        <a href="pet_add.php" class="btn btn-success">➕ Dodaj ljubimca</a>
    </div>

    <?php if (empty($pets)): ?>
        <div class="text-center">
            <p>Nemate još nijednog ljubimca.</p>
        </div>
    <?php else: ?>
        <div class="d-flex flex-wrap justify-content-center" id="petGallery">
            <?php foreach ($pets as $pet): ?>
                <a href="medical_record.php?pet_id=<?= $pet['id'] ?>" class="pet-card" style="text-decoration:none; color:inherit;">
                    <img src="Images/pets/<?= htmlspecialchars($pet['photo'] ?? 'default_pet.jpg') ?>" alt="Ljubimac" class="pet-form-image">
                    <p><strong><?= htmlspecialchars($pet['name']) ?></strong></p>
                    <small><?= htmlspecialchars($pet['type_name']) ?></small>
                </a>
            <?php endforeach; ?>
        </div>


        <?php foreach ($pets as $pet): ?>
            <form method="POST" enctype="multipart/form-data" id="form-<?= $pet['id'] ?>" class="pet-form-horizontal hidden">
                <input type="hidden" name="pet_id" value="<?= $pet['id'] ?>">

                <div class="form-left " >
                    <img src="Images/pets/<?= htmlspecialchars($pet['photo'] ?? 'default_pet.jpg') ?>" alt="Ljubimac" class="pet-form-image">


                </div>

                <div class="form-right">
                    <div class="form-group">
                        <label>Ime ljubimca</label>
                        <input type="text" name="pet_name" value="<?= htmlspecialchars($pet['name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Vrsta</label>
                        <select name="species" class="species-select" required onchange="populateBreed(this)">
                            <option value="">-- Izaberi vrstu --</option>
                            <?php foreach ($breeds_by_type as $type => $rases): ?>
                                <option value="<?= $type ?>" <?= $pet['type_name'] == $type ? 'selected' : '' ?>><?= $type ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Rasa</label>
                        <select name="breed" class="form-input breed-select" required>
                            <option value="">-- Prvo izaberi vrstu --</option>
                        </select>
                        <input type="hidden" class="current-breed-value" value="<?= htmlspecialchars($pet['breed_name']) ?>">

                    </div>

                    <div class="form-group">
                        <label>Starost</label>
                        <input type="number" name="age" value="<?= $pet['age'] ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Datum rođenja</label>
                        <input type="date" name="birth_date" value="<?= $pet['birth_date'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Pol</label>
                        <select name="gender" required>
                            <option value="muski" <?= $pet['gender'] == 'muski' ? 'selected' : '' ?>>Mužjak</option>
                            <option value="zenski" <?= $pet['gender'] == 'zenski' ? 'selected' : '' ?>>Ženka</option>
                            <option value="nepoznat" <?= $pet['gender'] == 'nepoznat' ? 'selected' : '' ?>>Nepoznat</option>
                        </select>
                    </div>


                    <div class="form-group">
                        <label>Promeni sliku</label>
                        <input type="file" name="photo" accept="image/*">
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="cta-button">Sačuvaj izmene</button>
                        <a href="#" onclick="resetView()" class="btn btn-outline-secondary">🔙 Nazad</a>
                        <a href="pet_information.php?delete_pet_id=<?= $pet['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Da li ste sigurni da želite da obrišete ljubimca?')">🗑️ Obriši ljubimca</a>
                    </div>
                </div>
            </form>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<footer class="custom-footer" style="padding: 10px; text-align: center;">
    <div class="footer-content" style="padding: 10px;">
        &copy; 2025 PetCare Ordinacija. Sva prava zadržana.
    </div>
</footer>
<script>
    window.breedsByType = <?= json_encode($breeds_by_type, JSON_UNESCAPED_UNICODE) ?>;
</script>


<script src="js/pet_information.js"></script>



</body>
</html>
