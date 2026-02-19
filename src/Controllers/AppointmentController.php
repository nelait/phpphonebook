<?php
namespace PhpGuru\Controllers;

use PhpGuru\Models\Appointment;
use PhpGuru\Storage\AppointmentStorage;

class AppointmentController
{
    private AppointmentStorage $storage;

    public function __construct()
    {
        $this->storage = new AppointmentStorage();
    }

    public function index(): void
    {
        $search = $_GET['search'] ?? '';
        $filter = $_GET['filter'] ?? '';
        $category = $_GET['category'] ?? '';
        
        if ($search) {
            $appointments = $this->storage->search($search);
        } elseif ($filter === 'upcoming') {
            $appointments = $this->storage->getUpcoming();
        } elseif ($filter === 'past') {
            $appointments = $this->storage->getPast();
        } elseif ($category) {
            $appointments = $this->storage->getByCategory($category);
        } else {
            $appointments = $this->storage->getAll();
        }
        
        // Sort appointments: upcoming first (by date/time), then past appointments
        usort($appointments, function($a, $b) {
            $aDateTime = strtotime($a->getDateTime());
            $bDateTime = strtotime($b->getDateTime());
            
            $now = time();
            $aIsUpcoming = $aDateTime > $now;
            $bIsUpcoming = $bDateTime > $now;
            
            // Upcoming appointments first
            if ($aIsUpcoming !== $bIsUpcoming) {
                return $bIsUpcoming ? 1 : -1;
            }
            
            // Within same category (upcoming or past), sort by date/time
            if ($aIsUpcoming) {
                // Upcoming: earliest first
                return $aDateTime - $bDateTime;
            } else {
                // Past: most recent first
                return $bDateTime - $aDateTime;
            }
        });
        
        $categories = $this->storage->getCategories();
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);
        
        require __DIR__ . '/../Views/appointments.php';
    }

    public function add(): void
    {
        $appointment = new Appointment();
        $errors = [];
        $isEdit = false;
        require __DIR__ . '/../Views/appointment_form.php';
    }

    public function store(): void
    {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';
        $location = trim($_POST['location'] ?? '');
        $category = trim($_POST['category'] ?? 'General');

        $errors = $this->validate($title, $date, $time);
        if (!empty($errors)) {
            $appointment = new Appointment($title, $description, $date, $time, $location, $category);
            $isEdit = false;
            require __DIR__ . '/../Views/appointment_form.php';
            return;
        }

        $appointment = new Appointment($title, $description, $date, $time, $location, $category);
        $this->storage->save($appointment);
        $_SESSION['success'] = 'Appointment added successfully!';
        header('Location: /appointments');
        exit;
    }

    public function edit(): void
    {
        $id = $_GET['id'] ?? '';
        $appointment = $this->storage->getById($id);
        if (!$appointment) {
            header('Location: /appointments');
            exit;
        }
        $errors = [];
        $isEdit = true;
        require __DIR__ . '/../Views/appointment_form.php';
    }

    public function update(): void
    {
        $id = $_POST['id'] ?? '';
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';
        $location = trim($_POST['location'] ?? '');
        $category = trim($_POST['category'] ?? 'General');

        $errors = $this->validate($title, $date, $time);
        if (!empty($errors)) {
            $appointment = new Appointment($title, $description, $date, $time, $location, $category, false, $id);
            $isEdit = true;
            require __DIR__ . '/../Views/appointment_form.php';
            return;
        }

        // Get original appointment to preserve creation date
        $originalAppt = $this->storage->getById($id);
        $createdAt = $originalAppt ? $originalAppt->createdAt : date('Y-m-d H:i:s');
        $reminderSent = $originalAppt ? $originalAppt->reminderSent : false;
        
        $appointment = new Appointment($title, $description, $date, $time, $location, $category, $reminderSent, $id, $createdAt);
        $this->storage->save($appointment);
        $_SESSION['success'] = 'Appointment updated successfully!';
        header('Location: /appointments');
        exit;
    }

    public function delete(): void
    {
        $id = $_GET['id'] ?? '';
        $this->storage->delete($id);
        $_SESSION['success'] = 'Appointment deleted successfully!';
        header('Location: /appointments');
        exit;
    }

    public function reminders(): void
    {
        $dueSoonAppointments = $this->storage->getDueSoon();
        $todayAppointments = array_filter($this->storage->getAll(), fn($a) => $a->isToday());
        
        require __DIR__ . '/../Views/appointment_reminders.php';
    }

    public function markReminderSent(): void
    {
        $id = $_POST['id'] ?? '';
        $appointment = $this->storage->getById($id);
        
        if ($appointment) {
            $appointment->reminderSent = true;
            $this->storage->save($appointment);
            
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                     strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Reminder marked as sent!'
                ]);
                exit;
            }
        }
        
        header('Location: /appointments/reminders');
        exit;
    }

    private function validate(string $title, string $date, string $time): array
    {
        $errors = [];
        
        if (empty($title)) {
            $errors[] = 'Appointment title is required.';
        }
        
        if (empty($date)) {
            $errors[] = 'Date is required.';
        } elseif (!strtotime($date)) {
            $errors[] = 'Please enter a valid date.';
        }
        
        if (empty($time)) {
            $errors[] = 'Time is required.';
        }
        
        // Check if appointment is in the past
        if (!empty($date) && !empty($time)) {
            $appointmentDateTime = strtotime($date . ' ' . $time);
            if ($appointmentDateTime < time()) {
                $errors[] = 'Appointment cannot be scheduled in the past.';
            }
        }
        
        return $errors;
    }
}