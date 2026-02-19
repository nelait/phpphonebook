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
            <div class="nav-links">
                <a href="/" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/') && !str_starts_with($_SERVER['REQUEST_URI'], '/websites') && !str_starts_with($_SERVER['REQUEST_URI'], '/tasks') && !str_starts_with($_SERVER['REQUEST_URI'], '/settings') ? 'active' : '' ?>">Contacts</a>
                <a href="/tasks" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/tasks') ? 'active' : '' ?>">Tasks</a>
                <a href="/websites" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'], '/websites') ? 'active' : '' ?>">Websites</a>
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

    <style>
    .nav-links {
        display: flex;
        gap: 1rem;
        margin-left: 2rem;
    }

    .nav-link {
        padding: 0.5rem 1rem;
        color: var(--text-secondary);
        text-decoration: none;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .nav-link:hover {
        color: var(--text-primary);
        background-color: var(--bg-secondary);
    }

    .nav-link.active {
        color: var(--primary-color);
        background-color: var(--primary-light);
    }

    @media (max-width: 768px) {
        .navbar {
            flex-wrap: wrap;
        }
        
        .nav-links {
            order: 3;
            width: 100%;
            margin-left: 0;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }
    }
    </style>
</body>

</html>