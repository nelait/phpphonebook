<?php $title = ($isEdit ? 'Edit' : 'Add') . ' Contact — PhoneBook'; ?>
<?php ob_start(); ?>

<div class="page-header">
    <div>
        <h1>
            <?= $isEdit ? 'Edit Contact' : 'New Contact' ?>
        </h1>
        <p class="subtitle">
            <?= $isEdit ? 'Update contact details' : 'Fill in the details below' ?>
        </p>
    </div>
    <a href="/" class="btn btn-ghost">← Back</a>
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
    <form method="POST" action="<?= $isEdit ? '/edit' : '/add' ?>" class="contact-form">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($contact->id) ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="name">Full Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($contact->name) ?>"
                    placeholder="John Doe" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number <span class="required">*</span></label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($contact->phone) ?>"
                    placeholder="+1 (555) 123-4567" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($contact->email) ?>"
                    placeholder="john@example.com">
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category">
                    <?php
                    $categories = ['General', 'Family', 'Friends', 'Work', 'Business', 'Emergency'];
                    foreach ($categories as $cat):
                        ?>
                        <option value="<?= $cat ?>" <?= ($contact->category === $cat) ? 'selected' : '' ?>>
                            <?= $cat ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="/" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <?= $isEdit ? 'Save Changes' : 'Add Contact' ?>
            </button>
        </div>
    </form>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>