<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class InicioAtendimento extends Controller
{
    public function index()
    {
        echo view('templates/header');
      
        echo view('templates/inicio/atendimento'); // view nova exclusiva
    }
}
