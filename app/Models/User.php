<?php

namespace App\Models;

use App\Utils\Database;
use PDO;
use PDOException;
use Carbon\Carbon;

class User
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Создание нового пользователя
     */
    public function create(array $data): ?int
    {
        try {
            $sql = "INSERT INTO users (name, email, phone, password_hash, created_at, updated_at) 
                    VALUES (:name, :email, :phone, :password, :created_at, :updated_at)";

            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            $params = [
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':phone' => $data['phone'],
                ':password' => $hashedPassword,
                ':created_at' => Carbon::now(),
                ':updated_at' => Carbon::now(),
            ];

            $user = (int)$this->db->insert($sql, $params);
            return $user;

        } catch (PDOException $e) {
            // Логирование ошибки
            error_log("User creation error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Поиск пользователя по email
     */
    public function findByEmail(string $email): ?array
    {
        try {
            $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
            $params = [':email' => $email];

            $user = $this->db->first($sql, $params);
            return $user;

        } catch (PDOException $e) {
            error_log("Find by email error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Поиск пользователя по телефону
     */
    public function findByPhone(string $phone): ?array
    {
        try {
            $sql = "SELECT * FROM users WHERE phone = :phone LIMIT 1";
            $params = [':phone' => $phone];

            $user = $this->db->first($sql, $params);
            return $user;

        } catch (PDOException $e) {
            error_log("Find by phone error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Поиск пользователя по ID
     */
    public function findById(int $id): ?array
    {
        try {
            $sql = "SELECT id, name, email, phone, created_at, updated_at 
                FROM users WHERE id = :id LIMIT 1";

            return $this->db->first($sql, [':id' => $id]) ?: null;

        } catch (PDOException $e) {
            error_log("Find by id error: " . $e->getMessage());
            return null;
        }
    }
}