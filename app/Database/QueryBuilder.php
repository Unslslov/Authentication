<?php
namespace App\Database;

class QueryBuilder {
    private $pdo;
    private $table;
    private $wheres = [];
    private $bindings = [];

    public function __construct(\PDO $pdo, $table) {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    public function where($column, $operator = '=', $value = null) {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = "{$column} {$operator} ?";
        $this->bindings[] = $value;

        return $this;
    }

    public function first() {
        $result = $this->get();
        return $result[0] ?? null;
    }

    public function get() {
        $sql = "SELECT * FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);

        return $stmt->fetchAll();
    }

    public function insert($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));

        return $this->pdo->lastInsertId();
    }

    public function update($id, $data) {
        $set = implode(', ', array_map(function($column) {
            return "{$column} = ?";
        }, array_keys($data)));

        $sql = "UPDATE {$this->table} SET {$set} WHERE id = ?";

        $bindings = array_values($data);
        $bindings[] = $id;

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($bindings);
    }
}
?>