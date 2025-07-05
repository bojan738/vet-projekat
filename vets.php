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

    <!-- Tvoj (glavni) CSS -->
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
    <section class="vets-section py-5">
        <h1 class="text-center mb-5">Naši veterinari</h1>

        <?php if ($veterinarians): ?>
            <div class="container">
                <div class="row justify-content-center gy-4">
                    <?php foreach ($veterinarians as $vet): ?>
                        <?php
                        $imageSrc = !empty($vet['photo']) && file_exists(__DIR__ . '/images/veterinarians/' . $vet['photo'])
                            ? 'images/veterinarians/' . $vet['photo']
                            : 'images/veterinarians/avatar.jpg';
                        ?>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="vet-card text-center p-3 h-100 border rounded shadow-sm bg-light">
                                <h5 class="text-success mb-2">
                                    Dr <?= htmlspecialchars($vet['first_name'] . ' ' . $vet['last_name']) ?>
                                </h5>

                                <img src="<?= htmlspecialchars($imageSrc) ?>"
                                     alt="Slika <?= htmlspecialchars($vet['first_name']) ?>"
                                     width="120" height="120"
                                     style="object-fit:cover;border-radius:50%;border:2px solid #ffffff;
                        display:block;margin:0 auto 15px;">

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

<footer class="custom-footer">
    <div class="footer-content">
        &copy; 2025 PetCare Ordinacija. Sva prava zadržana.
    </div>
</footer>

<!-- Bootstrap JS (bundled) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
