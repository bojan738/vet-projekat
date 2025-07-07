<?php
class DBConfig {
    private string $host = 'localhost';
    private string $dbname = 'cerouno';
    private string $username = 'cerouno';
    private string $password = 'iLWnRwC0ZfrDEyG';
    private ?PDO $pdo = null;

    public function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $this->pdo = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            exit("❌ Greška prilikom povezivanja sa bazom: " . $e->getMessage());
        }
    }

    public function getConnection(): PDO {
        return $this->pdo;
    }
}
