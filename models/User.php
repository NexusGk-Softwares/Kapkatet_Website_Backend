<?php
namespace Models;

use PDO;
use PDOException;

class User {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Register a new user.
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function register($name, $email, $password) {
        try {
            $stmt = $this->pdo->prepare('INSERT INTO users (name, email, password, created_at) VALUES (:name, :email, :password, NOW())');
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            return $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword
            ]);
        } catch (PDOException $e) {
            // Handle exception or log error
            return false;
        }
    }

    /**
     * Authenticate a user.
     *
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function authenticate($email, $password) {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Remove password from user data before returning
                unset($user['password']);
                return $user;
            }
            return false;
        } catch (PDOException $e) {
            // Handle exception or log error
            return false;
        }
    }

    /**
     * Get user by ID.
     *
     * @param int $id
     * @return array|false
     */
    public function getUserById($id) {
        try {
            $stmt = $this->pdo->prepare('SELECT id, name, email, created_at FROM users WHERE id = :id');
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle exception or log error
            return false;
        }
    }
}
?>
