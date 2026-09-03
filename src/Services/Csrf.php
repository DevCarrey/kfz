<?php
declare(strict_types=1);
namespace Src\Services;

class Csrf
{
  public function token(): string { return 'demo-token'; }
  public function check(string $token): bool { return $token === 'demo-token'; }
}
