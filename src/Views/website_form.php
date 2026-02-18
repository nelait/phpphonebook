<?php $title = ($isEdit ? 'Edit' : 'Add') . ' Website — PhoneBook'; ?>
<?php ob_start(); ?>

<div class="page-header">
    <div>
        <h1>
            <?= $isEdit ? 'Edit Website' : 'New Website' ?>
        </h1>
        <p class="subtitle">
            <?= $isEdit ? 'Update website details' : 'Add a website to your collection' ?>
        </p>
    </div>
    <a href="/websites" class="btn btn-ghost">← Back</a>
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
    <form method="POST" action="<?= $isEdit ? '/websites/edit' : '/websites/add' ?>" class="website-form">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($website->id) ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="name">Website Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($website->name) ?>"
                    placeholder="Google" required>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category">
                    <?php
                    $categories = ['General', 'Work', 'Social', 'News', 'Tools', 'Entertainment', 'Education', 'Shopping', 'Reference'];
                    foreach ($categories as $cat):
                        ?>
                        <option value="<?= $cat ?>" <?= ($website->category === $cat) ? 'selected' : '' ?>>
                            <?= $cat ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="url">Website URL <span class="required">*</span></label>
            <input type="url" id="url" name="url" value="<?= htmlspecialchars($website->url) ?>"
                placeholder="https://www.google.com" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"
                placeholder="Brief description of the website..."><?= htmlspecialchars($website->description) ?></textarea>
        </div>

        <div class="form-actions">
            <a href="/websites" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <?= $isEdit ? 'Save Changes' : 'Add Website' ?>
            </button>
        </div>
    </form>
</div>

<style>
.website-form textarea {
    resize: vertical;
    min-height: 80px;
}

.form-group textarea {
    font-family: inherit;
}
</style>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>