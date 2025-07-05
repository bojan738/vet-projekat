<?php
require_once 'db.php';
require_once 'functions.php';
session_start();

$message = null;

if (isset($_POST['delete_id'])) {
    delete_user($pdo, $_POST['delete_id']);
    header("Location: manage_users.php");
    exit;
}

if (isset($_POST['update_id'])) {
    update_user(
        $pdo,
        $_POST['update_id'],
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['email'],
        $_POST['phone_number'],
        $_POST['address']
    );
    header("Location: manage_users.php");
    exit;
}

if (isset($_POST['add_user'])) {
    $result = insert_user(
        $pdo,
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['email'],
        $_POST['phone_number'],
        $_POST['address'],
        3 // owner role
    );
    if (!$result['success']) {
        $message = $result['message'];
    } else {
        header("Location: manage_users.php");
        exit;
    }
}

$users = get_all_users($pdo);
$editing = isset($_GET['edit']) ? get_user_by_id($pdo, $_GET['edit']) : null;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Korisnici</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="manage_services.php">Manage Services</a></li>
            <li><a href="manage_vets.php">Manage Vets</a></li>
            <li><a href="manage_term.php">Manage Term</a></li>
            <li><a href="manage_users.php" class="active">Manage Users</a></li>
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
    <h2>Izmeni korisnika</h2>
    <form method="post">
        <input type="hidden" name="update_id" value="<?= $editing['id'] ?>">
        <input type="text" name="first_name" placeholder="Ime" value="<?= $editing['first_name'] ?>" required>
        <input type="text" name="last_name" placeholder="Prezime" value="<?= $editing['last_name'] ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?= $editing['email'] ?>" required>
        <input type="text" name="phone_number" placeholder="Telefon" value="<?= $editing['phone_number'] ?>" required>
        <input type="text" name="address" placeholder="Adresa" value="<?= $editing['address'] ?>" required>
        <button type="submit">Sačuvaj</button>
    </form>
<?php else: ?>
    <h2>Dodaj korisnika</h2>
    <form method="post">
        <input type="text" name="first_name" placeholder="Ime" required>
        <input type="text" name="last_name" placeholder="Prezime" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone_number" placeholder="Telefon" required>
        <input type="text" name="address" placeholder="Adresa" required>
        <button type="submit" name="add_user">Dodaj</button>
    </form>
<?php endif; ?>

<h2>Lista vlasnika</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Ime</th>
        <th>Prezime</th>
        <th>Email</th>
        <th>Telefon</th>
        <th>Adresa</th>
        <th>Akcije</th>
    </tr>
    <?php foreach ($users as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['first_name']) ?></td>
            <td><?= htmlspecialchars($u['last_name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['phone_number']) ?></td>
            <td><?= htmlspecialchars($u['address']) ?></td>
            <td>
                <form method="post" style="display:inline-block;">
                    <input type="hidden" name="delete_id" value="<?= $u['id'] ?>">
                    <button type="submit" onclick="return confirm('Obrisati korisnika?')">Obriši</button>
                </form>
                <a href="?edit=<?= $u['id'] ?>">
                    <button>Izmeni</button>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
