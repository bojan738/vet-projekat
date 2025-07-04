<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Dohvatanje vrsta i grupa rasa povezanih po vrsti
$breeds_by_type = get_breeds_grouped_by_type($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['pet_name'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = (int)$_POST['age'];
    $gender = $_POST['gender'];
    $birth_date = $_POST['birth_date'];

    // Validacija: starost i datum rođenja se moraju poklapati
    $today = new DateTime();
    $birth = new DateTime($birth_date);
    $interval = $birth->diff($today);
    $calculated_age = $interval->y;

    if ($calculated_age !== $age) {
        $message = "⚠️ Starost i datum rođenja se ne poklapaju (računato: $calculated_age god.).";
    } else {
        // Upload slike
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $targetDir = "uploads/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $image_path = $targetDir . time() . '_' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
        }

        // Ubacivanje vrste i rase u bazu ako ne postoje
        $type_id = insert_pet_type($pdo, $species);
        $breed_id = insert_pet_breed($pdo, $breed, $type_id);

        // Dohvati owner_id ili ga napravi
        $owner_id = get_owner_id_by_user_id($pdo, $_SESSION['user_id']);
        if (!$owner_id) {
            $stmt = $pdo->prepare("INSERT INTO pet_owners (user_id) VALUES (?)");
            $stmt->execute([$_SESSION['user_id']]);
            $owner_id = $pdo->lastInsertId();
        }

        // Dodaj ljubimca
        add_pet($pdo, $owner_id, $name, $type_id, $age, $gender, $breed_id, $image_path, $birth_date);
        $message = "✅ Ljubimac uspešno dodat!";
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
                <input type="date" name="birth_date" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">Pol</label>
                <select name="gender" class="form-input" required>
                    <option value="">-- Izaberi pol --</option>
                    <option value="m">Mužjak</option>
                    <option value="f">Ženka</option>
                    <option value="n">Nepoznat</option>
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

<!-- Script za dinamičko filtriranje rasa po vrsti -->
<script>
    const breedsByType = <?= json_encode($breeds_by_type, JSON_UNESCAPED_UNICODE) ?>;

    document.getElementById('speciesSelect').addEventListener('change', function () {
        const selectedType = this.value;
        const breedSelect = document.getElementById('breedSelect');
        breedSelect.innerHTML = '<option value="">-- Izaberi rasu --</option>';

        if (breedsByType[selectedType]) {
            breedsByType[selectedType].forEach(breed => {
                const option = document.createElement('option');
                option.value = breed;
                option.textContent = breed;
                breedSelect.appendChild(option);
            });
        }
    });
</script>

</body>
</html>
