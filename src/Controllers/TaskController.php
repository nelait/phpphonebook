<?php
namespace PhpGuru\Controllers;

use PhpGuru\Models\Task;
use PhpGuru\Storage\TaskStorage;

class TaskController
{
    private TaskStorage $storage;

    public function __construct()
    {
        $this->storage = new TaskStorage();
    }

    public function index(): void
    {
        $search = $_GET['search'] ?? '';
        $filter = $_GET['filter'] ?? '';
        
        if ($search) {
            $tasks = $this->storage->search($search);
        } elseif ($filter === 'completed') {
            $tasks = $this->storage->getByStatus(true);
        } elseif ($filter === 'pending') {
            $tasks = $this->storage->getByStatus(false);
        } else {
            $tasks = $this->storage->getAll();
        }
        
        // Sort tasks: pending first, then by priority (high > medium > low), then by due date
        usort($tasks, function($a, $b) {
            // Completed tasks go to the bottom
            if ($a->completed !== $b->completed) {
                return $a->completed ? 1 : -1;
            }
            
            // Sort by priority
            $priorityOrder = ['high' => 3, 'medium' => 2, 'low' => 1];
            $aPriority = $priorityOrder[$a->priority] ?? 2;
            $bPriority = $priorityOrder[$b->priority] ?? 2;
            
            if ($aPriority !== $bPriority) {
                return $bPriority - $aPriority; // Higher priority first
            }
            
            // Sort by due date (earliest first)
            if ($a->dueDate && $b->dueDate) {
                return strtotime($a->dueDate) - strtotime($b->dueDate);
            } elseif ($a->dueDate) {
                return -1;
            } elseif ($b->dueDate) {
                return 1;
            }
            
            // Finally sort by creation date (newest first)
            return strtotime($b->createdAt) - strtotime($a->createdAt);
        });
        
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);
        require __DIR__ . '/../Views/tasks.php';
    }

    public function add(): void
    {
        $task = new Task();
        $errors = [];
        $isEdit = false;
        require __DIR__ . '/../Views/task_form.php';
    }

    public function store(): void
    {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $priority = $_POST['priority'] ?? 'medium';
        $dueDate = $_POST['due_date'] ?? '';

        $errors = $this->validate($title);
        if (!empty($errors)) {
            $task = new Task($title, $description, false, $priority, $dueDate);
            $isEdit = false;
            require __DIR__ . '/../Views/task_form.php';
            return;
        }

        $task = new Task($title, $description, false, $priority, $dueDate);
        $this->storage->save($task);
        $_SESSION['success'] = 'Task added successfully!';
        header('Location: /tasks');
        exit;
    }

    public function edit(): void
    {
        $id = $_GET['id'] ?? '';
        $task = $this->storage->getById($id);
        if (!$task) {
            header('Location: /tasks');
            exit;
        }
        $errors = [];
        $isEdit = true;
        require __DIR__ . '/../Views/task_form.php';
    }

    public function update(): void
    {
        $id = $_POST['id'] ?? '';
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $priority = $_POST['priority'] ?? 'medium';
        $dueDate = $_POST['due_date'] ?? '';
        $completed = isset($_POST['completed']) ? (bool)$_POST['completed'] : false;

        $errors = $this->validate($title);
        if (!empty($errors)) {
            $task = new Task($title, $description, $completed, $priority, $dueDate, $id);
            $isEdit = true;
            require __DIR__ . '/../Views/task_form.php';
            return;
        }

        // Get original task to preserve creation date
        $originalTask = $this->storage->getById($id);
        $createdAt = $originalTask ? $originalTask->createdAt : date('Y-m-d H:i:s');
        
        $task = new Task($title, $description, $completed, $priority, $dueDate, $id, $createdAt);
        $this->storage->save($task);
        $_SESSION['success'] = 'Task updated successfully!';
        header('Location: /tasks');
        exit;
    }

    public function delete(): void
    {
        $id = $_GET['id'] ?? '';
        $this->storage->delete($id);
        $_SESSION['success'] = 'Task deleted successfully!';
        header('Location: /tasks');
        exit;
    }

    public function toggleComplete(): void
    {
        $id = $_POST['id'] ?? '';
        $task = $this->storage->getById($id);
        
        if ($task) {
            $task->completed = !$task->completed;
            $this->storage->save($task);
            
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                     strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'completed' => $task->completed,
                    'message' => $task->completed ? 'Task marked as completed!' : 'Task marked as pending!'
                ]);
                exit;
            }
        }
        
        header('Location: /tasks');
        exit;
    }

    private function validate(string $title): array
    {
        $errors = [];
        if (empty($title)) {
            $errors[] = 'Task title is required.';
        }
        return $errors;
    }
}