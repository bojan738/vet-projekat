<?php
session_start();
require_once 'db.php';

// Učitavanje usluga iz baze
$stmt = $pdo->query("SELECT name FROM services ORDER BY name");
$services = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetCare - Početna</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>

<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="users_index.php">Home</a></li>
            <li><a href="users_reservation.php">Reservation</a></li>
            <li><a href="user_information.php">User Information</a></li>
            <li><a href="pet_information.php">Pet</a></li>
            <li><a href="change_reservation.php" class="active">Change reservation</a></li>
            <li><a href="pet_treatments.php" class="active">Pet treatments</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="hero">
        <h1>Dobrodošli u PetCare ordinaciju!</h1>
        <p>Brinemo o vašim ljubimcima kao o članovima porodice. ❤️</p>
        <?php if (isset($_SESSION['user_name'])): ?>
            <p><strong>Prijavljeni ste kao:</strong> <?= htmlspecialchars($_SESSION['user_name']) ?></p>
        <?php endif; ?>
    </section>

    <section class="services">
        <h2>Naše usluge</h2>
        <ul>
            <?php foreach ($services as $service): ?>
                <li>✅ <?= htmlspecialchars($service['name']) ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
</main>

<footer>
    <p>&copy; 2025 PetCare Ordinacija. Sva prava zadržana.</p>
</footer>

</body>
</html>
