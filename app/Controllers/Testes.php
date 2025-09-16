<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Testes extends BaseController
{
    public function index()
    {
        return view('Testes/index');
    }

    public function novo()
    {
        echo "Testando";
    }
}
