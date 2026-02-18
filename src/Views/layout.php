<?php
use PhpGuru\Storage\ThemeStorage;

// Get current theme
$themeStorage = new ThemeStorage();
$currentTheme = $themeStorage->getTheme();
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?= $currentTheme ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PhoneBook — Manage your contacts with ease">
    <title>
        <?= htmlspecialchars($title ?? 'PhoneBook') ?>
    </title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>
    <?php if (isset($_SESSION['authenticated'])): ?>
        <nav class="navbar">
            <div class="nav-brand">
                <span class="nav-icon">📞</span>
                <span class="nav-title">PhoneBook</span>
            </div>
            <div class="nav-right">
                <a href="/settings" class="btn btn-ghost nav-settings" title="Settings">⚙️</a>
                <span class="nav-user">👤
                    <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
                </span>
                <a href="/logout" class="btn btn-ghost">Logout</a>
            </div>
        </nav>
    <?php endif; ?>

    <main class="container">
        <?= $content ?? '' ?>
    </main>

    <footer class="footer">
        <p>&copy;
            <?= date('Y') ?> PhoneBook App &mdash; Built with PHP
        </p>
    </footer>
</body>

</html>