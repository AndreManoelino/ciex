<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthAtendenteSupervisor implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // DEBUG TEMPORÁRIO
        //dd($session->get());

        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }

        if (!in_array($session->get('tipo'), ['atendente', 'supervisor_atendimento', 'atendente_rg'])) {
            return redirect()->to('/login');
        }
    }


    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
