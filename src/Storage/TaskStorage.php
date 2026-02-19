<?php
namespace PhpGuru\Storage;

use PhpGuru\Models\Task;

class TaskStorage
{
    private string $filePath;

    public function __construct()
    {
        $this->filePath = __DIR__ . '/../../data/tasks.json';
        if (!file_exists($this->filePath)) {
            if (!is_dir(dirname($this->filePath))) {
                mkdir(dirname($this->filePath), 0755, true);
            }
            file_put_contents($this->filePath, '[]');
        }
    }

    /** @return Task[] */
    public function getAll(): array
    {
        $data = json_decode(file_get_contents($this->filePath), true) ?: [];
        return array_map(fn($item) => Task::fromArray($item), $data);
    }

    public function getById(string $id): ?Task
    {
        foreach ($this->getAll() as $task) {
            if ($task->id === $id) {
                return $task;
            }
        }
        return null;
    }

    public function save(Task $task): void
    {
        $tasks = $this->getAll();
        $found = false;
        foreach ($tasks as $key => $existing) {
            if ($existing->id === $task->id) {
                $tasks[$key] = $task;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $tasks[] = $task;
        }
        $this->writeAll($tasks);
    }

    public function delete(string $id): void
    {
        $tasks = $this->getAll();
        $tasks = array_values(array_filter($tasks, fn($t) => $t->id !== $id));
        $this->writeAll($tasks);
    }

    /** @return Task[] */
    public function search(string $query): array
    {
        $query = strtolower($query);
        return array_values(array_filter($this->getAll(), function ($t) use ($query) {
            return str_contains(strtolower($t->title), $query)
                || str_contains(strtolower($t->description), $query);
        }));
    }

    /** @return Task[] */
    public function getByStatus(bool $completed): array
    {
        return array_values(array_filter($this->getAll(), fn($t) => $t->completed === $completed));
    }

    /** @return Task[] */
    public function getByPriority(string $priority): array
    {
        return array_values(array_filter($this->getAll(), fn($t) => $t->priority === $priority));
    }

    private function writeAll(array $tasks): void
    {
        $data = array_map(fn($t) => $t->toArray(), $tasks);
        file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
}