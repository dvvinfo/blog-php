<?php

/**
 * Database Connection Manager
 * 
 * Manages PDO connection with prepared statements for SQL injection prevention
 */
class Database
{
    private static ?PDO $connection = null;

    /**
     * Get database connection instance
     * 
     * @return PDO
     * @throws PDOException
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $config = require __DIR__ . '/../config/database.php';
            
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );

            try {
                self::$connection = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
            } catch (PDOException $e) {
                error_log('Database connection failed: ' . $e->getMessage());
                throw new PDOException('Database connection failed');
            }
        }

        return self::$connection;
    }

    /**
     * Close database connection
     */
    public static function closeConnection(): void
    {
        self::$connection = null;
    }
}
