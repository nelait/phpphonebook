<?php
namespace PhpGuru\Storage;

use PhpGuru\Models\Website;

class WebsiteStorage
{
    private string $filePath;

    public function __construct()
    {
        $this->filePath = __DIR__ . '/../../data/websites.json';
        if (!file_exists($this->filePath)) {
            if (!is_dir(dirname($this->filePath))) {
                mkdir(dirname($this->filePath), 0755, true);
            }
            file_put_contents($this->filePath, '[]');
        }
    }

    /** @return Website[] */
    public function getAll(): array
    {
        $data = json_decode(file_get_contents($this->filePath), true) ?: [];
        return array_map(fn($item) => Website::fromArray($item), $data);
    }

    public function getById(string $id): ?Website
    {
        foreach ($this->getAll() as $website) {
            if ($website->id === $id) {
                return $website;
            }
        }
        return null;
    }

    public function save(Website $website): void
    {
        $websites = $this->getAll();
        $found = false;
        foreach ($websites as $key => $existing) {
            if ($existing->id === $website->id) {
                $websites[$key] = $website;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $websites[] = $website;
        }
        $this->writeAll($websites);
    }

    public function delete(string $id): void
    {
        $websites = $this->getAll();
        $websites = array_values(array_filter($websites, fn($w) => $w->id !== $id));
        $this->writeAll($websites);
    }

    /** @return Website[] */
    public function search(string $query): array
    {
        $query = strtolower($query);
        return array_values(array_filter($this->getAll(), function ($w) use ($query) {
            return str_contains(strtolower($w->name), $query)
                || str_contains(strtolower($w->url), $query)
                || str_contains(strtolower($w->description), $query)
                || str_contains(strtolower($w->category), $query);
        }));
    }

    /** @return Website[] */
    public function getByCategory(string $category): array
    {
        return array_values(array_filter($this->getAll(), fn($w) => $w->category === $category));
    }

    /** @return array */
    public function getCategories(): array
    {
        $categories = [];
        foreach ($this->getAll() as $website) {
            if (!in_array($website->category, $categories)) {
                $categories[] = $website->category;
            }
        }
        sort($categories);
        return $categories;
    }

    private function writeAll(array $websites): void
    {
        $data = array_map(fn($w) => $w->toArray(), $websites);
        file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
}