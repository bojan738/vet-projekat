<?php
require_once 'db.php';
require_once 'functions.php';

$veterinarians = get_all_veterinarians($pdo);
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>PetCare - Veterinari</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tvoj CSS -->
    <link rel="stylesheet" href="css/css.css" />
</head>
<body>

<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="index.php">Početna</a></li>
            <li><a href="vets.php" class="active">Veterinari</a></li>
            <li><a href="login.php">Prijava</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="vets-section">
        <h1 class="mb-5">Naši veterinari</h1>

        <?php if ($veterinarians): ?>
            <div class="container">
                <div class="row justify-content-center gy-4">
                    <?php foreach ($veterinarians as $vet): ?>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="vet-card text-center p-3 h-100">
                                <h5 class="text-success mb-2">Dr <?= htmlspecialchars($vet['first_name'] . ' ' . $vet['last_name']) ?></h5>
                                <?php if (!empty($vet['photo'])): ?>
                                    <img src="images/veterinarians/<?= htmlspecialchars($vet['photo']) ?>" alt="Slika <?= htmlspecialchars($vet['first_name']) ?>" class="img-fluid rounded-circle mb-3" style="max-width: 120px; height: 120px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="images/veterinarians/vet1.jpg" alt="Podrazumevana slika" class="img-fluid rounded-circle mb-3" style="max-width: 120px; height: 120px; object-fit: cover;">
                                <?php endif; ?>
                                <p><strong>Specijalizacija:</strong> <?= htmlspecialchars($vet['specialization'] ?: 'Nije navedena') ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($vet['email']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <p class="text-center">Trenutno nema veterinara u bazi.</p>
        <?php endif; ?>
    </section>
</main>

<footer>
    <p>&copy; 2025 PetCare Ordinacija. Sva prava zadržana.</p>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
