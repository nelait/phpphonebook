<?php
namespace PhpGuru\Models;

class Website
{
    public string $id;
    public string $name;
    public string $url;
    public string $description;
    public string $category;
    public string $createdAt;

    public function __construct(
        string $name = '',
        string $url = '',
        string $description = '',
        string $category = 'General',
        string $id = '',
        string $createdAt = ''
    ) {
        $this->id = $id ?: uniqid('w_', true);
        $this->name = $name;
        $this->url = $url;
        $this->description = $description;
        $this->category = $category;
        $this->createdAt = $createdAt ?: date('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'description' => $this->description,
            'category' => $this->category,
            'createdAt' => $this->createdAt,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? '',
            $data['url'] ?? '',
            $data['description'] ?? '',
            $data['category'] ?? 'General',
            $data['id'] ?? '',
            $data['createdAt'] ?? ''
        );
    }
}