<?php
declare(strict_types=1);
namespace Src\Core;

use Src\Controllers\ContactController;
use Src\Controllers\HomeController;
use Src\Controllers\ImprintController;
use Src\Controllers\PrivacyController;
use Src\Controllers\ServicesController;
use Src\Controllers\TeamController;

class Router
{
  public function __construct(private array $routes) {}

  public function dispatch(Request $request): Response
  {
    $path = $request->path;

    // Default
    if (!isset($this->routes[$path])) {
      $path = '/';
    }

    [$controllerClass, $action] = $this->routes[$path] ?? $this->routes['/'];

    $controller = new $controllerClass();
    $method = $action;

    if (!method_exists($controller, $method)) {
      return new Response("Route action not found.", 500);
    }

    // Call with request if needed
    return $controller->$method($request);
  }
}
