<?php
session_start();
require_once 'functions.php';

// Simulacija korisnika (ukloni ovo ako već imaš $_SESSION['user_id'])
$_SESSION['user_id'] = 1;

// Postavi ID ljubimca koji želiš da obrišeš:
$pet_id = 1; // <-- OVDE stavi ID ljubimca iz baze koji postoji i ima owner_id = 1

echo "<h3>Test brisanja ljubimca ID $pet_id</h3>";

// Provera da li ljubimac postoji i pripada korisniku
$stmt = $pdo->prepare("SELECT * FROM pets WHERE id = :id");
$stmt->execute(['id' => $pet_id]);
$pet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pet) {
    exit("❌ Ljubimac ne postoji.");
}
if ($pet['owner_id'] != $_SESSION['user_id']) {
    exit("❌ Ljubimac NE pripada ulogovanom korisniku.");
}

// Funkcija brisanja

// Pokušaj brisanja
if (pet_delete($pdo, $pet_id)) {
    echo "✅ Ljubimac je uspešno obrisan.";
} else {
    echo "❌ Brisanje nije uspelo.";
}
?>
