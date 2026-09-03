<?php
declare(strict_types=1);
namespace Src\Controllers;

use Src\Core\Controller;
use Src\Core\Request;

class TeamController extends Controller
{
  public function index(Request $request)
  {
    return $this->render('pages/team', ['title' => 'Team']);
  }
}
