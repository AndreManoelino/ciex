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

}
