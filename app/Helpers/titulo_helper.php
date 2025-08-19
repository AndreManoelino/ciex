<?php

if (! function_exists('titulo')) {
    /**
     * Retorna um título <h2> centralizado com estilo fixo (você pode ajustar aqui).
     *
     * @param string $texto Texto do título
     * @param array $atributos Opcional, array com atributos HTML adicionais (ex: ['class' => 'minha-classe'])
     * @return string HTML do título formatado
     */
    function titulo(string $texto, array $atributos = []): string
    {
        $defaultStyle = "text-align:center; color:green; font-weight:bold; margin-bottom:20px; text-shadow:1px 1px 2px black;";

        // Permite adicionar mais atributos via array (class, id, style extra)
        $attrString = "";
        if (!empty($atributos)) {
            foreach ($atributos as $chave => $valor) {
                if ($chave === 'style') {
                    // Junta o style default com o novo
                    $valor = $defaultStyle . $valor;
                }
                $attrString .= " {$chave}=\"" . esc($valor) . "\"";
            }
        } else {
            $attrString = " style=\"$defaultStyle\"";
        }

        return "<h2{$attrString}>" . esc($texto) . "</h2>";
    }
}
