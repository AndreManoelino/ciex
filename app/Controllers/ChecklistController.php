<?php

namespace App\Controllers;

use App\Models\ChecklistModel;
use CodeIgniter\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ChecklistController extends Controller
{
    protected $session;
    protected $checklistModel;

    public function __construct()
    {
        $this->session = session();

        if (!$this->session->get('logged_in')) {
            return redirect()->to('/auth/login')->send();
        }

        $this->checklistModel = new ChecklistModel();
    }

    public function index()
    {
        $menu = $this->request->getGet('menu') ?? 'Sistema de CFTV'; // ou passa pelo form

        // Itens do checklist
        $itens = [];
        if ($menu == "Sistema de CFTV") {
            $itens = [
                "Câmeras estão bem fixadas (sem risco de queda)?",
                "Não há obstruções no campo de visão das câmeras?",
                "Câmeras estão limpas?",
                "Infraestrutura elétrica está funcionando corretamente?",
                "Fonte de energia ou PoE está ativa?",
                "Cabos estão bem conectados nos DVRs/NVRs e câmeras?",
                "Câmeras IP estão com IPs válidos?",
                "Roteador/Switch de rede está ligado?",
                "DVR/NVR está com acesso à rede/internet?",
                "Está possível acessar o sistema via app ou navegador?",
                "Ping responde normalmente?",
                "Todas as câmeras exibem imagem ao vivo?",
                "Imagens com boa resolução?",
                "Gravação funcionando?",
                "Reproduções funcionam sem falhas?",
                "Data e hora do DVR/NVR corretas?",
                "Aquecimento excessivo nos equipamentos?",
                "Teste semanal foi feito?",
                "Tempo de retenção das imagens adequado?"
            ];
        } else {
            $itens = [
                "Cabos organizados ?",
                "Pad de Assinatura está funcionando?",
                "Leitor Biomêtrico funcionando ?",
                "Cabos soltos e desorganizados encima da mesa ?",
                "Mouse e teclado estão Funcionando perfeitamente",
                "Câmeras estão funcionando perfeitamente ?",
                "Flash ID Bio está fazendo o disparo corretamente?",
                "Pastas de Redes configuradas e compartilhadas?",
                "Backup das pastas sendo feito corretamente?",
                "Quedas diárias de internet em algum desktop?",
                "IP duplicado causando lentidão?",
                "Limpeza das pastas de rede sendo realizada?",
                "Totens estão funcionando corretamente?",
                "Verificação de totens foi realizada?"
            ];
        }

        return view('checklist', [
            'menu' => $menu,
            'itens' => $itens,
            'nome_tecnico' => $this->session->get('nome'),
        ]);
    }

    public function salvar()
    {
        $post = $this->request->getPost();

        $tipo = $post['tipo'] ?? null;
        $itens = $post['item'] ?? [];
        $statuses = $post['status'] ?? [];
        $observacoes = $post['observacao'] ?? [];
        $nome_tecnico = $post['nome_tecnico'] ?? null;
        $nome_unidade = $post['nome_unidade'] ?? null;

        if (!$nome_tecnico || !$nome_unidade) {
            return redirect()->back()->with('error', 'Por favor, preencha o nome do técnico e da unidade.');
        }

        $dataHora = date('Y-m-d H:i:s');

        foreach ($itens as $i => $item) {
            $this->checklistModel->insert([
                'tipo' => $tipo,
                'item' => $item,
                'status' => $statuses[$i] ?? null,
                'observacao' => $observacoes[$i] ?? null,
                'nome_tecnico' => $nome_tecnico,
                'nome_unidade' => $nome_unidade,
                'data_hora' => $dataHora,
            ]);
        }

        return redirect()->to('/checklist?menu=' . urlencode($tipo))->with('success', 'Checklist salvo com sucesso!');
    }

    // Função para verificar alertas baseada em respostas
    private function verificarAlertas(array $respostas, string $menu): array
    {
        $alertas = [];

        // Exemplo simples: alerta se alguma resposta for "Não" (ajuste conforme sua regra)
        foreach ($respostas as $index => $resposta) {
            if (strtolower($resposta) == 'não' || strtolower($resposta) == 'nao' || strtolower($resposta) == 'não ') {
                $alertas[] = "Item '{$index}' respondeu NÃO e requer atenção.";
            }
        }
        return $alertas;
    }

    // Exemplo básico para gerar planilha Excel com PhpSpreadsheet
    private function gerarPlanilha(array $dados, string $tipo, string $nomeUnidade)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Item');
        $sheet->setCellValue('B1', 'Status');
        $sheet->setCellValue('C1', 'Observação');
        $sheet->setCellValue('D1', 'Técnico');
        $sheet->setCellValue('E1', 'Unidade');
        $sheet->setCellValue('F1', 'Data/Hora');

        $row = 2;
        foreach ($dados as $dado) {
            $sheet->setCellValue("A{$row}", $dado['item']);
            $sheet->setCellValue("B{$row}", $dado['status']);
            $sheet->setCellValue("C{$row}", $dado['observacao']);
            $sheet->setCellValue("D{$row}", $dado['nome_tecnico']);
            $sheet->setCellValue("E{$row}", $dado['nome_unidade']);
            $sheet->setCellValue("F{$row}", $dado['data_hora']);
            $row++;
        }

        $filename = WRITEPATH . "checklist_{$tipo}_{$nomeUnidade}_" . date('Ymd_His') . ".xlsx";

        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        return $filename;
    }

    public function enviarEmail()
    {
        helper('filesystem');
        $post = $this->request->getPost();

        $tipo = $post['tipo'] ?? null;
        $itens = $post['item'] ?? [];
        $statuses = $post['status'] ?? [];
        $observacoes = $post['observacao'] ?? [];
        $nome_tecnico = $post['nome_tecnico'] ?? null;
        $nome_unidade = $post['nome_unidade'] ?? null;
        $senha_smtp = $post['senha_smtp'] ?? null;

        if (!$nome_tecnico || !$nome_unidade || !$senha_smtp) {
            return redirect()->back()->with('error', 'Por favor, preencha o nome do técnico, unidade e a senha SMTP.');
        }

        // Montar array dos dados para planilha
        $dados = [];
        $dataHora = date('Y-m-d H:i:s');
        foreach ($itens as $i => $item) {
            $dados[] = [
                'item' => $item,
                'status' => $statuses[$i] ?? null,
                'observacao' => $observacoes[$i] ?? null,
                'nome_tecnico' => $nome_tecnico,
                'nome_unidade' => $nome_unidade,
                'data_hora' => $dataHora,
            ];
        }

        $alertas = $this->verificarAlertas($statuses, $tipo);

        // Gerar arquivo Excel
        $arquivoExcel = $this->gerarPlanilha($dados, $tipo, $nome_unidade);

        // Enviar email com attachment (use PHPMailer por exemplo)
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer();

            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'seu_email@cixbrasil.com'; // seu usuário fixo aqui ou pegue da sessão/env
            $mail->Password = $senha_smtp;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('seu_email@cixbrasil.com', 'Checklist Sistema');
            $mail->addAddress('andre.manoelino@cixbrasil.com');
            $mail->addAddress('roberta.guedes@cixbrasil.com');
            $mail->addAddress('supervisaotic.uai@cixbrasil.com');
            $mail->addAddress('luiz.amorim@cixbrasil.com');

            $mail->Subject = "✅ Relatório de Checklist - {$tipo} - {$nome_unidade}";

            $corpoEmail = "Relatório de Checklist\n\n";
            $corpoEmail .= "Data e Hora: {$dataHora}\n";
            $corpoEmail .= "Técnico: {$nome_tecnico}\n";
            $corpoEmail .= "Unidade: {$nome_unidade}\n\n";

            if ($alertas) {
                $corpoEmail .= "ALERTAS:\n";
                foreach ($alertas as $alerta) {
                    $corpoEmail .= $alerta . "\n";
                }
            }

            $corpoEmail .= "\nSegue em anexo o relatório completo.\n\nAtenciosamente,\nEquipe Técnica";

            $mail->Body = $corpoEmail;

            $mail->addAttachment($arquivoExcel);

            if (!$mail->send()) {
                throw new \Exception('Erro ao enviar email: ' . $mail->ErrorInfo);
            }

            // Apagar arquivo temporário após envio
            unlink($arquivoExcel);

            return redirect()->to('/checklist?menu=' . urlencode($tipo))->with('success', 'Relatório enviado por email com sucesso!');
        } catch (\Exception $e) {
            if (file_exists($arquivoExcel)) {
                unlink($arquivoExcel);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function historico()
    {
        $filtros = [
            'unidade'     => $this->request->getGet('unidade'),
            'tecnico'     => $this->request->getGet('tecnico'),
            'data_inicio' => $this->request->getGet('data_inicio'),
            'data_fim'    => $this->request->getGet('data_fim'),
        ];

        $query = $this->checklistModel;

        if (!empty($filtros['unidade'])) {
            $query = $query->where('nome_unidade', $filtros['unidade']);
        }

        if (!empty($filtros['tecnico'])) {
            $query = $query->where('nome_tecnico', $filtros['tecnico']);
        }

        if (!empty($filtros['data_inicio'])) {
            $query = $query->where('data_hora >=', $filtros['data_inicio'] . ' 00:00:00');
        }

        if (!empty($filtros['data_fim'])) {
            $query = $query->where('data_hora <=', $filtros['data_fim'] . ' 23:59:59');
        }

        $resultados = $query->orderBy('data_hora', 'DESC')->findAll();

        return view('checklist_historico', [
            'resultados' => $resultados,
            'filtros' => $filtros,
        ]);
    }
    public function relatorio()
    {
        $model = new ChecklistModel();

        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim = $this->request->getGet('data_fim');
        $unidade = $this->request->getGet('unidade');
        $tipo = $this->request->getGet('tipo');
        $usuario = $this->session->get('nome');
        $nivel = $this->session->get('nivel'); // admin, supervisor, tecnico

        $query = $model->where('data_hora >=', $dataInicio . ' 00:00:00')
                       ->where('data_hora <=', $dataFim . ' 23:59:59');

        if ($unidade) {
            $query->where('nome_unidade', $unidade);
        }

        if ($tipo) {
            $query->where('tipo', $tipo);
        }

        // Se for técnico, filtra apenas o nome dele
        if ($nivel == 'tecnico') {
            $query->where('nome_tecnico', $usuario);
        }

        $resultados = $query->orderBy('data_hora', 'DESC')->findAll();

        return view('checklist_filtro', [
            'resultados' => $resultados
        ]);
    }


}
