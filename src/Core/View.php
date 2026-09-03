<?php
declare(strict_types=1);
namespace Src\Core;

class View
{
  public static function render(string $view, array $data = []): string
  {
    // $view ist z.B. "pages/home" oder "pages/contact"
    $viewFile = __DIR__ . '/../Views/' . $view . '.php';

    if (!is_file($viewFile)) {
      throw new \RuntimeException("View not found: {$view}");
    }

    $meta = ['title' => 'Kfz Digital'];
    if (isset($data['title']) && is_string($data['title'])) {
      $meta['title'] = $data['title'];
    }

    ob_start();
    $contentView = $viewFile;
    include __DIR__ . '/../Views/layout/header.php';
    include $viewFile;
    include __DIR__ . '/../Views/layout/footer.php';
    return (string)ob_get_clean();
  }
}
