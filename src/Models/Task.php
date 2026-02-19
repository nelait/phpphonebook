<?php
namespace PhpGuru\Models;

class Task
{
    public string $id;
    public string $title;
    public string $description;
    public bool $completed;
    public string $priority;
    public string $dueDate;
    public string $createdAt;

    public function __construct(
        string $title = '',
        string $description = '',
        bool $completed = false,
        string $priority = 'medium',
        string $dueDate = '',
        string $id = '',
        string $createdAt = ''
    ) {
        $this->id = $id ?: uniqid('t_', true);
        $this->title = $title;
        $this->description = $description;
        $this->completed = $completed;
        $this->priority = $priority;
        $this->dueDate = $dueDate;
        $this->createdAt = $createdAt ?: date('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'completed' => $this->completed,
            'priority' => $this->priority,
            'dueDate' => $this->dueDate,
            'createdAt' => $this->createdAt,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['completed'] ?? false,
            $data['priority'] ?? 'medium',
            $data['dueDate'] ?? '',
            $data['id'] ?? '',
            $data['createdAt'] ?? ''
        );
    }
}