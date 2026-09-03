<?php
declare(strict_types=1);
namespace Src\Core;

class Response
{
  public function __construct(
    private string $body,
    private int $status = 200,
    private array $headers = []
  ) {}

  public function send(): void
  {
    http_response_code($this->status);
    foreach ($this->headers as $k => $v) {
      header($k . ': ' . $v);
    }
    echo $this->body;
  }
}
