<?php $title = 'My Contacts — PhoneBook'; ?>
<?php ob_start(); ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success">
        <span class="alert-icon">✅</span>
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1>My Contacts</h1>
        <p class="subtitle">
            <?= count($contacts) ?> contact
            <?= count($contacts) !== 1 ? 's' : '' ?> total
        </p>
    </div>
    <a href="/add" class="btn btn-primary">
        <span>＋</span> Add Contact
    </a>
</div>

<!-- Search -->
<form method="GET" action="/" class="search-bar">
    <input type="text" name="search" placeholder="Search by name, phone, or email…"
        value="<?= htmlspecialchars($search ?? '') ?>" class="search-input">
    <button type="submit" class="btn btn-secondary">Search</button>
    <?php if (!empty($search)): ?>
        <a href="/" class="btn btn-ghost">Clear</a>
    <?php endif; ?>
</form>

<!-- Contact Table -->
<?php if (empty($contacts)): ?>
    <div class="empty-state">
        <span class="empty-icon">📭</span>
        <h2>No contacts found</h2>
        <p>
            <?= !empty($search) ? 'Try a different search term.' : 'Add your first contact to get started!' ?>
        </p>
        <?php if (empty($search)): ?>
            <a href="/add" class="btn btn-primary">＋ Add Contact</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="table-wrapper">
        <table class="contacts-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Category</th>
                    <th class="th-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contacts as $c): ?>
                    <tr>
                        <td class="td-name">
                            <span class="avatar">
                                <?= strtoupper(mb_substr($c->name, 0, 1)) ?>
                            </span>
                            <?= htmlspecialchars($c->name) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($c->phone) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($c->email ?: '—') ?>
                        </td>
                        <td><span class="badge">
                                <?= htmlspecialchars($c->category) ?>
                            </span></td>
                        <td class="td-actions">
                            <a href="/edit?id=<?= urlencode($c->id) ?>" class="btn btn-sm btn-secondary">Edit</a>
                            <a href="/delete?id=<?= urlencode($c->id) ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete <?= htmlspecialchars(addslashes($c->name)) ?>?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>