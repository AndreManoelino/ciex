<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\Controller;

class Autenticacao extends Controller
{
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        helper(['form', 'url', 'session']);
    }

    public function login()
    {
        if ($this->request->getMethod() === 'POST') {
            // Normaliza o CPF vindo do formulário (remove pontos, traços, espaços)
            $cpf = preg_replace('/[^0-9]/', '', $this->request->getPost('cpf'));
            $senha = $this->request->getPost('senha');

            // Busca usuário no banco usando método que normaliza CPF (removendo formatação do banco)
            $user = $this->usuarioModel->findByCpfNormalized($cpf);

            if ($user) {
                if ($user['ativo'] == 0) {
                    return view('autenticador/login', ['errors' => 'Usuário inativo. Contate o administrador.']);
                }

                if ($user['senha'] === $senha) {
                    if (strtolower($user['tipo']) === 'administrador') {
                        $user['tipo'] = 'admin';
                    }
                    $sessionData = [
                        'user_id'    => $user['id'],
                        'nome'       => $user['nome'],
                        'cpf'        => $user['cpf'],
                        'tipo'       => $user['tipo'],
                        'estado'     => $user['estado'],
                        'unidade'    => $user['unidade'],
                        'senha_smtp' => $user['senha_smtp'],
                        'logged_in'  => true
                    ];
                    session()->set($sessionData);
                    return redirect()->to('/inicio');
                }
            }

            return view('autenticador/login', ['errors' => 'CPF ou senha inválidos']);
        }

        return view('autenticador/login');
    }


    public function registrar()
    {
        helper('form_custom');
        helper('interface');

        if ($this->request->getMethod() === 'POST') {
            $tipo = $this->request->getPost('tipo');

            // Normaliza CPF (somente números)
            $cpfLimpo = preg_replace('/[^0-9]/', '', $this->request->getPost('cpf'));

            // Valida CPF limpo
            if (!validaCPF($cpfLimpo)) {
                $estados = $this->getEstados();
                $unidades = $this->getUnidades();
                return view('autenticador/registro', [
                    'errors' => ['CPF inválido'],
                    'estados' => $estados,
                    'unidades' => $unidades,
                ]);
            }

            $tipos_validos = ['admin','Administrador', 'administrador','supervisor', 'tecnico', 'atendente','supervisor_atendimento','atendente_rg'];
            if (!in_array($tipo, $tipos_validos)) {
                return view('autenticador/registro', [
                    'errors' => ['Tipo de usuário inválido'],
                    'estados' => $this->getEstados(),
                    'unidades' => $this->getUnidades(),
                ]);
            }

            // Verifica se já existe esse CPF (comparando normalizado)
            $usuarioExistente = $this->usuarioModel->findByCpfNormalized($cpfLimpo);
            if ($usuarioExistente) {
                return view('autenticador/registro', [
                    'errors' => ['CPF já cadastrado'],
                    'estados' => $this->getEstados(),
                    'unidades' => $this->getUnidades(),
                ]);
            }

            // Reaplica a formatação no CPF para salvar formatado
            $cpfFormatado = substr($cpfLimpo, 0, 3) . '.' . substr($cpfLimpo, 3, 3) . '.' . substr($cpfLimpo, 6, 3) . '-' . substr($cpfLimpo, 9, 2);

            $data = [
                'nome'       => $this->request->getPost('nome'),
                'cpf'        => $cpfFormatado,
                'email'      => $this->request->getPost('email'),
                'senha'      => $this->request->getPost('senha'),
                'senha_smtp' => $this->request->getPost('senha_smtp'),
                'tipo'       => $tipo,
                'ativo'      => 1,
            ];

            if ($tipo === 'supervisor') {
                $data['estado'] = $this->request->getPost('estado');
                $data['unidade'] = 'TODAS_DO_ESTADO';
            } elseif ($tipo === 'tecnico' || $tipo === 'atendente' || $tipo === 'supervisor_atendimento' || $tipo === 'atendente_rg') {
                $data['estado'] = $this->request->getPost('estado');
                $data['unidade'] = $this->request->getPost('unidade');
            } elseif ($tipo === 'admin' || $tipo === 'administrador') {
                $data['estado'] = 'BRASIL';
                $data['unidade'] = 'BRASIL';

            } else {
                $data['estado'] = '';
                $data['unidade'] = '';
            }

            log_message('debug', 'Tentando registrar: ' . json_encode($data));

            if ($this->usuarioModel->insert($data)) {
                return redirect()->to('/login')->with('msg', 'Cadastro realizado com sucesso');
            } else {
                $erros = $this->usuarioModel->errors();
                log_message('error', 'Erro ao registrar: ' . json_encode($erros));
                return view('autenticador/registro', [
                    'errors' => $erros,
                    'estados' => $this->getEstados(),
                    'unidades' => $this->getUnidades(),
                ]);
            }
        }

        $estados = $this->getEstados();
        $unidades = $this->getUnidades();

        return view('autenticador/registro', compact('estados', 'unidades'));
    }


    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function recuperarSenha()
    {
        if ($this->request->getMethod() === 'POST') {
            // Normaliza o CPF para garantir consistência
            $cpf = preg_replace('/[^0-9]/', '', $this->request->getPost('cpf'));
            $senhaNova = $this->request->getPost('senha');

            $user = $this->usuarioModel->findByCpfNormalized($cpf);


            if ($user) {
                if ($user['ativo'] == 0) {
                    return view('autenticador/recuperar_senha', ['errors' => 'Usuário inativo. Recuperação de senha não permitida.']);
                }
                
                // Atualiza a senha normalmente
                $this->usuarioModel->update($user['id'], ['senha' => $senhaNova]);
                return redirect()->to('/login')->with('msg', 'Senha alterada com sucesso');
            } else {
                return view('autenticador/recuperar_senha', ['errors' => 'CPF não encontrado']);
            }
        }

        return view('autenticador/recuperar_senha');
    }

    // Métodos auxiliares para evitar repetição
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
            'São Paulo' => ['Poupatempo Sé', 'Poupatempo Santo Amaro', 'Poupatempo Itaquera','Poupatempo Luz','Poupatempo Móvel (Cidade Tiradentes)','Poupatempo Móvel (Ipiranga)','Poupatempo Guarulhos','Poupatempo Campinas'],
            'Rio de Janeiro' => ['Poupa Tempo Recreio dos Bandeirantes', 'Poupa Tempo Zona Oeste','Poupa Tempo Baixada','Poupa Tempo São Gonçalo','Poupa Tempo Bangu'],
        ];
    }

}
