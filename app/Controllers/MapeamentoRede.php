<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AcessoModel;

class MapeamentoRede extends BaseController
{
    protected $acessoModel;

    public function __construct()
    {
        $this->acessoModel = new AcessoModel();
    }

    public function index()
    {
        $session = session();
        $tipo = $session->get('tipo');
        $unidade = $session->get('unidade');
        $estado = $session->get('estado');

        $unidadesEstado = $this->getUnidadesPorEstado($estado);
        $unidadeFiltro = $this->request->getGet('unidade');
        $editarId = $this->request->getGet('editar');

        $query = $this->acessoModel;

        // Filtro de visualização
        if ($tipo === 'supervisor') {
            if ($unidadeFiltro) {
                $query->where('unidade', $unidadeFiltro);
            } else {
                $query->whereIn('unidade', $unidadesEstado);
            }
        } else {
            $query->where('unidade', $unidade);
        }

        $acessos = $query->orderBy('id', 'DESC')->findAll();

        // Verifica se há item para edição
        $editarAcesso = null;
        if ($editarId) {
            $acesso = $this->acessoModel->find($editarId);
            if ($acesso) {
                // Supervisor pode editar qualquer um do estado
                if ($tipo === 'supervisor' && in_array($acesso['unidade'], $unidadesEstado)) {
                    $editarAcesso = $acesso;
                }
                // Técnico só pode editar da sua própria unidade
                if ($tipo !== 'supervisor' && $acesso['unidade'] === $unidade) {
                    $editarAcesso = $acesso;
                }
            }
        }

        return view('acesso/index', [
            'acessos' => $acessos,
            'tipoUsuario' => $tipo,
            'unidadesEstado' => $unidadesEstado,
            'unidadeFiltro' => $unidadeFiltro ?? '',
            'editarAcesso' => $editarAcesso,
        ]);
    }


    public function salvar()
    {
        $session = session();
        $tipo = $session->get('tipo');
        $unidadeUsuario = $session->get('unidade');

        $dados = $this->request->getPost();

        if ($tipo !== 'supervisor') {
            $dados['unidade'] = $unidadeUsuario; // técnico não pode forçar unidade
        }

        $dados['created_at'] = date('Y-m-d H:i:s');
        $this->acessoModel->save($dados);

        return redirect()->to('/mapeamento-rede')->with('msg', 'Registro salvo com sucesso!');
    }

    public function excluir($id)
    {
        if (!$id) {
            return redirect()->to('/mapeamento-rede')->with('erro', 'ID inválido.');
        }

        $excluido = $this->acessoModel->delete($id);

        if ($excluido) {
            return redirect()->to('/mapeamento-rede')->with('msg', 'Registro excluído com sucesso.');
        } else {
            return redirect()->to('/mapeamento-rede')->with('erro', 'Erro ao excluir registro.');
        }
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
            'Ceara' => ['Unidade 1', 'Unidade 2', 'Unidade 3'],
            'Parana' => ['Unidade A', 'Unidade B', 'Unidade C'],
        ];
        return $unidades[$estado] ?? [];
    }

}
