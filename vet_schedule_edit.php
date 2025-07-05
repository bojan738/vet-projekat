<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['vet_id'])) {
    header("Location: login.php");
    exit;
}

$vetId = $_SESSION['vet_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && !isset($_POST['update'])) {
    // Prikaz forme za izmenu
    $id = (int)$_POST['id'];
    $schedule = get_schedule_by_id($pdo, $id, $vetId);
    if (!$schedule) {
        $_SESSION['msg'] = "Termin nije pronađen.";
        header("Location: vet_schedule.php");
        exit;
    }
    $timeSlots = get_time_slots(); // niz termina od pola sata od 08:00 do 22:00
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Izmeni termin</title>
        <link rel="stylesheet" href="css/css.css">
    </head>
    <body>
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

    <main style="max-width: 900px; margin: 30px auto; background: transparent; padding: 0;">
        <div class=" edit-schedule-container">
            <h2>Izmeni termin za dan: <?= htmlspecialchars($schedule['day_of_week']) ?></h2>

            <?php if (isset($_SESSION['msg'])): ?>
                <div class="msg-box"><?= htmlspecialchars($_SESSION['msg']) ?></div>
                <?php unset($_SESSION['msg']); ?>
            <?php endif; ?>

            <form method="POST" action="vet_schedule_edit.php" class="vet-form-section">
                <input type="hidden" name="id" value="<?= $id ?>">

                <div class="vet-form-row">
                    <div class="vet-form-group" style="max-width: 45%;">
                        <label for="vreme_start" class="vet-form-label">Početak:</label>
                        <select id="vreme_start" name="vreme_start" required class="vet-form-input">
                            <?php foreach ($timeSlots as $slot): ?>
                                <option value="<?= $slot ?>" <?= (date('H:i', strtotime($schedule['start_time'])) === $slot) ? 'selected' : '' ?>><?= $slot ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="vet-form-group" style="max-width: 45%;">
                        <label for="vreme_end" class="vet-form-label">Kraj:</label>
                        <select id="vreme_end" name="vreme_end" required class="vet-form-input">
                            <?php foreach ($timeSlots as $slot): ?>
                                <option value="<?= $slot ?>" <?= (date('H:i', strtotime($schedule['end_time'])) === $slot) ? 'selected' : '' ?>><?= $slot ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 25px;">
                    <button type="submit" name="update" class="cta-button vet-cta-button" style="width: 350px;">Sačuvaj izmene</button>
                </div>

                <p style="text-align: center; margin-top: 20px;">
                    <a href="vet_schedule.php" style="color: #4caf50; font-weight: 600; text-decoration: none;">Nazad</a>
                </p>
            </form>
        </div>
    </main>
    </body>
    </html>

    <?php
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Obrada izmene
    $id = (int)$_POST['id'];
    $start = $_POST['vreme_start'] ?? '';
    $end = $_POST['vreme_end'] ?? '';

    if (!$start || !$end) {
        $_SESSION['msg'] = "Sva polja su obavezna.";
        header("Location: vet_schedule_edit.php?id=$id");
        exit;
    }

    if (strtotime($start) >= strtotime($end)) {
        $_SESSION['msg'] = "Početak termina mora biti pre kraja.";
        header("Location: vet_schedule_edit.php?id=$id");
        exit;
    }

    // Učitaj dan termina da proslediš u proveru duplikata i update
    $schedule = get_schedule_by_id($pdo, $id, $vetId);
    if (!$schedule) {
        $_SESSION['msg'] = "Termin nije pronađen.";
        header("Location: vet_schedule.php");
        exit;
    }
    $dayOfWeek = $schedule['day_of_week'];

    // Provera da li već postoji isti termin (osim ovog koji menjamo)
    if (schedule_exists_edit($pdo, $vetId, $dayOfWeek, $start, $end, $id)) {
        $_SESSION['msg'] = "Termin sa odabranim danom i vremenom već postoji.";
        header("Location: vet_schedule_edit.php?id=$id");
        exit;
    }

    // Dodaj sekunde na vreme da baza prihvati (format HH:MM:SS)
    if (strlen($start) === 5) $start .= ':00';
    if (strlen($end) === 5) $end .= ':00';

    // Update termina u bazi
    update_schedule($pdo, $id, $vetId, $dayOfWeek, $start, $end);

    $_SESSION['msg'] = "Termin je uspešno izmenjen.";
    header("Location: vet_schedule.php");
    exit;

} else {
    header("Location: vet_schedule.php");
    exit;
}
