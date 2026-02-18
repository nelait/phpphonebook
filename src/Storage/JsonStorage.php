<?php
namespace PhpGuru\Storage;

use PhpGuru\Models\Contact;

class JsonStorage
{
    private string $filePath;

    public function __construct()
    {
        $this->filePath = __DIR__ . '/../../data/contacts.json';
        if (!file_exists($this->filePath)) {
            if (!is_dir(dirname($this->filePath))) {
                mkdir(dirname($this->filePath), 0755, true);
            }
            file_put_contents($this->filePath, '[]');
        }
    }

    /** @return Contact[] */
    public function getAll(): array
    {
        $data = json_decode(file_get_contents($this->filePath), true) ?: [];
        return array_map(fn($item) => Contact::fromArray($item), $data);
    }

    public function getById(string $id): ?Contact
    {
        foreach ($this->getAll() as $contact) {
            if ($contact->id === $id) {
                return $contact;
            }
        }
        return null;
    }

    public function save(Contact $contact): void
    {
        $contacts = $this->getAll();
        $found = false;
        foreach ($contacts as $key => $existing) {
            if ($existing->id === $contact->id) {
                $contacts[$key] = $contact;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $contacts[] = $contact;
        }
        $this->writeAll($contacts);
    }

    public function delete(string $id): void
    {
        $contacts = $this->getAll();
        $contacts = array_values(array_filter($contacts, fn($c) => $c->id !== $id));
        $this->writeAll($contacts);
    }

    /** @return Contact[] */
    public function search(string $query): array
    {
        $query = strtolower($query);
        return array_values(array_filter($this->getAll(), function ($c) use ($query) {
            return str_contains(strtolower($c->name), $query)
                || str_contains(strtolower($c->phone), $query)
                || str_contains(strtolower($c->email), $query);
        }));
    }

    private function writeAll(array $contacts): void
    {
        $data = array_map(fn($c) => $c->toArray(), $contacts);
        file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
}
