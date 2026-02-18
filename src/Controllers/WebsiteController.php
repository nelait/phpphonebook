<?php
namespace PhpGuru\Controllers;

use PhpGuru\Models\Website;
use PhpGuru\Storage\WebsiteStorage;

class WebsiteController
{
    private WebsiteStorage $storage;

    public function __construct()
    {
        $this->storage = new WebsiteStorage();
    }

    public function index(): void
    {
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        
        if ($search) {
            $websites = $this->storage->search($search);
        } elseif ($category) {
            $websites = $this->storage->getByCategory($category);
        } else {
            $websites = $this->storage->getAll();
        }
        
        $categories = $this->storage->getCategories();
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);
        
        require __DIR__ . '/../Views/websites.php';
    }

    public function add(): void
    {
        $website = new Website();
        $errors = [];
        $isEdit = false;
        require __DIR__ . '/../Views/website_form.php';
    }

    public function store(): void
    {
        $name = trim($_POST['name'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? 'General');

        $errors = $this->validate($name, $url);
        if (!empty($errors)) {
            $website = new Website($name, $url, $description, $category);
            $isEdit = false;
            require __DIR__ . '/../Views/website_form.php';
            return;
        }

        $website = new Website($name, $url, $description, $category);
        $this->storage->save($website);
        $_SESSION['success'] = 'Website added successfully!';
        header('Location: /websites');
        exit;
    }

    public function edit(): void
    {
        $id = $_GET['id'] ?? '';
        $website = $this->storage->getById($id);
        if (!$website) {
            header('Location: /websites');
            exit;
        }
        $errors = [];
        $isEdit = true;
        require __DIR__ . '/../Views/website_form.php';
    }

    public function update(): void
    {
        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = trim($_POST['category'] ?? 'General');

        $errors = $this->validate($name, $url);
        if (!empty($errors)) {
            $website = new Website($name, $url, $description, $category, $id);
            $isEdit = true;
            require __DIR__ . '/../Views/website_form.php';
            return;
        }

        $website = new Website($name, $url, $description, $category, $id);
        $this->storage->save($website);
        $_SESSION['success'] = 'Website updated successfully!';
        header('Location: /websites');
        exit;
    }

    public function delete(): void
    {
        $id = $_GET['id'] ?? '';
        $this->storage->delete($id);
        $_SESSION['success'] = 'Website deleted successfully!';
        header('Location: /websites');
        exit;
    }

    private function validate(string $name, string $url): array
    {
        $errors = [];
        if (empty($name)) {
            $errors[] = 'Website name is required.';
        }
        if (empty($url)) {
            $errors[] = 'Website URL is required.';
        } elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
            $errors[] = 'Please enter a valid URL.';
        }
        return $errors;
    }
}