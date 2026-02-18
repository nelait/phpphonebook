<?php
namespace PhpGuru\Storage;

class ThemeStorage
{
    private string $filePath;

    public function __construct()
    {
        $this->filePath = __DIR__ . '/../../data/theme.json';
        if (!file_exists($this->filePath)) {
            if (!is_dir(dirname($this->filePath))) {
                mkdir(dirname($this->filePath), 0755, true);
            }
            $this->setTheme('day'); // Default theme
        }
    }

    public function getTheme(): string
    {
        if (!file_exists($this->filePath)) {
            return 'day';
        }
        
        $data = json_decode(file_get_contents($this->filePath), true);
        return $data['theme'] ?? 'day';
    }

    public function setTheme(string $theme): void
    {
        $validThemes = ['day', 'night'];
        if (!in_array($theme, $validThemes)) {
            $theme = 'day';
        }
        
        $data = ['theme' => $theme];
        file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT));
    }
}