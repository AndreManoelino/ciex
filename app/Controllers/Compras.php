<?php
// Caminho: app/Controllers/Compras.php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CompraModel;
use App\Models\EquipamentoModel;
use CodeIgniter\I18n\Time;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Compras extends BaseController
{
    protected $compraModel;
    protected $equipamentoModel;

    public function __construct()
    {
        $this->compraModel = new CompraModel();
        $this->equipamentoModel = new EquipamentoModel();
    }

    public function index()
    {
        $tipoUsuario = strtolower(session()->get('tipo'));
        $estadoUsuario = session()->get('estado');
        $unidadeUsuario = session()->get('unidade');
        $userId = session()->get('user_id');
        $filtroEstado = $this->request->getGet('estado'); // Novo filtro para admin
        $filtroUnidade = $this->request->getGet('unidade');

        $builder = $this->compraModel->builder();
        $builder->select('compras.*, users.nome as nome_usuario');
        $builder->join('users', 'users.id = compras.usuario_id', 'left');

        if ($tipoUsuario === 'supervisor') {
            // Supervisor vê apenas seu estado e unidades
            $builder->where('compras.estado', $estadoUsuario);
            if (!empty($filtroUnidade)) {
                $builder->where('compras.unidade', $filtroUnidade);
            }
        } elseif ($tipoUsuario === 'tecnico') {
            // Técnico só vê as compras dele
            $builder->where('compras.usuario_id', $userId);
        } elseif ($tipoUsuario === 'admin') {
            // Admin pode filtrar qualquer estado/unidade
            if (!empty($filtroEstado)) {
                $builder->where('compras.estado', $filtroEstado);
            }
            if (!empty($filtroUnidade)) {
                $builder->where('compras.unidade', $filtroUnidade);
            }
        } else {
            // Bloqueia outros tipos
            $builder->where('1 = 0', null, false);
        }

        $compras = $builder->get()->getResultArray();

        // Preparar lista de filtros
        $estados = [];
        $unidadesEstado = [];

        if ($tipoUsuario === 'supervisor') {
            $unidadesEstado = $this->getUnidadesPorEstado($estadoUsuario);
        } elseif ($tipoUsuario === 'admin') {
            $estados = $this->getEstados();
            if (!empty($filtroEstado)) {
                $unidadesEstado = $this->getUnidades()[$filtroEstado] ?? [];
            }
        }

        echo view('templates/header');
        echo view('compras/index', [
            'compras' => $compras,
            'tipoUsuario' => $tipoUsuario,
            'estadoFiltro' => $filtroEstado,
            'unidadeFiltro' => $filtroUnidade,
            'estados' => $estados,
            'unidades' => $unidadesEstado
        ]);
        echo view('templates/footer');
    }



    public function salvar()
    {
        $session = session();
        $nome = $this->request->getPost('nome');
        $modelo = $this->request->getPost('modelo');
        $quantidade = (int) $this->request->getPost('quantidade');
        $link = $this->request->getPost('link');
        $valor_unitario = (float) $this->request->getPost('valor_unitario');

        $unidade = $session->get('unidade');
        $estado = $session->get('estado');
        $user_id = $session->get('user_id');

        // Bloqueio por data
        $diaAtual = (int) date('d');
        if ($diaAtual < 1 || $diaAtual > 20) {
            return redirect()->back()->with('erro', 'Solicitações só podem ser feitas entre os dias 1 e 20 de cada mês.');
        }

        // Verificação de estoque existente
        $existente = $this->equipamentoModel
            ->where('nome', $nome)
            ->where('modelo', $modelo)
            ->where('unidade', $unidade)
            ->first();

        $quantidadeAtualBackup = $existente ? (int) $existente['quantidade_backup'] : 0;
        if ($quantidadeAtualBackup + $quantidade > 50) {
            $disponivel = 50 - $quantidadeAtualBackup;
            return redirect()->back()->with('erro', "Você só pode solicitar mais $disponivel desse equipamento.");
        }

        // Verificação de orçamento
        $mesAno = date('Y-m');
        $orcamentoUtilizado = $this->compraModel->getOrcamentoUtilizadoPorUnidadeMes($unidade, $mesAno);
        $orcamentoTotal = 1000;
        $valorCompra = $valor_unitario * $quantidade;
        $disponivelOrcamento = $orcamentoTotal - $orcamentoUtilizado;

        if ($valorCompra > $disponivelOrcamento) {
            return redirect()->back()->with('erro', "Orçamento insuficiente. Disponível: R$ $disponivelOrcamento");
        }

        $data = [
            'nome' => $nome,
            'modelo' => $modelo,
            'quantidade' => $quantidade,
            'valor_unitario' => $valor_unitario,
            'link' => $link,
            'unidade' => $unidade,
            'estado' => $estado,
            'usuario_id' => $user_id,
            'status' => 'pendente',
            'created_at' => Time::now(),
        ];

        $this->compraModel->insert($data);

        return redirect()->to('/compras')->with('msg', 'Solicitação registrada com sucesso.');
    }



    public function exportar()
    {
        $session = session();
        $tipoUsuario = $session->get('tipo');
        $estadoUsuario = $session->get('estado');
        $unidadeFiltro = $this->request->getGet('unidade');
        $userId = $session->get('user_id');

        // Verifica permissão
        if ($tipoUsuario !== 'supervisor') {
            return redirect()->to('/compras')->with('erro', 'Acesso negado.');
        }

        // Consulta com filtros iguais ao index()
        $builder = $this->compraModel->builder();
        $builder->select('compras.*, users.nome as nome_usuario');
        $builder->join('users', 'users.id = compras.usuario_id', 'left');
        $builder->where('compras.estado', $estadoUsuario);

        if ($unidadeFiltro) {
            $builder->where('compras.unidade', $unidadeFiltro);
        }

        $compras = $builder->get()->getResultArray();

        // Gera Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Nome');
        $sheet->setCellValue('B1', 'Modelo');
        $sheet->setCellValue('C1', 'Quantidade');
        $sheet->setCellValue('D1', 'Valor Unitário');
        $sheet->setCellValue('E1', 'Valor Total');
        $sheet->setCellValue('F1', 'Status');
        $sheet->setCellValue('G1', 'Unidade');
        $sheet->setCellValue('H1', 'Link');

        $row = 2;
        foreach ($compras as $compra) {
            $sheet->setCellValue("A$row", $compra['nome']);
            $sheet->setCellValue("B$row", $compra['modelo']);
            $sheet->setCellValue("C$row", $compra['quantidade']);
            $sheet->setCellValue("D$row", $compra['valor_unitario']);
            $sheet->setCellValue("E$row", $compra['quantidade'] * $compra['valor_unitario']);
            $sheet->setCellValue("F$row", ucfirst($compra['status']));
            $sheet->setCellValue("G$row", $compra['unidade']);
            $sheet->setCellValue("H$row", $compra['link']);
            $row++;
        }

        // Saída
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="compras_filtradas.xlsx"');
        $writer->save('php://output');
        exit;
    }
    public function marcarEntregue($id)
    {
        // Busca a compra pelo id
        $compra = $this->compraModel->find($id);

        if (!$compra) {
            return redirect()->to('/compras')->with('erro', 'Compra não encontrada.');
        }

        // Atualiza o status para 'entregue'
        $this->compraModel->update($id, ['status' => 'entregue']);

        return redirect()->to('/compras')->with('msg', 'Compra marcada como entregue com sucesso.');
    }


    public function getComprasPorPermissao($tipo, $estado, $unidade, $filtroUnidade = null)
    {
        $mesAtual = date('Y-m');
        $builder = $this->builder()
            ->where("DATE_FORMAT(created_at, '%Y-%m') =", $mesAtual);

        if ($tipo === 'supervisor') {
            $builder->where('estado', $estado);
            if ($filtroUnidade) {
                $builder->where('unidade', $filtroUnidade);
            }
        } else {
            $builder->where('unidade', $unidade);
        }

        return $builder->get()->getResultArray();
    }
    private function getUnidadesPorEstado($estado)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->select('DISTINCT(unidade)');
        $builder->where('estado', $estado);
        return $builder->get()->getResultArray(); // Aqui consigo garantir que cada unidade terá um item ['unidade' => ...]
    }


    public function enviarNF($id)
    {
        $file = $this->request->getFile('documento_nf');

        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/nfs/', $newName);

            $this->compraModel->update($id, [
                'nf_path' => $newName,
                'status' => 'enviado'
            ]);

            return redirect()->back()->with('msg', 'Nota fiscal enviada com sucesso.');
        }

        return redirect()->back()->with('erro', 'Erro ao enviar a NF.');
    }
    private function getEstados()
    {
        return ['Minas Gerais', 'São Paulo', 'Rio de Janeiro'];
    }

    private function getUnidades()
    {
        return [
            'Minas Gerais' => [
                'Barreiro','Betim','Contagem','Contagem Avançada','Curvelo',
                'Governador Valadares','Ipatinga','Juiz de Fora','Montes Claros',
                'Poços de Caldas','Pouso Alegre','Praça Sete','São Sebastião do Paraiso',
                'Sete Lagoas','Sete Lagoas Avançada','Teofilo Otoni','Uberlândia',
                'Uberlândia Avançada','Varginha'
            ],
            'São Paulo' => [
                'Poupatempo Sé', 'Poupatempo Santo Amaro', 'Poupatempo Itaquera',
                'Poupatempo Luz','Poupatempo Móvel (Cidade Tiradentes)',
                'Poupatempo Móvel (Ipiranga)','Poupatempo Guarulhos','Poupatempo Campinas'
            ],
            'Rio de Janeiro' => [
                'Poupa Tempo Recreio dos Bandeirantes', 'Poupa Tempo Zona Oeste',
                'Poupa Tempo Baixada','Poupa Tempo São Gonçalo','Poupa Tempo Bangu'
            ],
            'Ceara' => ['Unidade 1', 'Unidade 2', 'Unidade 3'],
            'Parana' => ['Unidade A', 'Unidade B', 'Unidade C'],
        ];
    }



}
