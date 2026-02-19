<?php
namespace PhpGuru\Models;

class Appointment
{
    public string $id;
    public string $title;
    public string $description;
    public string $date;
    public string $time;
    public string $location;
    public string $category;
    public bool $reminderSent;
    public string $createdAt;

    public function __construct(
        string $title = '',
        string $description = '',
        string $date = '',
        string $time = '',
        string $location = '',
        string $category = 'General',
        bool $reminderSent = false,
        string $id = '',
        string $createdAt = ''
    ) {
        $this->id = $id ?: uniqid('a_', true);
        $this->title = $title;
        $this->description = $description;
        $this->date = $date;
        $this->time = $time;
        $this->location = $location;
        $this->category = $category;
        $this->reminderSent = $reminderSent;
        $this->createdAt = $createdAt ?: date('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date,
            'time' => $this->time,
            'location' => $this->location,
            'category' => $this->category,
            'reminderSent' => $this->reminderSent,
            'createdAt' => $this->createdAt,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['date'] ?? '',
            $data['time'] ?? '',
            $data['location'] ?? '',
            $data['category'] ?? 'General',
            $data['reminderSent'] ?? false,
            $data['id'] ?? '',
            $data['createdAt'] ?? ''
        );
    }

    public function getDateTime(): string
    {
        return $this->date . ' ' . $this->time;
    }

    public function isUpcoming(): bool
    {
        $appointmentDateTime = strtotime($this->getDateTime());
        return $appointmentDateTime > time();
    }

    public function isPast(): bool
    {
        return !$this->isUpcoming();
    }

    public function isToday(): bool
    {
        return date('Y-m-d') === $this->date;
    }

    public function isDueSoon(): bool
    {
        $appointmentDateTime = strtotime($this->getDateTime());
        $now = time();
        $in24Hours = $now + (24 * 60 * 60);
        
        return $appointmentDateTime > $now && $appointmentDateTime <= $in24Hours;
    }
}