<?php

namespace Aries\MiniFrameworkStore\Models;

use Aries\MiniFrameworkStore\Includes\Database;
use PDO;

class User extends Database {
    private $db;

    public function __construct() {
        parent::__construct(); // Call the parent constructor to establish the connection
        $this->db = $this->getConnection(); // Get the connection instance
    }

    public function login($data) {
        $sql = "SELECT u.*, up.address, up.phone, up.birthdate, r.name as role 
                FROM users u 
                LEFT JOIN user_profiles up ON u.id = up.user_id 
                LEFT JOIN roles r ON u.role_id = r.id 
                WHERE u.email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'email' => $data['email'],
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function register($data) {
        $this->db->beginTransaction();
        try {
            $sqlUser = "INSERT INTO users (name, email, password, role_id, created_at, updated_at) 
                       VALUES (:name, :email, :password, :role_id, :created_at, :updated_at)";
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_BCRYPT),
                'role_id' => 2, // 2 is the ID for 'customer' role
                'created_at' => $data['created_at'],
                'updated_at' => $data['updated_at']
            ]);
            $userId = $this->db->lastInsertId();

            $sqlProfile = "INSERT INTO user_profiles (user_id, address, phone, birthdate) 
                          VALUES (:user_id, :address, :phone, :birthdate)";
            $stmtProfile = $this->db->prepare($sqlProfile);
            $stmtProfile->execute([
                'user_id' => $userId,
                'address' => $data['address'] ?? null,
                'phone' => $data['phone'] ?? null,
                'birthdate' => $data['birthdate'] ?? null
            ]);

            $this->db->commit();
            return $userId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($data) {
        $this->db->beginTransaction();
        try {
            $sqlUser = "UPDATE users SET name = :name, email = :email WHERE id = :id";
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute([
                'id' => $data['id'],
                'name' => $data['name'],
                'email' => $data['email']
            ]);

            $sqlProfile = "UPDATE user_profiles SET address = :address, phone = :phone, birthdate = :birthdate WHERE user_id = :user_id";
            $stmtProfile = $this->db->prepare($sqlProfile);
            $stmtProfile->execute([
                'user_id' => $data['id'],
                'address' => $data['address'] ?? null,
                'phone' => $data['phone'] ?? null,
                'birthdate' => $data['birthdate'] ?? null
            ]);

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete($id) {
        $this->db->beginTransaction();
        try {
            $sqlProfile = "DELETE FROM user_profiles WHERE user_id = :user_id";
            $stmtProfile = $this->db->prepare($sqlProfile);
            $stmtProfile->execute(['user_id' => $id]);

            $sqlUser = "DELETE FROM users WHERE id = :id";
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute(['id' => $id]);

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
