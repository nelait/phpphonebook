<?php $title = ($isEdit ? 'Edit' : 'Add') . ' Appointment — PhoneBook'; ?>
<?php ob_start(); ?>

<div class="page-header">
    <div>
        <h1>
            <?= $isEdit ? 'Edit Appointment' : 'New Appointment' ?>
        </h1>
        <p class="subtitle">
            <?= $isEdit ? 'Update appointment details' : 'Schedule a new appointment' ?>
        </p>
    </div>
    <a href="/appointments" class="btn btn-ghost">← Back</a>
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
    <form method="POST" action="<?= $isEdit ? '/appointments/edit' : '/appointments/add' ?>" class="appointment-form">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($appointment->id) ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="title">Appointment Title <span class="required">*</span></label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($appointment->title) ?>"
                placeholder="Meeting with client" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"
                placeholder="Additional details about the appointment..."><?= htmlspecialchars($appointment->description) ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="date">Date <span class="required">*</span></label>
                <input type="date" id="date" name="date" 
                       value="<?= $appointment->date ? date('Y-m-d', strtotime($appointment->date)) : '' ?>" required>
            </div>
            <div class="form-group">
                <label for="time">Time <span class="required">*</span></label>
                <input type="time" id="time" name="time" 
                       value="<?= $appointment->time ? date('H:i', strtotime($appointment->time)) : '' ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" value="<?= htmlspecialchars($appointment->location) ?>"
                    placeholder="Office, Home, etc.">
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category">
                    <?php
                    $categories = ['General', 'Work', 'Personal', 'Medical', 'Business', 'Social'];
                    foreach ($categories as $cat):
                        ?>
                        <option value="<?= $cat ?>" <?= ($appointment->category === $cat) ? 'selected' : '' ?>>
                            <?= $cat ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="/appointments" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <?= $isEdit ? 'Save Changes' : 'Add Appointment' ?>
            </button>
        </div>
    </form>
</div>

<style>
.appointment-form textarea {
    resize: vertical;
    min-height: 80px;
    font-family: inherit;
}

.form-row {
    display: flex;
    gap: 1rem;
}

.form-row .form-group {
    flex: 1;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        gap: 0;
    }
}
</style>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>