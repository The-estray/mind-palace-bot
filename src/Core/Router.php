<?php

class Router
{
    private array $routes = [];

    public function add(string $command, array $action): void
    {
        $this->routes[$command] = $action;
    }

    public function dispatch(Update $update): void
    {
        $text = $update->getText();

        if (!isset($this->routes[$text])) {
            exit;
        }

        $controllerName = $this->routes[$text][0];
        $actionName = $this->routes[$text][1];

        $controller = new $controllerName();
        $controller->$actionName($update);
    }
}
