<?php
declare(strict_types=1);
namespace Src\Core;

class Request
{
  public function __construct(
    public readonly string $path,
    public readonly string $method,
    public readonly array $query,
    public readonly array $post
  ) {}

  public static function fromGlobals(): self
  {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    $path = rtrim($path, '/');
    if ($path === '') $path = '/';

    return new self(
      path: $path,
      method: strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
      query: $_GET ?? [],
      post: $_POST ?? []
    );
  }
}
