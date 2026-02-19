<?php $title = 'To Do List — PhoneBook'; ?>
<?php ob_start(); ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success">
        <span class="alert-icon">✅</span>
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1>To Do List</h1>
        <p class="subtitle">
            <?php 
            $pendingCount = count(array_filter($tasks, fn($t) => !$t->completed));
            $completedCount = count(array_filter($tasks, fn($t) => $t->completed));
            ?>
            <?= $pendingCount ?> pending, <?= $completedCount ?> completed
        </p>
    </div>
    <a href="/tasks/add" class="btn btn-primary">
        <span>＋</span> Add Task
    </a>
</div>

<!-- Search and Filter -->
<div class="filter-bar">
    <form method="GET" action="/tasks" class="search-bar">
        <input type="text" name="search" placeholder="Search tasks..."
            value="<?= htmlspecialchars($search ?? '') ?>" class="search-input">
        <button type="submit" class="btn btn-secondary">Search</button>
        <?php if (!empty($search)): ?>
            <a href="/tasks" class="btn btn-ghost">Clear</a>
        <?php endif; ?>
    </form>
    
    <div class="task-filters">
        <a href="/tasks" class="filter-btn <?= empty($filter) ? 'active' : '' ?>">All</a>
        <a href="/tasks?filter=pending" class="filter-btn <?= ($filter ?? '') === 'pending' ? 'active' : '' ?>">Pending</a>
        <a href="/tasks?filter=completed" class="filter-btn <?= ($filter ?? '') === 'completed' ? 'active' : '' ?>">Completed</a>
    </div>
</div>

<!-- Tasks List -->
<?php if (empty($tasks)): ?>
    <div class="empty-state">
        <span class="empty-icon">📝</span>
        <h2>No tasks found</h2>
        <p>
            <?php if (!empty($search)): ?>
                Try a different search term.
            <?php elseif (!empty($filter)): ?>
                No <?= htmlspecialchars($filter) ?> tasks found.
            <?php else: ?>
                Add your first task to get started!
            <?php endif; ?>
        </p>
        <?php if (empty($search) && empty($filter)): ?>
            <a href="/tasks/add" class="btn btn-primary">＋ Add Task</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="tasks-list">
        <?php foreach ($tasks as $task): ?>
            <div class="task-card <?= $task->completed ? 'completed' : '' ?> priority-<?= $task->priority ?>">
                <div class="task-header">
                    <form method="POST" action="/tasks/toggle" class="task-toggle-form">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($task->id) ?>">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" <?= $task->completed ? 'checked' : '' ?> 
                                   onchange="toggleTask('<?= htmlspecialchars($task->id) ?>', this)">
                            <span class="checkbox-mark"></span>
                        </label>
                    </form>
                    
                    <div class="task-content">
                        <h3 class="task-title <?= $task->completed ? 'strikethrough' : '' ?>">
                            <?= htmlspecialchars($task->title) ?>
                        </h3>
                        <?php if (!empty($task->description)): ?>
                            <p class="task-description"><?= htmlspecialchars($task->description) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="task-meta">
                        <span class="priority-badge priority-<?= $task->priority ?>">
                            <?= ucfirst($task->priority) ?>
                        </span>
                        <?php if (!empty($task->dueDate)): ?>
                            <?php 
                            $dueTimestamp = strtotime($task->dueDate);
                            $now = time();
                            $isOverdue = !$task->completed && $dueTimestamp < $now;
                            $isDueSoon = !$task->completed && $dueTimestamp < ($now + 86400); // Due within 24 hours
                            ?>
                            <span class="due-date <?= $isOverdue ? 'overdue' : ($isDueSoon ? 'due-soon' : '') ?>">
                                Due: <?= date('M j, Y', $dueTimestamp) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="task-actions">
                    <a href="/tasks/edit?id=<?= urlencode($task->id) ?>" class="btn btn-sm btn-secondary">Edit</a>
                    <a href="/tasks/delete?id=<?= urlencode($task->id) ?>" class="btn btn-sm btn-danger"
                        onclick="return confirm('Delete this task?')">Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
function toggleTask(taskId, checkbox) {
    const formData = new FormData();
    formData.append('id', taskId);
    
    fetch('/tasks/toggle', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const taskCard = checkbox.closest('.task-card');
            const taskTitle = taskCard.querySelector('.task-title');
            
            if (data.completed) {
                taskCard.classList.add('completed');
                taskTitle.classList.add('strikethrough');
            } else {
                taskCard.classList.remove('completed');
                taskTitle.classList.remove('strikethrough');
            }
            
            // Show temporary success message
            showToast(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert checkbox on error
        checkbox.checked = !checkbox.checked;
        showToast('Failed to update task status', 'error');
    });
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => document.body.removeChild(toast), 300);
    }, 3000);
}
</script>

<style>
.filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: end;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.task-filters {
    display: flex;
    gap: 0.5rem;
}

.filter-btn {
    padding: 0.5rem 1rem;
    text-decoration: none;
    color: var(--text-secondary);
    border: 1px solid var(--border-color);
    border-radius: 0.375rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.filter-btn:hover {
    color: var(--text-primary);
    border-color: var(--primary-color);
}

.filter-btn.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.tasks-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.task-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.75rem;
    padding: 1.5rem;
    transition: all 0.2s ease;
    position: relative;
}

.task-card.priority-high {
    border-left: 4px solid #ef4444;
}

.task-card.priority-medium {
    border-left: 4px solid #f59e0b;
}

.task-card.priority-low {
    border-left: 4px solid #10b981;
}

.task-card.completed {
    opacity: 0.6;
}

.task-card:hover {
    border-color: var(--primary-color);
    box-shadow: 0 2px 8px var(--shadow-color);
}

.task-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.task-toggle-form {
    flex-shrink: 0;
}

.checkbox-wrapper {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.checkbox-wrapper input[type="checkbox"] {
    display: none;
}

.checkbox-mark {
    width: 1.25rem;
    height: 1.25rem;
    border: 2px solid var(--border-color);
    border-radius: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.checkbox-wrapper input[type="checkbox"]:checked + .checkbox-mark {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.checkbox-wrapper input[type="checkbox"]:checked + .checkbox-mark::after {
    content: '✓';
    color: white;
    font-size: 0.875rem;
    font-weight: bold;
}

.task-content {
    flex: 1;
    min-width: 0;
}

.task-title {
    margin: 0 0 0.5rem 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    transition: all 0.2s ease;
}

.task-title.strikethrough {
    text-decoration: line-through;
    color: var(--text-secondary);
}

.task-description {
    margin: 0 0 1rem 0;
    color: var(--text-secondary);
    line-height: 1.5;
}

.task-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.5rem;
    flex-shrink: 0;
}

.priority-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.priority-badge.priority-high {
    background: #fee2e2;
    color: #dc2626;
}

.priority-badge.priority-medium {
    background: #fef3c7;
    color: #d97706;
}

.priority-badge.priority-low {
    background: #dcfce7;
    color: #16a34a;
}

.due-date {
    font-size: 0.75rem;
    color: var(--text-secondary);
}

.due-date.due-soon {
    color: #d97706;
    font-weight: 500;
}

.due-date.overdue {
    color: #dc2626;
    font-weight: 600;
}

.task-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-light);
}

.toast {
    position: fixed;
    top: 1rem;
    right: 1rem;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    color: white;
    font-size: 0.875rem;
    z-index: 1000;
    transform: translateX(100%);
    opacity: 0;
    transition: all 0.3s ease;
}

.toast.toast-success {
    background: #10b981;
}

.toast.toast-error {
    background: #ef4444;
}

.toast.show {
    transform: translateX(0);
    opacity: 1;
}

@media (max-width: 768px) {
    .filter-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .task-header {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .task-meta {
        align-items: flex-start;
    }
    
    .task-actions {
        flex-direction: column;
    }
}
</style>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>