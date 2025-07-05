<?php
session_start();
require_once 'db.php';
require_once 'functions.php';
$services = get_all_services($pdo);
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
        <!-- Leva strana: Usluge -->
        <div class="col-md-6">
            <ul class="list-group text-start">
                <?php foreach ($services as $service): ?>
                    <li class="list-group-item">✅ <?= htmlspecialchars($service['name']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Desna strana: Carousel bez strelica -->
        <div class="col-md-6">
            <div id="serviceCarousel" class="carousel custom-carousel">
                <div class="carousel-inner rounded shadow">
                    <div class="carousel-item active">
                        <img src="images/clinic/index1.jpg" class="d-block w-100" alt="slika 1">
                    </div>
                    <div class="carousel-item">
                        <img src="images/clinic/index2.jpg" class="d-block w-100" alt="slika 2">
                    </div>
                    <div class="carousel-item">
                        <img src="images/clinic/index3.jpg" class="d-block w-100" alt="slika 3">
                    </div>
                    <div class="carousel-item">
                        <img src="images/clinic/index4.jpg" class="d-block w-100" alt="slika 4">
                    </div>
                    <div class="carousel-item">
                        <img src="images/clinic/index5.jpg" class="d-block w-100" alt="slika 5">
                    </div>
                    <div class="carousel-item">
                        <img src="images/clinic/index6.jpg" class="d-block w-100" alt="slika 6">
                    </div>
                    <div class="carousel-item">
                        <img src="images/clinic/index7.jpg" class="d-block w-100" alt="slika 7">
                    </div>
                    <div class="carousel-item">
                        <img src="images/clinic/index8.jpg" class="d-block w-100" alt="slika 8">
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="custom-footer">
    <div class="footer-content">
        &copy; 2025 PetCare Ordinacija. Sva prava zadržana.
    </div>
</footer>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>
