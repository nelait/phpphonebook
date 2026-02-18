<?php
namespace PhpGuru\Controllers;

use PhpGuru\Models\Contact;
use PhpGuru\Storage\JsonStorage;

class PhonebookController
{
    private JsonStorage $storage;

    public function __construct()
    {
        $this->storage = new JsonStorage();
    }

    public function index(): void
    {
        $search = $_GET['search'] ?? '';
        $contacts = $search ? $this->storage->search($search) : $this->storage->getAll();
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);
        require __DIR__ . '/../Views/phonebook.php';
    }

    public function add(): void
    {
        $contact = new Contact();
        $errors = [];
        $isEdit = false;
        require __DIR__ . '/../Views/contact_form.php';
    }

    public function store(): void
    {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $category = trim($_POST['category'] ?? 'General');

        $errors = $this->validate($name, $phone);
        if (!empty($errors)) {
            $contact = new Contact($name, $phone, $email, $category);
            $isEdit = false;
            require __DIR__ . '/../Views/contact_form.php';
            return;
        }

        $contact = new Contact($name, $phone, $email, $category);
        $this->storage->save($contact);
        $_SESSION['success'] = 'Contact added successfully!';
        header('Location: /');
        exit;
    }

    public function edit(): void
    {
        $id = $_GET['id'] ?? '';
        $contact = $this->storage->getById($id);
        if (!$contact) {
            header('Location: /');
            exit;
        }
        $errors = [];
        $isEdit = true;
        require __DIR__ . '/../Views/contact_form.php';
    }

    public function update(): void
    {
        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $category = trim($_POST['category'] ?? 'General');

        $errors = $this->validate($name, $phone);
        if (!empty($errors)) {
            $contact = new Contact($name, $phone, $email, $category, $id);
            $isEdit = true;
            require __DIR__ . '/../Views/contact_form.php';
            return;
        }

        $contact = new Contact($name, $phone, $email, $category, $id);
        $this->storage->save($contact);
        $_SESSION['success'] = 'Contact updated successfully!';
        header('Location: /');
        exit;
    }

    public function delete(): void
    {
        $id = $_GET['id'] ?? '';
        $this->storage->delete($id);
        $_SESSION['success'] = 'Contact deleted successfully!';
        header('Location: /');
        exit;
    }

    private function validate(string $name, string $phone): array
    {
        $errors = [];
        if (empty($name))
            $errors[] = 'Name is required.';
        if (empty($phone))
            $errors[] = 'Phone number is required.';
        return $errors;
    }
}
