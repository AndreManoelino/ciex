<?php
namespace App\Controllers;

use App\Models\ProjetoModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class ProjetoController extends BaseController
{
    protected $projetoModel;

    public function __construct()
    {
        $this->projetoModel = new ProjetoModel();
    }

    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        $session = session();
        $tipo = $session->get('tipo');
        $estado = $session->get('estado');
        $unidade = $session->get('unidade');

        $projetos = [];
        $unidades_estado = [];
        $estados = [];
        $mostrarConcluidos = $this->request->getGet('mostrar_concluidos') ?? '1';


        if ($tipo === 'admin') {
            // Admin pode ver todos os estados e unidades
            $estados = $this->getEstados();
            $estadoFiltro = $this->request->getGet('estado') ?? null;
            $unidadeFiltro = $this->request->getGet('unidade') ?? null;

            if ($estadoFiltro) {
                $unidades_estado = $this->getUnidades()[$estadoFiltro] ?? [];
            }

            $builder = $this->projetoModel;

            if ($estadoFiltro) {
                $builder->where('estado', $estadoFiltro);
            }
            if ($unidadeFiltro) {
                $builder->where('unidade', $unidadeFiltro);
            }

            $projetos = $builder->findAll();

        } elseif ($tipo === 'supervisor') {
            $unidades = $this->getUnidadesPorEstado($estado);
            $projetos = $this->projetoModel->whereIn('unidade', $unidades)->findAll();
            $unidades_estado = $unidades;

        } else { // Técnico
            $projetos = $this->projetoModel->where('unidade', $unidade)->findAll();
        }

        return $this->response->setContentType('text/html')->setBody(
            view('projetos/index', [
                'projetos'        => $projetos,
                'tipo'            => $tipo,
                'unidades_estado' => $unidades_estado,
                'estados'         => $estados,
                'estadoFiltro'    => $estadoFiltro ?? '',
                'unidadeFiltro'   => $unidadeFiltro ?? '',
                'mostrarConcluidos' => $mostrarConcluidos, 
            ])
        );
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
                'Poupatempo Sé', 'Poupatempo Santo Amaro', 'Poupatempo Itaquera','Poupatempo Luz',
                'Poupatempo Móvel (Cidade Tiradentes)','Poupatempo Móvel (Ipiranga)',
                'Poupatempo Guarulhos','Poupatempo Campinas'
            ],
            'Rio de Janeiro' => [
                'Poupa Tempo Recreio dos Bandeirantes', 'Poupa Tempo Zona Oeste',
                'Poupa Tempo Baixada','Poupa Tempo São Gonçalo','Poupa Tempo Bangu'
            ],
        ];
    }

    public function novo()
    {
        return view('projetos/novo');
    }

    public function criar(): RedirectResponse
    {
        $data = [
            'nome' => $this->request->getPost('nome'),
            'descricao' => $this->request->getPost('descricao'),
            'estado' => $this->request->getPost('estado'),
            'unidade' => $this->request->getPost('unidade'),
            'progresso' => 0,
            'status' => 'EM ANDAMENTO',
            'tecnico_responsavel' => session()->get('nome'),
            'acoes' => ''
        ];

        $this->projetoModel->insert($data);

        return redirect()->to('/projetos');
    }

    public function editar($id)
    {
        $projeto = $this->projetoModel->find($id);

        return view('projetos/editar', ['projeto' => $projeto]);
    }

    public function atualizar($id): RedirectResponse
    {
        $projeto = $this->projetoModel->find($id);

        $novasAcoes = $this->request->getPost('acoes');
        $dataHoje = date('d/m/Y H:i');

        $acoesAtualizadas = $projeto['acoes'] . "\n[$dataHoje] " . $novasAcoes;

        $progresso = $this->request->getPost('progresso');
        $status = $progresso == 100 ? 'CONCLUÍDO' : 'EM ANDAMENTO';

        $data = [
            'acoes' => $acoesAtualizadas,
            'progresso' => $progresso,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($progresso == 100 && !$projeto['data_conclusao']) {
            $data['data_conclusao'] = date('Y-m-d H:i:s');
        }

        $this->projetoModel->update($id, $data);

        return redirect()->to('/projetos');
    }

    public function concluidos()
    {
        $doisMesesAtras = date('Y-m-d H:i:s', strtotime('-2 months'));

        $projetos = $this->projetoModel
            ->where('status', 'CONCLUÍDO')
            ->where('data_conclusao >=', $doisMesesAtras)
            ->findAll();

        return view('projetos/concluidos', ['projetos' => $projetos]);
    }

    public function excluir($id): RedirectResponse
    {
        $this->projetoModel->delete($id);
        return redirect()->to('/projetos');
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
    public function salvar()
	{
	    $session = session();

	    // Verifica se é POST
	    if ($this->request->getMethod() !== 'POST') {
	        return redirect()->to('/projetos');
	    }

	    // Validação básica
	    $validation = \Config\Services::validation();
	    $validation->setRules([
	        'nome' => 'required',
	        'descricao' => 'required',
	        'unidade' => 'required',
	        'estado' => 'required',
	        'progresso' => 'required',
	        'status' => 'required'
	    ]);

	    if (!$validation->withRequest($this->request)->run()) {
	        return redirect()->back()->withInput()->with('errors', $validation->getErrors());
	    }

	    // Dados do formulário
	    $data = [
	        'nome'           => $this->request->getPost('nome'),
	        'descricao'      => $this->request->getPost('descricao'),
	        'unidade'        => $this->request->getPost('unidade'),
	        'estado'         => $this->request->getPost('estado'),
	        'progresso'      => $this->request->getPost('progresso'),
	        'status'         => $this->request->getPost('status'),
	        'acoes'          => $this->request->getPost('acoes'),
	        'data_conclusao' => $this->request->getPost('data_conclusao'),
	        'created_at'     => date('Y-m-d H:i:s'),
	        'updated_at'     => date('Y-m-d H:i:s'),
	    ];

	    // Salva no banco
	    $this->projetoModel->insert($data);

	    return redirect()->to('/projetos')->with('msg', 'Projeto salvo com sucesso.');
	}

}
