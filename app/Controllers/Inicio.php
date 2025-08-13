<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Inicio extends Controller
{
    public function index()
    {
        $tiposAtendimento = ['atendente', 'supervisor_atendimento', 'atendente_rg'];
        $tipoUsuario = session('tipo');

        if (in_array($tipoUsuario, $tiposAtendimento)) {
            return redirect()->to(base_url('inicioatendimento'));
        }

        echo view('templates/header');
        echo view('templates/inicio/index');
    }
}
