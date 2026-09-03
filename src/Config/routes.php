<?php
declare(strict_types=1);

use Src\Controllers\HomeController;
use Src\Controllers\ContactController;
use Src\Controllers\ImprintController;
use Src\Controllers\PrivacyController;
use Src\Controllers\ServicesController;
use Src\Controllers\TeamController;

return [
  '/' => [HomeController::class, 'index'],
  '/contact' => [ContactController::class, 'index'],
  '/imprint' => [ImprintController::class, 'index'],
  '/privacy' => [PrivacyController::class, 'index'],
  '/services' => [ServicesController::class, 'index'],
  '/team' => [TeamController::class, 'index'],
];
