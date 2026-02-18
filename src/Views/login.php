<?php $title = 'Login — PhoneBook'; ?>
<?php ob_start(); ?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <span class="auth-icon">📞</span>
            <h1>PhoneBook</h1>
            <p class="auth-subtitle">Sign in to manage your contacts</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <span class="alert-icon">⚠️</span>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/login" class="auth-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter username" autofocus required
                    autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required
                    autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary btn-full">
                Sign In
            </button>
        </form>

        <div class="auth-footer">
            <p>Default: <code>admin</code> / <code>admin123</code></p>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/layout.php'; ?>