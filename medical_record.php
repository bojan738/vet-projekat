<?php
require_once 'db.php';
require_once 'functions.php';
session_start();

if (isset($_POST['delete_id'])) {
    delete_medical_record($pdo, $_POST['delete_id']);
    header("Location: medical_record.php");
    exit;
}

if (isset($_POST['update_id'])) {
    update_medical_record(
        $pdo,
        $_POST['update_id'],
        $_POST['diagnosis'],
        $_POST['treatment'],
        $_POST['price']
    );
    header("Location: medical_record.php");
    exit;
}

$records = get_all_medical_records($pdo);
$editing = isset($_GET['edit']) ? get_medical_record_by_id($pdo, $_GET['edit']) : null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Medicinski kartoni</title>
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
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="medical_record.php" class="active">Medical Records</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<h2><?= $editing ? 'Izmena zapisa' : 'Pregled zapisa' ?></h2>
<?php if ($editing): ?>
    <form method="post">
        <input type="hidden" name="update_id" value="<?= $editing['id'] ?>">
        <input type="text" name="diagnosis" placeholder="Dijagnoza" value="<?= $editing['diagnosis'] ?>" required>
        <input type="text" name="treatment" placeholder="Tretman" value="<?= $editing['treatment'] ?>" required>
        <input type="number" step="0.01" name="price" placeholder="Cena" value="<?= $editing['price'] ?>" required>
        <button type="submit" name="update">Sačuvaj izmene</button>
    </form>
<?php endif; ?>

<h2>Lista medicinskih kartona</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Ljubimac</th>
        <th>Veterinar</th>
        <th>Dijagnoza</th>
        <th>Tretman</th>
        <th>Cena</th>
        <th>Datum</th>
        <th>Akcije</th>
    </tr>
    <?php foreach ($records as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['pet_name']) ?></td>
            <td><?= htmlspecialchars($r['vet_first_name'] . ' ' . $r['vet_last_name']) ?></td>
            <td><?= htmlspecialchars($r['diagnosis']) ?></td>
            <td><?= htmlspecialchars($r['treatment']) ?></td>
            <td><?= number_format($r['price'], 2) ?> RSD</td>
            <td><?= htmlspecialchars($r['created_at']) ?></td>
            <td>
                <form method="post" style="display:inline-block;">
                    <input type="hidden" name="delete_id" value="<?= $r['id'] ?>">
                    <button type="submit" onclick="return confirm('Obrisati zapis?')">Obriši</button>
                </form>
                <a href="?edit=<?= $r['id'] ?>"><button>Izmeni</button></a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
