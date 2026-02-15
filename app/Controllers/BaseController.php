<?php
class BaseController
{
    protected function render(string $view, array $data = [])
    {
        extract($data, EXTR_SKIP);
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            http_response_code(500);
            echo "View not found: " . htmlspecialchars($view);
        }
    }
}
