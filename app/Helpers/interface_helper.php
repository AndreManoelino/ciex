<?php

// =====================
// FUNÇÃO PARA GERAR FORMULÁRIOS DINÂMICOS
// =====================
if (!function_exists('gerar_formulario')) {
    function gerar_formulario(array $campos = [])
    {
        $html = '';

        foreach ($campos as $campo) {
            $nome = $campo['nome'] ?? '';
            $label = $campo['label'] ?? ucfirst($nome);
            $tipo = $campo['tipo'] ?? 'text';
            $classe = $campo['class'] ?? 'form-control';
            $valor = $campo['valor'] ?? '';
            $col = $campo['col'] ?? '12';
            $readonly = !empty($campo['readonly']) ? 'readonly' : '';
            $required = !empty($campo['required']) ? 'required' : '';
            $accept = isset($campo['accept']) ? 'accept="'.$campo['accept'].'"' : '';

            $html .= "<div class=\"form-group col-md-$col\">";
            $html .= "<label for=\"$nome\">$label</label>";

            if ($tipo === 'file') {
                $html .= "<input type=\"$tipo\" name=\"$nome\" class=\"form-control-file\" $accept $required>";
            } else {
                $html .= "<input type=\"$tipo\" name=\"$nome\" class=\"$classe\" value=\"$valor\" $readonly $required>";
            }

            $html .= "</div>";
        }

        return $html;
    }
}


// =====================
// GERA BOTÕES PADRÃO
// =====================

function gerar_botoes_formulario($voltar_url = '', $textoSalvar = 'Salvar')
{
    return '
    <div class="form-group col-md-12 text-right">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> ' . $textoSalvar . '
        </button>
        <a href="' . $voltar_url . '" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>';
}

// =====================
// FORMULÁRIO COMPLETO
// =====================

function gerar_formulario_completo($action, $campos = [], $metodo = 'post', $voltar_url = '')
{
    $html  = "<form action=\"$action\" method=\"$metodo\" enctype=\"multipart/form-data\">";
    $html .= "<div class=\"row\">";
    $html .= gerar_formulario($campos);
    $html .= gerar_botoes_formulario($voltar_url);
    $html .= "</div></form>";
    return $html;
}

// =====================
// FORMATADORES
// =====================

function formatar_dinheiro($valor)
{
    return 'R$ ' . number_format(floatval($valor), 2, ',', '.');
}

function formatar_data($data, $formato = 'd/m/Y')
{
    if (!$data) return '';
    return \DateTime::createFromFormat('Y-m-d', $data)->format($formato);
}

function formatar_data_para_sql($data)
{
    if (!$data) return null;
    return \DateTime::createFromFormat('d/m/Y', $data)->format('Y-m-d');
}

function formatar_hora($hora)
{
    return date('H\hi', strtotime($hora));
}

// =====================
// LAYOUT / UTILITÁRIOS
// =====================

function sidebar_layout()
{
    return '<div class="content-wrapper" style="margin-left: 250px; padding: 20px;">';
}

function mostrar_flash_msg()
{
    $msg = session()->getFlashdata('msg');
    if ($msg) {
        return "<div class=\"alert alert-info\">$msg</div>";
    }
    return '';
}

function icone($nome)
{
    return "<i class=\"fas fa-$nome\"></i> ";
}
/**
 * Retorna um título centralizado em h2 com estilo.
 *
 * @param string $title Texto do título
 * @param string $cor Cor do texto (opcional, padrão: preto)
 * @param string $margemMargem inferior opcional para espaçamento (ex: '20px')
 * @return string HTML do título formatado
 */
function titulo_centralizado(string $title, string $cor = '#000', string $margemInferior = '20px'): string
{
    return "<h2 style=\"text-align:center; color: {$cor}; margin-bottom: {$margemInferior};\">{$title}</h2>";
}

/**
 * Retorna informações de permissão do usuário logado
 * - Técnico: vê apenas sua unidade
 * - Supervisor: vê todas as unidades do estado
 *
 * @return array ['tipo' => 'tecnico|supervisor', 'estado' => ..., 'unidades' => [...]]
 */
function getPermissaoUsuario(): array
{
    $session = session();

    $tipo    = $session->get('tipo');
    $estado  = $session->get('estado');
    $unidade = $session->get('unidade');

    // Todas as unidades disponíveis por estado
    $todasUnidades = [
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
            'Poupa Tempo Recreio dos Bandeirantes','Poupa Tempo Zona Oeste',
            'Poupa Tempo Baixada','Poupa Tempo São Gonçalo','Poupa Tempo Bangu'
        ]
    ];

    // Admin: vê tudo
    if ($tipo === 'admin') {
        return [
            'tipo' => 'admin',
            'estado' => 'TODOS',
            'unidades' => $todasUnidades,
        ];
    }

    // Supervisor: vê apenas o estado dele
    if ($tipo === 'supervisor') {
        return [
            'tipo'     => 'supervisor',
            'estado'   => $estado,
            'unidades' => $todasUnidades[$estado] ?? [],
        ];
    }

    // Técnico: vê só a unidade dele
    return [
        'tipo'     => 'tecnico',
        'estado'   => $estado,
        'unidades' => [$unidade],
    ];
}



/**
 * Valida CPF brasileiro.
 *
 * @param string $cpf CPF numérico, com ou sem pontos/traços
 * @return bool Retorna true se CPF for válido
 */
function validaCPF(string $cpf): bool
{
    // Normaliza: remove tudo que não for número
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    if (strlen($cpf) !== 11) {
        return false;
    }

    // Elimina CPFs com todos os dígitos iguais
    if (preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    for ($t = 9; $t < 11; $t++) {
        $d = 0;
        for ($c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }

    return true;
}

