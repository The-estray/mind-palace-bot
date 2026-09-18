<?php

class Database
{
    private PDO $pdo;

    public function __construct(string $host, string $dbname, string $user, string $password, string $charset = 'utf8mb4')
    {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $password, $options);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            die('Database error!');
        }
    }

    public function getConn(): PDO
    {
        return $this->pdo;
    }
}

