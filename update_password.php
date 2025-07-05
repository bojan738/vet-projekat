<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// PHPMailer autoload i namespace
require_once __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if ($new_password !== $confirm_password) {
    die("Lozinke se ne poklapaju.");
}

// Dohvati korisnika iz baze
$stmt = $pdo->prepare("SELECT password, email, first_name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Korisnik nije pronađen.");
}

if (!password_verify($current_password, $user['password'])) {
    die("Trenutna lozinka nije tačna.");
}

// Generiši token i heš nove lozinke
$token = bin2hex(random_bytes(16));
$hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

// Ubaci u tabelu password_resets (napravi je ako već nemaš)
$stmt = $pdo->prepare("INSERT INTO password_resets (user_id, new_password, token, created_at) VALUES (?, ?, ?, NOW())");
$stmt->execute([$user_id, $hashed_new_password, $token]);

// Pripremi aktivacioni link
$activation_link = "http://localhost/VetProjekat/confirm_password_change.php?token=$token";


// Podesi PHPMailer i pošalji mejl
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth = true;
    $mail->Username = '1a8d7f596b2e99';
    $mail->Password = 'ee70af4ea947bc'; // koristi App Password za Gmail!
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 2525;

    $mail->setFrom('noreply@petcare.com', 'PetCare');
    $mail->addAddress($user['email'], $user['first_name']);

    $mail->isHTML(true);
    $mail->Subject = "Potvrda promene lozinke";
    $mail->Body = "Zdravo " . htmlspecialchars($user['first_name']) . ",<br><br>" .
        "Kliknite na link da potvrdite promenu lozinke:<br>" .
        "<a href='$activation_link'>$activation_link</a><br><br>" .
        "Ako niste vi zahtevali ovu promenu, zanemarite ovaj mejl.";

    $mail->send();

    echo "Poslali smo vam mejl sa linkom za potvrdu promene lozinke. Proverite svoj inbox.";
} catch (Exception $e) {
    echo "Greška pri slanju mejla: " . $mail->ErrorInfo;
}
