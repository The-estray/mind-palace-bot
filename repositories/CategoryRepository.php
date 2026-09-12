<?php

class CategoryRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(int $userId, string $title): int
    {
        $sql = "INSERT INTO categories (user_id, title) VALUES (?,?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId,$title]);
        return (int)$this->pdo->lastInsertId();
    }

    public function getAllByUserId(int $userId): array
    {
        $sql = "SELECT id,title FROM categories WHERE user_id = ? AND is_deleted = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        $category = $stmt->fetchAll();
        return $category;
    }

    public function countQuotesByCategoryId( int $categoryId, int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM quotes WHERE category_id = ? AND user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$categoryId, $userId]);
        return (int)$stmt->fetchColumn();
    }

    public function delete(int $id, int $userId): void
    {
        $sql = "UPDATE categories SET is_deleted = 1 WHERE id = ? AND user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id, $userId]);
    }
}