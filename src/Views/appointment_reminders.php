<?php $title = 'Appointment Reminders — PhoneBook'; ?>
<?php ob_start(); ?>

<div class="page-header">
    <div>
        <h1>Appointment Reminders</h1>
        <p class="subtitle">Upcoming appointments and reminders</p>
    </div>
    <a href="/appointments" class="btn btn-ghost">← Back to Appointments</a>
</div>

<!-- Due Soon Appointments -->
<?php if (!empty($dueSoonAppointments)): ?>
    <div class="reminder-section">
        <h2 class="section-title">Due Within 24 Hours</h2>
        <div class="reminder-list">
            <?php foreach ($dueSoonAppointments as $appointment): ?>
                <div class="reminder-card urgent">
                    <div class="reminder-header">
                        <div class="reminder-time">
                            <div class="reminder-date"><?= date('M j, Y', strtotime($appointment->date)) ?></div>
                            <div class="reminder-clock"><?= date('g:i A', strtotime($appointment->time)) ?></div>
                        </div>
                        <div class="reminder-content">
                            <h3><?= htmlspecialchars($appointment->title) ?></h3>
                            <?php if (!empty($appointment->description)): ?>
                                <p><?= htmlspecialchars($appointment->description) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($appointment->location)): ?>
                                <div class="location">📍 <?= htmlspecialchars($appointment->location) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="reminder-actions">
                            <?php if (!$appointment->reminderSent): ?>
                                <form method="POST" action="/appointments/mark-reminder-sent" class="reminder-form">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($appointment->id) ?>">
                                    <button type="submit" class="btn btn-sm btn-primary">Mark as Reminded</button>
                                </form>
                            <?php else: ?>
                                <span class="reminded-badge">✅ Reminded</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Today's Appointments -->
<?php if (!empty($todayAppointments)): ?>
    <div class="reminder-section">
        <h2 class="section-title">Today's Appointments</h2>
        <div class="reminder-list">
            <?php foreach ($todayAppointments as $appointment): ?>
                <div class="reminder-card today">
                    <div class="reminder-header">
                        <div class="reminder-time">
                            <div class="reminder-date">Today</div>
                            <div class="reminder-clock"><?= date('g:i A', strtotime($appointment->time)) ?></div>
                        </div>
                        <div class="reminder-content">
                            <h3><?= htmlspecialchars($appointment->title) ?></h3>
                            <?php if (!empty($appointment->description)): ?>
                                <p><?= htmlspecialchars($appointment->description) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($appointment->location)): ?>
                                <div class="location">📍 <?= htmlspecialchars($appointment->location) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="reminder-actions">
                            <?php if (!$appointment->reminderSent): ?>
                                <form method="POST" action="/appointments/mark-reminder-sent" class="reminder-form">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($appointment->id) ?>">
                                    <button type="submit" class="btn btn-sm btn-primary">Mark as Reminded</button>
                                </form>
                            <?php else: ?>
                                <span class="reminded-badge">✅ Reminded</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Empty State -->
<?php if (empty($dueSoonAppointments) && empty($todayAppointments)): ?>
    <div class="empty-state">
        <span class="empty-icon">🔔</span>
        <h2>No upcoming reminders</h2>
        <p>You don't have any appointments due soon or today.</p>
        <a href="/appointments/add" class="btn btn-primary">Schedule New Appointment</a>
    </div>
<?php endif; ?>

<style>
.reminder-section {
    margin-bottom: 2rem;
}

.section-title {
    color: var(--text-primary);
    margin-bottom: 1rem;
    font-size: 1.25rem;
    font-weight: 600;
}

.reminder-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.reminder-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.75rem;
    padding: 1.5rem;
    transition: all 0.2s ease;
}

.reminder-card.urgent {
    border-left: 4px solid #f59e0b;
    background: linear-gradient(90deg, #fef3c7 0%, var(--card-bg) 10%);
}

.reminder-card.today {
    border-left: 4px solid #10b981;
    background: linear-gradient(90deg, #dcfce7 0%, var(--card-bg) 10%);
}

.reminder-card:hover {
    border-color: var(--primary-color);
    box-shadow: 0 2px 8px var(--shadow-color);
}

.reminder-header {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
}

.reminder-time {
    flex-shrink: 0;
    text-align: center;
    min-width: 100px;
}

.reminder-date {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.875rem;
}

.reminder-clock {
    color: var(--text-secondary);
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.reminder-content {
    flex: 1;
    min-width: 0;
}

.reminder-content h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
}

.reminder-content p {
    margin: 0 0 0.5rem 0;
    color: var(--text-secondary);
    line-height: 1.5;
}

.location {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.reminder-actions {
    flex-shrink: 0;
}

.reminder-form {
    margin: 0;
}

.reminded-badge {
    color: #10b981;
    font-size: 0.875rem;
    font-weight: 500;
}

@media (max-width: 768px) {
    .reminder-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .reminder-time {
        text-align: left;
    }
}
</style>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>