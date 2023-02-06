<?php
declare(strict_types=1);

namespace Rodion\TaskList\Controller;

/**
 *
 */
abstract class BaseController
{
    protected function render(string $viewName, array $params=[], string $layout='base'): void
    {
        extract($params, EXTR_OVERWRITE);

        ob_start();
        include __DIR__ . '/../view/' . $viewName . '.phtml';
        $internalContent = ob_get_clean();

        $baseUrl = $this->getBaseUrl();
        include __DIR__ . '/../view/layout/' . $layout . '.phtml';
    }

    protected function isUserAuthorized(): bool
    {
        return $_SESSION['isAdmin'] ?? false;
    }

    protected function getBaseUrl(): string
    {
        return 'http://' . $_SERVER['SERVER_NAME'] . '/beejee/';
    }
}
