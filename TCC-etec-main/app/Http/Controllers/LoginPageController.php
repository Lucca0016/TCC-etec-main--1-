<?php

namespace App\Http\Controllers;

use App\Core\Controller;

class LoginPageController extends Controller
{
    public function __invoke(): void
    {
        echo $this->view('react-app');
    }
}
