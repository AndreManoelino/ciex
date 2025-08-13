<?php

namespace App\Controllers;
use App\Models\InfraConectividadeModel;
use CodeIgniter\Controller;

class Infraestrutura extends Controller
{
    public function index()
    {
        $model = new InfraConectividadeModel();

        $filtroUnidade = $this->request->getGet('unidade');
        $dados = $filtroUnidade
            ? $model->where('unidade', $filtroUnidade)->findAll()
            : $model->findAll();

        // Coletar todas as unidades distintas para o filtro
        $unidades = $model->select('unidade')->distinct()->orderBy('unidade')->findAll();
        $listaUnidades = array_column($unidades, 'unidade');

        return view('infraestrutura/index', [
            'dados' => $dados,
            'unidades' => $listaUnidades,
            'filtroUnidade' => $filtroUnidade,
            'modoFormulario' => false, // não exibe o formulário aqui
        ]);
    }

    public function criar()
    {
        return view('infraestrutura/index', [
            'dados' => [],
            'item' => null,
            'unidades' => [],
            'action' => 'criar',
            'filtroUnidade' => '',
            'modoFormulario' => true,
            'acaoFormulario' => 'salvar',
        ]);
    }

    public function salvar()
    {
        $model = new InfraConectividadeModel();

        $model->insert([
            'unidade'       => $this->request->getPost('unidade'),
            'estado'        => $this->request->getPost('estado'),
            'operadora'     => $this->request->getPost('operadora'),
            'banda_mb'      => $this->request->getPost('banda_mb'),
            'valor'         => $this->request->getPost('valor'),
            'tipo_servico'  => $this->request->getPost('tipo_servico'),
            'observacoes'   => $this->request->getPost('observacoes'),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/infraestrutura')->with('msg', 'Registro salvo com sucesso.');
    }

    public function editar($id)
    {
        $model = new InfraConectividadeModel();
        $item = $model->find($id);

        return view('infraestrutura/index', [
            'item' => $item,
            'dados' => [],
            'unidades' => [],
            'action' => "atualizar/{$id}",
            'filtroUnidade' => '',
            'modoFormulario' => true,
            'acaoFormulario' => "atualizar/{$id}",
        ]);
    }

    public function atualizar($id)
    {
        $model = new InfraConectividadeModel();

        $model->update($id, [
            'unidade'       => $this->request->getPost('unidade'),
            'estado'        => $this->request->getPost('estado'),
            'operadora'     => $this->request->getPost('operadora'),
            'banda_mb'      => $this->request->getPost('banda_mb'),
            'valor'         => $this->request->getPost('valor'),
            'tipo_servico'  => $this->request->getPost('tipo_servico'),
            'observacoes'   => $this->request->getPost('observacoes'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/infraestrutura')->with('msg', 'Registro atualizado com sucesso.');
    }

    public function excluir($id)
    {
        $model = new InfraConectividadeModel();
        $model->delete($id);

        return redirect()->to('/infraestrutura')->with('msg', 'Registro excluído.');
    }
}
