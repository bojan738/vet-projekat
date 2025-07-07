<?php
session_start();
require_once 'db_config.php';
require_once 'functions.php';

$ordinacija = new VeterinarskaOrdinacija();
$services = $ordinacija->getAllServices1();
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetCare - Početna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/css.css">
</head>
<body>

<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="index.php" class="active">Početna</a></li>
            <li><a href="vets.php">Veterinari</a></li>
            <li><a href="login.php">Prijava</a></li>
        </ul>
    </nav>
</header>

<main class="container my-5">
    <section class="hero text-center mb-5">
        <h1>Dobrodošli u PetCare ordinaciju!</h1>
        <p>Brinemo o vašim ljubimcima kao o članovima porodice. ❤️</p>
        <?php if (isset($_SESSION['user_name'])): ?>
            <p><strong>Prijavljeni ste kao:</strong> <?= htmlspecialchars($_SESSION['user_name']) ?></p>
        <?php endif; ?>
    </section>

    <div class="row align-items-start">
        <div class="col-md-6">
            <ul class="list-group text-start">
                <?php foreach ($services as $service): ?>
                    <li class="list-group-item">✅ <?= htmlspecialchars($service['name']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="col-md-6">
            <div id="serviceCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner rounded shadow">
                    <?php for ($i = 1; $i <= 8; $i++): ?>
                        <div class="carousel-item <?= $i === 1 ? 'active' : '' ?>">
                            <img src="images/clinic/index<?= $i ?>.jpg" class="d-block w-100" alt="slika <?= $i ?>">
                        </div>
                    <?php endfor; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#serviceCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#serviceCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
    </div>
</main>

<footer class="custom-footer" >
    <div class="footer-content">
        &copy; 2025 PetCare Ordinacija. Sva prava zadržana.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>
