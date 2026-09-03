<?php
declare(strict_types=1);
namespace Src\Controllers;

use Src\Core\Controller;
use Src\Core\Request;

class ContactController extends Controller
{
  public function index(Request $request)
  {
    $notice = null;

    if ($request->method === 'POST') {
      $name = trim((string)($request->post['name'] ?? ''));
      $email = trim((string)($request->post['email'] ?? ''));
      $message = trim((string)($request->post['message'] ?? ''));

      // Minimal demo: nur UI bestätigen
      if ($name !== '' && $email !== '' && $message !== '') {
        $notice = 'Danke! Deine Nachricht wurde gesendet (Demo, keine echte Mail).';
      } else {
        $notice = 'Bitte fülle alle Felder aus.';
      }
    }

    return $this->render('pages/contact', [
      'title' => 'Kontakt',
      'notice' => $notice
    ]);
  }
}
