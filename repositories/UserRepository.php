<?php

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createOrUpdate(
        int $telegramId, string $firstName, ?string $username): void
    {
        $sql = "INSERT INTO users (telegram_id, first_name, username) VALUES (?,?,?)
        ON DUPLICATE KEY UPDATE first_name = VALUES(first_name), username = VALUES(username)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$telegramId, $firstName, $username]);
    }

    public function getStep(int $telegramId): ?string
    {
        $sql = "SELECT step FROM users WHERE telegram_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$telegramId]);
        $step = $stmt->fetchColumn();
        return $step !== false ? $step : null;
    }

    public function setStep(int $telegramId, ?string $step): void
    {
        $sql = "UPDATE users SET step = ? WHERE telegram_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$step, $telegramId]);
    }
}