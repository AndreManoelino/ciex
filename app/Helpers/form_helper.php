<?php

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
        $readonly = isset($campo['readonly']) && $campo['readonly'] ? 'readonly' : '';
        $required = isset($campo['required']) && $campo['required'] ? 'required' : '';
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
