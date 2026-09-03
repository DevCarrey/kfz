<?php
declare(strict_types=1);
namespace Src\Models;

class ContactMessage
{
  public function __construct(
    public string $name = '',
    public string $email = '',
    public string $message = ''
  ) {}
}
