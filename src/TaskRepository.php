<?php

namespace App;

use PDO;
use PDOException;

class TaskRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM tasks ORDER BY id DESC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => $this->hydrate($row), $rows);
    }

    public function findById(int $id): ?Task
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->hydrate($row) : null;
    }

    public function save(Task $task): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO tasks (title, description, status) VALUES (:title, :description, :status)'
        );
        return $stmt->execute([
            ':title'       => $task->getTitle(),
            ':description' => $task->getDescription(),
            ':status'      => $task->getStatus(),
        ]);
    }

    public function update(Task $task): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE tasks SET title = :title, description = :description, status = :status WHERE id = :id'
        );
        return $stmt->execute([
            ':title'       => $task->getTitle(),
            ':description' => $task->getDescription(),
            ':status'      => $task->getStatus(),
            ':id'          => $task->getId(),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function countByStatus(string $status): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM tasks WHERE status = :status');
        $stmt->execute([':status' => $status]);
        return (int) $stmt->fetchColumn();
    }

    private function hydrate(array $row): Task
    {
        return new Task(
            (int) $row['id'],
            $row['title'],
            $row['description'] ?? '',
            $row['status']
        );
    }
}
