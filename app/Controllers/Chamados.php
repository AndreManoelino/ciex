<?php
// Caminho: app/Controllers/Chamados.php

namespace App\Controllers;

require_once ROOTPATH . 'vendor/autoload.php';
use CodeIgniter\Controller;
use App\Models\ChamadoModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class Chamados extends Controller
{
    protected $chamado_model;
    protected $session;
    protected $chamadoModel;
    protected $sistemas = [
        'SDAK'         => ['email' => 'andre.manoelino@cixbrasil.com', 'envia_email' => true],
        'SS06'         => ['email' => 'agmphandre@gmail.com', 'envia_email' => true],
        'Prova Detran' => ['email' => 'andre.manoelino@cixbrasil.com', 'envia_email' => true],
        'SIP'          => ['email' => 'andre.manoelino@cixbrasil.com', 'envia_email' => true],
    ];

    public function __construct()
    {
        helper(['form', 'url']);
        $this->chamado_model = new ChamadoModel();
        $this->chamadoModel = new ChamadoModel();
        $this->session = session();
    }

    public function index()
    {
        $tipoUsuario = strtolower(session()->get('tipo'));
        $estadoUsuario = session()->get('estado');
        $unidadeUsuario = session()->get('unidade');
        $userNome = session()->get('nome');

        // Pegando filtros vindos da URL
        $filtroEstado = $this->request->getGet('estado');
        $filtroUnidade = $this->request->getGet('unidade');

        // --- CHAMADOS ATIVOS ---
        $builder = $this->chamadoModel->builder();
        $builder->select('incidentes.*')
                ->where('incidentes.status', 'ativo')
                ->where('incidentes.data_fim IS NULL');

        if ($tipoUsuario === 'supervisor') {
            $builder->where('incidentes.estado', $estadoUsuario);
            if (!empty($filtroUnidade)) {
                $builder->where('incidentes.unidade', $filtroUnidade);
            }
        } elseif ($tipoUsuario === 'tecnico') {
            $builder->where('incidentes.tecnico', $userNome);
        } elseif ($tipoUsuario === 'admin' || $tipoUsuario === 'administrador') {
            // Admin pode filtrar livremente
            if (!empty($filtroEstado) && $filtroEstado !== 'todos') {
                $builder->where('incidentes.estado', $filtroEstado);
            }
            if (!empty($filtroUnidade) && $filtroUnidade !== 'todas') {
                $builder->where('incidentes.unidade', $filtroUnidade);
            }
        } else {
            $builder->where('1 = 0', null, false); // Sem acesso
        }

        $ativos = $builder->get()->getResultArray();

        // --- CHAMADOS ENCERRADOS ---
        $recentesQuery = $this->chamadoModel->builder();
        $recentesQuery->select('incidentes.*')
                      ->where('incidentes.status', 'encerrado')
                      ->where('incidentes.data_fim IS NOT NULL', null, false);

        if ($tipoUsuario === 'supervisor') {
            $recentesQuery->where('incidentes.estado', $estadoUsuario);
            if (!empty($filtroUnidade)) {
                $recentesQuery->where('incidentes.unidade', $filtroUnidade);
            }
        } elseif ($tipoUsuario === 'tecnico') {
            $recentesQuery->where('incidentes.tecnico', $userNome);
        } elseif ($tipoUsuario === 'admin' || $tipoUsuario === 'administrador') {
            if (!empty($filtroEstado) && $filtroEstado !== 'todos') {
                $recentesQuery->where('incidentes.estado', $filtroEstado);
            }
            if (!empty($filtroUnidade) && $filtroUnidade !== 'todas') {
                $recentesQuery->where('incidentes.unidade', $filtroUnidade);
            }
        } else {
            $recentesQuery->where('1 = 0', null, false);
        }

        $recentes = $recentesQuery->get()->getResultArray();

        // --- Carrega lista de estados e unidades ---
        if ($tipoUsuario === 'admin' || $tipoUsuario === 'administrador') {
            $estados = $this->getEstados();
            $unidadesEstado = !empty($filtroEstado) ? $this->getUnidades()[$filtroEstado] ?? [] : [];
        } elseif ($tipoUsuario === 'supervisor') {
            $estados = [$estadoUsuario];
            $unidadesEstado = $this->getUnidadesPorEstado($estadoUsuario);
        } else {
            $estados = [];
            $unidadesEstado = [];
        }

        $sistemas = $this->sistemas;

        echo view('templates/header');
        echo view('chamados/index', [
            'ativos' => $ativos,
            'recentes' => $recentes,
            'tipoUsuario' => $tipoUsuario,
            'estadoUsuario' => $estadoUsuario,
            'estados' => $estados,
            'unidades' => $unidadesEstado,
            'filtroEstado' => $filtroEstado,
            'unidadeFiltro' => $filtroUnidade,
            'sistemas' => $sistemas,
        ]);
        echo view('templates/footer');
    }


    private function getUnidadesPorEstado($estado)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $builder->select('DISTINCT(unidade)');
        $builder->where('estado', $estado);
        return $builder->get()->getResultArray();
    }

    // Função para abrir chamado com autenticação por senha email
    public function abrir()
    {
        $estado = $this->session->get('estado'); // Pega o estado do usuário logado (técnico)
        $sistema     = $this->request->getPost('sistema');
        $inicio_raw  = $this->request->getPost('data_inicio') ?: date('Y-m-d H:i:s');
        $data_inicio = date('Y-m-d H:i:s', strtotime($inicio_raw));
        $tecnico     = $this->session->get('nome');
        $unidade     = $this->session->get('unidade');
        $emailUsuario = $this->session->get('email');
        $senhaEmail   = $this->request->getPost('senha_email'); 

        // Debug para verificar o estado antes de inserir
        //log_message('debug', "Abrindo chamado: estado='$estado', sistema='$sistema', técnico='$tecnico'");

        $insertData = [
            'sistema'     => $sistema,
            'unidade'     => $unidade,
            'tecnico'     => $tecnico,
            'data_inicio' => $data_inicio,
            'estado'      => $estado,
            'status'      => 'ativo',  // Garantindo que o status ele inicie como ativo 
            'email_enviado' => 0,      // Definindo um valor para os e-mail enviados
            'num_edicoes' => 0,        // Aqui criei esses paramentros para criar um limite de edições
        ];
        log_message('debug', 'Insert chamado data: ' . print_r($insertData, true));

        $this->chamado_model->insert($insertData);

        $id = $this->chamado_model->getInsertID();

        // Verificando  se deve enviar e-mail e se a senha ela foi informada 
        if (!empty($this->sistemas[$sistema]['envia_email']) && !empty($senhaEmail)) {
            $mail = new PHPMailer(true);
            try {
                $hora = (int)date('H');
                $diario = $hora < 12 ? 'Bom dia' : ($hora < 18 ? 'Boa tarde' : 'Boa noite');
                $corpo = "$diario, prezado(a).\n\n" .
                        "Sou $tecnico da unidade $unidade.\n" .
                        "O sistema **$sistema** encontra-se indisponível desde " . date('d/m/Y H:i', strtotime($data_inicio)) . ".\n" .
                        "Solicito verificação.\n\nObrigado.";

                $mail->isSMTP();
                $mail->Host = 'smtp.office365.com';
                $mail->SMTPAuth = true;
                $mail->Username = $emailUsuario;
                $mail->Password = $senhaEmail;
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';

                $mail->setFrom($emailUsuario, $tecnico);
                $mail->addAddress($this->sistemas[$sistema]['email']);
                $mail->Subject = "Indisponibilidade - $sistema ($unidade)";
                $mail->Body = $corpo;
                $mail->send();

                $this->chamado_model->update($id, ['email_enviado' => 1]);

                return redirect()->to('/chamados')->with('msg', 'Chamado registrado e e-mail enviado com sucesso!');
            } catch (Exception $e) {
                log_message('error', 'Erro PHPMailer: '. $e->getMessage());
                return redirect()->to('/chamados')->with('erro', 'Incidente salvo, mas o envio de e-mail falhou.');
            }
        }

        // Se não tem senha ou não precisa enviar, só salva
        return redirect()->to('/chamados')->with('msg', 'Incidente registrado com sucesso!');
    }

    public function encerrar($id)
    {
        $registro = $this->chamadoModel->find($id);

        if (!$registro) {
            return redirect()->to('/chamados')->with('erro', 'Chamado não encontrado.');
        }

        // Se já encerrado, evita reprocessar
        if ($registro['status'] === 'encerrado') {
            return redirect()->to('/chamados')->with('msg', 'Chamado já encerrado.');
        }

        $data_fim = date('Y-m-d H:i:s');
        $inicio = new \DateTime($registro['data_inicio']);
        $fim = new \DateTime($data_fim);
        $intervalo = $fim->diff($inicio);

        $minutos = ($intervalo->days * 24 * 60) + ($intervalo->h * 60) + $intervalo->i;

        // Debug para garantir atualização correta
        log_message('debug', "Encerrando chamado id=$id, minutos parado=$minutos");

        // Atualiza no banco: data fim, status, e minutos indisponibilidade
        $this->chamadoModel->update($id, [
            'data_fim' => $data_fim,
            'minutos_indisponibilidade' => $minutos,
            'status' => 'encerrado',
        ]);

        return redirect()->to('/chamados')->with('msg', "Chamado encerrado com sucesso. Tempo parado: {$minutos} minuto(s).");
    }



    // Salvar edição do número do chamado (limite 2 edições)
    public function salvarChamado()
    {
        $id = (int)$this->request->getPost('id');
        $num = trim($this->request->getPost('numero_chamado'));

        $registro = $this->chamado_model->find($id);

        if ($registro && $registro['num_edicoes'] < 2) {
            $this->chamado_model->update($id, [
                'numero_chamado' => $num,
                'num_edicoes' => $registro['num_edicoes'] + 1,
            ]);
            return redirect()->to('/chamados')->with('msg', 'Número do chamado atualizado.');
        }

        return redirect()->to('/chamados')->with('erro', 'Limite de edições atingido ou chamado não encontrado.');
    }

    // Tela para editar número do chamado
    public function editarNumero($id)
    {
        $chamado = $this->chamado_model->find($id);

        if (!$chamado) {
            return redirect()->to('/chamados')->with('erro', 'Chamado não encontrado.');
        }

        return view('templates/header')
            . view('templates/sidebar')
            . view('chamados/editarNumero', ['chamado' => $chamado])
            . view('templates/footer');
    }

    // Função para filtrar chamados 
    public function filtrar()
    {
        $unidade = $this->session->get('unidade');
        $sistema = $this->request->getGet('sistema');
        $data_inicio = $this->request->getGet('data_inicio');
        $data_fim = $this->request->getGet('data_fim');

        $builder = $this->chamado_model->builder();

        $builder->where('unidade', $unidade);

        if (!empty($sistema)) {
            $builder->where('sistema', $sistema);
        }

        if (!empty($data_inicio)) {
            $builder->where('data_inicio >=', $data_inicio . ' 00:00:00');
        }

        if (!empty($data_fim)) {
            $builder->where('data_inicio <=', $data_fim . ' 00:00:00');
        }

        $dados = $builder->orderBy('data_inicio', 'DESC')->get()->getResultArray();

        return view('templates/header')
            . view('templates/sidebar')
            . view('chamados/index', [
                'sistemas' => $this->sistemas,
                'ativos' => $dados,
                'recentes' => $dados,
            ])
            . view('templates/footer');
    }
    private function formatarTempo(\DateInterval $intervalo): string
    {
        $partes = [];

        if ($intervalo->d > 0) {
            $partes[] = $intervalo->d . ' dia(s)';
        }
        if ($intervalo->h > 0) {
            $partes[] = $intervalo->h . ' hora(s)';
        }
        if ($intervalo->i > 0) {
            $partes[] = $intervalo->i . ' minuto(s)';
        }

        return implode(' ', $partes) ?: '0 minuto(s)';
    }

    private function getEstados()
    {
        return ['Minas Gerais', 'São Paulo', 'Rio de Janeiro','Ceara','Parana'];
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
            'Parana' => ['Unidade A', 'Unidade B','Unidade C' ],
        ];
    }


    // Irei criar a função para exportar os chamados para um excel para que se for da necessidade ter isso salvo em outro local o usuário irá conseguir visualizar e consultar além de conseguir fazer pelo proprio programa

    // Essa atualização será feita psoteriormente  
}