<?php

namespace App;

class Task
{
    private int $id;
    private string $title;
    private string $status;
    private string $description;

    public function __construct(int $id, string $title, string $description = '', string $status = 'pending')
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setTitle(string $title): void
    {
        if (empty(trim($title))) {
            throw new \InvalidArgumentException('Title cannot be empty.');
        }
        $this->title = $title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function complete(): void
    {
        $this->status = 'completed';
    }

    public function cancel(): void
    {
        $this->status = 'cancelled';
    }

    public function reopen(): void
    {
        if ($this->status !== 'cancelled') {
            throw new \LogicException('Only cancelled tasks can be reopened.');
        }
        $this->status = 'pending';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'status'      => $this->status,
        ];
    }
}
