<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

$vetId = $_SESSION['vet_id'];
$schedule = get_vet_schedule($pdo, $vetId);
$daysOfWeek = ['Ponedeljak', 'Utorak', 'Sreda', 'Cetvrtak', 'Petak', 'Subota'];
$timeSlots = get_time_slots();  // niz vremena po 30 min, npr. '08:00:00', '08:30:00', ...

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Upravljanje radnim vremenom</title>
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

<h2 class="form-title">Dodaj termin</h2>
<form method="POST" action="vet_add_schedule.php" class="form-section vet-schedule-form">
    <div class="form-row vet-form-row">
        <div class="form-group vet-form-group">
            <label for="day_of_week" class="form-label vet-form-label">Dan u nedelji:</label>
            <select id="day_of_week" name="day_of_week" required class="form-input vet-form-input">
                <option value="" disabled selected>Izaberi dan</option>
                <?php foreach ($daysOfWeek as $day): ?>
                    <option value="<?= $day ?>"><?= $day ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group vet-form-group">
            <label for="start_time" class="form-label vet-form-label">Početak:</label>
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

    <button type="submit" name="add_schedule" class="cta-button vet-cta-button">Dodaj termin</button>
</form>


<h2>Lista termina</h2>
<table class="vet-table">
    <thead>
    <tr>
        <th>Dan</th>
        <th>Početak</th>
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
                    <button type="submit" class="vet-btn-link">Izmeni</button>
                </form>
                <form method="POST" action="vet_schedule_delete.php" style="display:inline;" onsubmit="return confirm('Da li ste sigurni da želite da obrišete ovaj termin?');">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit" class="vet-btn-link" style="color:#f44336;">Obriši</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
