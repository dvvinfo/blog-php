<?php

require_once __DIR__ . '/../Database.php';

/**
 * User Model
 * 
 * Handles user data and authentication operations
 */
class User
{
    public int $id;
    public string $login;
    private string $password_hash;
    public string $created_at;

    /**
     * Constructor
     * 
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = (int)$data['id'];
        $this->login = $data['login'];
        $this->password_hash = $data['password_hash'];
        $this->created_at = $data['created_at'];
    }

    /**
     * Create new user
     * 
     * @param string $login
     * @param string $password
     * @return User|false
     */
    public static function create(string $login, string $password): User|false
    {
        try {
            $db = Database::getConnection();
            $passwordHash = self::hashPassword($password);

            $stmt = $db->prepare(
                'INSERT INTO users (login, password_hash) VALUES (:login, :password_hash)'
            );

            $stmt->execute([
                'login' => $login,
                'password_hash' => $passwordHash
            ]);

            $userId = $db->lastInsertId();
            return self::findById((int)$userId);
        } catch (PDOException $e) {
            error_log('User creation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find user by login
     * 
     * @param string $login
     * @return User|false
     */
    public static function findByLogin(string $login): User|false
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare('SELECT * FROM users WHERE login = :login');
            $stmt->execute(['login' => $login]);

            $data = $stmt->fetch();
            return $data ? new self($data) : false;
        } catch (PDOException $e) {
            error_log('User find by login failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find user by ID
     * 
     * @param int $id
     * @return User|false
     */
    public static function findById(int $id): User|false
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
            $stmt->execute(['id' => $id]);

            $data = $stmt->fetch();
            return $data ? new self($data) : false;
        } catch (PDOException $e) {
            error_log('User find by ID failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify password
     * 
     * @param string $password
     * @return bool
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    /**
     * Hash password
     * 
     * @param string $password
     * @return string
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
