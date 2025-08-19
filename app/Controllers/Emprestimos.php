<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmprestimoModel;
use App\Models\EquipamentoModel;

class Emprestimos extends BaseController
{
    protected $emprestimoModel;
    protected $equipamentoModel;

    public function __construct()
    {
        $this->emprestimoModel = new EmprestimoModel();
        $this->equipamentoModel = new EquipamentoModel();
    }
    public function index()
    {
        $estadoFiltro = $this->request->getGet('estado_filtro');
        $unidadeFiltro = $this->request->getGet('unidade_filtro');
        $todosEstados = $this->getTodosEstados();

        $session = session();
        $tipo = $session->get('tipo');
        $unidade = $session->get('unidade');
        $estado = $session->get('estado');

        // Para admin: carrega lista de estados normalmente
        if ($tipo === 'admin') {
            $unidadesEstado = !empty($estadoFiltro) ? $this->getUnidadesPorEstado($estadoFiltro) : [];
        } else {
            $unidadesEstado = $this->getUnidadesPorEstado($estado);
        }

        // Monta a query base de empréstimos
        $query = $this->emprestimoModel;

        if ($tipo === 'admin') {
            if (!empty($estadoFiltro)) {
                $unidadesEstado = $this->getUnidadesPorEstado($estadoFiltro);

                if (!empty($unidadeFiltro)) {
                    $query->groupStart()
                          ->where('unidade_origem', $unidadeFiltro)
                          ->orWhere('unidade_destino', $unidadeFiltro)
                          ->groupEnd();
                } else {
                    $query->groupStart();
                    foreach ($unidadesEstado as $uni) {
                        $query->orWhere('unidade_origem', $uni)
                              ->orWhere('unidade_destino', $uni);
                    }
                    $query->groupEnd();
                }
            }
        } elseif ($tipo === 'supervisor') {
            $query->groupStart()
                  ->whereIn('unidade_origem', $unidadesEstado)
                  ->orWhereIn('unidade_destino', $unidadesEstado)
                  ->groupEnd();

            if (!empty($unidadeFiltro)) {
                $query->groupStart()
                      ->where('unidade_origem', $unidadeFiltro)
                      ->orWhere('unidade_destino', $unidadeFiltro)
                      ->groupEnd();
            }
        } else {
            $unidadesPermitidas = [$unidade];
            $unidadesAdicionais = $session->get('unidades_adicionais') ?? [];
            $unidadesPermitidas = array_merge($unidadesPermitidas, $unidadesAdicionais);

            $query->groupStart()
                  ->whereIn('unidade_origem', $unidadesPermitidas)
                  ->orWhereIn('unidade_destino', $unidadesPermitidas)
                  ->groupEnd();
        }

        $emprestimos = $query->orderBy('data_emprestimo', 'DESC')->findAll();

        // --- Filtragem de equipamentos ---
        if ($tipo === 'admin') {
            $equipamentosPermitidos = $this->equipamentoModel->findAll();
        } elseif ($tipo === 'supervisor') {
            // Supervisor vê equipamentos de qualquer unidade do estado dele
            $equipamentosPermitidos = $this->equipamentoModel
                ->whereIn('unidade', $unidadesEstado)
                ->findAll();
        } else {
            // Técnico vê somente equipamentos da própria unidade
            $equipamentosPermitidos = $this->equipamentoModel
                ->where('unidade', $unidade)
                ->findAll();
        }

        echo view('templates/header');
        echo view('templates/sidebar');
        echo view('emprestimos/index', [
            'emprestimos'    => $emprestimos,
            'tipoUsuario'    => $tipo,
            'unidadesEstado' => $unidadesEstado,
            'equipamentos'   => $equipamentosPermitidos,
            'estadoFiltro'   => $estadoFiltro,
            'unidadeFiltro'  => $unidadeFiltro,
            'todosEstados'   => $todosEstados
        ]);
        echo view('templates/footer');
    }


        
    // Registrar empréstimo com upload do termo de envio
    public function registrar()
    {
        $session = session();

        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/emprestimos')->with('erro', 'Método inválido.');
        }

        $equipamento_nome = $this->request->getPost('equipamento_nome');
        $quantidade = $this->request->getPost('quantidade');
        $unidade_origem = $session->get('unidade');
        $unidade_destino = $this->request->getPost('unidade_destino');
        $estado = $session->get('estado');
        $tipo = $session->get('tipo');

        // 🔹 1. Validação da unidade de destino conforme perfil
        $unidadesEstado = $this->getUnidadesPorEstado($estado);
        if ($tipo === 'admin') {
            $unidadesPermitidas = $this->getTodosEstados() ? $this->getUnidadesPorEstado($estado) : []; // admin pode tudo
        } elseif ($tipo === 'supervisor') {
            $unidadesPermitidas = $unidadesEstado; // supervisor pode qualquer unidade do estado
        } else {
            $unidadesPermitidas = [$unidade_origem];
            $unidadesAdicionais = $session->get('unidades_adicionais') ?? [];
            $unidadesPermitidas = array_merge($unidadesPermitidas, $unidadesAdicionais); // técnico pode sua unidade + adicionais
        }

        if (!in_array($unidade_destino, $unidadesPermitidas)) {
            return redirect()->back()->with('erro', 'Unidade de destino inválida para seu perfil.');
        }

        // 🔹 2. Validação do equipamento conforme perfil
        $equipamento = $this->equipamentoModel
            ->where('nome', $equipamento_nome)
            ->first();

        if (!$equipamento) {
            return redirect()->back()->with('erro', 'Equipamento não encontrado.');
        }

        if ($tipo === 'supervisor') {
            if (!in_array($equipamento['unidade'], $unidadesEstado)) {
                return redirect()->back()->with('erro', 'Você não pode emprestar este equipamento.');
            }
        } elseif ($tipo !== 'admin') {
            // técnico
            if ($equipamento['unidade'] !== $unidade_origem) {
                return redirect()->back()->with('erro', 'Você não pode emprestar este equipamento.');
            }
        }
        // Admin passa sem restrição

        // 🔹 3. Upload termo de envio
        $arquivoTermo = $this->request->getFile('termo_envio');
        if (!$arquivoTermo->isValid()) {
            return redirect()->back()->with('erro', 'Termo de envio obrigatório.');
        }
        $nomeTermoEnvio = $arquivoTermo->getRandomName();
        $arquivoTermo->move(WRITEPATH . 'uploads/termos/', $nomeTermoEnvio);

        $data_emprestimo = date('Y-m-d H:i:s');

        // 🔹 4. Inserção
        $dados = [
            'equipamento_nome' => $equipamento_nome,
            'quantidade' => $quantidade,
            'unidade_origem' => $unidade_origem,
            'unidade_destino' => $unidade_destino,
            'data_emprestimo' => $data_emprestimo,
            'confirmado_envio' => true,
            'termo_envio' => $nomeTermoEnvio,
            'confirmado_devolucao' => false,
            'termo_devolucao' => null,
            'created_at' => $data_emprestimo,
            'updated_at' => $data_emprestimo,
        ];

        $this->emprestimoModel->insert($dados);

        return redirect()->to('/emprestimos')->with('msg', 'Empréstimo registrado e termo enviado.');
    }


    // Registrar devolução com upload do termo de devolução
    public function registrarDevolucao($id)
    {
        $session = session();

        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/emprestimos')->with('erro', 'Método inválido.');
        }

        $emprestimo = $this->emprestimoModel->find($id);
        if (!$emprestimo) {
            return redirect()->back()->with('erro', 'Empréstimo não encontrado.');
        }

        $arquivoTermoDev = $this->request->getFile('termo_devolucao');
        if (!$arquivoTermoDev->isValid()) {
            return redirect()->back()->with('erro', 'Termo de devolução obrigatório.');
        }
        $nomeTermoDev = $arquivoTermoDev->getRandomName();
        $arquivoTermoDev->move(WRITEPATH . 'uploads/termos/', $nomeTermoDev);

        $data_devolucao = date('Y-m-d H:i:s');

        $this->emprestimoModel->update($id, [
            'data_devolucao' => $data_devolucao,
            'confirmado_devolucao' => true,
            'termo_devolucao' => $nomeTermoDev,
            'updated_at' => $data_devolucao,
        ]);

        return redirect()->to('/emprestimos')->with('msg', 'Devolução registrada e termo enviado.');
    }

    // Confirmar que o remetente recebeu a devolução (feedback)
    public function confirmarResolucaoDevolucao($id)
    {
        $session = session();

        $emprestimo = $this->emprestimoModel->find($id);
        if (!$emprestimo) {
            return redirect()->back()->with('erro', 'Empréstimo não encontrado.');
        }

        // Só o usuário da unidade_origem pode confirmar
        if ($session->get('unidade') !== $emprestimo['unidade_origem']) {
            return redirect()->back()->with('erro', 'Você não tem permissão para confirmar essa devolução.');
        }

        $this->emprestimoModel->update($id, ['devolucao_resolvida' => true]);

        return redirect()->to('/emprestimos')->with('msg', 'Devolução confirmada e resolvida.');
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


    private function getTodosEstados()
    {
        return [
            'Minas Gerais' => ['Barreiro','Betim','Contagem','Contagem Avançada','Curvelo',
                'Governador Valadares','Ipatinga','Juiz de Fora','Montes Claros',
                'Poços de Caldas','Pouso Alegre','Praça Sete','São Sebastião do Paraiso',
                'Sete Lagoas','Sete Lagoas Avançada','Teofilo Otoni','Uberlândia',
                'Uberlândia Avançada','Varginha'],
                
            'São Paulo' => ['Poupatempo Sé', 'Poupatempo Santo Amaro', 'Poupatempo Itaquera','Poupatempo Luz','Poupatempo Móvel (Cidade Tiradentes)','Poupatempo Móvel (Ipiranga)','Poupatempo Guarulhos','Poupatempo Campinas'],
            'Rio de Janeiro' => ['Poupa Tempo Recreio dos Bandeirantes', 'Poupa Tempo Zona Oeste','Poupa Tempo Baixada','Poupa Tempo São Gonçalo','Poupa Tempo Bangu'],

            'Ceara' => ['Antonio Bezerra', 'Central Administrativa','Centro Fortaleza','Juazeiro do Norte','Mesejana','Papicu','Parangaba','Sobral'],
            'Parana' => ['Administração Central','Apucarama','Arapongas','Araucaria','Campo Largo','Cascavel','Colombo','Curitiba - Boa Vista','Curitiba - Centro','Curitiba - Pinheirinho','Foz do Iguaçu','Guarapuava','Londrina','Maringa','Paranagua','Pinhais','Ponta Grossa','São José dos Pinhais','Toledo','UDS','Umurama'],
        ];
    }

}
