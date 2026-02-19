<?php
namespace PhpGuru;

use PhpGuru\Controllers\AppointmentController;
use PhpGuru\Controllers\AuthController;
use PhpGuru\Controllers\PhonebookController;
use PhpGuru\Controllers\SettingsController;
use PhpGuru\Controllers\StockController;
use PhpGuru\Controllers\TaskController;
use PhpGuru\Controllers\WebsiteController;

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
        $websites = new WebsiteController();
        $tasks = new TaskController();
        $appointments = new AppointmentController();
        $stocks = new StockController();

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

        // Appointment routes (protected)
        $this->router->get('/appointments', [$appointments, 'index']);
        $this->router->get('/appointments/add', [$appointments, 'add']);
        $this->router->post('/appointments/add', [$appointments, 'store']);
        $this->router->get('/appointments/edit', [$appointments, 'edit']);
        $this->router->post('/appointments/edit', [$appointments, 'update']);
        $this->router->get('/appointments/delete', [$appointments, 'delete']);
        $this->router->get('/appointments/reminders', [$appointments, 'reminders']);
        $this->router->post('/appointments/mark-reminder-sent', [$appointments, 'markReminderSent']);

        // Task routes (protected)
        $this->router->get('/tasks', [$tasks, 'index']);
        $this->router->get('/tasks/add', [$tasks, 'add']);
        $this->router->post('/tasks/add', [$tasks, 'store']);
        $this->router->get('/tasks/edit', [$tasks, 'edit']);
        $this->router->post('/tasks/edit', [$tasks, 'update']);
        $this->router->get('/tasks/delete', [$tasks, 'delete']);
        $this->router->post('/tasks/toggle', [$tasks, 'toggleComplete']);

        // Stock routes (protected)
        $this->router->get('/stocks', [$stocks, 'index']);
        $this->router->get('/stocks/add', [$stocks, 'add']);
        $this->router->post('/stocks/add', [$stocks, 'store']);
        $this->router->get('/stocks/edit', [$stocks, 'edit']);
        $this->router->post('/stocks/edit', [$stocks, 'update']);
        $this->router->get('/stocks/delete', [$stocks, 'delete']);

        // Website routes (protected)
        $this->router->get('/websites', [$websites, 'index']);
        $this->router->get('/websites/add', [$websites, 'add']);
        $this->router->post('/websites/add', [$websites, 'store']);
        $this->router->get('/websites/edit', [$websites, 'edit']);
        $this->router->post('/websites/edit', [$websites, 'update']);
        $this->router->get('/websites/delete', [$websites, 'delete']);

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