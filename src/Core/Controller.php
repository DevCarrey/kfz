<?php
declare(strict_types=1);
namespace Src\Core;

abstract class Controller
{
  protected function render(string $view, array $data = []): Response
  {
    $html = View::render($view, $data);
    return new Response($html, 200);
  }

  protected function redirect(string $to): Response
  {
    return new Response('', 302, ['Location' => $to]);
  }
}
