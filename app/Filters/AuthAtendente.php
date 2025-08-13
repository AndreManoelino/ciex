<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthAtendente implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Verifica se está logado e se o tipo é atendente
        if (
            !$session->get('logged_in') ||
            !in_array($session->get('tipo'), ['atendente', 'supervisor_atendimento','atendente_rg'])
        ) {
            return redirect()->to('/login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
