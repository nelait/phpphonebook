<?php
namespace PhpGuru\Controllers;

class AuthController
{
    private const USERNAME = 'admin';
    private const PASSWORD = 'admin123';

    public function showLogin(): void
    {
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);
        require __DIR__ . '/../Views/login.php';
    }

    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === self::USERNAME && $password === self::PASSWORD) {
            $_SESSION['authenticated'] = true;
            $_SESSION['username'] = $username;
            header('Location: /');
            exit;
        }

        $_SESSION['login_error'] = 'Invalid username or password.';
        header('Location: /login');
        exit;
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: /login');
        exit;
    }
}
