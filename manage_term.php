<?php
require_once 'db.php';
require_once 'functions.php';
session_start();

if (isset($_POST['delete_id'])) {
    delete_appointment($pdo, $_POST['delete_id']);
    header("Location: manage_term.php");
    exit;
}

if (isset($_POST['update_id'])) {
    update_appointment(
        $pdo,
        $_POST['update_id'],
        $_POST['appointment_date'],
        $_POST['status'],
        $_POST['notes']
    );
    header("Location: manage_term.php");
    exit;
}

$filter_date = $_GET['date'] ?? null;
$appointments = $filter_date ? get_appointments_by_date($pdo, $filter_date) : get_all_appointments($pdo);
$editing = isset($_GET['edit']) ? get_appointment_by_id($pdo, $_GET['edit']) : null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Termini</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="manage_services.php">Manage Services</a></li>
            <li><a href="manage_vets.php">Manage Vets</a></li>
            <li><a href="manage_term.php" class="active">Manage Term</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<h2>Filter po datumu</h2>
<form method="get">
    <input type="date" name="date" value="<?= htmlspecialchars($filter_date ?? '') ?>">
    <button type="submit">Filtriraj</button>
    <a href="manage_term.php"><button type="button">Poništi filter</button></a>
</form>

<?php if ($editing): ?>
    <h2>Izmena termina</h2>
    <form method="post">
        <input type="hidden" name="update_id" value="<?= $editing['id'] ?>">
        <input type="datetime-local" name="appointment_date" value="<?= date('Y-m-d\TH:i', strtotime($editing['appointment_date'])) ?>" required>
        <select name="status">
            <option value="zakazano" <?= $editing['status'] === 'zakazano' ? 'selected' : '' ?>>Zakazano</option>
            <option value="obavljeno" <?= $editing['status'] === 'obavljeno' ? 'selected' : '' ?>>Obavljeno</option>
            <option value="otkazano" <?= $editing['status'] === 'otkazano' ? 'selected' : '' ?>>Otkazano</option>
        </select>
        <input type="text" name="notes" placeholder="Napomena" value="<?= $editing['notes'] ?? '' ?>">
        <button type="submit" name="update">Izmeni</button>
    </form>
<?php endif; ?>

<h2><?= $filter_date ? 'Termini za: ' . htmlspecialchars($filter_date) : 'Svi termini' ?></h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Datum</th>
        <th>Status</th>
        <th>Ljubimac</th>
        <th>Veterinar</th>
        <th>Napomena</th>
        <th>Akcije</th>
    </tr>
    <?php foreach ($appointments as $a): ?>
        <tr>
            <td><?= htmlspecialchars($a['appointment_date']) ?></td>
            <td><?= htmlspecialchars($a['status']) ?></td>
            <td><?= htmlspecialchars($a['pet_name']) ?></td>
            <td><?= htmlspecialchars($a['vet_first_name'] . ' ' . $a['vet_last_name']) ?></td>
            <td><?= htmlspecialchars($a['notes']) ?></td>
            <td>
                <form method="post" style="display:inline-block;">
                    <input type="hidden" name="delete_id" value="<?= $a['id'] ?>">
                    <button type="submit" onclick="return confirm('Obrisati termin?')">Obriši</button>
                </form>
                <a href="?edit=<?= $a['id'] ?>"><button>Izmeni</button></a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
