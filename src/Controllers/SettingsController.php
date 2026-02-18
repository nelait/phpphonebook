<?php
namespace PhpGuru\Controllers;

use PhpGuru\Storage\ThemeStorage;

class SettingsController
{
    private ThemeStorage $themeStorage;

    public function __construct()
    {
        $this->themeStorage = new ThemeStorage();
    }

    public function index(): void
    {
        $currentTheme = $this->themeStorage->getTheme();
        require __DIR__ . '/../Views/settings.php';
    }

    public function toggleTheme(): void
    {
        try {
            $currentTheme = $this->themeStorage->getTheme();
            $newTheme = $currentTheme === 'day' ? 'night' : 'day';
            $this->themeStorage->setTheme($newTheme);
            
            // Check if this is an AJAX request
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                     strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            
            if ($isAjax) {
                // Return JSON response for AJAX requests
                header('Content-Type: application/json');
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'theme' => $newTheme,
                    'message' => 'Theme changed to ' . $newTheme
                ]);
                exit;
            }
            
            // Fallback: redirect for non-AJAX requests
            header('Location: /settings');
            exit;
            
        } catch (Exception $e) {
            error_log('Theme toggle error: ' . $e->getMessage());
            
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                     strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Failed to toggle theme'
                ]);
                exit;
            }
            
            header('Location: /settings');
            exit;
        }
    }
}