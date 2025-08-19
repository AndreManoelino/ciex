<?php

namespace App\Controllers;

use App\Models\JaChegouModel;
use CodeIgniter\Controller;


class JaChegouController extends Controller
{
    protected $model;

    public function __construct()
    {
        helper(['form', 'url', 'session','form_helper','valida']);
        $this->model = new JaChegouModel();
    }

    private function verificaLogin()
    {
        if (!session()->get('logged_in') || session()->get('tipo') !== 'atendente') {
            return redirect()->to('/login')->send();
        }
    }

    public function index()
    {
        $this->verificaLogin();

        $data['title'] = 'Já Chegou - Documentos Recebidos';
        $data['documentos'] = $this->model->getDocumentosComNome(); // <-- aqui

        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('ja_chegou/index', $data);
        echo view('templates/footer', $data);
    }

    public function inserir()
    {
        $this->verificaLogin();

        if ($this->request->getMethod() === 'POST') {
            $cpf = preg_replace('/[^0-9]/', '', $this->request->getPost('cpf'));

            if (!valida_cpf($cpf)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', ['CPF inválido. Verifique o número digitado.']);
            }

            $data = [
                'nome_cidadao'    => $this->request->getPost('nome_cidadao'),
                'cpf'             => $cpf,
                'tipo_documento'  => $this->request->getPost('tipo_documento'),
                'codigo_entrega'  => $this->request->getPost('codigo_entrega'),
                'unidade'         => session()->get('unidade'),
                'estado'          => 'RECEBIDO',
                'contato'         => $this->request->getPost('contato'),
                'recebido_por'    => session()->get('user_id'),
            ];

            if ($this->model->insert($data)) {
                return redirect()->to('/ja_chegou')->with('success', 'Documento registrado com sucesso!');
            } else {
                return redirect()->back()->with('errors', $this->model->errors())->withInput();
            }
        }

        $data['title'] = 'Registrar Documento';
        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('ja_chegou/inserir', $data);
        echo view('templates/footer', $data);
    }


    public function entregar($id = null)
    {
        $this->verificaLogin();

        $documento = $this->model->find($id);

        if (!$documento || $documento['unidade'] != session()->get('unidade')) {
            return redirect()->to('/ja_chegou')->with('error', 'Documento não encontrado ou acesso negado.');
        }

        if ($documento['estado'] === 'ENTREGUE') {
            return redirect()->to('/ja_chegou')->with('info', 'Documento já foi entregue.');
        }

        $this->model->update($id, [
            'estado' => 'ENTREGUE',
            'data_entrega' => date('Y-m-d H:i:s'),
            'entregue_por' => session()->get('user_id')
        ]);

        return redirect()->to('/ja_chegou')->with('success', 'Documento entregue com sucesso!');
    }

    public function buscar()
    {
        $this->verificaLogin();

        $nome = $this->request->getGet('nome');
        $cpf = preg_replace('/[^0-9]/', '', $this->request->getGet('cpf'));

        $query = $this->model->where('unidade', session()->get('unidade'));

        if ($nome) {
            $query = $query->like('nome_cidadao', $nome);
        }

        if ($cpf) {
            $query = $query->where('cpf', $cpf);
        }

        $data = [
            'title'      => 'Buscar Documentos',
            'documentos' => $query->orderBy('data_recebimento', 'DESC')->findAll(),
            'nome'       => $nome,
            'cpf'        => $cpf
        ];

        echo view('templates/header', $data);
        echo view('templates/sidebar', $data);
        echo view('ja_chegou/buscar', $data);
        echo view('templates/footer', $data);
    }

}
