<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EquipamentoModel;

class Equipamentos extends BaseController
{
    protected $equipamentoModel;

    public function __construct()
    {
        $this->equipamentoModel = new EquipamentoModel();
    }

    public function index()
    {
        $session = session();
        $tipo = $session->get('tipo');
        $unidade = $session->get('unidade');
        $estado = $session->get('estado');

        $filtroEstado = $this->request->getGet('filtro_estado');
        $filtroUnidade = $this->request->getGet('filtro_unidade');

        // ADMIN pode filtrar todos os estados/unidades
        if ($tipo === 'admin' && $estado === 'BRASIL' && $unidade === 'BRASIL') {
            // Lista todos os estados
            $estadosDisponiveis = $this->getEstados();

            // Se selecionou um estado no filtro, pega as unidades dele
            $unidades = [];
            if (!empty($filtroEstado)) {
                $unidades = $this->getUnidadesPorEstado($filtroEstado);
            }

            // Busca equipamentos com filtro opcional de estado/unidade
            $equipamentos = $this->equipamentoModel->getEquipamentosPorUsuario(
                $tipo,
                $filtroUnidade ?? null,
                $filtroEstado ?? null
            );

        } else {
            // Regra normal para supervisor/técnico
            $equipamentos = $this->equipamentoModel->getEquipamentosPorUsuario(
                $tipo,
                $unidade,
                $estado,
                $filtroUnidade
            );

            $unidades = [];
            if ($tipo === 'supervisor') {
                $query = $this->equipamentoModel
                    ->distinct()
                    ->select('unidade')
                    ->where('estado', $estado)
                    ->findAll();

                foreach ($query as $row) {
                    $unidades[] = $row['unidade'];
                }
            }
            $estadosDisponiveis = []; // supervisor não precisa listar todos os estados
        }

        echo view('templates/header');
        echo view('templates/sidebar');
        echo view('equipamentos/index', [
            'equipamentos' => $equipamentos,
            'unidades' => $unidades,
            'estados' => $estadosDisponiveis,
            'estadoSelecionado' => $filtroEstado,
            'unidadeSelecionada' => $filtroUnidade,
            'tipoUsuario' => $tipo,
        ]);
        echo view('templates/footer');
    }

    // Adiciona funções para admin
    private function getEstados()
    {
        return ['Minas Gerais', 'São Paulo', 'Rio de Janeiro'];
    }

    private function getUnidadesPorEstado($estado)
    {
        $unidades = [
            'Minas Gerais' => ['Barreiro','Betim','Contagem','Contagem Avançada','Curvelo',
                'Governador Valadares','Ipatinga','Juiz de Fora','Montes Claros',
                'Poços de Caldas','Pouso Alegre','Praça Sete','São Sebastião do Paraiso',
                'Sete Lagoas','Sete Lagoas Avançada','Teofilo Otoni','Uberlândia',
                'Uberlândia Avançada','Varginha'],
            'São Paulo' => ['Poupatempo Sé', 'Poupatempo Santo Amaro', 'Poupatempo Itaquera','Poupatempo Luz','Poupatempo Móvel (Cidade Tiradentes)','Poupatempo Móvel (Ipiranga)','Poupatempo Guarulhos','Poupatempo Campinas'],
            'Rio de Janeiro' => ['Poupa Tempo Recreio dos Bandeirantes', 'Poupa Tempo Zona Oeste','Poupa Tempo Baixada','Poupa Tempo São Gonçalo','Poupa Tempo Bangu'],
        ];

        return $unidades[$estado] ?? [];
    }

    public function usar($id)
    {
        $this->equipamentoModel->alterarStatusUsoBackup($id, 'usar');
        return redirect()->to('/equipamentos')->with('msg', 'Equipamento colocado em uso.');
    }

    public function liberar($id)
    {
        $this->equipamentoModel->alterarStatusUsoBackup($id, 'liberar');
        return redirect()->to('/equipamentos')->with('msg', 'Equipamento liberado para backup.');
    }

    public function adicionar()
    {
        $id = $this->request->getPost('id');
        $qtd = (int) $this->request->getPost('quantidade');

        $this->equipamentoModel->adicionarQuantidade($id, $qtd);

        return redirect()->to('/equipamentos')->with('msg', 'Estoque de backup atualizado.');
    }



    public function alterarStatus($id, $novoStatus)
    {
        $equipamento = $this->equipamentoModel->find($id);

        if (!$equipamento) {
            return redirect()->back()->with('erro', 'Equipamento não encontrado.');
        }

        $this->equipamentoModel->update($id, ['status' => $novoStatus]);

        return redirect()->to('/equipamentos')->with('msg', 'Status alterado com sucesso.');
    }

    public function adicionarQuantidade($id)
    {
        if ($this->request->getMethod() === 'post') {
            $qtdNova = (int) $this->request->getPost('quantidade');

            $equipamento = $this->equipamentoModel->find($id);

            if ($equipamento) {
                $novaQtd = $equipamento['quantidade'] + $qtdNova;
                $this->equipamentoModel->update($id, ['quantidade' => $novaQtd]);

                return redirect()->to('/equipamentos')->with('msg', 'Quantidade atualizada.');
            }
        }

        return redirect()->back()->with('erro', 'Erro ao atualizar quantidade.');
    }
    public function salvar()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/equipamentos')->with('erro', 'Método inválido.');
        }

        $data = [
            'nome' => $this->request->getPost('nome'),
            'modelo' => $this->request->getPost('modelo'),
            'quantidade_backup' => (int) $this->request->getPost('quantidade'),
            'quantidade_uso' => 0,
            'unidade' => session('unidade'),
            'estado' => session('estado'),
            // pode preencher outros campos se necessário
        ];

        // Validação simples (pode ser melhorada)
        if (empty($data['nome']) || empty($data['modelo']) || $data['quantidade_backup'] < 1) {
            return redirect()->back()->withInput()->with('erro', 'Preencha todos os campos corretamente.');
        }

        // Salvar no banco
        $this->equipamentoModel->insert($data);

        return redirect()->to('/equipamentos')->with('msg', 'Equipamento adicionado com sucesso.');
    }


}
