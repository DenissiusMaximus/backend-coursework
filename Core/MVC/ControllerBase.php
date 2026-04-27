<?php

namespace Core\MVC;

use Core\Response;

abstract class ControllerBase
{
    protected function view(string $viewName, array $data = [], int $statusCode = 200): Response
    {
        extract($data);

        ob_start();
        include __DIR__ . "/../../Views/$viewName.php";
        $content = ob_get_clean(); 

        ob_start();
        include __DIR__ . "/../../Views/layout.php";
        $fullHtml = ob_get_clean(); 

        return new Response($fullHtml, $statusCode);
    }
}