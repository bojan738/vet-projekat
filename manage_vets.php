<?php
require_once 'db.php';
require_once 'functions.php';
session_start();

$message = null;

if (isset($_POST['delete_id'])) {
    delete_veterinarian($pdo, $_POST['delete_id']);
    header("Location: manage_vets.php");
    exit;
}

if (isset($_POST['update_id'])) {
    update_veterinarian_full(
        $pdo,
        $_POST['update_id'],
        $_POST['user_id'],
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['email'],
        $_POST['phone_number'],
        $_POST['address'],
        $_POST['specialization'],
        $_POST['license_number']
    );
    header("Location: manage_vets.php");
    exit;
}

if (isset($_POST['add_vet'])) {
    $result = insert_full_veterinarian(
        $pdo,
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['email'],
        $_POST['phone_number'],
        $_POST['address'],
        $_POST['specialization'],
        $_POST['license_number']
    );

    if (!$result['success']) {
        $message = $result['message'];
    } else {
        header("Location: manage_vets.php");
        exit;
    }
}

$vets = get_all_veterinarians($pdo);
$editing = isset($_GET['edit']) ? get_veterinarian_by_id($pdo, $_GET['edit']) : null;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Veterinari</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="manage_services.php">Manage Services</a></li>
            <li><a href="manage_vets.php" class="active">Manage Vets</a></li>
            <li><a href="manage_term.php">Manage Term</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<?php if ($message): ?>
    <script>
        alert("<?= htmlspecialchars($message) ?>");
    </script>
<?php endif; ?>

<?php if ($editing): ?>
    <h2>Izmena veterinara</h2>
    <form method="post">
        <input type="hidden" name="update_id" value="<?= $editing['id'] ?>">
        <input type="hidden" name="user_id" value="<?= $editing['user_id'] ?>">
        <input type="text" name="first_name" placeholder="Ime" value="<?= $editing['first_name'] ?>" required>
        <input type="text" name="last_name" placeholder="Prezime" value="<?= $editing['last_name'] ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?= $editing['email'] ?>" required>
        <input type="text" name="phone_number" placeholder="Telefon" value="<?= $editing['phone_number'] ?>" required>
        <input type="text" name="address" placeholder="Adresa" value="<?= $editing['address'] ?>" required>
        <input type="text" name="specialization" placeholder="Specijalizacija" value="<?= $editing['specialization'] ?>" required>
        <input type="text" name="license_number" placeholder="Licenca" value="<?= $editing['license_number'] ?>" required>
        <button type="submit">Sačuvaj</button>
    </form>
<?php else: ?>
    <h2>Dodaj veterinara</h2>
    <form method="post">
        <input type="text" name="first_name" placeholder="Ime" required>
        <input type="text" name="last_name" placeholder="Prezime" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone_number" placeholder="Telefon" required>
        <input type="text" name="address" placeholder="Adresa" required>
        <input type="text" name="specialization" placeholder="Specijalizacija" required>
        <input type="text" name="license_number" placeholder="Licenca" required>
        <button type="submit" name="add_vet">Dodaj</button>
    </form>
<?php endif; ?>

<h2>Lista veterinara</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Ime</th>
        <th>Prezime</th>
        <th>Email</th>
        <th>Telefon</th>
        <th>Adresa</th>
        <th>Specijalizacija</th>
        <th>Licenca</th>
        <th>Akcije</th>
    </tr>
    <?php foreach ($vets as $v): ?>
        <tr>
            <td><?= htmlspecialchars($v['first_name']) ?></td>
            <td><?= htmlspecialchars($v['last_name']) ?></td>
            <td><?= htmlspecialchars($v['email']) ?></td>
            <td><?= htmlspecialchars($v['phone_number']) ?></td>
            <td><?= htmlspecialchars($v['address']) ?></td>
            <td><?= htmlspecialchars($v['specialization']) ?></td>
            <td><?= htmlspecialchars($v['license_number']) ?></td>
            <td>
                <form method="post" style="display:inline-block;">
                    <input type="hidden" name="delete_id" value="<?= $v['id'] ?>">
                    <button type="submit" onclick="return confirm('Obrisati veterinara?')">Obriši</button>
                </form>
                <a href="?edit=<?= $v['id'] ?>">
                    <button>Izmeni</button>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
