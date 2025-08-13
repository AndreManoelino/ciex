<?php

namespace App\Controllers;

use App\Models\MovimentacaoModel;
use CodeIgniter\Controller;

class MovimentacaoController extends BaseController
{
    protected $movimentacaoModel;

    public function __construct()
    {
        $this->movimentacaoModel = new MovimentacaoModel();
    }
    public function index()
    {
        // Por exemplo, lista de movimentações ou redirecionamento
        return redirect()->to('/estoque');
    }


    public function registrar()
    {
        $estoqueModel = new \App\Models\EstoqueModel();
        $produtos = $estoqueModel->findAll();

        return view('movimentacao/form', [
            'produtos' => $produtos,
        ]);
    }



    public function salvar()
    {
        $estoqueModel = new \App\Models\EstoqueModel();
        $movimentacaoModel = new \App\Models\MovimentacaoModel();

        $estoque_id = $this->request->getPost('estoque_id');
        $quantidade = (int) $this->request->getPost('quantidade');
        $tipo = $this->request->getPost('tipo'); // entrada ou saida
        $responsavel = $this->request->getPost('responsavel');

        $estoque = $estoqueModel->find($estoque_id);

        if (!$estoque) {
            return redirect()->back()->with('error', 'Estoque não encontrado');
        }

        // Cálculo do novo estoque final
        if ($tipo === 'entrada') {
            $novoEstoque = $estoque['estoque_final'] + $quantidade;
            $entrada = $quantidade;
            $saida = 0;
        } else {
            $novoEstoque = $estoque['estoque_final'] - $quantidade;
            $entrada = 0;
            $saida = $quantidade;
        }

        // Salva a movimentação
        $movimentacaoModel->insert([
            'estoque_id'        => $estoque_id,
            'entrada'           => $entrada,
            'saida'             => $saida,
            'estoque_final'     => $novoEstoque,
            'responsavel'       => $responsavel,
            'data_movimentacao' => date('Y-m-d H:i:s'),
        ]);

        // Atualiza estoque final na tabela estoque, se necessário
        $estoqueModel->update($estoque_id, ['estoque_final' => $novoEstoque]);

        return redirect()->to('/movimentacao')->with('success', 'Movimentação registrada com sucesso!');
    }

}
