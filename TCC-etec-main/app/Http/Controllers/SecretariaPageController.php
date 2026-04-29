<?php

namespace App\Http\Controllers;

use App\Core\Controller;

class SecretariaPageController extends Controller
{
    public function __invoke(): void
    {
        echo $this->view('secretaria');
    }
}
