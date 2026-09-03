<?php
declare(strict_types=1);
namespace Src\Services;

class Mailer
{
  public function send(string $to, string $subject, string $body): void
  {
    // Demo: no-op
  }
}
