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
        $equipamentosModelos = $this->getEquipamentosEModulos();
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
            'equipamentosModelos' => $equipamentosModelos,
        ]);
        echo view('templates/footer');
    }

    // Adiciona funções para admin
    private function getEstados()
    {
        return ['Minas Gerais', 'São Paulo', 'Rio de Janeiro','Ceara', 'Parana'];
    }

    private function getUnidadesPorEstado($estado)
    {
        $unidades = [
            'Minas Gerais' => [
                'Barreiro','Betim','Contagem','Contagem Avançada','Curvelo',
                'Governador Valadares','Ipatinga','Juiz de Fora','Montes Claros',
                'Poços de Caldas','Pouso Alegre','Praça Sete','Regional BH','São Sebastião do Paraiso',
                'Sete Lagoas','Sete Lagoas Avançada','Teofilo Otoni','Uberlândia',
                'Uberlândia Avançada','Varginha'
            ],
            'São Paulo' => ['Administração Regional','Avaré','Botucatu','Capão Bonito','Caraguatatuba','Guaratingueta','Guarujá','Iguape','Itapeva','Itaquaquecetuba','Itu','Jacareí','Mogi das Cruzes','Pindamonhangaba','Piquete','Praia Grande','Registro','Santos','São José dos Campos','São Vicente','Sorocaba','Tatuí','Taubaté'],
            'Rio de Janeiro' => ['Bangu', 'Caxias'],
            'Ceara' => ['Antonio Bezerra', 'Central Administrativa','Centro Fortaleza','Juazeiro do Norte','Mesejana','Papicu','Parangaba','Sobral'],
            'Parana' => ['Administração Central','Apucarama','Arapongas','Araucaria','Campo Largo','Cascavel','Colombo','Curitiba - Boa Vista','Curitiba - Centro','Curitiba - Pinheirinho','Foz do Iguaçu','Guarapuava','Londrina','Maringa','Paranagua','Pinhais','Ponta Grossa','São José dos Pinhais','Toledo','UDS','Umurama'],
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

        $nome = $this->request->getPost('nome');
        $modelo = $this->request->getPost('modelo');
        $quantidade = (int) $this->request->getPost('quantidade');
        $unidade = session('unidade');
        $estado = session('estado');

        if (empty($nome) || empty($modelo) || $quantidade < 1) {
            return redirect()->back()->withInput()->with('erro', 'Preencha todos os campos corretamente.');
        }

        // Verifica se já existe o mesmo equipamento/modelo/unidade/estado
        $equipamentoExistente = $this->equipamentoModel
            ->where('nome', $nome)
            ->where('modelo', $modelo)
            ->where('unidade', $unidade)
            ->where('estado', $estado)
            ->first();

        if ($equipamentoExistente) {
            // Atualiza a quantidade de backup
            $novaQtd = $equipamentoExistente['quantidade_backup'] + $quantidade;
            $this->equipamentoModel->update($equipamentoExistente['id'], [
                'quantidade_backup' => $novaQtd
            ]);
            return redirect()->to('/equipamentos')->with('msg', 'Quantidade atualizada com sucesso.');
        } else {
            // Insere novo
            $this->equipamentoModel->insert([
                'nome' => $nome,
                'modelo' => $modelo,
                'quantidade_backup' => $quantidade,
                'quantidade_uso' => 0,
                'unidade' => $unidade,
                'estado' => $estado,
            ]);
            return redirect()->to('/equipamentos')->with('msg', 'Equipamento adicionado com sucesso.');
        }
    }

    private function getEquipamentosEModulos()
    {
        return [
            'Televisão'           => ['Samsung 43 Polegadas','Samsung 50 Polegadas','Philco 43 Polegadas'],

            'Notebook'            => ['Dell Inspiron 15','Lenovo ThinkPad'],

            'Impressora'          => ['HP LaserJet 1020','Epson EcoTank L3150'],

            'Pad Assinatura'      => ['AKYAMA AK560', 'Pad Assinatura Topaz'],

            'Monitor '            => ['Monitor RG DELL 24 Polegadas','Monitor AOC','Monitor Itaú Tec'],

            'Suporte'             => ['Televisão','Tablet','Câmera'],

            'Biombo'              => ['Para RG ou CNH'],

            'Leitor Biomêtrico'   => ['Leitor biométrico Akiyama Kojak AK06-12741','Leitor biométrico CNH','Leitor Biométrico Finger-Tech'],

            'Fonte para câmera'   => ['Fonte ACK-e10 Adaptador Ac Canon T3 A T7'],

            'Cabos de Energia'    =>['Desktop', 'Televisão','Rádio comunicador', 'Fortigate','Carregador de Tablet '],
            
            'Tablet de Avaliação' => ['Tablet A9', 'Tablet A7'],

            'Fita para Fixação'   => ['3M  dupla face 3 metros', '3M dupla face 2 metros','3M dupla face 1 metro'],

            'Pendrive'            => ['Sandisk Cruzer Blade 16GB','Sandisk Cruzer Blade 32GB','Sandisk Ultra Flair 64GB','Kingston DataTraveler 16GB','Kingston DataTraveler 32GB','Kingston DataTraveler 64GB','Multilaser Twist 16GB','Multilaser Twist 32GB','Sony MicroVault 16GB','Sony MicroVault 32GB'],

            'Switch'              => ["Aruba 2930F 48G PoE+", "Cisco Catalyst 2960X 50-Port PoE+", "Ubiquiti UniFi Switch 30-Port PoE"],
            'Patch Cord (Cabo de Rede)' => ['5 metros','4 metros','3 metros','2 metros','1 metro'],
            'Cabo de Imagem'           => ['HDMI','VGA '],
            'Desktop'             => ['HP i5', 'DEll i7'],
            'Patch Panel'        => ['24 Portas','12 Portas'],

        ];
    }



}
