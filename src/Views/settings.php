<?php $title = 'Settings — PhoneBook'; ?>
<?php ob_start(); ?>

<div class="page-header">
    <div>
        <h1>Settings</h1>
        <p class="subtitle">Customize your PhoneBook experience</p>
    </div>
    <a href="/" class="btn btn-ghost">← Back</a>
</div>

<div class="settings-card">
    <div class="settings-section">
        <h2>Appearance</h2>
        <div class="setting-item">
            <div class="setting-info">
                <h3>Theme</h3>
                <p>Choose between day and night theme for better visibility</p>
            </div>
            <div class="setting-control">
                <div class="theme-toggle">
                    <span class="theme-label <?= $currentTheme === 'day' ? 'active' : '' ?>">☀️ Day</span>
                    <label class="toggle-switch">
                        <input type="checkbox" id="theme-toggle" <?= $currentTheme === 'night' ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                    <span class="theme-label <?= $currentTheme === 'night' ? 'active' : '' ?>">🌙 Night</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('theme-toggle');
    
    if (themeToggle) {
        themeToggle.addEventListener('change', function() {
            console.log('Theme toggle clicked, current checked state:', this.checked);
            
            // Add loading state
            this.disabled = true;
            
            // Make the AJAX request
            fetch('/settings/toggle-theme', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Theme toggle response:', data);
                
                // Update document theme
                document.documentElement.setAttribute('data-theme', data.theme);
                
                // Update active labels
                const dayLabel = document.querySelector('.theme-label:first-of-type');
                const nightLabel = document.querySelector('.theme-label:last-of-type');
                
                if (data.theme === 'day') {
                    dayLabel.classList.add('active');
                    nightLabel.classList.remove('active');
                    this.checked = false;
                } else {
                    nightLabel.classList.add('active');
                    dayLabel.classList.remove('active');
                    this.checked = true;
                }
                
                // Re-enable the toggle
                this.disabled = false;
                
                console.log('Theme successfully changed to:', data.theme);
            })
            .catch(error => {
                console.error('Error toggling theme:', error);
                
                // Revert toggle state on error
                this.checked = !this.checked;
                this.disabled = false;
                
                // Show user-friendly error message
                alert('Failed to change theme. Please try again.');
            });
        });
    }
});
</script>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>