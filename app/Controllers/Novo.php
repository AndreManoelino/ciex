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
        helper(['form', 'url', 'session', 'cpf_helper']); // helper customizado para CPF
    }

    /**
     * Tela de login e validação do usuário
     */
    public function login()
    {
        if ($this->request->getMethod() === 'POST') {
            $cpf = normalizaCPF($this->request->getPost('cpf'));
            $senha = $this->request->getPost('senha');

            $usuario = $this->usuarioModel->findByCpfNormalized($cpf);

            if ($usuario && $usuario['senha'] === $senha) {
                if ($usuario['ativo'] == 0) {
                    return view('autenticador/login', ['errors' => 'Usuário inativo. Contate o administrador.']);
                }

                $sessionData = [
                    'usuario_id' => $usuario['id'],
                    'nome'       => $usuario['nome'],
                    'cpf'        => $usuario['cpf'],
                    'cargo'      => $usuario['cargo'],
                    'estado'     => $usuario['estado'],
                    'unidade'    => $usuario['unidade'],
                    'senha_smtp' => $usuario['senha_smtp'],
                    'logado'     => true
                ];

                session()->set($sessionData);
                return redirect()->to('/inicio');
            }

            return view('autenticador/login', ['errors' => 'CPF ou senha inválidos']);
        }

        return view('autenticador/login');
    }

    /**
     * Registro de novo usuário com lógica por cargo
     */
    public function registrar()
    {
        if ($this->request->getMethod() === 'POST') {
            $cargo = $this->request->getPost('cargo');
            $cpf = normalizaCPF($this->request->getPost('cpf'));

            if (!validaCPF($cpf)) {
                return $this->erroRegistro('CPF inválido');
            }

            $cargosValidos = ['administrador', 'supervisor_tec', 'tecnico', 'gerente_atendimento', 'atendente'];
            if (!in_array($cargo, $cargosValidos)) {
                return $this->erroRegistro('Cargo inválido');
            }

            if ($this->usuarioModel->findByCpfNormalized($cpf)) {
                return $this->erroRegistro('CPF já cadastrado');
            }

            $cpfFormatado = formataCPF($cpf);

            $data = [
                'nome'       => $this->request->getPost('nome'),
                'cpf'        => $cpfFormatado,
                'email'      => $this->request->getPost('email'),
                'senha'      => $this->request->getPost('senha'), // sem criptografia
                'senha_smtp' => $this->request->getPost('senha_smtp'),
                'cargo'      => $cargo,
                'ativo'      => 1,
            ];

            // Define estado e unidade conforme o cargo
            if ($cargo === 'administrador') {
                $data['estado'] = '';
                $data['unidade'] = '';
            } elseif ($cargo === 'supervisor_tec') {
                $data['estado'] = $this->request->getPost('estado');
                $data['unidade'] = 'TODAS_DO_ESTADO';
            } else {
                $data['estado'] = $this->request->getPost('estado');
                $data['unidade'] = $this->request->getPost('unidade');
            }

            if ($this->usuarioModel->insert($data)) {
                return redirect()->to('/login')->with('msg', 'Cadastro realizado com sucesso');
            }

            return $this->erroRegistro('Erro ao salvar. Verifique os dados.');
        }

        return view('autenticador/registro', [
            'estados' => $this->getEstados(),
            'unidades' => $this->getUnidades()
        ]);
    }

    private function erroRegistro($mensagem)
    {
        return view('autenticador/registro', [
            'errors' => [$mensagem],
            'estados' => $this->getEstados(),
            'unidades' => $this->getUnidades()
        ]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function recuperarSenha()
    {
        if ($this->request->getMethod() === 'POST') {
            $cpf = normalizaCPF($this->request->getPost('cpf'));
            $senha = $this->request->getPost('senha');

            $usuario = $this->usuarioModel->findByCpfNormalized($cpf);

            if ($usuario) {
                if ($usuario['ativo'] == 0) {
                    return view('autenticador/recuperar_senha', ['errors' => 'Usuário inativo.']);
                }

                $this->usuarioModel->update($usuario['id'], ['senha' => $senha]);
                return redirect()->to('/login')->with('msg', 'Senha atualizada com sucesso');
            }

            return view('autenticador/recuperar_senha', ['errors' => 'CPF não encontrado']);
        }

        return view('autenticador/recuperar_senha');
    }

    private function getEstados()
    {
        return ['Minas Gerais', 'São Paulo', 'Rio de Janeiro'];
    }

    private function getUnidades()
    {
        return [
            'Minas Gerais' => [
                'Barreiro', 'Betim', 'Contagem', 'Curvelo', 'GV', 'Ipatinga', 'JF',
                'Montes Claros', 'Poços de Caldas', 'Pouso Alegre', 'Praça Sete',
                'Paraiso', 'Sete Lagoas', 'Teófilo Otoni', 'Uberlândia', 'Varginha'
            ],
            'São Paulo' => ['Sé', 'Santo Amaro', 'Itaquera', 'Luz', 'Guarulhos', 'Campinas'],
            'Rio de Janeiro' => ['Recreio', 'Zona Oeste', 'Baixada', 'São Gonçalo', 'Bangu']
        ];
    }
}
