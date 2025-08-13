<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class FormularioController extends Controller
{
    public function index()
    {
        
        helper('interface'); // Carrega o helper manualmente

        $data['campos'] = [
            ['nome' => 'nome', 'label' => 'Nome Completo', 'tipo' => 'text', 'required' => true, 'col' => 6],
            ['nome' => 'email', 'label' => 'E-mail', 'tipo' => 'email', 'required' => true, 'col' => 6],
            ['nome' => 'data_nascimento', 'label' => 'Data de Nascimento', 'tipo' => 'date', 'required' => true, 'col' => 4],
            ['nome' => 'sexo', 'label' => 'Sexo', 'tipo' => 'select', 'opcoes' => [
                '' => 'Selecione',
                'M' => 'Masculino',
                'F' => 'Feminino',
                'O' => 'Outro'
            ], 'required' => true, 'col' => 4],
            ['nome' => 'mensagem', 'label' => 'Mensagem', 'tipo' => 'textarea', 'required' => false, 'col' => 12],
            ['nome' => 'arquivo', 'label' => 'Anexar Arquivo', 'tipo' => 'file', 'accept' => '.pdf,.jpg,.png', 'required' => false, 'col' => 12]
        ];

        return view('formulario/index', $data);
    }

    public function enviar()
    {
        $post = $this->request->getPost();

        echo '<pre>';
        print_r($post);
        echo '</pre>';
    }
}
