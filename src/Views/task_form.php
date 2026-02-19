<?php $title = ($isEdit ? 'Edit' : 'Add') . ' Task — PhoneBook'; ?>
<?php ob_start(); ?>

<div class="page-header">
    <div>
        <h1>
            <?= $isEdit ? 'Edit Task' : 'New Task' ?>
        </h1>
        <p class="subtitle">
            <?= $isEdit ? 'Update task details' : 'Add a new task to your list' ?>
        </p>
    </div>
    <a href="/tasks" class="btn btn-ghost">← Back</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <span class="alert-icon">⚠️</span>
        <ul class="error-list">
            <?php foreach ($errors as $err): ?>
                <li>
                    <?= htmlspecialchars($err) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="form-card">
    <form method="POST" action="<?= $isEdit ? '/tasks/edit' : '/tasks/add' ?>" class="task-form">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($task->id) ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="title">Task Title <span class="required">*</span></label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($task->title) ?>"
                placeholder="What do you need to do?" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"
                placeholder="Add more details about this task..."><?= htmlspecialchars($task->description) ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="priority">Priority</label>
                <select id="priority" name="priority">
                    <option value="low" <?= $task->priority === 'low' ? 'selected' : '' ?>>Low</option>
                    <option value="medium" <?= $task->priority === 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="high" <?= $task->priority === 'high' ? 'selected' : '' ?>>High</option>
                </select>
            </div>
            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="date" id="due_date" name="due_date" 
                       value="<?= $task->dueDate ? date('Y-m-d', strtotime($task->dueDate)) : '' ?>">
            </div>
        </div>

        <?php if ($isEdit): ?>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="completed" value="1" <?= $task->completed ? 'checked' : '' ?>>
                    <span class="checkbox-mark"></span>
                    Mark as completed
                </label>
            </div>
        <?php endif; ?>

        <div class="form-actions">
            <a href="/tasks" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <?= $isEdit ? 'Save Changes' : 'Add Task' ?>
            </button>
        </div>
    </form>
</div>

<style>
.task-form textarea {
    resize: vertical;
    min-height: 80px;
    font-family: inherit;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    padding: 0.75rem;
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    transition: all 0.2s ease;
}

.checkbox-label:hover {
    border-color: var(--primary-color);
    background: var(--bg-secondary);
}

.checkbox-label input[type="checkbox"] {
    display: none;
}

.checkbox-label .checkbox-mark {
    width: 1.25rem;
    height: 1.25rem;
    border: 2px solid var(--border-color);
    border-radius: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.checkbox-label input[type="checkbox"]:checked + .checkbox-mark {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.checkbox-label input[type="checkbox"]:checked + .checkbox-mark::after {
    content: '✓';
    color: white;
    font-size: 0.875rem;
    font-weight: bold;
}
</style>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>