<?php
namespace App\Database;

class Connection {
    private $pdo;

    public function __construct($host, $username, $password, $database) {
        $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";

        try {
            $this->pdo = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]);
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function table($table) {
        return new QueryBuilder($this->pdo, $table);
    }
}
?>