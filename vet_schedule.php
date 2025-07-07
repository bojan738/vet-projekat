<?php
session_start();
require_once 'auth.php';
requireVeterinarian();
require_once 'db_config.php';
require_once 'functions.php';


if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

$vetId = $_SESSION['vet_id'];

$db = new DBConfig();
$pdo = $db->getConnection();
$ordinacija = new VeterinarskaOrdinacija($pdo);

$schedule = $ordinacija->getVetSchedule($vetId);
$daysOfWeek = ['Ponedeljak', 'Utorak', 'Sreda', 'Cetvrtak', 'Petak', 'Subota'];
$timeSlots = $ordinacija->getTimeSlots();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Radno vreme</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body class="vet-schedule-page">

<header>
    <div class="logo">🐾 PetCare</div>
    <nav>
        <ul>
            <li><a href="vet_treatments_info.php">Tretmani</a></li>
            <li><a href="vet_electronic_card.php">Karton</a></li>
            <li><a href="vet_profile.php">Vet profil</a></li>
            <li><a href="vet_schedule.php" class="active">Radno vreme</a></li>
            <li><a href="logout.php">Odjavi se</a></li>
        </ul>
    </nav>
</header>

<?php if (isset($_SESSION['msg'])): ?>
    <script>alert("<?= htmlspecialchars($_SESSION['msg']) ?>");</script>
    <?php unset($_SESSION['msg']); ?>
<?php endif; ?>

<h2 class="form-title">Dodaj radni termin</h2>
<form method="POST" action="vet_add_schedule.php" class="form-section vet-schedule-form">
    <div class="form-row vet-form-row">
        <div class="form-group vet-form-group">
            <label for="day_of_week" class="form-label vet-form-label">Dan:</label>
            <select id="day_of_week" name="day_of_week" required class="form-input vet-form-input">
                <option value="" disabled selected>Select day</option>
                <?php foreach ($daysOfWeek as $day): ?>
                    <option value="<?= $day ?>"><?= $day ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group vet-form-group">
            <label for="start_time" class="form-label vet-form-label">Pocetak:</label>
            <select id="start_time" name="start_time" required class="form-input vet-form-input">
                <option value="" disabled selected>Izaberi vreme</option>
                <?php foreach ($timeSlots as $time): ?>
                    <option value="<?= $time ?>"><?= substr($time, 0, 5) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group vet-form-group">
            <label for="end_time" class="form-label vet-form-label">Kraj:</label>
            <select id="end_time" name="end_time" required class="form-input vet-form-input">
                <option value="" disabled selected>Izaberi vreme</option>
                <?php foreach ($timeSlots as $time): ?>
                    <option value="<?= $time ?>"><?= substr($time, 0, 5) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <button type="submit" name="add_schedule" class="cta-button vet-cta-button">Dodaj</button>
</form>
<div style="padding-left: 20px; padding-right: 20px">
<h2>Lista termina</h2>
<table class="vet-table">
    <thead>
    <tr>
        <th>Dan</th>
        <th>Pocetak</th>
        <th>Kraj</th>
        <th>Akcije</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($schedule as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['day_of_week']) ?></td>
            <td><?= substr($row['start_time'], 0, 5) ?></td>
            <td><?= substr($row['end_time'], 0, 5) ?></td>
            <td>
                <form method="POST" action="vet_schedule_edit.php" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit" class="cta-button" style="height: 40px; width: 100px;">Izmeni</button>
                </form>
                <form method="POST" action="vet_schedule_delete.php" style="display:inline;" onsubmit="return confirm('Da li ste sigurni da zelite da izbrisete ovaj termin?');">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit" class="cta-button" style="height: 40px; width: 100px;">Izbrisi</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</body>
</html>
