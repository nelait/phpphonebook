<?php $title = 'Website Manager — PhoneBook'; ?>
<?php ob_start(); ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success">
        <span class="alert-icon">✅</span>
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1>Website Manager</h1>
        <p class="subtitle">
            <?= count($websites) ?> website
            <?= count($websites) !== 1 ? 's' : '' ?> total
        </p>
    </div>
    <a href="/websites/add" class="btn btn-primary">
        <span>＋</span> Add Website
    </a>
</div>

<!-- Search and Filter -->
<div class="filter-bar">
    <form method="GET" action="/websites" class="search-bar">
        <input type="text" name="search" placeholder="Search websites by name, URL, or description…"
            value="<?= htmlspecialchars($search ?? '') ?>" class="search-input">
        <button type="submit" class="btn btn-secondary">Search</button>
        <?php if (!empty($search)): ?>
            <a href="/websites" class="btn btn-ghost">Clear</a>
        <?php endif; ?>
    </form>
    
    <?php if (!empty($categories)): ?>
        <div class="category-filter">
            <label for="category-select">Filter by category:</label>
            <select id="category-select" onchange="filterByCategory(this.value)">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= ($category ?? '') === $cat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endif; ?>
</div>

<!-- Website Grid -->
<?php if (empty($websites)): ?>
    <div class="empty-state">
        <span class="empty-icon">🌐</span>
        <h2>No websites found</h2>
        <p>
            <?php if (!empty($search)): ?>
                Try a different search term.
            <?php elseif (!empty($category)): ?>
                No websites found in this category.
            <?php else: ?>
                Add your first website to get started!
            <?php endif; ?>
        </p>
        <?php if (empty($search) && empty($category)): ?>
            <a href="/websites/add" class="btn btn-primary">＋ Add Website</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="website-grid">
        <?php foreach ($websites as $w): ?>
            <div class="website-card">
                <div class="website-header">
                    <div class="website-favicon">
                        <span class="website-icon">🌐</span>
                    </div>
                    <div class="website-info">
                        <h3 class="website-name"><?= htmlspecialchars($w->name) ?></h3>
                        <a href="<?= htmlspecialchars($w->url) ?>" target="_blank" class="website-url">
                            <?= htmlspecialchars(parse_url($w->url, PHP_URL_HOST) ?: $w->url) ?>
                            <span class="external-link">↗</span>
                        </a>
                    </div>
                    <span class="badge badge-<?= strtolower(str_replace(' ', '-', $w->category)) ?>">
                        <?= htmlspecialchars($w->category) ?>
                    </span>
                </div>
                
                <?php if (!empty($w->description)): ?>
                    <p class="website-description"><?= htmlspecialchars($w->description) ?></p>
                <?php endif; ?>
                
                <div class="website-meta">
                    <span class="website-date">Added <?= date('M j, Y', strtotime($w->createdAt)) ?></span>
                </div>
                
                <div class="website-actions">
                    <a href="<?= htmlspecialchars($w->url) ?>" target="_blank" class="btn btn-sm btn-ghost">Visit</a>
                    <a href="/websites/edit?id=<?= urlencode($w->id) ?>" class="btn btn-sm btn-secondary">Edit</a>
                    <a href="/websites/delete?id=<?= urlencode($w->id) ?>" class="btn btn-sm btn-danger"
                        onclick="return confirm('Delete <?= htmlspecialchars(addslashes($w->name)) ?>?')">Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
function filterByCategory(category) {
    const url = new URL(window.location);
    if (category) {
        url.searchParams.set('category', category);
    } else {
        url.searchParams.delete('category');
    }
    url.searchParams.delete('search'); // Clear search when filtering by category
    window.location = url;
}
</script>

<style>
.filter-bar {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    align-items: end;
}

.category-filter label {
    display: block;
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
}

.category-filter select {
    padding: 0.5rem;
    border: 1px solid var(--border-color);
    border-radius: 0.375rem;
    background: var(--bg-color);
    color: var(--text-primary);
    font-size: 0.875rem;
}

.website-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
}

.website-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.75rem;
    padding: 1.5rem;
    transition: all 0.2s ease;
}

.website-card:hover {
    border-color: var(--primary-color);
    box-shadow: 0 4px 12px var(--shadow-color);
}

.website-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.website-favicon {
    flex-shrink: 0;
}

.website-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    background: var(--primary-light);
    border-radius: 0.5rem;
    font-size: 1.25rem;
}

.website-info {
    flex: 1;
    min-width: 0;
}

.website-name {
    margin: 0 0 0.25rem 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.website-url {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 0.875rem;
    transition: color 0.2s ease;
}

.website-url:hover {
    color: var(--primary-color);
}

.external-link {
    font-size: 0.75rem;
    opacity: 0.7;
}

.website-description {
    color: var(--text-secondary);
    font-size: 0.875rem;
    line-height: 1.5;
    margin: 0 0 1rem 0;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.website-meta {
    margin-bottom: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-light);
}

.website-date {
    color: var(--text-secondary);
    font-size: 0.75rem;
}

.website-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.badge-general { background-color: #e3f2fd; color: #1565c0; }
.badge-work { background-color: #f3e5f5; color: #7b1fa2; }
.badge-social { background-color: #e8f5e8; color: #2e7d32; }
.badge-news { background-color: #fff3e0; color: #ef6c00; }
.badge-tools { background-color: #fce4ec; color: #c2185b; }
.badge-entertainment { background-color: #f1f8e9; color: #558b2f; }
</style>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>