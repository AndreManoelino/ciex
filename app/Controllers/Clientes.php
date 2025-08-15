<?php
namespace App\Controllers;

use App\Models\EmpresaConcertoModel;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;

class Clientes extends Controller
{
    protected $session;
    protected $empresaConcertoModel;

    public function __construct()
    {
        helper(['form', 'url', 'permissao', 'localizacao', 'titulo']);
        $this->session = session();
        $this->empresaConcertoModel = new EmpresaConcertoModel();
    }

    public function novo()
    {
        helper(['localizacao', 'permissao', 'titulo']);
         $session = session();

        $tipoUsuario = getTipoUsuario();
        $estadoUsuario = getEstadoUsuario();

        // Recebe filtro GET para admin (filtrar estado/unidade)
        $filtroEstado = $this->request->getGet('estado');
        $filtroUnidade = $this->request->getGet('unidade');

        $builder = $this->empresaConcertoModel->builder();
        $builder->select('empresa_concerto.*, users.nome as tecnico, users.unidade, empresa_concerto.usuario_id');
        $builder->join('users', 'users.id = empresa_concerto.usuario_id', 'left');

        // Definir estados e unidades possíveis para filtro dropdown
        $estadosPossiveis = usuarioEhAdmin() ? getEstados() : [$estadoUsuario];
        $estadoParaFiltroUnidade = null;

        if (usuarioEhAdmin()) {
            // Se admin escolheu estado no filtro, filtra por ele
            if (!empty($filtroEstado)) {
                $builder->where('empresa_concerto.estado', $filtroEstado);
                $estadoParaFiltroUnidade = $filtroEstado;
            }
            if (!empty($filtroUnidade)) {
                $builder->where('empresa_concerto.unidade', $filtroUnidade);
            }
        } elseif (usuarioEhSupervisor()) {
            $estadoParaFiltroUnidade = $estadoUsuario;
            $builder->where('empresa_concerto.estado', $estadoUsuario);

            if (!empty($filtroUnidade)) {
                $unidadesEstado = $this->getUnidadesReaisPorEstado($estadoUsuario); // corrigi aqui também
                if (in_array($filtroUnidade, $unidadesEstado)) {
                    $builder->where('empresa_concerto.unidade', $filtroUnidade);
                }
            }
        } elseif (usuarioEhTecnico()) {
            $builder->where('empresa_concerto.usuario_id', $this->session->get('id'));
        } else {
            $builder->where('1=0', null, false); // Nenhum resultado
        }

        $solicitacoes = $builder->get()->getResultArray();
        

        // Lista de estados para dropdown
        $estados = $estadosPossiveis;

        // Lista de unidades para dropdown, depende do estado do filtro (ou do usuário)
        $unidadesPorEstado = [];
        if ($estadoParaFiltroUnidade) {
            $unidadesPorEstado = $this->getUnidadesReaisPorEstado($estadoParaFiltroUnidade);

        }

        // Lista completa de unidades para admin (opcional, se usar em outro lugar)
        $todasUnidades = [];
        if (usuarioEhAdmin()) {
            $todasUnidades = [
                'Minas Gerais' => [
                    'Barreiro','Betim','Contagem','Contagem Avançada','Curvelo',
                    'Governador Valadares','Ipatinga','Juiz de Fora','Montes Claros',
                    'Poços de Caldas','Pouso Alegre','Praça Sete','São Sebastião do Paraiso',
                    'Sete Lagoas','Sete Lagoas Avançada','Teofilo Otoni','Uberlândia',
                    'Uberlândia Avançada','Varginha',
                ],
                'São Paulo' => [
                    'Poupatempo Sé', 'Poupatempo Santo Amaro', 'Poupatempo Itaquera',
                    'Poupatempo Luz', 'Poupatempo Móvel (Cidade Tiradentes)',
                    'Poupatempo Móvel (Ipiranga)', 'Poupatempo Guarulhos', 'Poupatempo Campinas'
                ],
                'Rio de Janeiro' => [
                    'Poupa Tempo Recreio dos Bandeirantes', 'Poupa Tempo Zona Oeste',
                    'Poupa Tempo Baixada', 'Poupa Tempo São Gonçalo', 'Poupa Tempo Bangu'
                ],
                'Ceara' => ['Unidade 1', 'Unidade 2', 'Unidade 3'],
                'Parana' => ['Unidade A', 'Unidade B', 'Unidade C'],
            ];
        }

        echo view('templates/header');
        echo view('clientes/novo', [
            'solicitacoes'       => $solicitacoes,
            'tipoUsuario'        => $tipoUsuario,
            'filtroEstado'       => $filtroEstado,
            'filtroUnidade'      => $filtroUnidade,
            'estados'            => $estados,
            'estadoSelecionado'  => $filtroEstado ?: ($tipoUsuario === 'supervisor' ? $estadoUsuario : null),
            'unidadeSelecionado' => $filtroUnidade,
            'unidades'           => $unidadesPorEstado,
            'todasUnidades'      => $todasUnidades,
        ]);
        echo view('templates/footer');
    }


    public function salvar()
    {

        $arquivoOrcamento = $this->request->getFile('documento_orcamento');
        $orcamentoNome = null;

        if ($arquivoOrcamento && $arquivoOrcamento->isValid() && !$arquivoOrcamento->hasMoved()) {
            $orcamentoNome = $arquivoOrcamento->getRandomName();
            $arquivoOrcamento->move(WRITEPATH . 'uploads/orcamentos/', $orcamentoNome);
        }

        $data = [
            'usuario_id'       => $this->session->get('id'),
            'nome_empresa'     => $this->request->getPost('nome_empresa'),
            'cidade'           => $this->request->getPost('cidade'),
            'estado'           => getTipoUsuario() === 'admin' ? $this->request->getPost('estado') : getEstadoUsuario(),
            'unidade'          => getTipoUsuario() === 'admin' ? $this->request->getPost('unidade') : getUnidadeUsuario(),
            'endereco_rua'     => $this->request->getPost('endereco_rua'),
            'bairro'           => $this->request->getPost('bairro'),
            'numero'           => $this->request->getPost('numero'),
            'cnpj'             => $this->request->getPost('cnpj'),
            'nome_equipamento' => $this->request->getPost('nome_equipamento'),
            'orcamento_path'   => $orcamentoNome,
            'status'           => EmpresaConcertoModel::STATUS_AGUARDANDO,
            'created_at'       => Time::now(),
            'updated_at'       => Time::now(),
        ];

        $this->empresaConcertoModel->insert($data);

        return redirect()->to('/clientes/novo')->with('msg', 'Solicitação registrada com sucesso.');
    }

    private function getUnidadesReaisPorEstado($estado)
    {
        $query = $this->empresaConcertoModel->builder()
            ->distinct()
            ->select('empresa_concerto.unidade') // <- Aqui está o ajuste
            ->where('empresa_concerto.estado', $estado)
            ->get()
            ->getResultArray();

        return array_column($query, 'unidade');
    }


    public function aprovar($id)
    {
        $registro = $this->empresaConcertoModel->find($id);

        if (
            $this->session->get('tipo') === 'supervisor' &&
            $registro &&
            $registro['estado'] === $this->session->get('estado') &&
            $registro['status'] === EmpresaConcertoModel::STATUS_AGUARDANDO
        ) {
            $this->empresaConcertoModel->update($id, [
                'status' => EmpresaConcertoModel::STATUS_APROVADO,
                'updated_at' => Time::now(),
            ]);

            return redirect()->to('/clientes/novo')->with('msg', 'Solicitação aprovada com sucesso.');
        }

        return redirect()->back()->with('erro', 'Acesso negado ou registro inválido.');
    }

    public function enviarNF($id)
    {
        $registro = $this->empresaConcertoModel->find($id);

        if (
            $this->session->get('tipo') === 'tecnico' &&
            $registro &&
            $registro['usuario_id'] === $this->session->get('id') &&
            $registro['status'] === EmpresaConcertoModel::STATUS_APROVADO
        ) {
            $arquivoNF = $this->request->getFile('documento_nf');

            $nfNome = null;

            if ($arquivoNF && $arquivoNF->isValid() && !$arquivoNF->hasMoved()) {
                $nfNome = $arquivoNF->getRandomName();
                $arquivoNF->move(WRITEPATH . 'uploads/notas', $nfNome);

                $source = WRITEPATH . 'uploads/notas/' . $nfNome;
                $destino = FCPATH . 'uploads/nfs/' . $nfNome;

                if (!is_dir(FCPATH . 'uploads/nfs')) {
                    mkdir(FCPATH . 'uploads/nfs', 0777, true);
                }

                copy($source, $destino);
            }

            $data = [
                'data_envio' => $this->request->getPost('data_envio'),
                'nf_path'    => $nfNome,
                'status'     => EmpresaConcertoModel::STATUS_ENVIADO,
                'updated_at' => Time::now(),
            ];

            $this->empresaConcertoModel->update($id, $data);

            return redirect()->to('/clientes/novo')->with('msg', 'NF enviada com sucesso.');
        }

        return redirect()->back()->with('erro', 'Acesso negado ou registro inválido.');
    }

    public function downloadNF($filename)
    {
        $path = WRITEPATH . 'uploads/notas/' . $filename;

        if (!file_exists($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Arquivo não encontrado.");
        }

        $tipo = session('tipo');
        if (!in_array($tipo, ['tecnico', 'supervisor'])) {
            return redirect()->back()->with('erro', 'Acesso negado.');
        }

        return $this->response->download($path, null)->setFileName($filename);
    }

    public function downloadOrcamento($filename)
    {
        $path = WRITEPATH . 'uploads/orcamentos/' . $filename;

        if (!file_exists($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Orçamento não encontrado.");
        }

        $tipo = session('tipo');
        if (!in_array($tipo, ['tecnico', 'supervisor'])) {
            return redirect()->back()->with('erro', 'Acesso negado.');
        }

        return $this->response->download($path, null)->setFileName($filename);
    }
}
