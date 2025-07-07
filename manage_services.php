<?php
session_start();
require_once 'auth.php';
requireAdmin();
require_once 'db_config.php';
require_once 'functions.php';

$ordinacija = new VeterinarskaOrdinacija();

if (isset($_POST['delete_id'])) {
    $ordinacija->deleteService($_POST['delete_id']);
    header("Location: manage_services.php");
    exit;
}

if (isset($_POST['update_id'])) {
    $ordinacija->updateService(
        $_POST['update_id'],
        $_POST['name'],
        $_POST['description'],
        $_POST['price']
    );
    header("Location: manage_services.php");
    exit;
}

if (isset($_POST['add'])) {
    $ordinacija->insertService(
        $_POST['name'],
        $_POST['description'],
        $_POST['price']
    );
    header("Location: manage_services.php");
    exit;
}

$services = $ordinacija->getAllServices1();
$editing = isset($_GET['edit']) ? $ordinacija->getServiceById1($_GET['edit']) : null;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Usluge</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="manage_services.php" class="active">Upravljanje servisa</a></li>
            <li><a href="manage_vets.php">Upravljanje veterinarima</a></li>
            <li><a href="manage_term.php">Upravljanje terminima</a></li>
            <li><a href="manage_users.php">Upravljanje korisnicima</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<div style="padding: 0 20px;">
    <h2><?= $editing ? 'Izmena usluge' : 'Dodaj novu uslugu' ?></h2>

    <form method="post">
        <?php if ($editing): ?>
            <input type="hidden" name="update_id" value="<?= $editing['id'] ?>">
        <?php endif; ?>
        <input type="text" name="name" class="form-input" style="width: 200px;" placeholder="Naziv" value="<?= htmlspecialchars($editing['name'] ?? '') ?>" required>
        <input type="text" name="description" class="form-input" style="width: 200px;" placeholder="Opis" value="<?= htmlspecialchars($editing['description'] ?? '') ?>">
        <input type="number" step="0.01" name="price"  class="form-input" style="width: 200px;" placeholder="Cena" value="<?= htmlspecialchars($editing['price'] ?? '') ?>" required>
        <button type="submit" name="<?= $editing ? 'update' : 'add' ?>" class="cta-button" style="height: 40px; width: 100px;">
            <?= $editing ? 'Izmeni' : 'Dodaj' ?>
        </button>
    </form>

    <h2>Lista usluga</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Naziv</th>
            <th>Opis</th>
            <th>Cena</th>
            <th>Akcije</th>
        </tr>
        <?php foreach ($services as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['name']) ?></td>
                <td><?= htmlspecialchars($s['description']) ?></td>
                <td><?= number_format($s['price'], 2) ?> RSD</td>
                <td>
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="delete_id" value="<?= $s['id'] ?>">
                        <button type="submit" onclick="return confirm('Obrisati uslugu?')" class="cta-button" style="height: 40px; width: 100px;">Obriši</button>
                    </form>
                    <a href="?edit=<?= $s['id'] ?>" ><button class="cta-button" style="height: 40px; width: 100px;">Izmeni</button></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
