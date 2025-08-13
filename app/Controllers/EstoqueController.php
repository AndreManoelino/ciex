<?php

namespace App\Controllers;

use App\Models\EstoqueModel;
use CodeIgniter\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EstoqueController extends BaseController
{
    protected $estoqueModel;

    public function __construct()
    {
        $this->estoqueModel = new EstoqueModel();
    }

    public function index()
	{
		//dd(session()->get('estado'), session()->get('unidade'));


	    $filtro = $this->request->getGet('filtro');

	    // Pega a unidade e o estado da sessão
	    $estadoUsuario = session()->get('estado');
	    $unidadeUsuario = session()->get('unidade');

	    // Filtro base: apenas os registros da unidade do usuário
	    $builder = $this->estoqueModel
	        ->where('estado', $estadoUsuario)
	        ->where('unidade', $unidadeUsuario);

	    // Filtro adicional pelo nome do produto, se for usado
	    if ($filtro) {
	        $builder->like('produto', $filtro);
	    }

	    $dados = $builder->findAll();

	    return view('estoque/index', [
	        'estoques' => $dados,
	        'filtro' => $filtro,
	    ]);
	}

    public function cadastrar()
    {
        return view('estoque/cadastrar');
    }

    public function salvar()
	{
		$estadoSessao = session()->get('estado');
    	$unidadeSessao = session()->get('unidade');

	    $data = $this->request->getPost();

	    // Pega estado e unidade da sessão — importante para filtro no index
	    $data['estado'] = session()->get('estado');
	    $data['unidade'] = session()->get('unidade');

	    // Se inventariado, entrada e saida estiverem vazios, seta 0
	    $data['inventariado'] = $data['inventariado'] ?? 0;
	    $data['entrada'] = $data['entrada'] ?? 0;
	    $data['saida'] = $data['saida'] ?? 0;

	    $data['estoque_final'] = floatval($data['inventariado']) + floatval($data['entrada']) - floatval($data['saida']);
	    $data['created_at'] = date('Y-m-d H:i:s');
	    $data['estado'] = $estadoSessao;
        $data['unidade'] = $unidadeSessao;

	    $this->estoqueModel->save($data);

	    return redirect()->to('/estoque')->with('success', 'Produto salvo com sucesso!');
	}


    public function editar($id = null)
	{
	    if (!$id) {
	        return redirect()->to('/estoque');
	    }

	    $produto = $this->estoqueModel->find($id);
	    if (!$produto) {
	        return redirect()->to('/estoque');
	    }

	    return view('estoque/editar', [
	        'produto' => $produto
	    ]);
	}

	public function atualizar($id = null)
	{
	    if (!$id) {
	        return redirect()->to('/estoque');
	    }

	    $data = $this->request->getPost();
	    unset($data['entrada'], $data['saida'], $data['estoque_final']); // REMOVER
	    $data['updated_at'] = date('Y-m-d H:i:s');

	    $this->estoqueModel->update($id, $data);
	    return redirect()->to('/estoque');
	}
	public function getEstoqueAtual($id)
	{
	    $db = \Config\Database::connect();
	    $result = $db->table('movimentacoes_estoque')
	        ->select("SUM(CASE WHEN tipo = 'ENTRADA' THEN quantidade ELSE 0 END) - SUM(CASE WHEN tipo = 'SAIDA' THEN quantidade ELSE 0 END) AS total")
	        ->where('estoque_id', $id)
	        ->get()
	        ->getRow();

	    return $result->total ?? 0;
	}



    public function exportarExcel()
	{
	    $dados = $this->estoqueModel->findAll();

	    $spreadsheet = new Spreadsheet();
	    $sheet = $spreadsheet->getActiveSheet();

	    $sheet->setTitle('Estoque');

	    $sheet->setCellValue('A1', 'Produto');
	    $sheet->setCellValue('B1', 'Especificacao');
	    $sheet->setCellValue('C1', 'Codigo');
	    $sheet->setCellValue('D1', 'Inventariado');
	    $sheet->setCellValue('E1', 'Entrada');
	    $sheet->setCellValue('F1', 'Saida');
	    $sheet->setCellValue('G1', 'Estoque Final');
	    $sheet->setCellValue('H1', 'Criado Em');

	    $linha = 2;
	    foreach ($dados as $dado) {
	        $sheet->setCellValue("A{$linha}", $dado['produto']);
	        $sheet->setCellValue("B{$linha}", $dado['especificacao']);
	        $sheet->setCellValue("C{$linha}", $dado['codigo']);
	        $sheet->setCellValue("D{$linha}", $dado['inventariado']);
	        $sheet->setCellValue("E{$linha}", $dado['entrada']);
	        $sheet->setCellValue("F{$linha}", $dado['saida']);
	        $sheet->setCellValue("G{$linha}", $dado['estoque_final']);
	        $sheet->setCellValue("H{$linha}", $dado['created_at']);
	        $linha++;
	    }

	    $writer = new Xlsx($spreadsheet);
	    $fileName = 'estoque_' . date('Ymd_His') . '.xlsx';
	    $dirPath = WRITEPATH . 'exports/';

	    // Verifica se o diretório existe, se não, cria
	    if (!is_dir($dirPath)) {
	        mkdir($dirPath, 0777, true);
	    }

	    $filePath = $dirPath . $fileName;

	    $writer->save($filePath);

	    return $this->response->download($filePath, null);
	}


    public function dashboard()
    {
        $dados = $this->estoqueModel->findAll();
        return view('estoque/dashboard', ['estoques' => $dados]);
    }
}
