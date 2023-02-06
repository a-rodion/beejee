<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

function callAction(string $controllerName, string $actionName) {
    $controllerClass = 'Rodion\\TaskList\\Controller\\' . ucfirst($controllerName) . 'Controller';
    $actionMethod = 'action' . $actionName;
    if (!is_callable([$controllerClass, $actionMethod])) {
        http_response_code(404);
        exit('Not found');
    }
    $controller = new $controllerClass();
    $controller->$actionMethod();
}

$controller = 'task';
$action = 'list';
if (isset($_GET['r']) && strpos($_GET['r'], '/')) {
    [$controller, $action] = explode('/', $_GET['r']);
}
unset($_GET['r']);

session_start();
callAction($controller, $action);

?>
