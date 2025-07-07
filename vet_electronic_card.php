<?php
session_start();
require_once 'auth.php';
requireVeterinarian();
require_once 'db_config.php';
require_once 'functions.php';

$pdo = (new DBConfig())->getConnection();
$ordinacija = new VeterinarskaOrdinacija($pdo);



if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

$vetId = $_SESSION['vet_id'];

$db = new DBConfig();
$pdo = $db->getConnection();
$ordinacija = new VeterinarskaOrdinacija($pdo);
$pets = $ordinacija->getAllPets();

// Dinamički dohvataš podfolder gde se izvršava skripta (npr. /VetProjekat ili prazno ako je u root)
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

// Web putanje za prikaz slika
$photoDirWeb = $baseUrl . '/images/pets/';
$defaultPhotoWeb = $baseUrl . '/images/default.jpg';

// Fizička putanja za proveru fajla
$photoDirPhysical = __DIR__ . '/images/pets/';

?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Elektronski kartoni</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/css.css" />
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="vet_treatments_info.php">Tretmani</a></li>
            <li><a href="vet_electronic_card.php" class="active">Karton</a></li>
            <li><a href="vet_profile.php">Vet profil</a></li>
            <li><a href="vet_schedule.php">Radno vreme</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="pets-section py-5">
        <h2 class="text-center mb-5">Elektronski kartoni ljubimaca</h2>

        <?php if ($pets): ?>
            <div class="container">
                <div class="row justify-content-center gy-4">
                    <?php foreach ($pets as $pet): ?>
                        <?php
                        $photoFile = $pet['photo'] ?? '';
                        $physicalPath = $photoDirPhysical . $photoFile;

                        $imgSrc = (!empty($photoFile) && file_exists($physicalPath))
                            ? $photoDirWeb . $photoFile
                            : $defaultPhotoWeb;
                        ?>

                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="pet-card text-center p-3 h-100 border rounded shadow-sm bg-light">
                                <a href="vet_medical_record.php?pet_id=<?= htmlspecialchars($pet['pet_id']) ?>">
                                    <img src="<?= htmlspecialchars($imgSrc) ?>"
                                         alt="Slika ljubimca"
                                         width="120" height="120"
                                         style="object-fit: cover; border-radius: 50%; border: 2px solid #fff; margin-bottom: 15px;">
                                </a>
                                <h5 class="text-success mb-2"><?= htmlspecialchars($pet['name']) ?></h5>
                                <p><strong>Vlasnik:</strong> <?= htmlspecialchars($pet['owner_name']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <p class="text-center">Trenutno nema ljubimaca u bazi.</p>
        <?php endif; ?>
    </section>
</main>

<footer class="custom-footer">
    <div class="footer-content text-center py-3">
        &copy; 2025 PetCare Ordinacija. Sva prava zadržana.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
