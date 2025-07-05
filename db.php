<?php
$host = 'localhost';      // ili 127.0.0.1
$db   = 'vet';            // ime tvoje baze
$user = 'root';           // korisničko ime
$pass = '';               // lozinka ako postoji
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Greška pri povezivanju s bazom: " . $e->getMessage());
}
?>
