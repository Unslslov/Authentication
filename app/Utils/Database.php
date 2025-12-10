<?php
namespace App\Utils;

use PDO;
USE PDOException;

class Database {
    private $pdo;
    private static $instance = null;

    private function __construct() {
        $config = Config::get('database.connections.mysql');

        $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset={$config['charset']}";

        try {
            $this->pdo = new \PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function select($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function first($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        return $this->pdo->lastInsertId();
    }

    public function update($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function delete($sql, $params = []) {
        return $this->update($sql, $params);
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollback() {
        return $this->pdo->rollback();
    }

    public function getPdo() {
        return $this->pdo;
    }
}
?>