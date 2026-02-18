<?php
namespace PhpGuru\Models;

class Contact
{
    public string $id;
    public string $name;
    public string $phone;
    public string $email;
    public string $category;

    public function __construct(
        string $name = '',
        string $phone = '',
        string $email = '',
        string $category = 'General',
        string $id = ''
    ) {
        $this->id = $id ?: uniqid('c_', true);
        $this->name = $name;
        $this->phone = $phone;
        $this->email = $email;
        $this->category = $category;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'category' => $this->category,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? '',
            $data['phone'] ?? '',
            $data['email'] ?? '',
            $data['category'] ?? 'General',
            $data['id'] ?? ''
        );
    }
}
