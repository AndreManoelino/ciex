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
            $cpf = preg_replace('/[^0-9]/', '', $this->request->getPost('cpf'));
            $senha = $this->request->getPost('senha');

            $user = $this->usuarioModel->findByCpfNormalized($cpf);

            if ($user) {
                if ($user['ativo'] == 0) {
                    return view('autenticador/login', ['errors' => 'Usuário inativo. Contate o administrador.']);
                }

                if ($user['senha'] === $senha) {
                    // RECARREGA OS DADOS DO BANCO PARA GARANTIR O ESTADO ATUAL
                    $user = $this->usuarioModel->find($user['id']);

                    // SE A SENHA AINDA PRECISA SER ALTERADA → REDIRECIONA
                    if ((int) $user['precisa_alterar_senha'] === 1) {
                        session()->setFlashdata('cpf', $cpf);
                        return redirect()->to('/recuperar-senha');
                    }

                    // Normaliza tipo de usuário
                    if (strtolower($user['tipo']) === 'administrador') {
                        $user['tipo'] = 'admin';
                    }

                    // Prepara sessão normal
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
    public function alterarSenha()
    {
        $usuarioModel = new \App\Models\UsuarioModel();

        if ($this->request->getMethod() === 'post') {
            $idUsuario = session()->get('user_id'); // Corrigi para pegar o ID da sessão correta
            $novaSenha = $this->request->getPost('senha');
            $confirmarSenha = $this->request->getPost('confirmar_senha');

            // Validação: As senhas devem ser iguais
            if ($novaSenha !== $confirmarSenha) {
                return redirect()->back()->with('error', 'As senhas não coincidem. Tente novamente.');
            }

            // Validação extra: tamanho mínimo da senha
            if (strlen($novaSenha) < 6) {
                return redirect()->back()->with('error', 'A senha deve ter pelo menos 6 caracteres.');
            }

            // Atualiza a senha com hash e remove o flag de alteração
            $usuarioModel->update($idUsuario, [
                'senha' => password_hash($novaSenha, PASSWORD_DEFAULT),
                'precisa_alterar_senha' => 0
            ]);

            session()->setFlashdata('success', 'Senha alterada com sucesso! Faça login novamente.');

            // Redireciona para a página inicial do sistema
            return redirect()->to(base_url('login'));
        }

        return view('auth/alterar_senha');
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
                'precisa_alterar_senha' => 1
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
            $cpf = preg_replace('/[^0-9]/', '', $this->request->getPost('cpf'));
            $senhaNova = $this->request->getPost('senha');

            $user = $this->usuarioModel->findByCpfNormalized($cpf);

            if ($user) {
                if ($user['ativo'] == 0) {
                    return view('autenticador/recuperar_senha', ['errors' => 'Usuário inativo. Recuperação de senha não permitida.']);
                }

                // Atualiza senha e define que não precisa mais alterar
                $this->usuarioModel->update($user['id'], [
                    'senha' => $senhaNova,
                    'precisa_alterar_senha' => 0
                ]);

                // Força recarregar os dados atualizados do usuário
                $user = $this->usuarioModel->find($user['id']);

                // Mensagem amigável e redirecionamento para login
                session()->setFlashdata('msg', 'Senha alterada com sucesso! Faça login com a nova senha.');
                return redirect()->to('/login');
            } else {
                return view('autenticador/recuperar_senha', ['errors' => 'CPF não encontrado']);
            }
        }

        return view('autenticador/recuperar_senha');
    }



    // Função atribuindo os estados para autenticação 
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
                'Poços de Caldas','Pouso Alegre','Praça Sete','Regional BH','São Sebastião do Paraiso',
                'Sete Lagoas','Sete Lagoas Avançada','Teofilo Otoni','Uberlândia',
                'Uberlândia Avançada','Varginha'
            ],
            'São Paulo' => ['Administração Regional','Avaré','Botucatu','Capão Bonito','Caraguatatuba','Guaratingueta','Guarujá','Iguape','Itapeva','Itaquaquecetuba','Itu','Jacareí','Mogi das Cruzes','Pindamonhangaba','Piquete','Praia Grande','Registro','Santos','São José dos Campos','São Vicente','Sorocaba','Tatuí','Taubaté'],
            'Rio de Janeiro' => ['Bangu', 'Caxias'],
            'Ceara' => ['Antonio Bezerra', 'Central Administrativa','Centro Fortaleza','Juazeiro do Norte','Mesejana','Papicu','Parangaba','Sobral'],
            'Parana' => ['Administração Central','Apucarama','Arapongas','Araucaria','Campo Largo','Cascavel','Colombo','Curitiba - Boa Vista','Curitiba - Centro','Curitiba - Pinheirinho','Foz do Iguaçu','Guarapuava','Londrina','Maringa','Paranagua','Pinhais','Ponta Grossa','São José dos Pinhais','Toledo','UDS','Umurama'],
        ];
    }

}
