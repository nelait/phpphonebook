<?php $title = 'Appointments — PhoneBook'; ?>
<?php ob_start(); ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success">
        <span class="alert-icon">✅</span>
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1>Appointments</h1>
        <p class="subtitle">
            <?php 
            $upcomingCount = count(array_filter($appointments, fn($a) => $a->isUpcoming()));
            $pastCount = count(array_filter($appointments, fn($a) => $a->isPast()));
            ?>
            <?= $upcomingCount ?> upcoming, <?= $pastCount ?> past
        </p>
    </div>
    <div class="header-actions">
        <a href="/appointments/reminders" class="btn btn-secondary">🔔 Reminders</a>
        <a href="/appointments/add" class="btn btn-primary">
            <span>＋</span> Add Appointment
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="filter-bar">
    <form method="GET" action="/appointments" class="search-bar">
        <input type="text" name="search" placeholder="Search appointments..."
            value="<?= htmlspecialchars($search ?? '') ?>" class="search-input">
        <button type="submit" class="btn btn-secondary">Search</button>
        <?php if (!empty($search)): ?>
            <a href="/appointments" class="btn btn-ghost">Clear</a>
        <?php endif; ?>
    </form>
    
    <div class="appointment-filters">
        <a href="/appointments" class="filter-btn <?= empty($filter) && empty($category) ? 'active' : '' ?>">All</a>
        <a href="/appointments?filter=upcoming" class="filter-btn <?= ($filter ?? '') === 'upcoming' ? 'active' : '' ?>">Upcoming</a>
        <a href="/appointments?filter=past" class="filter-btn <?= ($filter ?? '') === 'past' ? 'active' : '' ?>">Past</a>
    </div>

    <?php if (!empty($categories)): ?>
        <div class="category-filter">
            <label for="category-select">Category:</label>
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

<!-- Appointments List -->
<?php if (empty($appointments)): ?>
    <div class="empty-state">
        <span class="empty-icon">📅</span>
        <h2>No appointments found</h2>
        <p>
            <?php if (!empty($search)): ?>
                Try a different search term.
            <?php elseif (!empty($filter)): ?>
                No <?= htmlspecialchars($filter) ?> appointments found.
            <?php elseif (!empty($category)): ?>
                No appointments found in this category.
            <?php else: ?>
                Add your first appointment to get started!
            <?php endif; ?>
        </p>
        <?php if (empty($search) && empty($filter) && empty($category)): ?>
            <a href="/appointments/add" class="btn btn-primary">＋ Add Appointment</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="appointments-list">
        <?php foreach ($appointments as $appointment): ?>
            <div class="appointment-card <?= $appointment->isUpcoming() ? 'upcoming' : 'past' ?> <?= $appointment->isDueSoon() ? 'due-soon' : '' ?>">
                <div class="appointment-header">
                    <div class="appointment-datetime">
                        <div class="appointment-date">
                            <?= date('M j, Y', strtotime($appointment->date)) ?>
                            <?php if ($appointment->isToday()): ?>
                                <span class="today-badge">Today</span>
                            <?php endif; ?>
                        </div>
                        <div class="appointment-time">
                            <?= date('g:i A', strtotime($appointment->time)) ?>
                        </div>
                    </div>
                    
                    <div class="appointment-content">
                        <h3 class="appointment-title"><?= htmlspecialchars($appointment->title) ?></h3>
                        <?php if (!empty($appointment->description)): ?>
                            <p class="appointment-description"><?= htmlspecialchars($appointment->description) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($appointment->location)): ?>
                            <div class="appointment-location">
                                📍 <?= htmlspecialchars($appointment->location) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="appointment-meta">
                        <span class="category-badge category-<?= strtolower(str_replace(' ', '-', $appointment->category)) ?>">
                            <?= htmlspecialchars($appointment->category) ?>
                        </span>
                        <?php if ($appointment->isDueSoon()): ?>
                            <span class="due-soon-badge">Due Soon!</span>
                        <?php endif; ?>
                        <?php if ($appointment->reminderSent): ?>
                            <span class="reminder-badge">🔔 Reminded</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="appointment-actions">
                    <a href="/appointments/edit?id=<?= urlencode($appointment->id) ?>" class="btn btn-sm btn-secondary">Edit</a>
                    <a href="/appointments/delete?id=<?= urlencode($appointment->id) ?>" class="btn btn-sm btn-danger"
                        onclick="return confirm('Delete this appointment?')">Delete</a>
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
    url.searchParams.delete('filter'); // Clear other filters
    window.location = url;
}
</script>

<style>
.header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.filter-bar {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    align-items: end;
}

.appointment-filters {
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

.appointments-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.appointment-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.75rem;
    padding: 1.5rem;
    transition: all 0.2s ease;
    position: relative;
}

.appointment-card.upcoming {
    border-left: 4px solid var(--primary-color);
}

.appointment-card.past {
    opacity: 0.7;
    border-left: 4px solid var(--text-secondary);
}

.appointment-card.due-soon {
    border-left-color: #f59e0b;
    background: linear-gradient(90deg, #fef3c7 0%, var(--card-bg) 10%);
}

.appointment-card:hover {
    border-color: var(--primary-color);
    box-shadow: 0 2px 8px var(--shadow-color);
}

.appointment-header {
    display: flex;
    gap: 1.5rem;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.appointment-datetime {
    flex-shrink: 0;
    text-align: center;
    min-width: 100px;
}

.appointment-date {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.appointment-time {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.today-badge {
    background: #10b981;
    color: white;
    padding: 0.125rem 0.375rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.appointment-content {
    flex: 1;
    min-width: 0;
}

.appointment-title {
    margin: 0 0 0.5rem 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
}

.appointment-description {
    margin: 0 0 0.75rem 0;
    color: var(--text-secondary);
    line-height: 1.5;
}

.appointment-location {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.appointment-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-end;
    flex-shrink: 0;
}

.category-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.category-general { background: #e3f2fd; color: #1565c0; }
.category-work { background: #f3e5f5; color: #7b1fa2; }
.category-personal { background: #e8f5e8; color: #2e7d32; }
.category-medical { background: #ffebee; color: #c62828; }
.category-business { background: #fff3e0; color: #ef6c00; }
.category-social { background: #fce4ec; color: #c2185b; }

.due-soon-badge {
    background: #f59e0b;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.reminder-badge {
    background: #10b981;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
}

.appointment-actions {
    display: flex;
    gap: 0.5rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-light);
}

@media (max-width: 768px) {
    .filter-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .appointment-filters {
        order: -1;
    }
    
    .appointment-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .appointment-datetime {
        text-align: left;
    }
    
    .appointment-meta {
        align-items: flex-start;
    }
    
    .appointment-actions {
        flex-direction: column;
    }
}
</style>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>