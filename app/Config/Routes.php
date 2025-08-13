<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('dashboard', 'Dashboard::index');
$routes->get('inicioatendimento', 'InicioAtendimento::index');

// AUTENTICAÇÃO
$routes->get('/', 'Autenticacao::login');
$routes->match(['get', 'post'], 'login', 'Autenticacao::login');
$routes->match(['get', 'post'], 'registrar', 'Autenticacao::registrar');
$routes->match(['get', 'post'], 'recuperar-senha', 'Autenticacao::recuperarSenha');
$routes->match(['get', 'post'], 'confirmar-supervisor', 'Autenticacao::confirmarSupervisor');
$routes->match(['get', 'post'], 'confirmar_admin', 'Autenticacao::confirmarAdmin');
$routes->match(['get', 'post'], 'confirmar', 'Autenticacao::confirmarPermissao');
$routes->get('logout', 'Autenticacao::logout');

// DASHBOARD / INÍCIO
$routes->get('inicio', 'Inicio::index');

// CLIENTES
$routes->group('clientes', function ($routes) {
    $routes->get('/', 'Clientes::novo');
    $routes->get('novo', 'Clientes::novo');
    $routes->post('salvar', 'Clientes::salvar');
    $routes->get('aprovar/(:num)', 'Clientes::aprovar/$1');
    $routes->post('enviarNF/(:num)', 'Clientes::enviarNF/$1');
    $routes->get('downloadNF/(:segment)', 'Clientes::downloadNF/$1');
    $routes->get('downloadOrcamento/(:segment)', 'Clientes::downloadOrcamento/$1');
    $routes->post('store', 'Clientes::store');
});

// CHAMADOS
$routes->group('chamados', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Chamados::index');
    $routes->post('abrir', 'Chamados::abrir');
    $routes->get('encerrar/(:num)', 'Chamados::encerrar/$1');
    $routes->get('editarNumero/(:num)', 'Chamados::editarNumero/$1');
    $routes->post('salvarChamado', 'Chamados::salvarChamado');
    $routes->post('inserir', 'Chamados::inserir');
});

// EQUIPAMENTOS
$routes->group('equipamentos', function ($routes) {
    $routes->get('/', 'Equipamentos::index');
    $routes->post('salvar', 'Equipamentos::salvar');
    $routes->post('adicionar', 'Equipamentos::adicionar');
    $routes->match(['get', 'post'], 'usar/(:num)', 'Equipamentos::usar/$1');
    $routes->match(['get', 'post'], 'liberar/(:num)', 'Equipamentos::liberar/$1');
    $routes->post('atualizarStatus/(:num)', 'Equipamentos::atualizarStatus/$1');
    $routes->get('devolver/(:num)', 'Equipamentos::devolver/$1');
});

// COMPRAS
$routes->group('compras', function ($routes) {
    $routes->get('/', 'Compras::index');
    $routes->post('salvar', 'Compras::salvar');
    $routes->get('entregar/(:num)', 'Compras::marcarEntregue/$1');
    $routes->post('enviarNF/(:num)', 'Compras::enviarNF/$1');
    $routes->get('downloadNF/(:any)', 'Compras::downloadNF/$1');
    $routes->get('exportar', 'Compras::exportar');
});

// EMPRESTIMOS
$routes->group('emprestimos', function ($routes) {
    $routes->get('/', 'Emprestimos::index');
    $routes->post('adicionar-unidade', 'Emprestimos::adicionarUnidade');
    $routes->match(['get', 'post'], 'registrar', 'Emprestimos::registrar');
    $routes->post('registrar-devolucao/(:num)', 'Emprestimos::registrarDevolucao/$1');
    $routes->post('confirmar-devolucao/(:num)', 'Emprestimos::confirmarResolucaoDevolucao/$1');
});

// MAPEAMENTO DE REDE / ACESSO TÉCNICO
$routes->group('mapeamento-rede', function ($routes) {
    $routes->get('/', 'MapeamentoRede::index');
    $routes->post('salvar', 'MapeamentoRede::salvar');
    $routes->get('excluir/(:num)', 'MapeamentoRede::excluir/$1');
});
$routes->get('acesso-tecnico', 'MapeamentoRede::index');
$routes->post('acesso-tecnico/salvar', 'MapeamentoRede::salvar');







// JA CHEGOU (Filtro Atendente)
$routes->group('ja_chegou', ['filter' => 'authAtendenteSupervisor'], function ($routes) {
    $routes->get('/', 'JaChegouController::index');
    $routes->post('/', 'JaChegouController::index');
    $routes->match(['get', 'post'], 'inserir', 'JaChegouController::inserir');
    $routes->get('entregar/(:num)', 'JaChegouController::entregar/$1');
    $routes->get('buscar', 'JaChegouController::buscar');
});

$routes->group('ja_chegou', ['filter' => 'authAtendente'], function ($routes) {
    $routes->get('/', 'JaChegouController::index');
    $routes->match(['get', 'post'], 'inserir', 'JaChegouController::inserir');
    $routes->get('entregar/(:num)', 'JaChegouController::entregar/$1');
    $routes->get('buscar', 'JaChegouController::buscar');
});

// ESTOQUE (Filtro Atendente + Supervisor)
$routes->group('estoque', ['filter' => 'authAtendenteSupervisor'], function ($routes) {
    $routes->get('/', 'EstoqueController::index');
    $routes->get('cadastrar', 'EstoqueController::cadastrar');
    $routes->post('salvar', 'EstoqueController::salvar');
    $routes->get('editar/(:num)', 'EstoqueController::editar/$1');
    $routes->post('atualizar/(:num)', 'EstoqueController::atualizar/$1');
    $routes->get('exportarExcel', 'EstoqueController::exportarExcel');
});


// MOVIMENTAÇÃO
$routes->group('movimentacao', ['filter' => 'authAtendenteSupervisor'], function ($routes) {
    $routes->get('/', 'MovimentacaoController::index');
    $routes->get('registrar/(:num)', 'MovimentacaoController::registrar/$1');
    $routes->post('salvar', 'MovimentacaoController::salvar');
});

// PROJETOS
$routes->group('projetos', function ($routes) {
    $routes->get('/', 'ProjetoController::index');
    $routes->post('salvar', 'ProjetoController::salvar');
    $routes->post('salvar/(:num)', 'ProjetoController::salvar/$1');
    $routes->get('atualizar/(:num)', 'ProjetoController::atualizar/$1');
    $routes->post('atualizar/(:num)', 'ProjetoController::atualizar/$1');
    $routes->post('atualizarProgresso/(:num)', 'ProjetoController::atualizarProgresso/$1');
});

// FORMULÁRIO E CHECKLIST
$routes->get('formulario', 'FormularioController::index');
$routes->post('formulario/enviar', 'FormularioController::enviar');

$routes->group('checklist', function ($routes) {
    $routes->get('/', 'ChecklistController::index');
    $routes->get('historico', 'ChecklistController::historico');
    $routes->get('relatorio', 'ChecklistController::relatorio');
    $routes->post('enviarEmail', 'ChecklistController::enviarEmail');
});



// Encaixes

$routes->group('encaixes', ['filter' => 'authAtendenteSupervisor'], function($routes) {
    $routes->get('/', 'EncaixeController::index');
    $routes->get('criar', 'EncaixeController::criar');
    $routes->post('salvar', 'EncaixeController::salvar');
    $routes->post('criar', 'EncaixeController::criar');
});


// ROTAS DE ATENDIMENTO
$routes->group('atendimentos', ['filter' => 'authAtendenteSupervisor'], function($routes) {
    $routes->get('/', 'AtendimentoController::index');
    $routes->get('criar', 'AtendimentoController::criar');
    $routes->post('salvar', 'AtendimentoController::salvar');
    $routes->get('relatorio', 'AtendimentoController::relatorio');
    $routes->post('filtrar', 'AtendimentoController::filtrar');
});
$routes->post('atendimentos/salvar', 'AtendimentoController::salvar');

