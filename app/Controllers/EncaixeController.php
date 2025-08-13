<?php

namespace App\Controllers;

use App\Models\EncaixeModel;

class EncaixeController extends BaseController
{
    protected $encaixeModel;

    public function __construct()
    {
        $this->encaixeModel = new EncaixeModel();
    }

    // Restringir acesso somente para 'atendente' e 'supervisor_atendimento'
    private function verificarAcesso()
    {
        $tipo = session()->get('tipo');
        if (!in_array($tipo, ['atendente', 'supervisor_atendimento','atendente_rg'])) {
            return redirect()->to('/'); // Ou outra página de erro/acesso negado
        }
        
    }

    public function index()
    {
        $acesso = $this->verificarAcesso();
        if ($acesso) return $acesso;

        $mes = $this->request->getGet('mes') ?? date('m');
        $nome = $this->request->getGet('nome');
        $horario = $this->request->getGet('horario');

        // Dados de sessão
        $estadoUsuario = session()->get('estado');
        $unidadeUsuario = session()->get('unidade');

        $encaixes = $this->encaixeModel->buscar($mes, $nome, $horario, $estadoUsuario, $unidadeUsuario);

        $totalMes = $this->encaixeModel->contarPorMes($mes, date('Y'), $estadoUsuario, $unidadeUsuario);

        return view('encaixes/index', [
            'encaixes' => $encaixes,
            'mes' => $mes,
            'nome' => $nome,
            'horario' => $horario,
            'totalMes' => $totalMes
        ]);
    }

    public function criar()
    {
        $acesso = $this->verificarAcesso();
        if ($acesso) return $acesso;

        return view('encaixes/criar');
    }

    public function salvar()
    {


        $acesso = $this->verificarAcesso();
        if ($acesso) return $acesso;

        $data = $this->request->getPost();
        

        // Validação básica
        if (empty($data['nome']) || empty($data['cpf']) || empty($data['horario']) || empty($data['tipo'])) {
            return redirect()->back()->with('error', 'Preencha todos os campos obrigatórios');
        }

        $data['data'] = date('Y-m-d');

        // 👉 Substitua o trecho abaixo pelo novo com estado/unidade
        $this->encaixeModel->save([
            'nome' => $data['nome'],
            'cpf' => $data['cpf'],
            'horario' => $data['horario'],
            'tipo' => $data['tipo'],
            'data' => $data['data'],
            'estado' => session()->get('estado'),
            'unidade' => session()->get('unidade'),
        ]);

        return redirect()->to('/encaixes')->with('success', 'Encaixe salvo com sucesso!');
    }

}
