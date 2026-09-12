<?php

class QuoteRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(int $userId, string $text): void
    {
        $sql = "INSERT INTO quotes (text, user_id) VALUES (?,?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$text, $userId]);
    }

    public function countByUserId(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM quotes WHERE user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);

        return (int)$stmt->fetchColumn();
    }

    public function getRandomByUserId(int $userId): ?string
    {   
        $sql = "SELECT text FROM quotes WHERE user_id = ? ORDER BY RAND() LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        $quote = $stmt->fetchColumn();
        return $quote !== false ? $quote: null;
    }
}