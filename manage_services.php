<?php
require_once 'db.php';
require_once 'functions.php';
session_start();

if (isset($_POST['delete_id'])) {
    delete_service($pdo, $_POST['delete_id']);
    header("Location: manage_services.php");
    exit;
}

if (isset($_POST['update_id'])) {
    update_service($pdo, $_POST['update_id'], $_POST['name'], $_POST['description'], $_POST['price']);
    header("Location: manage_services.php");
    exit;
}

if (isset($_POST['add'])) {
    insert_service($pdo, $_POST['name'], $_POST['description'], $_POST['price']);
    header("Location: manage_services.php");
    exit;
}

$services = get_all_services($pdo);
$editing = isset($_GET['edit']) ? get_service_by_id($pdo, $_GET['edit']) : null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Usluge</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="manage_services.php" class="active">Manage Services</a></li>
            <li><a href="manage_vets.php">Manage Vets</a></li>
            <li><a href="manage_term.php">Manage Term</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<h2><?= $editing ? 'Izmena usluge' : 'Dodaj novu uslugu' ?></h2>
<form method="post">
    <?php if ($editing): ?>
        <input type="hidden" name="update_id" value="<?= $editing['id'] ?>">
    <?php endif; ?>
    <input type="text" name="name" placeholder="Naziv" value="<?= $editing['name'] ?? '' ?>" required>
    <input type="text" name="description" placeholder="Opis" value="<?= $editing['description'] ?? '' ?>">
    <input type="number" step="0.01" name="price" placeholder="Cena" value="<?= $editing['price'] ?? '' ?>" required>
    <button type="submit" name="<?= $editing ? 'update' : 'add' ?>">
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
                    <button type="submit" onclick="return confirm('Obrisati uslugu?')">Obriši</button>
                </form>
                <a href="?edit=<?= $s['id'] ?>"><button>Izmeni</button></a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
