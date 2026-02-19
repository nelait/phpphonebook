<?php
namespace PhpGuru\Storage;

use PhpGuru\Models\Appointment;

class AppointmentStorage
{
    private string $filePath;

    public function __construct()
    {
        $this->filePath = __DIR__ . '/../../data/appointments.json';
        if (!file_exists($this->filePath)) {
            if (!is_dir(dirname($this->filePath))) {
                mkdir(dirname($this->filePath), 0755, true);
            }
            file_put_contents($this->filePath, '[]');
        }
    }

    /** @return Appointment[] */
    public function getAll(): array
    {
        $data = json_decode(file_get_contents($this->filePath), true) ?: [];
        return array_map(fn($item) => Appointment::fromArray($item), $data);
    }

    public function getById(string $id): ?Appointment
    {
        foreach ($this->getAll() as $appointment) {
            if ($appointment->id === $id) {
                return $appointment;
            }
        }
        return null;
    }

    public function save(Appointment $appointment): void
    {
        $appointments = $this->getAll();
        $found = false;
        foreach ($appointments as $key => $existing) {
            if ($existing->id === $appointment->id) {
                $appointments[$key] = $appointment;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $appointments[] = $appointment;
        }
        $this->writeAll($appointments);
    }

    public function delete(string $id): void
    {
        $appointments = $this->getAll();
        $appointments = array_values(array_filter($appointments, fn($a) => $a->id !== $id));
        $this->writeAll($appointments);
    }

    /** @return Appointment[] */
    public function search(string $query): array
    {
        $query = strtolower($query);
        return array_values(array_filter($this->getAll(), function ($a) use ($query) {
            return str_contains(strtolower($a->title), $query)
                || str_contains(strtolower($a->description), $query)
                || str_contains(strtolower($a->location), $query)
                || str_contains(strtolower($a->category), $query);
        }));
    }

    /** @return Appointment[] */
    public function getUpcoming(): array
    {
        return array_values(array_filter($this->getAll(), fn($a) => $a->isUpcoming()));
    }

    /** @return Appointment[] */
    public function getPast(): array
    {
        return array_values(array_filter($this->getAll(), fn($a) => $a->isPast()));
    }

    /** @return Appointment[] */
    public function getByCategory(string $category): array
    {
        return array_values(array_filter($this->getAll(), fn($a) => $a->category === $category));
    }

    /** @return Appointment[] */
    public function getByDate(string $date): array
    {
        return array_values(array_filter($this->getAll(), fn($a) => $a->date === $date));
    }

    /** @return Appointment[] */
    public function getDueSoon(): array
    {
        return array_values(array_filter($this->getAll(), fn($a) => $a->isDueSoon()));
    }

    /** @return array */
    public function getCategories(): array
    {
        $categories = [];
        foreach ($this->getAll() as $appointment) {
            if (!in_array($appointment->category, $categories)) {
                $categories[] = $appointment->category;
            }
        }
        sort($categories);
        return $categories;
    }

    private function writeAll(array $appointments): void
    {
        $data = array_map(fn($a) => $a->toArray(), $appointments);
        file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
}