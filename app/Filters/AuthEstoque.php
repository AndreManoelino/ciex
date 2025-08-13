<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthEstoque implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Permite apenas atendente ou supervisor_atendimento
        if (
            !$session->get('logged_in') ||
            !in_array($session->get('tipo'), ['atendente', 'supervisor_atendimento'])
        ) {
            return redirect()->to('/login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nenhuma ação após
    }
}
