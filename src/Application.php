<?php
namespace PhpGuru;

use PhpGuru\Controllers\AuthController;
use PhpGuru\Controllers\PhonebookController;
use PhpGuru\Controllers\SettingsController;

class Application
{
    private Router $router;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->router = new Router();
        $this->registerRoutes();
    }

    private function registerRoutes(): void
    {
        $auth = new AuthController();
        $phonebook = new PhonebookController();
        $settings = new SettingsController();

        // Auth routes
        $this->router->get('/login', [$auth, 'showLogin']);
        $this->router->post('/login', [$auth, 'login']);
        $this->router->get('/logout', [$auth, 'logout']);

        // Phonebook routes (protected)
        $this->router->get('/', [$phonebook, 'index']);
        $this->router->get('/add', [$phonebook, 'add']);
        $this->router->post('/add', [$phonebook, 'store']);
        $this->router->get('/edit', [$phonebook, 'edit']);
        $this->router->post('/edit', [$phonebook, 'update']);
        $this->router->get('/delete', [$phonebook, 'delete']);

        // Settings routes (protected)
        $this->router->get('/settings', [$settings, 'index']);
        $this->router->post('/settings/toggle-theme', [$settings, 'toggleTheme']);
    }

    public function start(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        // Auth guard: redirect unauthenticated users to login
        $publicRoutes = ['/login'];
        if (!isset($_SESSION['authenticated']) && !in_array($uri, $publicRoutes)) {
            header('Location: /login');
            exit;
        }

        // Already logged in? Skip login page
        if (isset($_SESSION['authenticated']) && $uri === '/login') {
            header('Location: /');
            exit;
        }

        $this->router->dispatch($method, $uri);
    }
}