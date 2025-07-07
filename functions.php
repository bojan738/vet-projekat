
<?php
require_once 'db_config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'vendor/autoload.php'; // Composer autoload

class VeterinarskaOrdinacija
{
    private $pdo;

    public function __construct()
    {
        $db = new DBConfig();
        $this->pdo = $db->getConnection();
    }


    public function getAllVeterinarians(): array
    {
        $stmt = $this->pdo->query("
        SELECT 
            v.id,
            v.user_id,
            v.specialization,
            v.license_number,
            v.photo,
            u.first_name,
            u.last_name,
            u.email,
            u.phone_number,
            u.address
        FROM veterinarians v
        INNER JOIN users u ON v.user_id = u.id
    ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getAllServices(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM services");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServiceById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    public function getUserByEmail(string $email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getAppointmentById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM appointments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    public function blockUser(int $userId): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    public function unblockUser(int $userId): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
        return $stmt->execute([$userId]);
    }


    public function sendAccountStatusEmail(string $email, string $fullName, bool $isActive): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = 'f9cd07efd4a868';
            $mail->Password = 'f4d0acd5a04c9b';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 2525;

            $mail->setFrom('noreply@petcare.com', 'PetCare');
            $mail->addAddress($email, $fullName);
            $mail->isHTML(true);

            if ($isActive) {
                $mail->Subject = 'Nalog ponovo aktiviran - PetCare';
                $mail->Body = "Postovani $fullName,<br><br>Vas nalog je ponovo aktiviran. Mozete se prijaviti.<br><br>Srdacno,<br>PetCare tim";
            } else {
                $mail->Subject = 'Nalog deaktiviran - PetCare';
                $mail->Body = "Postovani $fullName,<br><br>Vas nalog je deaktiviran i vise ne mozete pristupiti sistemu.<br><br>Srdacno,<br>PetCare tim";
            }

            return $mail->send();
        } catch (Exception $e) {
            error_log("Greška pri slanju mejla: " . $mail->ErrorInfo);
            return false;
        }
    }
    public function getMedicalRecordById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM medical_records WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function createAppointment(int $user_id, int $pet_id, int $schedule_id, string $date, int $service_id): array
    {
        try {
            $stmt = $this->pdo->prepare("
            SELECT veterinarian_id, start_time, end_time 
            FROM veterinarian_schedule 
            WHERE id = ?
        ");
            $stmt->execute([$schedule_id]);
            $slot = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$slot) {
                return ['success' => false, 'message' => '❌ Termin ne postoji.'];
            }

            // Provera da li je termin već zauzet
            $check = $this->pdo->prepare("
            SELECT COUNT(*) FROM appointments 
            WHERE schedule_id = ? AND appointment_date = ? AND status = 'scheduled'
        ");
            $check->execute([$schedule_id, $date]);

            if ($check->fetchColumn() > 0) {
                return ['success' => false, 'message' => '❌ Termin je već zauzet.'];
            }

            // GENERATE CODE
            $code = random_int(100000, 999999);

            // CREATE TERM
            $stmtInsert = $this->pdo->prepare("
            INSERT INTO appointments (
                user_id, pet_id, veterinarian_id, schedule_id, service_id,
                appointment_date, start_time, end_time, status, reservation_code
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'scheduled', ?)
        ");
            $stmtInsert->execute([
                $user_id,
                $pet_id,
                $slot['veterinarian_id'],
                $schedule_id,
                $service_id,
                $date,
                $slot['start_time'],
                $slot['end_time'],
                $code
            ]);

            // SEND EMIAL
            $user = $this->getUserById($user_id);
            if ($user) {
                $this->sendReservationConfirmationMail($user['email'], $user['first_name'], $code);
            }

            return ['success' => true, 'message' => "✅ Termin zakazan. Kod potvrde: $code"];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Greška: ' . $e->getMessage()];
        }
    }

    public function getPetsByUser(int $user_id): array
    {
        $stmt = $this->pdo->prepare("
        SELECT p.* FROM pets p
        JOIN pet_owners po ON p.owner_id = po.id
        WHERE po.user_id = :user_id
    ");
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllVets(): array
    {
        $stmt = $this->pdo->query("
        SELECT v.id AS vet_id, u.first_name, u.last_name, u.email
        FROM veterinarians v
        JOIN users u ON v.user_id = u.id
        ORDER BY u.first_name, u.last_name
    ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getAllTreatments(): array
    {
        $stmt = $this->pdo->query("SELECT id, name, price FROM services ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPetFullInfo(int $pet_id): ?array
    {
        $stmt = $this->pdo->prepare("
        SELECT 
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
        LIMIT 1
    ");
        $stmt->execute(['pet_id' => $pet_id]);
        $pet = $stmt->fetch(PDO::FETCH_ASSOC);
        return $pet ?: null;
    }


    public function addPenaltyToOwner(int $user_id): void
    {
        $stmt = $this->pdo->prepare("UPDATE users SET negative_points = negative_points + 1 WHERE id = ?");
        $stmt->execute([$user_id]);
    }


    public function getOwnerIdByAppointment(int $appointment_id): ?int
    {
        $stmt = $this->pdo->prepare("SELECT user_id FROM appointments WHERE id = :appointment_id");
        $stmt->execute(['appointment_id' => $appointment_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['user_id'] : null;
    }

    public function saveMedicalNote(int $appointment_id, int $vet_id, int $pet_id, string $diagnosis, string $treatment, float $price): void
    {
        $stmt = $this->pdo->prepare("
        INSERT INTO medical_records (appointment_id, veterinarian_id, pet_id, diagnosis, treatment, price, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
        $stmt->execute([$appointment_id, $vet_id, $pet_id, $diagnosis, $treatment, $price]);
    }

    public function updateMedicalNote(int $id, string $diagnosis, string $treatment, float $price): void
    {
        $stmt = $this->pdo->prepare("
        UPDATE medical_records SET diagnosis = ?, treatment = ?, price = ? WHERE id = ?
    ");
        $stmt->execute([$diagnosis, $treatment, $price, $id]);
    }

    public function deleteMedicalNote(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM medical_records WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function getMedicalRecordsByAppointment(int $appointment_id): array
    {
        $stmt = $this->pdo->prepare("
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

    public function getUserProfile(int $user_id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT first_name, last_name, email, phone_number FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }


    public function getVetInfo(int $vetId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT u.first_name, u.last_name, u.email, u.phone_number, 
                   v.specialization, v.license_number, v.photo
            FROM veterinarians v
            JOIN users u ON v.user_id = u.id
            WHERE v.id = ?
        ");
        $stmt->execute([$vetId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getVetSchedule(int $vetId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT day_of_week, start_time, end_time, id 
            FROM veterinarian_schedule 
            WHERE veterinarian_id = ? 
            ORDER BY day_of_week, start_time
        ");
        $stmt->execute([$vetId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTimeSlots(): array
    {
        $timeSlots = [];
        $start = strtotime('08:00');
        $end = strtotime('16:00');
        for ($time = $start; $time <= $end; $time += 30 * 60) {
            $timeSlots[] = date('H:i', $time);
        }
        return $timeSlots;
    }

    public function scheduleExists(int $vetId, string $dayOfWeek, string $startTime, string $endTime): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM veterinarian_schedule 
            WHERE veterinarian_id = ? AND day_of_week = ? AND start_time = ? AND end_time = ?
        ");
        $stmt->execute([$vetId, $dayOfWeek, $startTime, $endTime]);
        return $stmt->fetchColumn() > 0;
    }

    public function scheduleExistsEdit(int $vetId, string $dayOfWeek, string $startTime, string $endTime, int $excludeId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM veterinarian_schedule
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

    public function getScheduleById(int $id, int $vetId): array|false
    {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM veterinarian_schedule 
            WHERE id = ? AND veterinarian_id = ?
        ");
        $stmt->execute([$id, $vetId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateSchedule(int $id, int $vetId, string $dayOfWeek, string $startTime, string $endTime): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE veterinarian_schedule 
            SET day_of_week = ?, start_time = ?, end_time = ? 
            WHERE id = ? AND veterinarian_id = ?
        ");
        $stmt->execute([$dayOfWeek, $startTime, $endTime, $id, $vetId]);
    }

    public function addVetSchedule(int $vetId, string $dayOfWeek, string $startTime, string $endTime): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO veterinarian_schedule (veterinarian_id, day_of_week, start_time, end_time) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$vetId, $dayOfWeek, $startTime, $endTime]);
    }

    public function getAppointmentsForVet(int $vetId): array
    {
        $stmt = $this->pdo->prepare("
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

    public function getTreatmentDetails(int $appointmentId, int $vetId): array|false
    {
        $stmt = $this->pdo->prepare("
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
        $stmt->execute([$appointmentId, $vetId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getMedicalHistoryForVet(int $petId, int $vetId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT mr.id, mr.diagnosis, mr.treatment, mr.price, mr.created_at,
                   CONCAT(u.first_name, ' ', u.last_name) AS vet_name,
                   mr.veterinarian_id AS vet_id
            FROM medical_records mr
            JOIN veterinarians v ON mr.veterinarian_id = v.id
            JOIN users u ON v.user_id = u.id
            WHERE mr.pet_id = ?
            ORDER BY mr.created_at DESC
        ");
        $stmt->execute([$petId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function loginUser(string $email, string $password): int|string|null
    {
        $stmt = $this->pdo->prepare("
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
                return 'not_active';
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


    public function getAllServices1(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM services ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPetsByOwner1($user_id)
    {
        $stmt = $this->pdo->prepare("
        SELECT 
            p.id, 
            p.name, 
            p.age, 
            p.gender, 
            p.birth_date,
            t.name AS type_name, 
            b.name AS breed_name, 
            p.photo 
        FROM pets p
        JOIN pet_types t ON p.type_id = t.id
        JOIN pet_breeds b ON p.breed_id = b.id
        WHERE p.owner_id = (
            SELECT id FROM pet_owners WHERE user_id = ?
        )
    ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getBreedsGroupedByType(): array
    {
        $stmt = $this->pdo->query("
        SELECT t.name AS type_name, b.name AS breed_name
        FROM pet_breeds b
        JOIN pet_types t ON b.type_id = t.id
        ORDER BY t.name, b.name
    ");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($results as $row) {
            $type = $row['type_name'];
            $breed = $row['breed_name'];
            $grouped[$type][] = $breed;
        }
        return $grouped;
    }

    public function petBelongsToUser(int $pet_id, int $user_id): bool
    {
        $stmt = $this->pdo->prepare("
        SELECT p.id
        FROM pets p
        JOIN pet_owners o ON p.owner_id = o.id
        WHERE p.id = ? AND o.user_id = ?
    ");
        $stmt->execute([$pet_id, $user_id]);
        return $stmt->fetch() !== false;
    }

    public function petDelete(int $pet_id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM pets WHERE id = ?");
        $stmt->execute([$pet_id]);
    }

    public function updatePet($id, $name, $type_name, $age, $birth_date, $gender, $breed_name, $image_path = null)
    {
        $type_id = $this->insertPetType($type_name);
        $breed_id = $this->insertPetBreed($breed_name, $type_id);

        $sql = "UPDATE pets SET name = ?, type_id = ?, age = ?, birth_date = ?, gender = ?, breed_id = ?";
        $params = [$name, $type_id, $age, $birth_date, $gender, $breed_id];

        if ($image_path !== null) {
            $sql .= ", photo = ?";
            $params[] = $image_path;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }


    public function insertPetType(string $type_name): int
    {
        $stmt = $this->pdo->prepare("SELECT id FROM pet_types WHERE name = ?");
        $stmt->execute([$type_name]);
        $existing = $stmt->fetchColumn();

        if ($existing) {
            return (int)$existing;
        }

        $stmt = $this->pdo->prepare("INSERT INTO pet_types (name) VALUES (?)");
        $stmt->execute([$type_name]);
        return (int)$this->pdo->lastInsertId();
    }

    public function insertPetBreed(string $breed_name, int $type_id): int
    {
        $stmt = $this->pdo->prepare("SELECT id FROM pet_breeds WHERE name = ? AND type_id = ?");
        $stmt->execute([$breed_name, $type_id]);
        $existing = $stmt->fetchColumn();

        if ($existing) {
            return (int)$existing;
        }

        $stmt = $this->pdo->prepare("INSERT INTO pet_breeds (name, type_id) VALUES (?, ?)");
        $stmt->execute([$breed_name, $type_id]);
        return (int)$this->pdo->lastInsertId();
    }

    public function getOwnerIdByUserId(int $user_id): ?int
    {
        $stmt = $this->pdo->prepare("SELECT id FROM pet_owners WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['id'] : null;
    }

    public function add_pet(
        int     $owner_id,
        string  $name,
        int     $type_id,
        int     $age,
        string  $gender,
        int     $breed_id,
        ?string $photo,
        string  $birth_date
    ): void
    {
        $stmt = $this->pdo->prepare("
        INSERT INTO pets (owner_id, name, type_id, age, gender, breed_id, photo, birth_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
        $stmt->execute([
            $owner_id,
            $name,
            $type_id,
            $age,
            $gender,
            $breed_id,
            $photo,
            $birth_date
        ]);
    }

    public function getPetsByUser1(int $user_id): array
    {
        $stmt = $this->pdo->prepare("
        SELECT p.id, p.name, pt.name AS type_name, pb.name AS breed_name, p.age, p.gender, p.birth_date, p.photo
        FROM pets p
        JOIN pet_owners po ON p.owner_id = po.id
        JOIN pet_types pt ON p.type_id = pt.id
        JOIN pet_breeds pb ON p.breed_id = pb.id
        WHERE po.user_id = ?
        ORDER BY p.name
    ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTreatmentsByPet1(int $pet_id): array
    {
        $stmt = $this->pdo->prepare("
        SELECT 
            a.appointment_date,
            a.start_time,
            s.name AS service_name,
            CONCAT(u.first_name, ' ', u.last_name) AS vet_name
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
    public function getAvailableSlots(int $vet_id, string $date): array
    {
        $dayOfWeekMap = [
            'Monday' => 'Ponedeljak',
            'Tuesday' => 'Utorak',
            'Wednesday' => 'Sreda',
            'Thursday' => 'Cetvrtak',
            'Friday' => 'Petak',
            'Saturday' => 'Subota',
            'Sunday' => 'Nedelja'
        ];
        $phpDay = date('l', strtotime($date));
        $dbDay = $dayOfWeekMap[$phpDay] ?? null;

        if (!$dbDay) return [];

        $stmt = $this->pdo->prepare("
        SELECT vs.id AS schedule_id, vs.start_time, vs.end_time
        FROM veterinarian_schedule vs
        WHERE vs.veterinarian_id = :vet_id
          AND vs.day_of_week = :day_of_week
          AND NOT EXISTS (
              SELECT 1 FROM appointments a
              WHERE a.schedule_id = vs.id
                AND a.appointment_date = :date
                AND a.status = 'zakazano'
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

    public function getAppointmentsForPet(int $pet_id): array
    {
        $stmt = $this->pdo->prepare("
        SELECT 
            a.id,
            a.appointment_date,
            vs.start_time,
            vs.end_time,
            CONCAT(vs.start_time, ' - ', vs.end_time) AS time_slot,
            CONCAT(u.first_name, ' ', u.last_name) AS vet_name,
            a.veterinarian_id,
            a.schedule_id
        FROM appointments a
        JOIN veterinarian_schedule vs ON a.schedule_id = vs.id
        JOIN veterinarians v ON a.veterinarian_id = v.id
        JOIN users u ON v.user_id = u.id
        WHERE a.pet_id = ?
          AND a.status = 'scheduled'
        ORDER BY a.appointment_date DESC
    ");
        $stmt->execute([$pet_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableScheduleSlots(int $vet_id, string $date, int $exclude_schedule_id = 0): array
    {
        $dayOfWeekMap = [
            'Monday' => 'Ponedeljak',
            'Tuesday' => 'Utorak',
            'Wednesday' => 'Sreda',
            'Thursday' => 'Cetvrtak',
            'Friday' => 'Petak',
            'Saturday' => 'Subota',
            'Sunday' => 'Nedelja'
        ];

        $phpDay = date('l', strtotime($date));
        $dbDay = $dayOfWeekMap[$phpDay] ?? null;

        if (!$dbDay) return [];

        $query = "
        SELECT vs.id, vs.start_time, vs.end_time
        FROM veterinarian_schedule vs
        WHERE vs.veterinarian_id = :vet_id
          AND vs.day_of_week = :day_of_week
          AND vs.id != :exclude_id
          AND NOT EXISTS (
              SELECT 1 FROM appointments a
              WHERE a.schedule_id = vs.id
                AND a.appointment_date = :date
                AND a.status = 'scheduled'
          )
        ORDER BY vs.start_time
    ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':vet_id' => $vet_id,
            ':day_of_week' => $dbDay,
            ':exclude_id' => $exclude_schedule_id,
            ':date' => $date
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAppointmentById1(int $appointment_id): ?array
    {
        $stmt = $this->pdo->prepare("
        SELECT a.id, a.pet_id, a.veterinarian_id, a.schedule_id, a.appointment_date,
               vs.start_time, vs.end_time
        FROM appointments a
        JOIN veterinarian_schedule vs ON a.schedule_id = vs.id
        WHERE a.id = ?
    ");
        $stmt->execute([$appointment_id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        return $appointment ?: null;
    }

    public function getScheduleSlotById(int $slot_id): ?array
    {
        $stmt = $this->pdo->prepare("
        SELECT id, veterinarian_id, start_time, end_time
        FROM veterinarian_schedule
        WHERE id = ?
    ");
        $stmt->execute([$slot_id]);
        $slot = $stmt->fetch(PDO::FETCH_ASSOC);

        return $slot ?: null;
    }

    public function isTimeSlotTaken(int $slot_id, string $date, int $vet_id): bool
    {
        $stmt = $this->pdo->prepare("
        SELECT COUNT(*) FROM appointments
        WHERE schedule_id = :slot_id
          AND appointment_date = :date
          AND veterinarian_id = :vet_id
          AND status = 'scheduled'
    ");
        $stmt->execute([
            ':slot_id' => $slot_id,
            ':date' => $date,
            ':vet_id' => $vet_id
        ]);
        return $stmt->fetchColumn() > 0;
    }

    public function petHasAppointmentForDateAndSlot(int $pet_id, int $slot_id, string $date): bool
    {
        $stmt = $this->pdo->prepare("
        SELECT COUNT(*) FROM appointments
        WHERE pet_id = :pet_id
          AND schedule_id = :slot_id
          AND appointment_date = :date
          AND status = 'scheduled'
    ");
        $stmt->execute([
            ':pet_id' => $pet_id,
            ':slot_id' => $slot_id,
            ':date' => $date
        ]);
        return $stmt->fetchColumn() > 0;
    }

    public function updateAppointmentSlot(int $appointment_id, int $new_schedule_id, string $new_start_time, string $new_end_time): bool
    {
        $stmt = $this->pdo->prepare("
        UPDATE appointments
        SET schedule_id = :schedule_id,
            start_time = :start_time,
            end_time = :end_time
        WHERE id = :appointment_id
    ");
        return $stmt->execute([
            ':schedule_id' => $new_schedule_id,
            ':start_time' => $new_start_time,
            ':end_time' => $new_end_time,
            ':appointment_id' => $appointment_id
        ]);
    }

    public function getUserAppointmentForCancellation(int $appointment_id, int $user_id): ?array
    {
        $stmt = $this->pdo->prepare("
        SELECT a.*, v.user_id AS vet_user_id
        FROM appointments a
        JOIN veterinarians v ON a.veterinarian_id = v.id
        WHERE a.id = :appointment_id AND a.user_id = :user_id
    ");
        $stmt->execute([
            ':appointment_id' => $appointment_id,
            ':user_id' => $user_id
        ]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
        return $appointment ?: null;
    }

    public function freeAppointment(int $appointment_id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE appointments SET status = 'otkazano' WHERE id = :id");
        return $stmt->execute([':id' => $appointment_id]);
    }

    public function updateUserProfile(int $user_id, string $first_name, string $last_name, string $email, string $phone): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone_number = ? WHERE id = ?");
        return $stmt->execute([$first_name, $last_name, $email, $phone, $user_id]);
    }

    public function getPasswordResetByToken(string $token): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function confirmNewPassword(int $user_id, string $hashedPassword, string $token): void
    {
        // UPDATE USER PASS
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $user_id]);

        // DELETE TOKEN
        $stmt = $this->pdo->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);
    }


    public function storeResetCode(int $user_id, string $code): void
    {
        $stmt = $this->pdo->prepare("
        INSERT INTO password_resets_codes (user_id, code, created_at) 
        VALUES (?, ?, NOW())
    ");
        $stmt->execute([$user_id, $code]);
    }


    public function getResetCodeData(string $code): ?array
    {
        $stmt = $this->pdo->prepare("SELECT user_id, code, created_at FROM password_resets_codes WHERE code = ?");
        $stmt->execute([$code]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    public function insertService(string $name, string $description, float $price): array {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO services (name, description, price) VALUES (?, ?, ?)");
            $stmt->execute([$name, $description, $price]);
            return ['success' => true, 'message' => 'Usluga je uspešno dodata.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Greška pri dodavanju usluge: ' . $e->getMessage()];
        }
    }
    public function deleteService(int $id): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM services WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    public function getServiceById1(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$id]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);
        return $service ?: null;
    }
    public function updateService(int $id, string $name, string $description, float $price): bool {
        $stmt = $this->pdo->prepare("UPDATE services SET name = ?, description = ?, price = ? WHERE id = ?");
        return $stmt->execute([$name, $description, $price, $id]);
    }

    public function getAllAppointments1(): array {
        $stmt = $this->pdo->query("
       SELECT 
    a.*,
    CONCAT(u_owner.first_name, ' ', u_owner.last_name) AS owner_name,
    p.name AS pet_name,
    v.id AS vet_id,
    u_vet.first_name AS vet_first_name,
    u_vet.last_name AS vet_last_name
FROM appointments a
JOIN pets p ON a.pet_id = p.id
JOIN pet_owners po ON p.owner_id = po.id
JOIN users u_owner ON po.user_id = u_owner.id
JOIN veterinarians v ON a.veterinarian_id = v.id
JOIN users u_vet ON v.user_id = u_vet.id
ORDER BY a.appointment_date DESC, a.start_time ASC


    ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function activateUserByToken(string $token): bool {
        $stmt = $this->pdo->prepare("
        UPDATE users 
        SET is_active = 1, activation_token = NULL 
        WHERE activation_token = ?
    ");
        $stmt->execute([$token]);
        return $stmt->rowCount() > 0;
    }

    public function registerUser(string $first_name, string $last_name, string $email, string $phone, string $address, string $password): string|bool
    {
        try {
            // CHECK IF EMAIL EXIST
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return "Email adresa je već zauzeta.";
            }

            // GENERATE TOKEN FOR ACTIVATION
            $activation_token = bin2hex(random_bytes(16));

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // INSERT IN USER WITH STATUS NEGATIVE
            $stmt = $this->pdo->prepare("
            INSERT INTO users 
            (first_name, last_name, email, phone_number, address, password, role_id, is_active, activation_token)
            VALUES (?, ?, ?, ?, ?, ?, 3, 0, ?)
        ");
            $stmt->execute([$first_name, $last_name, $email, $phone, $address, $hashed_password, $activation_token]);

            $user_id = $this->pdo->lastInsertId();

            if ($user_id) {
                $stmt = $this->pdo->prepare("INSERT INTO pet_owners (user_id) VALUES (?)");
                $stmt->execute([$user_id]);
            } else {
                return "Greška prilikom kreiranja korisnika.";
            }

            // LINK FOR ACTIVATION PROFILE
            $activation_link = "http://localhost/VetProjekat/activate.php?token=$activation_token";

            // SEND EMAIL WITH LINK FOR ACTIVATION
            require_once 'vendor/autoload.php';
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'sandbox.smtp.mailtrap.io';
                $mail->SMTPAuth = true;
                $mail->Username = 'f9cd07efd4a868';
                $mail->Password = 'f4d0acd5a04c9b';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 2525;

                $mail->setFrom('noreply@petcare.com', 'PetCare');
                $mail->addAddress($email, "$first_name $last_name");

                $mail->isHTML(true);
                $mail->Subject = 'Aktivirajte svoj PetCare nalog';
                $mail->Body = "Postovani $first_name,<br><br>
                          Da biste zavrsili registraciju, kliknite na sledeci link:<br>
                          <a href='$activation_link'>$activation_link</a><br><br>
                          Ukoliko niste vi zapoceli registraciju, zanemarite ovu poruku.";

                $mail->send();
            } catch (Exception $e) {
                error_log("Greška pri slanju aktivacionog mejla: " . $mail->ErrorInfo);
            }

            return true;

        } catch (PDOException $e) {
            return "Greška prilikom registracije: " . $e->getMessage();
        }
    }



    public function updateUserPassword(int $user_id, string $hashedPassword): void
    {
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $user_id]);
    }

    public function deleteResetCode(string $code): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->execute([$code]);
    }

    public function getUserById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    public function getAppointmentById2(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM appointments WHERE id = ?");
        $stmt->execute([$id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
        return $appointment ?: null;
    }
    public function getAppointmentsByDate(string $date): array {
        $stmt = $this->pdo->prepare("
        SELECT a.*, 
               CONCAT(u.first_name, ' ', u.last_name) AS owner_name,
               p.name AS pet_name,
               vu.first_name AS vet_first_name,
               vu.last_name AS vet_last_name
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN pets p ON a.pet_id = p.id
        JOIN veterinarians v ON a.veterinarian_id = v.id
        JOIN users vu ON v.user_id = vu.id
        WHERE a.appointment_date = ?
        ORDER BY a.start_time
    ");
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllPets(): array
    {
        try {
            $stmt = $this->pdo->query("
            SELECT 
                p.id AS pet_id,
                p.name,
                p.photo,
                CONCAT(u.first_name, ' ', u.last_name) AS owner_name
            FROM pets p
            JOIN pet_owners po ON p.owner_id = po.id
            JOIN users u ON po.user_id = u.id
        ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Greška u getAllPets(): " . $e->getMessage());
            return [];
        }
    }


    public function deleteScheduleById(int $schedule_id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM veterinarian_schedule WHERE id = ?");
        return $stmt->execute([$schedule_id]);
    }


    public function sendReservationConfirmationMail(string $email, string $name, string $code): void
    {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = 'f9cd07efd4a868';
            $mail->Password = 'f4d0acd5a04c9b';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 2525;

            $mail->setFrom('noreply@petcare.com', 'PetCare');
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = "Potvrda zakazivanja termina";
            $mail->Body = "Postovani " . htmlspecialchars($name) . ",<br><br>" .
                "Uspesno ste zakazali termin.<br>" .
                "Vas potvrdjujuci kod je: <strong>$code</strong><br><br>" .
                "Vidimo se uskoro!";

            $mail->send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {

        }
    }

    public function deleteVeterinarian(int $vet_id): bool
    {

        try {

            $stmt = $this->pdo->prepare("SELECT user_id FROM veterinarians WHERE id = ?");
            $stmt->execute([$vet_id]);
            $vet = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$vet) {
                return false;
            }

            $user_id = $vet['user_id'];

            // delete veterinarians
            $stmt = $this->pdo->prepare("DELETE FROM veterinarians WHERE id = ?");
            $stmt->execute([$vet_id]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getAllUsers(): array
    {
        $stmt = $this->pdo->query("
        SELECT id, first_name, last_name, email, phone_number, role_id, is_active ,address,negative_points
        FROM users
        WHERE role_id=3
        ORDER BY last_name, first_name
    ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function insertFullVeterinarian(string $firstName, string $lastName, string $email, string $phoneNumber, string $address, string $specialization, string $licenseNumber, string $password, ?string $photo): array
    {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->pdo->prepare("
            INSERT INTO users (first_name, last_name, email, phone_number, address, password, role_id, is_active)
            VALUES (?, ?, ?, ?, ?, ?, 2, 1)
        ");
            $stmt->execute([$firstName, $lastName, $email, $phoneNumber, $address, $hashedPassword]);

            $userId = $this->pdo->lastInsertId();

            $stmtVet = $this->pdo->prepare("
            INSERT INTO veterinarians (user_id, specialization, license_number, photo)
            VALUES (?, ?, ?, ?)
        ");
            $stmtVet->execute([$userId, $specialization, $licenseNumber, $photo]);

            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }



    public function updateUser(int $userId, string $firstName, string $lastName, string $email, string $phoneNumber, string $address): bool
    {
        $stmt = $this->pdo->prepare("
        UPDATE users
        SET first_name = ?, last_name = ?, email = ?, phone_number = ?, address = ?
        WHERE id = ?
    ");
        return $stmt->execute([$firstName, $lastName, $email, $phoneNumber, $address, $userId]);
    }

    public function getAllVeterinarians2(): array
    {
        $stmt = $this->pdo->query("
        SELECT v.id, u.id AS user_id, u.first_name, u.last_name, u.email, u.phone_number, u.address, v.specialization, v.license_number
        FROM veterinarians v
        JOIN users u ON v.user_id = u.id
        ORDER BY u.first_name, u.last_name
    ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVeterinarianById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("
        SELECT v.id, u.id AS user_id, u.first_name, u.last_name, u.email, u.phone_number, u.address, v.specialization, v.license_number
        FROM veterinarians v
        JOIN users u ON v.user_id = u.id
        WHERE v.id = ?
    ");
        $stmt->execute([$id]);
        $vet = $stmt->fetch(PDO::FETCH_ASSOC);
        return $vet ?: null;
    }


    public function updateVeterinarianFull($vet_id, $user_id, $first_name, $last_name, $email, $phone_number, $address, $specialization, $license_number)
    {
        $sql = "UPDATE veterinarians v
            JOIN users u ON v.user_id = u.id
            SET u.first_name = ?, u.last_name = ?, u.email = ?, u.phone_number = ?, u.address = ?,
                v.specialization = ?, v.license_number = ?
            WHERE v.id = ? AND v.user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$first_name, $last_name, $email, $phone_number, $address, $specialization, $license_number, $vet_id, $user_id]);
    }


    public function getMedicalRecordById1(int $pet_id): array
    {
        $stmt = $this->pdo->prepare("
        SELECT mr.*, u.first_name AS vet_first_name, u.last_name AS vet_last_name, p.name AS pet_name
FROM medical_records mr
JOIN veterinarians v ON mr.veterinarian_id = v.id
JOIN users u ON v.user_id = u.id
JOIN pets p ON mr.pet_id = p.id
WHERE mr.pet_id = ?
ORDER BY mr.created_at DESC

    ");
        $stmt->execute([$pet_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getAppointmentById3(int $appointment_id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM appointments WHERE id = ?");
        $stmt->execute([$appointment_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }


    public function addPenaltyToOwner1(int $user_id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET negative_points = negative_points + 1 WHERE id = ?");
        return $stmt->execute([$user_id]);
    }

    public function deleteAppointment(int $appointment_id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM appointments WHERE id = ?");
        return $stmt->execute([$appointment_id]);
    }

    public function verifyReservationCode(int $appointment_id, string $code_input, int $vet_id): bool {
        $stmt = $this->pdo->prepare("SELECT 1 FROM appointments WHERE id = ? AND reservation_code = ? AND veterinarian_id = ?");
        $stmt->execute([$appointment_id, $code_input, $vet_id]);
        return (bool)$stmt->fetchColumn();
    }


    public function getAppointmentCode(int $appointment_id, int $vet_id): ?string {
        $stmt = $this->pdo->prepare("
        SELECT reservation_code 
        FROM appointments 
        WHERE id = :appointment_id AND veterinarian_id = :vet_id
    ");
        $stmt->execute([
            ':appointment_id' => $appointment_id,
            ':vet_id' => $vet_id
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['reservation_code'] : null;
    }

    public function saveTreatmentDetails($appointment_id, $veterinarian_id, $pet_id, $diagnosis, $treatment_name, $price) {
        $sql = "INSERT INTO medical_records (appointment_id, veterinarian_id, pet_id, diagnosis, treatment, price, created_at)
            VALUES (:appointment_id, :veterinarian_id, :pet_id, :diagnosis, :treatment, :price, NOW())";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':appointment_id' => $appointment_id,
            ':veterinarian_id' => $veterinarian_id,
            ':pet_id' => $pet_id,
            ':diagnosis' => $diagnosis,
            ':treatment' => $treatment_name,
            ':price' => $price
        ]);
    }




    public function getTreatmentRecords(int $appointment_id): array
    {
        $sql = "
        SELECT 
            mr.*,
            u.first_name,
            u.last_name
        FROM medical_records mr
        JOIN veterinarians v ON mr.veterinarian_id = v.id
        JOIN users u ON v.user_id = u.id
        WHERE mr.appointment_id = ?
        ORDER BY mr.created_at DESC
    ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$appointment_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function deleteTreatmentRecord(int $record_id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM medical_records WHERE id = ?");
        return $stmt->execute([$record_id]);
    }


}

?>
