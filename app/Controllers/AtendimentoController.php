<?php namespace App\Controllers;

use App\Models\AtendimentoModel;
use App\Models\UsuarioModel;
use CodeIgniter\Controller;

class AtendimentoController extends Controller
{
    protected $atendimentoModel;
    protected $usuarioModel;

    public function __construct()
    {
        helper(['url', 'form', 'session']); // helpers comuns
        $this->atendimentoModel = new AtendimentoModel();
        $this->usuarioModel = new \App\Models\UsuarioModel();
        
    }

    /**
     * Página principal dos atendimentos, dependendo do tipo de usuário
     */
    public function index()
    {
        $session = session();
        $tipo = $session->get('tipo');
        $estado = $session->get('estado');
        $unidade = $session->get('unidade');
        $usuarioId = $session->get('user_id');


        // Verifica se está logado e tipo válido
        if (!$usuarioId) {
            return redirect()->to('/login')->with('error', 'Por favor, faça login para acessar.');
        }

        $dados = [];

        if ($tipo === 'atendente_rg') {
            $dados['atendimentos'] = $this->atendimentoModel->getAtendimentosDoDia($usuarioId);
            $dados['resumo_ontem'] = $this->atendimentoModel->getResumoOntem($usuarioId);
            $dados['resumo_hoje'] = $this->atendimentoModel->getResumoHoje($usuarioId);
        } elseif ($tipo === 'supervisor_atendimento') {
            $dados['atendimentos'] = $this->atendimentoModel->getTodosDeUnidade($estado, $unidade);
            $dados['usuarios'] = $this->usuarioModel
                ->where('tipo', 'atendente_rg')
                ->where('estado', $estado)
                ->where('unidade', $unidade)
                ->findAll();
        } else {
            return redirect()->to('/painel')->with('error', 'Acesso não autorizado.');
        }

        return view('atendimentos/index', $dados);
    }

    /**
     * Salvar novo atendimento, validando sessão e dados
     */
    public function salvar()
    {
        $session = session();
        $usuarioId = $session->get('user_id');


        if (!$usuarioId) {
            return redirect()->back()->with('error', 'Usuário não autenticado.');
        }

        $data = $this->request->getPost();

        // Validação básica (pode expandir com validadores)
        if (empty($data['nome']) || empty($data['cpf']) || empty($data['numero_senha']) || empty($data['codigo_atendente'])) {
            return redirect()->back()->withInput()->with('error', 'Por favor, preencha todos os campos obrigatórios.');
        }

        $salvar = $this->atendimentoModel->save([
            'nome' => $data['nome'],
            'cpf' => $data['cpf'],
            'numero_senha' => $data['numero_senha'],
            'codigo_atendente' => $data['codigo_atendente'],
            'usuario_id' => $usuarioId,
            'estado' => $session->get('estado'),
            'unidade' => $session->get('unidade'),
        ]);

        if ($salvar) {
            return redirect()->back()->with('success', 'Atendimento registrado com sucesso!');
        } else {
            // Pega erros de validação se existirem
            $errors = $this->atendimentoModel->errors();
            return redirect()->back()->withInput()->with('error', implode(' ', $errors));
        }
    }

    /**
     * Filtrar atendimentos pela data e atendente
     */
    public function filtrar()
    {
        $session = session();
        $estado = $session->get('estado');
        $unidade = $session->get('unidade');
        $atendenteId = $this->request->getPost('usuario_id');

        $dataInicio = $this->request->getPost('data_inicio');
        $dataFim = $this->request->getPost('data_fim');

        $dados['atendimentos'] = $this->atendimentoModel->getFiltrado($estado, $unidade, $atendenteId, $dataInicio, $dataFim);

        $dados['usuarios'] = $this->usuarioModel
            ->where('tipo', 'atendente_rg')
            ->where('estado', $estado)
            ->where('unidade', $unidade)
            ->findAll();

        return view('atendimentos/index', $dados);
    }
    public function getResumoHoje($usuarioId)
    {
        return $this->where('usuario_id', $usuarioId)
                    ->where('DATE(created_at)', date('Y-m-d'))
                    ->countAllResults();
    }

}
