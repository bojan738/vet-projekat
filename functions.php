<?php
/**
 * PetCare – zajedničke PDO funkcije
 * Sve funkcije koje smo do sada koristili + još nekoliko korisnih helpera.
 * Pretpostavlja se da $pdo (PDO konekcija) postoji u fajlu koji includuje ove funkcije.
 */

// -------------------- Opšti helperi -------------------- //

if (!function_exists('sanitize')) {
    /** Bezbedno ispiši HTML vrednost */
    function sanitize(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('safeRedirect')) {
    /**
     * Brzi redirect i prekid izvršavanja.
     */
    function safeRedirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}



// -------------------- Raspored & slobodni slotovi ----- //

/**
 * Vrati sve SLOBODNE slotove (one iz tabele veterinarian_schedule
 * koji još nisu zauzeti u appointments).
 *
 * @return array[] Svaki element sadrži: id, start_time, end_time, vet_name
 */
function getFreeSlots(PDO $pdo): array {
    $stmt = $pdo->query("
        SELECT s.*, CONCAT(u.first_name, ' ', u.last_name) AS vet_name
        FROM veterinarian_schedule s
        JOIN veterinarians v ON s.veterinarian_id = v.id
        JOIN users u ON v.user_id = u.id
        WHERE s.id NOT IN (
            SELECT schedule_id FROM appointments WHERE status = 'zakazano'
        )
        ORDER BY s.start_time
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




/**
 * Izbriši slot iz veterinarskog rasporeda.
 */
function deleteSlot(PDO $pdo, int $slotId): void
{
    $stmt = $pdo->prepare("DELETE FROM veterinarian_schedule WHERE id = :id");
    $stmt->execute(['id' => $slotId]);
}

/**
 * Izmeni slot (datum + vremenski opseg).
 * $date      format YYYY-MM-DD
 * $timeRange format HH:MM-HH:MM (npr. 09:00-09:30)
 */
function updateSlot(PDO $pdo, int $slotId, string $date, string $timeRange): void
{
    [$start, $end] = explode('-', $timeRange);
    $startDateTime = sprintf('%s %s:00', $date, trim($start));
    $endDateTime   = sprintf('%s %s:00', $date, trim($end));

    $sql = "
        UPDATE veterinarian_schedule
        SET start_time = :start, end_time = :end
        WHERE id = :id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'start' => $startDateTime,
        'end'   => $endDateTime,
        'id'    => $slotId,
    ]);
}

/**
 * Vrati sve buduće datume koji postoje u rasporedu veterinara (za select).
 * Koristi se za generisanje dropdown‑a datuma.
 */
function getScheduleDates(PDO $pdo): array
{
    $sql = "
        SELECT DISTINCT DATE(start_time) AS date
        FROM veterinarian_schedule
        WHERE start_time >= CURDATE()
        ORDER BY date
    ";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Vrati vremenske opsege (HH:MM-HH:MM) za dati datum iz rasporeda.
 */
function getTimeRangesForDate(PDO $pdo, string $date): array
{
    $stmt = $pdo->prepare("SELECT DATE_FORMAT(start_time,'%H:%i') AS s, DATE_FORMAT(end_time,'%H:%i') AS e FROM veterinarian_schedule WHERE DATE(start_time)=:d ORDER BY start_time");
    $stmt->execute(['d' => $date]);
    return array_map(fn($r)=>$r['s'].'-'.$r['e'], $stmt->fetchAll());
}

//ADMIN DEOOOOO

require_once 'db.php';

// USERS
function get_all_users($pdo) {
    $stmt = $pdo->prepare("SELECT u.*, r.name AS role FROM users u JOIN roles r ON u.role_id = r.id WHERE u.role_id = 3");
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_user_by_id($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function insert_user($pdo, $first, $last, $email, $phone, $address, $role_id = 3) {
    // Proveri da li već postoji korisnik sa tim emailom
    $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetchColumn() > 0) {
        return ["success" => false, "message" => "Korisnik sa tim emailom već postoji."];
    }

    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone_number, address, password, role_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$first, $last, $email, $phone, $address, password_hash("default123", PASSWORD_DEFAULT), $role_id]);
    return ["success" => true, "id" => $pdo->lastInsertId()];
}

function update_user($pdo, $id, $first, $last, $email, $phone, $address) {
    $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone_number = ?, address = ? WHERE id = ?");
    $stmt->execute([$first, $last, $email, $phone, $address, $id]);
}

function delete_user($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
}
// VETERINARI
function get_all_veterinarians(PDO $pdo): array {
    $stmt = $pdo->query("
        SELECT 
            v.id,
            v.specialization,
            v.photo,
            u.first_name,
            u.last_name,
            u.email
        FROM veterinarians v
        INNER JOIN users u ON v.user_id = u.id
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function get_veterinarian_by_id($pdo, $id) {
    $stmt = $pdo->prepare("SELECT v.*, u.id AS user_id, u.first_name, u.last_name, u.email, u.phone_number, u.address FROM veterinarians v JOIN users u ON v.user_id = u.id WHERE v.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function insert_veterinarian($pdo, $user_id, $specialization, $license) {
    // Proveri da li licenca već postoji
    $check = $pdo->prepare("SELECT COUNT(*) FROM veterinarians WHERE license_number = ?");
    $check->execute([$license]);
    if ($check->fetchColumn() > 0) {
        return ["success" => false, "message" => "Veterinar sa ovom licencom već postoji."];
    }

    $stmt = $pdo->prepare("INSERT INTO veterinarians (user_id, specialization, license_number) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $specialization, $license]);
    return ["success" => true];
}

function insert_full_veterinarian($pdo, $first, $last, $email, $phone, $address, $specialization, $license) {
    $result = insert_user($pdo, $first, $last, $email, $phone, $address, 2); // role_id 2 = vet
    if (!$result['success']) {
        return $result; // Vrati grešku ako korisnik već postoji
    }
    $vetResult = insert_veterinarian($pdo, $result['id'], $specialization, $license);
    if (!$vetResult['success']) {
        delete_user($pdo, $result['id']); // očisti korisnika ako licenca već postoji
        return $vetResult;
    }
    return ["success" => true];
}

function update_veterinarian_full($pdo, $vet_id, $user_id, $first, $last, $email, $phone, $address, $specialization, $license) {
    update_user($pdo, $user_id, $first, $last, $email, $phone, $address);
    $stmt = $pdo->prepare("UPDATE veterinarians SET specialization = ?, license_number = ? WHERE id = ?");
    $stmt->execute([$specialization, $license, $vet_id]);
}

function delete_veterinarian($pdo, $id)
{
// Prvo dohvati user_id pre brisanja
    $stmt = $pdo->prepare("SELECT user_id FROM veterinarians WHERE id = ?");
    $stmt->execute([$id]);
    $user_id = $stmt->fetchColumn();

// Obrisi veterinara
    $stmt = $pdo->prepare("DELETE FROM veterinarians WHERE id = ?");
    $stmt->execute([$id]);

// Obrisi korisnika iz users tabele
    if ($user_id) {
        delete_user($pdo, $user_id);
    }
}

// SERVICES
function get_all_services($pdo) {
    $stmt = $pdo->query("SELECT * FROM services");
    return $stmt->fetchAll();
}

function get_service_by_id($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function insert_service($pdo, $name, $description, $price) {
    $stmt = $pdo->prepare("INSERT INTO services (name, description, price) VALUES (?, ?, ?)");
    $stmt->execute([$name, $description, $price]);
}

function update_service($pdo, $id, $name, $description, $price) {
    $stmt = $pdo->prepare("UPDATE services SET name = ?, description = ?, price = ? WHERE id = ?");
    $stmt->execute([$name, $description, $price, $id]);
}

function delete_service($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
    $stmt->execute([$id]);
}

// APPOINTMENTS
function get_all_appointments($pdo) {
    $stmt = $pdo->query("SELECT a.*, p.name AS pet_name, u.first_name AS vet_first_name, u.last_name AS vet_last_name FROM appointments a JOIN pets p ON a.pet_id = p.id JOIN veterinarians v ON a.veterinarian_id = v.id JOIN users u ON v.user_id = u.id ORDER BY a.appointment_date ASC");
    return $stmt->fetchAll();
}

function get_appointment_by_id($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function update_appointment($pdo, $id, $date, $status, $notes) {
    $stmt = $pdo->prepare("UPDATE appointments SET appointment_date = ?, status = ?, notes = ? WHERE id = ?");
    $stmt->execute([$date, $status, $notes, $id]);
}

function delete_appointment($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->execute([$id]);
}

function get_appointments_by_date($pdo, $date) {
    $stmt = $pdo->prepare("SELECT a.*, p.name AS pet_name, u.first_name AS vet_first_name, u.last_name AS vet_last_name FROM appointments a JOIN pets p ON a.pet_id = p.id JOIN veterinarians v ON a.veterinarian_id = v.id JOIN users u ON v.user_id = u.id WHERE DATE(a.appointment_date) = ? ORDER BY a.appointment_date ASC");
    $stmt->execute([$date]);
    return $stmt->fetchAll();
}

// MEDICAL RECORDS
function get_all_medical_records($pdo) {
    $stmt = $pdo->query("SELECT mr.*, p.name AS pet_name, u.first_name AS vet_first_name, u.last_name AS vet_last_name FROM medical_records mr JOIN pets p ON mr.pet_id = p.id JOIN veterinarians v ON mr.veterinarian_id = v.id JOIN users u ON v.user_id = u.id ORDER BY mr.created_at DESC");
    return $stmt->fetchAll();
}

function get_medical_record_by_id($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM medical_records WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function update_medical_record($pdo, $id, $diagnosis, $treatment, $price) {
    $stmt = $pdo->prepare("UPDATE medical_records SET diagnosis = ?, treatment = ?, price = ? WHERE id = ?");
    $stmt->execute([$diagnosis, $treatment, $price, $id]);
}

function delete_medical_record($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM medical_records WHERE id = ?");
    $stmt->execute([$id]);
}



//USERS DEOOOO

//user_reservation
function get_available_terms_by_vet_and_date(PDO $pdo, int $vet_id, string $date): array {
    // Dobavi termine veterinara za dan u sedmici
    $dayOfWeekMap = [
        'Monday' => 'Ponedeljak',
        'Tuesday' => 'Utorak',
        'Wednesday' => 'Sreda',
        'Thursday' => 'Cetvrtak',
        'Friday' => 'Petak',
        'Saturday' => 'Subota',
        'Sunday' => 'Nedelja'
    ];
    $phpDay = date('l', strtotime($date)); // npr. Monday
    $dbDay = $dayOfWeekMap[$phpDay] ?? null;
    if (!$dbDay) return [];

    // Izbaci termine koji su već zauzeti u tabeli appointments za isti datum i veterinara
    $stmt = $pdo->prepare("
        SELECT vs.id AS schedule_id, vs.start_time, vs.end_time, vs.day_of_week,
               u.first_name, u.last_name
        FROM veterinarian_schedule vs
        JOIN veterinarians v ON vs.veterinarian_id = v.id
        JOIN users u ON v.user_id = u.id
        WHERE vs.veterinarian_id = :vet_id
          AND vs.day_of_week = :day_of_week
          AND NOT EXISTS (
            SELECT 1 FROM appointments a
            WHERE a.schedule_id = vs.id
              AND a.appointment_date = :date
              AND a.status = 'scheduled'
          )
        ORDER BY vs.start_time
    ");
    $stmt->execute([
        ':vet_id' => $vet_id,
        ':day_of_week' => $dbDay,
        ':date' => $date
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function is_schedule_slot_taken(PDO $pdo, int $schedule_id, string $date): bool {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE schedule_id = :schedule_id AND appointment_date = :date AND status = 'scheduled'");
    $stmt->execute([':schedule_id' => $schedule_id, ':date' => $date]);
    return $stmt->fetchColumn() > 0;
}

function pet_has_overlapping_appointment(PDO $pdo, int $pet_id, string $date, string $start_time, string $end_time): bool {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM appointments
        WHERE pet_id = :pet_id
          AND appointment_date = :date
          AND status = 'scheduled'
          AND (
            (start_time < :end_time AND end_time > :start_time)
          )
    ");
    $stmt->execute([
        ':pet_id' => $pet_id,
        ':date' => $date,
        ':start_time' => $start_time,
        ':end_time' => $end_time
    ]);
    return $stmt->fetchColumn() > 0;
}
function create_appointment(
    PDO $pdo,
    int $pet_id,
    int $veterinarian_id,
    int $service_id,
    string $appointment_date,
    string $start_time,
    string $end_time,
    string $status,
    int $user_id,
    int $schedule_id
): string {
    $reservation_code = 'RSV-' . strtoupper(bin2hex(random_bytes(3))); // npr. RSV-4F3A1B

    $stmt = $pdo->prepare("
        INSERT INTO appointments 
        (pet_id, veterinarian_id, service_id, appointment_date, start_time, end_time, status, user_id, schedule_id, reservation_code)
        VALUES (:pet_id, :vet_id, :service_id, :appointment_date, :start_time, :end_time, :status, :user_id, :schedule_id, :reservation_code)
    ");
    $stmt->execute([
        ':pet_id' => $pet_id,
        ':vet_id' => $veterinarian_id,
        ':service_id' => $service_id,
        ':appointment_date' => $appointment_date,
        ':start_time' => $start_time,
        ':end_time' => $end_time,
        ':status' => $status,
        ':user_id' => $user_id,
        ':schedule_id' => $schedule_id,
        ':reservation_code' => $reservation_code
    ]);
    return $reservation_code;
}
function send_appointment_email(string $to_email, string $to_name, string $reservation_code, string $appointment_date, string $start_time): bool {
    $subject = "Potvrda rezervacije termina - PetCare";
    $message = "Poštovani $to_name,\n\nVaša rezervacija termina za $appointment_date u $start_time je uspešno zavedena.\n" .
        "Vaš kod rezervacije je: $reservation_code\n" .
        "Molimo Vas da ovaj kod ponesete prilikom dolaska u ordinaciju.\n\n" .
        "Srdačan pozdrav,\nPetCare tim";

    $headers = "From: noreply@petcare.com\r\n" .
        "Reply-To: noreply@petcare.com\r\n" .
        "Content-Type: text/plain; charset=utf-8\r\n";

    return mail($to_email, $subject, $message, $headers);
}
function get_user_active_appointments(PDO $pdo, int $user_id): array {
    $stmt = $pdo->prepare("
        SELECT a.*, v.user_id AS vet_user_id, u.first_name AS vet_first_name, u.last_name AS vet_last_name
        FROM appointments a
        JOIN veterinarians v ON a.veterinarian_id = v.id
        JOIN users u ON v.user_id = u.id
        WHERE a.user_id = :user_id AND a.status = 'scheduled' AND a.appointment_date >= CURDATE()
        ORDER BY a.appointment_date, a.start_time
    ");
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function can_cancel_appointment(string $appointment_date, string $start_time): bool {
    $appointment_datetime = new DateTime("$appointment_date $start_time");
    $now = new DateTime();
    $interval = $now->diff($appointment_datetime);
    $hours_diff = ($interval->days * 24) + $interval->h + ($interval->i / 60);
    return $appointment_datetime > $now && $hours_diff >= 4;
}

function cancel_appointment(PDO $pdo, int $appointment_id, int $cancelled_by, string $cancellation_reason = null): bool {
    if (!$cancelled_by) return false;

    $pdo->beginTransaction();
    try {
        // Update status
        $stmt1 = $pdo->prepare("UPDATE appointments SET status = 'canceled' WHERE id = :id");
        $stmt1->execute([':id' => $appointment_id]);

        // Insert cancellation record
        $stmt2 = $pdo->prepare("
            INSERT INTO appointment_cancellations (appointment_id, cancelled_by, cancellation_reason)
            VALUES (:appointment_id, :cancelled_by, :reason)
        ");
        $stmt2->execute([
            ':appointment_id' => $appointment_id,
            ':cancelled_by' => $cancelled_by,
            ':reason' => $cancellation_reason
        ]);

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}
function get_pets_by_user(PDO $pdo, int $user_id): array {
    $stmt = $pdo->prepare("
        SELECT p.* FROM pets p
        JOIN pet_owners po ON p.owner_id = po.id
        WHERE po.user_id = :user_id
    ");
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



//TERMINI
// Dohvatanje svih veterinara


// Dohvatanje slobodnih termina za izabranog veterinara
function get_available_terms_by_vet(PDO $pdo, int $vet_id): array {
    $stmt = $pdo->prepare("
        SELECT s.id, 
               DATE(s.start_time) AS date,
               TIME(s.start_time) AS start_time, 
               TIME(s.end_time) AS end_time,
               CONCAT(u.first_name, ' ', u.last_name) AS vet_name
        FROM veterinarian_schedule s
        JOIN veterinarians v ON s.veterinarian_id = v.id
        JOIN users u ON v.user_id = u.id
        WHERE s.veterinarian_id = ?
          AND NOT EXISTS (
              SELECT 1
              FROM appointments a
              WHERE a.schedule_id = s.id
                AND a.status = 'zakazano'
          )
        ORDER BY s.start_time ASC
    ");
    $stmt->execute([$vet_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//narednih 7 radnih dana
function getNextWeekDaysWithoutSunday() {
    $dates = [];
    $current = new DateTime();  // trenutni dan (danas)
    $oneDay = new DateInterval('P1D');

    while (count($dates) < 7) {
        // Proveri da li je trenutni dan nedelja (7)
        if ($current->format('N') != 7) {
            $dates[] = $current->format('Y-m-d');
        }
        $current->add($oneDay);
    }
    return $dates;
}


// NEGATIVNI POENI
function mark_no_show(PDO $pdo, int $appointment_id): void {
    // Označi u tabeli appointment_attendance da korisnik nije došao (attended=0)
    $stmt = $pdo->prepare("
        INSERT INTO appointment_attendance (appointment_id, attended)
        VALUES (?, 0)
        ON DUPLICATE KEY UPDATE attended = 0
    ");
    $stmt->execute([$appointment_id]);

    // Takođe možeš update status termina u appointments na 'otkazano' ili 'nepojavljen'
    $stmt2 = $pdo->prepare("UPDATE appointments SET status = 'otkazano' WHERE id = ?");
    $stmt2->execute([$appointment_id]);
}

function mark_attended(PDO $pdo, int $appointment_id): void {
    // Označi da je korisnik došao (attended=1)
    $stmt = $pdo->prepare("
        INSERT INTO appointment_attendance (appointment_id, attended)
        VALUES (?, 1)
        ON DUPLICATE KEY UPDATE attended = 1
    ");
    $stmt->execute([$appointment_id]);

    // Update status na 'obavljeno'
    $stmt2 = $pdo->prepare("UPDATE appointments SET status = 'obavljeno' WHERE id = ?");
    $stmt2->execute([$appointment_id]);
}

function get_user_id_by_appointment(PDO $pdo, int $appointment_id): ?int {
    $stmt = $pdo->prepare("SELECT user_id FROM appointments WHERE id = ?");
    $stmt->execute([$appointment_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ? (int)$user['user_id'] : null;
}

function increment_negative_points(PDO $pdo, int $user_id): void {
    $stmt = $pdo->prepare("UPDATE users SET negative_points = negative_points + 1 WHERE id = ?");
    $stmt->execute([$user_id]);
}


//CHANGE APPOINTMENTS



function get_appointments_for_pet(PDO $pdo, int $pet_id): array {
    $stmt = $pdo->prepare("
        SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) AS vet_name
        FROM appointments a
        JOIN veterinarians v ON a.veterinarian_id = v.id
        JOIN users u ON v.user_id = u.id
        WHERE a.pet_id = ?
          AND a.status = 'zakazano'
        ORDER BY a.appointment_date, a.time_slot
    ");
    $stmt->execute([$pet_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function free_appointment(PDO $pdo, int $appointmentId): void {
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->execute([$appointmentId]);
}



function rebook_appointment(PDO $pdo, int $appointmentId, int $newScheduleId): void {
    if (is_schedule_slot_taken($pdo, $newScheduleId)) {
        throw new Exception("Odabrani termin je već zauzet.");
    }

    $stmt = $pdo->prepare("SELECT start_time, veterinarian_id FROM veterinarian_schedule WHERE id = ?");
    $stmt->execute([$newScheduleId]);
    $newSlot = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$newSlot) {
        throw new Exception("Novi slot nije pronađen.");
    }

    $stmt = $pdo->prepare("
        UPDATE appointments
        SET appointment_date = ?, 
            time_slot = TIME(?), 
            veterinarian_id = ?, 
            schedule_id = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $newSlot['start_time'],
        $newSlot['start_time'],
        $newSlot['veterinarian_id'],
        $newScheduleId,
        $appointmentId
    ]);
}
function get_all_vets(PDO $pdo): array {
    $stmt = $pdo->query("
        SELECT v.id AS vet_id, u.first_name, u.last_name, u.email
        FROM veterinarians v
        JOIN users u ON v.user_id = u.id
        ORDER BY u.first_name, u.last_name
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function pet_has_appointment(PDO $pdo, int $pet_id, int $schedule_id): bool {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE pet_id = ? AND schedule_id = ? AND status = 'zakazano'");
    $stmt->execute([$pet_id, $schedule_id]);
    return $stmt->fetchColumn() > 0;
}

function get_pet_id_by_appointment(PDO $pdo, int $appointment_id): int {
    $stmt = $pdo->prepare("SELECT pet_id FROM appointments WHERE id = ?");
    $stmt->execute([$appointment_id]);
    return (int) $stmt->fetchColumn();
}

//pet_treatments.php
function get_treatments_by_pet(PDO $pdo, int $pet_id): array {
    $stmt = $pdo->prepare("
        SELECT 
            a.appointment_date,
            s.name AS service_name,
            CONCAT(u.first_name, ' ', u.last_name) AS vet_name,
            a.status,
            a.notes
        FROM appointments a
        JOIN services s ON a.service_id = s.id
        JOIN veterinarians v ON a.veterinarian_id = v.id
        JOIN users u ON v.user_id = u.id
        WHERE a.pet_id = ?
        ORDER BY a.appointment_date DESC
    ");
    $stmt->execute([$pet_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


//VET_ELECTRONIC_CARD
function get_all_pets(PDO $pdo): array {
    $sql = "
        SELECT 
            p.id AS pet_id,
            p.name,
            p.photo,
            CONCAT(u.first_name, ' ', u.last_name) AS owner_name
        FROM pets p
        LEFT JOIN users u ON p.owner_id = u.id
        ORDER BY p.name ASC
    ";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


//medical record

function get_all_treatments(PDO $pdo): array {
    $stmt = $pdo->query("SELECT id, name, price FROM services ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function get_all_service_prices(PDO $pdo): array {
    $stmt = $pdo->query("SELECT DISTINCT price FROM medical_records WHERE price IS NOT NULL");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
function get_treatment_price(PDO $pdo, string $treatmentName): ?float {
    $stmt = $pdo->prepare("SELECT price FROM services WHERE name = :name LIMIT 1");
    $stmt->execute([':name' => $treatmentName]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (float)$row['price'] : null;
}

function get_pet_full_info(PDO $pdo, int $pet_id): ?array {
    $sql = "SELECT 
                p.*, 
                pt.name AS type_name, 
                pb.name AS breed_name,
                po.id AS owner_id,
                u.first_name AS owner_first_name,
                u.last_name AS owner_last_name
            FROM pets p
            LEFT JOIN pet_types pt ON p.type_id = pt.id
            LEFT JOIN pet_breeds pb ON p.breed_id = pb.id
            LEFT JOIN pet_owners po ON p.owner_id = po.id
            LEFT JOIN users u ON po.user_id = u.id
            WHERE p.id = :pet_id
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['pet_id' => $pet_id]);
    $pet = $stmt->fetch(PDO::FETCH_ASSOC);
    return $pet ?: null;
}
// Dobavlja sve različite dijagnoze (distinct)
function get_all_diagnoses(PDO $pdo): array {
    $sql = "SELECT DISTINCT diagnosis FROM medical_records ORDER BY diagnosis";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Dobavlja sve usluge (tretmane)
function get_all_services_vet(PDO $pdo): array {
    $sql = "SELECT id, name, price FROM services ORDER BY name";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Dobavlja najnoviji (najkasniji) zakazani termin (appointment) za ljubimca i veterinara
function get_latest_appointment_id(PDO $pdo, int $pet_id, int $vet_id): ?int {
    $sql = "SELECT id FROM appointments WHERE pet_id = :pet_id AND veterinarian_id = :vet_id ORDER BY appointment_date DESC, start_time DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['pet_id' => $pet_id, 'vet_id' => $vet_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int)$row['id'] : null;
}

//negativni poeni
function mark_appointment_no_show(PDO $pdo, int $appointment_id): void {
    $stmt = $pdo->prepare("UPDATE appointments SET no_show = 1 WHERE id = ?");
    $stmt->execute([$appointment_id]);
}

function add_penalty_to_owner(PDO $pdo, int $user_id): void {
    $stmt = $pdo->prepare("UPDATE users SET negative_points = negative_points + 1 WHERE id = ?");
    $stmt->execute([$user_id]);
}



function save_medical_note(PDO $pdo, int $appointment_id, int $vet_id, int $pet_id, string $diagnosis, string $treatment, float $price): void {
    $stmt = $pdo->prepare("INSERT INTO medical_records (appointment_id, veterinarian_id, pet_id, diagnosis, treatment, price, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$appointment_id, $vet_id, $pet_id, $diagnosis, $treatment, $price]);
}

function delete_medical_note(PDO $pdo, int $id): void {
    $stmt = $pdo->prepare("DELETE FROM medical_records WHERE id = ?");
    $stmt->execute([$id]);
}

function update_medical_note(PDO $pdo, int $id, string $diagnosis, string $treatment, float $price): void {
    $stmt = $pdo->prepare("UPDATE medical_records SET diagnosis = ?, treatment = ?, price = ? WHERE id = ?");
    $stmt->execute([$diagnosis, $treatment, $price, $id]);
}

function get_medical_records_by_appointment(PDO $pdo, int $appointment_id): array {
    $stmt = $pdo->prepare("
        SELECT mr.*, u.first_name, u.last_name
        FROM medical_records mr
        JOIN veterinarians v ON mr.veterinarian_id = v.id
        JOIN users u ON v.user_id = u.id
        WHERE mr.appointment_id = ?
        ORDER BY mr.created_at DESC
    ");
    $stmt->execute([$appointment_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




function add_negative_point(PDO $pdo, int $appointment_id): bool {
    // Pronađi user_id preko termina
    $stmt = $pdo->prepare("
        SELECT u.id AS user_id
        FROM appointments a
        JOIN pets p ON a.pet_id = p.id
        JOIN users u ON p.owner_id = u.id
        WHERE a.id = ?
    ");
    $stmt->execute([$appointment_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return false;
    }

    // Uvećaj broj negativnih poena za tog korisnika
    $stmtUpdate = $pdo->prepare("UPDATE users SET negative_points = negative_points + 1 WHERE id = ?");
    return $stmtUpdate->execute([$row['user_id']]);
}
function get_owner_id_by_appointment(PDO $pdo, int $appointment_id): ?int {
    $stmt = $pdo->prepare("
        SELECT a.user_id 
        FROM appointments a
        WHERE a.id = :appointment_id
    ");
    $stmt->execute(['appointment_id' => $appointment_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int)$row['user_id'] : null;
}


function get_or_create_today_appointment(PDO $pdo, int $pet_id, int $veterinarian_id): ?int {
    $todayStart = date('Y-m-d 00:00:00');
    $todayEnd = date('Y-m-d 23:59:59');

    // Pokušaj da pronađeš termin za danas
    $stmt = $pdo->prepare("
        SELECT id FROM appointments 
        WHERE pet_id = :pet_id 
          AND veterinarian_id = :vet_id
          AND appointment_date BETWEEN :start AND :end
        LIMIT 1
    ");
    $stmt->execute([
        ':pet_id' => $pet_id,
        ':vet_id' => $veterinarian_id,
        ':start' => $todayStart,
        ':end' => $todayEnd
    ]);

    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($appointment) {
        return (int)$appointment['id'];
    }

    // Nema termina za danas, kreiraj novi
    $now = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare("
        INSERT INTO appointments (pet_id, veterinarian_id, appointment_date, start_time, end_time, service_id)
        VALUES (:pet_id, :vet_id, :date, :start, :end, :service)
    ");

    // Ovde stavljamo vreme termina kao sada (start) i pola sata kasnije (end)
    $start_time = date('H:i:s');
    $end_time = date('H:i:s', strtotime('+30 minutes'));

    // Moraš imati validan service_id (možeš izabrati neki default ili najjeftiniji)
    $defaultServiceId = get_default_service_id($pdo); // Napravi ovu funkciju

    $stmt->execute([
        ':pet_id' => $pet_id,
        ':vet_id' => $veterinarian_id,
        ':date' => date('Y-m-d'),
        ':start' => $start_time,
        ':end' => $end_time,
        ':service' => $defaultServiceId
    ]);

    return (int)$pdo->lastInsertId();
}
function get_default_service_id(PDO $pdo): int {
    $stmt = $pdo->query("SELECT id FROM services ORDER BY price ASC LIMIT 1");
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    return $service ? (int)$service['id'] : 1; // Ako nema usluga, vrati 1 (ili neki validan id)
}

function get_appointments_for_pet_and_vet(PDO $pdo, int $petId, int $vetId): array {
    $stmt = $pdo->prepare("SELECT id, appointment_date, start_time, end_time FROM appointments WHERE pet_id = ? AND veterinarian_id = ? AND status = 'scheduled' ORDER BY appointment_date DESC");
    $stmt->execute([$petId, $vetId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//VET TREATMENTS
function get_services(PDO $pdo): array {
    $stmt = $pdo->query("SELECT id, name, price FROM services ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



//USER INFORMATION
function get_user_profile($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT first_name, last_name, email, phone_number FROM users WHERE id = ?");

    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

function update_user_profile($pdo, $user_id, $first_name, $last_name, $email, $phone) {
    $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone_number = ? WHERE id = ?");
    $stmt->execute([$first_name, $last_name, $email, $phone, $user_id]);
}


//PET INFORMATION

function get_pets_by_owner($pdo, $owner_id) {
    $stmt = $pdo->prepare("SELECT * FROM pets WHERE owner_id = ?");
    $stmt->execute([$owner_id]);
    return $stmt->fetchAll();
}
function get_pets_by_owner1($pdo, $user_id) {
    $stmt = $pdo->prepare("
        SELECT p.*, t.name AS type_name, b.name AS breed_name, po.id AS pet_owner_id, u.first_name, u.last_name
        FROM pets p
        JOIN pet_owners po ON p.owner_id = po.id
        JOIN users u ON po.user_id = u.id
        LEFT JOIN pet_types t ON p.type_id = t.id
        LEFT JOIN pet_breeds b ON p.breed_id = b.id
        WHERE u.id = ?
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function update_pet($pdo, $pet_id, $name, $type_name, $age, $birth_date, $gender, $breed_name, $photo = null) {
    // Insert or get type ID
    $type_id = insert_pet_type($pdo, $type_name);

    // Insert or get breed ID
    $breed_id = insert_pet_breed($pdo, $breed_name, $type_id);

    if ($photo !== null) {
        // Ako postoji nova slika
        $stmt = $pdo->prepare("
            UPDATE pets 
            SET name = ?, type_id = ?, age = ?, birth_date = ?, gender = ?, breed_id = ?, photo = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $type_id, $age, $birth_date, $gender, $breed_id, $photo, $pet_id]);
    } else {
        // Ako nije postavljena nova slika
        $stmt = $pdo->prepare("
            UPDATE pets 
            SET name = ?, type_id = ?, age = ?, birth_date = ?, gender = ?, breed_id = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $type_id, $age, $birth_date, $gender, $breed_id, $pet_id]);
    }
}




//PET ADD
function add_pet($pdo, $owner_id, $name, $type_id, $age, $gender, $breed_id, $image_path, $birth_date) {
    $stmt = $pdo->prepare("INSERT INTO pets (owner_id, name, type_id, age, gender, breed_id, photo, birth_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$owner_id, $name, $type_id, $age, $gender, $breed_id, $image_path, $birth_date]);
}
function get_owner_id_by_user_id($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT id FROM pet_owners WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    return $result ? $result['id'] : null;
}


function insert_pet_type($pdo, $type_name) {
    $stmt = $pdo->prepare("SELECT id FROM pet_types WHERE name = ?");
    $stmt->execute([$type_name]);
    $existing = $stmt->fetch();
    if ($existing) {
        return $existing['id'];
    }

    $stmt = $pdo->prepare("INSERT INTO pet_types (name) VALUES (?)");
    $stmt->execute([$type_name]);
    return $pdo->lastInsertId();
}

function insert_pet_breed($pdo, $breed_name, $type_id) {
    $stmt = $pdo->prepare("SELECT id FROM pet_breeds WHERE name = ? AND type_id = ?");
    $stmt->execute([$breed_name, $type_id]);
    $existing = $stmt->fetch();
    if ($existing) {
        return $existing['id'];
    }

    $stmt = $pdo->prepare("INSERT INTO pet_breeds (name, type_id) VALUES (?, ?)");
    $stmt->execute([$breed_name, $type_id]);
    return $pdo->lastInsertId();
}

function get_all_pet_types($pdo) {
    $stmt = $pdo->query("SELECT id, name FROM pet_types ORDER BY name");
    return $stmt->fetchAll();
}

function get_all_pet_breeds($pdo) {
    $stmt = $pdo->query("SELECT id, name FROM pet_breeds ORDER BY name");
    return $stmt->fetchAll();
}
function get_breeds_grouped_by_type($pdo) {
    $stmt = $pdo->query("
        SELECT pt.name AS type_name, pb.name AS breed_name
        FROM pet_breeds pb
        JOIN pet_types pt ON pb.type_id = pt.id
        ORDER BY pt.name, pb.name
    ");

    $breeds = [];
    while ($row = $stmt->fetch()) {
        $type = $row['type_name'];
        $breed = $row['breed_name'];
        if (!isset($breeds[$type])) {
            $breeds[$type] = [];
        }
        $breeds[$type][] = $breed;
    }

    return $breeds;
}


//BRISANJE PET
function pet_belongs_to_user(PDO $pdo, int $pet_id, int $user_id): bool {
    $stmt = $pdo->prepare("
        SELECT 1
        FROM pets p
        JOIN pet_owners o ON p.owner_id = o.id
        WHERE p.id = :pet_id AND o.user_id = :user_id
        LIMIT 1
    ");
    $stmt->execute([
        'pet_id' => $pet_id,
        'user_id' => $user_id
    ]);
    return (bool)$stmt->fetch();
}

function pet_delete(PDO $pdo, int $pet_id): bool {
    $stmt = $pdo->prepare("SELECT photo FROM pets WHERE id = :id");
    $stmt->execute(['id' => $pet_id]);
    $pet = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pet && !empty($pet['photo']) && $pet['photo'] !== 'images/default_pet.jpg' && file_exists($pet['photo'])) {
        @unlink($pet['photo']);
    }

    $stmt = $pdo->prepare("DELETE FROM pets WHERE id = :id");
    return $stmt->execute(['id' => $pet_id]);
}






//CHANGE RESERVATIN
function deleteSlot_res(PDO $pdo, int $slotId): void {
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->execute([$slotId]);
}

function updateSlot_res(PDO $pdo, int $slotId, string $newDate, string $timeRange): void {
    [$start, $end] = explode(' - ', $timeRange);
    $startTime = "$newDate $start";
    $endTime = "$newDate $end";

    $stmt = $pdo->prepare("UPDATE appointments SET start_time = ?, end_time = ? WHERE id = ?");
    $stmt->execute([$startTime, $endTime, $slotId]);
}




// VET


function get_vet_info(PDO $pdo, int $vetId): array {
    $stmt = $pdo->prepare("
        SELECT u.first_name, u.last_name, u.email, u.phone_number, 
               v.specialization, v.license_number, v.photo
        FROM veterinarians v
        JOIN users u ON v.user_id = u.id
        WHERE v.id = ?
    ");
    $stmt->execute([$vetId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function get_vet_schedule($pdo, $vetId) {
    $stmt = $pdo->prepare("SELECT DISTINCT day_of_week, start_time, end_time, id FROM veterinarian_schedule WHERE veterinarian_id = ? ORDER BY day_of_week, start_time");
    $stmt->execute([$vetId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function get_time_slots() {
    $timeSlots = [];
    $start = strtotime('08:00');
    $end = strtotime('16:00');
    for ($time = $start; $time <= $end; $time += 30 * 60) {
        $timeSlots[] = date('H:i', $time);
    }
    return $timeSlots;
}

function schedule_exists($pdo, $vetId, $dayOfWeek, $startTime, $endTime) {
    $sql = "SELECT COUNT(*) FROM veterinarian_schedule 
            WHERE veterinarian_id = ? AND day_of_week = ? AND start_time = ? AND end_time = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$vetId, $dayOfWeek, $startTime, $endTime]);
    return $stmt->fetchColumn() > 0;
}
function schedule_exists_edit(PDO $pdo, int $vetId, string $dayOfWeek, string $startTime, string $endTime, int $excludeId): bool {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM veterinarian_schedule
        WHERE veterinarian_id = :vetId
          AND day_of_week = :dayOfWeek
          AND id != :excludeId
          AND (
            (start_time < :endTime AND end_time > :startTime)
          )
    ");
    $stmt->execute([
        ':vetId' => $vetId,
        ':dayOfWeek' => $dayOfWeek,
        ':excludeId' => $excludeId,
        ':startTime' => $startTime,
        ':endTime' => $endTime,
    ]);

    return $stmt->fetchColumn() > 0;
}

function get_schedule_by_id_and_vet($pdo, int $id, int $vetId): ?array {
    $stmt = $pdo->prepare("SELECT start_time, end_time FROM veterinarian_schedule WHERE id = ? AND veterinarian_id = ?");
    $stmt->execute([$id, $vetId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}

function get_schedule_by_id($pdo, $id, $vetId) {
    $stmt = $pdo->prepare("SELECT * FROM veterinarian_schedule WHERE id = ? AND veterinarian_id = ?");
    $stmt->execute([$id, $vetId]);
    return $stmt->fetch();
}

function update_schedule($pdo, $id, $vetId, $dayOfWeek, $startTime, $endTime) {
    $stmt = $pdo->prepare("UPDATE veterinarian_schedule SET day_of_week = ?, start_time = ?, end_time = ? WHERE id = ? AND veterinarian_id = ?");
    $stmt->execute([$dayOfWeek, $startTime, $endTime, $id, $vetId]);
}




function add_vet_schedule($pdo, $vetId, $dayOfWeek, $startTime, $endTime) {
    $sql = "INSERT INTO veterinarian_schedule (veterinarian_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$vetId, $dayOfWeek, $startTime, $endTime]);
}

function delete_vet_schedule(PDO $pdo, int $scheduleId, int $vetId): void {
    $stmt = $pdo->prepare("DELETE FROM veterinarian_schedule WHERE id = ? AND veterinarian_id = ?");
    $stmt->execute([$scheduleId, $vetId]);
}


function delete_schedule(PDO $pdo, int $id): void {
    $stmt = $pdo->prepare("DELETE FROM veterinarian_schedule WHERE id = ?");
    $stmt->execute([$id]);
}

function add_schedule(PDO $pdo, int $vet_id, string $start_time, string $end_time) {
    $date = substr($start_time, 0, 10);

    $stmt = $pdo->prepare("SELECT SUM(TIMESTAMPDIFF(MINUTE, start_time, end_time)) AS total_minutes FROM veterinarian_schedule WHERE veterinarian_id = ? AND DATE(start_time) = ?");
    $stmt->execute([$vet_id, $date]);
    $row = $stmt->fetch();

    $total_minutes = $row['total_minutes'] ?? 0;
    $new_appointment_minutes = (strtotime($end_time) - strtotime($start_time)) / 60;
    $total_minutes += $new_appointment_minutes;

    // Ukloni ili komentariši proveru minimalnog trajanja:
    // if ($total_minutes < 240) {
    //    throw new Exception("Minimalno radno vreme je 4 sata.");
    // }

    $stmt = $pdo->prepare("INSERT INTO veterinarian_schedule (veterinarian_id, start_time, end_time) VALUES (?, ?, ?)");
    $stmt->execute([$vet_id, $start_time, $end_time]);
}
function generate_time_options() {
    $times = [];
    $start = strtotime('08:00');
    $end = strtotime('22:00');
    for ($time = $start; $time <= $end; $time += 1800) {
        $format = date('H:i', $time);
        $times[] = $format;
    }
    return $times;
}
function getDaysOptions($selected = null)
{
    $days = ['Monday' => 'Ponedeljak', 'Tuesday' => 'Utorak', 'Wednesday' => 'Sreda', 'Thursday' => 'Četvrtak', 'Friday' => 'Petak', 'Saturday' => 'Subota'];
    $html = '';
    foreach ($days as $eng => $serb) {
        $sel = ($selected === $eng) ? 'selected' : '';
        $html .= "<option value=\"$eng\" $sel>$serb</option>";
    }
    return $html;
}

function getTimeOptions($selected = null)
{
    $start = strtotime("08:00");
    $end = strtotime("22:00");
    $html = '';
    for ($time = $start; $time <= $end; $time += 1800) {
        $label = date("H:i", $time);
        $sel = ($selected === $label) ? 'selected' : '';
        $html .= "<option value=\"$label\" $sel>$label</option>";
    }
    return $html;
}


function get_appointments_for_vet(PDO $pdo, int $vetId): array {
    $stmt = $pdo->prepare("
        SELECT 
            a.id AS appointment_id,
            a.appointment_date,
            a.start_time,
            p.name AS pet_name,
            CONCAT(u.first_name, ' ', u.last_name) AS owner_name
        FROM appointments a
        JOIN pets p ON a.pet_id = p.id
        JOIN pet_owners po ON p.owner_id = po.id
        JOIN users u ON po.user_id = u.id
        WHERE a.veterinarian_id = ?
        ORDER BY a.appointment_date ASC
    ");
    $stmt->execute([$vetId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_treatment_details($pdo, $appointment_id, $vet_id) {
    $stmt = $pdo->prepare("
        SELECT 
            a.*, 
            p.name AS pet_name,
            p.photo,
            CONCAT(u.first_name, ' ', u.last_name) AS owner_name
        FROM appointments a
        JOIN pets p ON a.pet_id = p.id
        JOIN pet_owners po ON p.owner_id = po.id
        JOIN users u ON po.user_id = u.id
        WHERE a.id = ? AND a.veterinarian_id = ?
    ");
    $stmt->execute([$appointment_id, $vet_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_service_price(PDO $pdo, int $id): float {
    $stmt = $pdo->prepare("SELECT price FROM services WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn() ?: 0.0;
}

function get_service_name(PDO $pdo, int $id): string {
    $stmt = $pdo->prepare("SELECT name FROM services WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn() ?: '';
}
function save_treatment_note(PDO $pdo, int $vet_id, int $appointment_id, string $diagnosis, string $treatment, float $price): void {
    $check = $pdo->prepare("SELECT id FROM medical_records WHERE appointment_id = ? AND veterinarian_id = ?");
    $check->execute([$appointment_id, $vet_id]);

    if ($check->fetch()) {
        $update = $pdo->prepare("
            UPDATE medical_records 
            SET diagnosis = ?, treatment = ?, price = ?
            WHERE appointment_id = ? AND veterinarian_id = ?
        ");
        $update->execute([$diagnosis, $treatment, $price, $appointment_id, $vet_id]);
    } else {
        $insert = $pdo->prepare("
            INSERT INTO medical_records (appointment_id, veterinarian_id, pet_id, diagnosis, treatment, price)
            SELECT a.id, a.veterinarian_id, a.pet_id, ?, ?, ?
            FROM appointments a WHERE a.id = ?
        ");
        $insert->execute([$diagnosis, $treatment, $price, $appointment_id]);
    }
}

function get_vet_pets(PDO $pdo, int $vetId): array {
    $stmt = $pdo->prepare("
    SELECT p.id AS pet_id, p.name, p.photo,
           CONCAT(u.first_name, ' ', u.last_name) AS owner_name
    FROM appointments a
    JOIN pets p ON a.pet_id = p.id
    JOIN pet_owners po ON p.owner_id = po.id
    JOIN users u ON po.user_id = u.id
    WHERE a.veterinarian_id = ?
    GROUP BY p.id
");


    $stmt->execute([$vetId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function get_pet_with_owner(PDO $pdo, int $pet_id): ?array {
    $stmt = $pdo->prepare("
        SELECT p.name, p.gender, p.birth_date, p.photo,
               CONCAT(u.first_name, ' ', u.last_name) AS owner_name
        FROM pets p
        JOIN pet_owners po ON p.owner_id = po.id
        JOIN users u ON po.user_id = u.id
        WHERE p.id = ?
    ");
    $stmt->execute([$pet_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function get_medical_history_for_vet(PDO $pdo, int $pet_id, int $vet_id): array {
    $stmt = $pdo->prepare("
        SELECT mr.id, mr.diagnosis, mr.treatment, mr.price, mr.created_at,
               CONCAT(u.first_name, ' ', u.last_name) AS vet_name,
               mr.veterinarian_id AS vet_id
        FROM medical_records mr
        JOIN veterinarians v ON mr.veterinarian_id = v.id
        JOIN users u ON v.user_id = u.id
        WHERE mr.pet_id = ?
        ORDER BY mr.created_at DESC
    ");
    $stmt->execute([$pet_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function get_latest_appointment(PDO $pdo, int $pet_id, int $vet_id): ?int {
    $stmt = $pdo->prepare("
        SELECT id FROM appointments
        WHERE pet_id = ? AND veterinarian_id = ?
        ORDER BY appointment_date DESC
        LIMIT 1
    ");
    $stmt->execute([$pet_id, $vet_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['id'] : null;
}

function get_all_medical_records_for_appointment(PDO $pdo, int $appointment_id): array {
    $stmt = $pdo->prepare("
        SELECT 
            mr.id,
            mr.veterinarian_id AS vet_id,  -- OVO JE KLJUČNO!
            mr.diagnosis, 
            mr.treatment, 
            mr.price, 
            mr.created_at,
            CONCAT(u.first_name, ' ', u.last_name) AS vet_name
        FROM medical_records mr
        JOIN veterinarians v ON mr.veterinarian_id = v.id
        JOIN users u ON v.user_id = u.id
        WHERE mr.appointment_id = ?
        ORDER BY mr.created_at DESC
    ");
    $stmt->execute([$appointment_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


//LOGIN DEOOOOO

require_once 'db.php';

function login_user(PDO $pdo, string $email, string $password): int|string|null {
    $stmt = $pdo->prepare("
        SELECT u.id AS user_id, u.email, u.password, u.role_id, u.is_active, v.id AS vet_id
        FROM users u
        LEFT JOIN veterinarians v ON u.id = v.user_id
        WHERE u.email = ?
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        if (!$user['is_active']) {
            return 'not_active'; // važno: poseban signal da nije aktiviran
        }

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role_id'] = $user['role_id'];

        if ($user['role_id'] == 2 && $user['vet_id']) {
            $_SESSION['vet_id'] = $user['vet_id'];
        }

        return $user['role_id'];
    }

    return null;
}


//REGITER
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'vendor/autoload.php'; // Obavezno ako koristiš Composer za PHPMailer

function register_user($pdo, $first_name, $last_name, $email, $phone, $address, $password) {
    // Provera da li e-mail već postoji
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return "Email adresa je već zauzeta.";
    }

    // Generisanje tokena
    $activation_token = bin2hex(random_bytes(16));
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Unos korisnika u bazu (neaktivan)
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone_number, address, password, role_id, is_active, activation_token)
                           VALUES (?, ?, ?, ?, ?, ?, 3, 0, ?)");
    $stmt->execute([$first_name, $last_name, $email, $phone, $address, $hashed_password, $activation_token]);

    $user_id = $pdo->lastInsertId();

    // Dodavanje u pet_owners ako je korisnik vlasnik
    if ($user_id) {
        $stmt = $pdo->prepare("INSERT INTO pet_owners (user_id) VALUES (?)");
        $stmt->execute([$user_id]);
    }

    // Slanje aktivacionog emaila
    $activation_link = "http://localhost/VetProjekat/activate.php?token=$activation_token";


    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = '1a8d7f596b2e99';
        $mail->Password = 'ee70af4ea947bc'; // koristi App Password za Gmail!
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 2525;

        $mail->setFrom('tvojemail@gmail.com', 'PetCare');
        $mail->addAddress($email, "$first_name $last_name");

        $mail->isHTML(true);
        $mail->Subject = 'Aktivirajte svoj PetCare nalog';
        $mail->Body = "Poštovani $first_name,<br><br>
                      Da biste završili registraciju, kliknite na sledeći link:<br>
                      <a href='$activation_link'>$activation_link</a><br><br>
                      Ukoliko niste vi započeli registraciju, zanemarite ovu poruku.";

        $mail->send();
        return true;

    } catch (Exception $e) {
        return "Greška prilikom slanja e-maila: " . $mail->ErrorInfo;
    }
}