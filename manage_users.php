<?php
session_start();
require_once 'auth.php';
requireAdmin();
require_once 'db_config.php';
require_once 'functions.php';

$ordinacija = new VeterinarskaOrdinacija();
$message = null;

if (isset($_POST['block_id'])) {
    $user = $ordinacija->getUserById($_POST['block_id']);
    $ordinacija->blockUser($_POST['block_id']);
    $ordinacija->sendAccountStatusEmail($user['email'], $user['first_name'], false);
    header("Location: manage_users.php");
    exit;
}

if (isset($_POST['unblock_id'])) {
    $user = $ordinacija->getUserById($_POST['unblock_id']);
    $ordinacija->unblockUser($_POST['unblock_id']);
    $ordinacija->sendAccountStatusEmail($user['email'], $user['first_name'], true);
    header("Location: manage_users.php");
    exit;
}

if (isset($_POST['update_id'])) {
    $ordinacija->updateUser(
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

$users = $ordinacija->getAllUsers();
$editing = isset($_GET['edit']) ? $ordinacija->getUserById($_GET['edit']) : null;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Korisnici</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="manage_services.php">Upravljanje servisa</a></li>
            <li><a href="manage_vets.php"  >Upravljanje veterinarima</a></li>
            <li><a href="manage_term.php">Upravljanje terminima</a></li>
            <li><a href="manage_users.php" class="active">Upravljanje korisnicima</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<div style="padding: 0 20px;">
    <?php if ($message): ?>
        <script>alert("<?= htmlspecialchars($message) ?>");</script>
    <?php endif; ?>

    <?php if ($editing): ?>
        <h2>Izmeni korisnika</h2>
        <form method="post">
            <input type="hidden" class="form-input" style="width: 180px;" name="update_id" value="<?= $editing['id'] ?>">
            <input type="text" class="form-input" style="width: 180px;" name="first_name" placeholder="Ime" value="<?= htmlspecialchars($editing['first_name']) ?>" required>
            <input type="text" class="form-input" style="width: 180px;" name="last_name" placeholder="Prezime" value="<?= htmlspecialchars($editing['last_name']) ?>" required>
            <input type="email" class="form-input" style="width: 180px;" name="email" placeholder="Email" value="<?= htmlspecialchars($editing['email']) ?>" required>
            <input type="text" class="form-input" style="width: 180px;" name="phone_number" placeholder="Telefon" value="<?= htmlspecialchars($editing['phone_number']) ?>" required>
            <input type="text" class="form-input" style="width: 180px;" name="address" placeholder="Adresa" value="<?= htmlspecialchars($editing['address']) ?>" required>
            <button type="submit" class="cta-button" style="height: 40px; width: 100px;">Sačuvaj</button>
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
            <th>Negativni poeni</th>
            <th>Status</th>
            <th>Akcije</th>
        </tr>
        <?php foreach ($users as $u): ?>
            <tr>
                <td ><?= htmlspecialchars($u['first_name']) ?></td>
                <td><?= htmlspecialchars($u['last_name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['phone_number']) ?></td>
                <td><?= htmlspecialchars($u['address'] ?? 'Nije unesena adresa') ?></td>
                <td><?= (int)($u['negative_points'] ?? 0) ?></td>
                <td><?= $u['is_active'] ? 'Aktivan' : 'Blokiran' ?></td>
                <td>
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="block_id" value="<?= $u['id'] ?>">
                        <button type="submit" class="cta-button" style="height: 40px; width: 100px;">Blokiraj</button>
                    </form>
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="unblock_id" value="<?= $u['id'] ?>">
                        <button type="submit" class="cta-button" style="height: 40px; width: 100px;">Odblokiraj</button>
                    </form>
                    <a href="?edit=<?= $u['id'] ?>"><button class="cta-button" style="height: 40px; width: 100px;">Izmeni</button></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
