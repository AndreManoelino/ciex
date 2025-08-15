<?php

if (!function_exists('get_lista_unidades')) {
    function get_lista_unidades(): array
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

if (!function_exists('render_select_unidades')) {
    function render_select_unidades(string $name = 'filtro_unidade', ?string $selecionada = null, bool $obrigatorio = false): string
    {
        $unidades = get_lista_unidades();
        $tipoUsuario = session()->get('tipo');
        $unidadeUsuario = session()->get('unidade');

        $html = '<div class="form-group col-md-6">';
        $html .= '<label for="' . $name . '">Filtrar por Unidade</label>';

        // Se for técnico, mostra apenas a unidade dele (bloqueado)
        if ($tipoUsuario === 'tecnico') {
            $html .= '<input type="text" name="' . $name . '" class="form-control" value="' . $unidadeUsuario . '" readonly>';
        } else {
            // Se for supervisor, mostra o select com todas
            $html .= '<select name="' . $name . '" class="form-control" ' . ($obrigatorio ? 'required' : '') . '>';
            $html .= '<option value="">-- Todas as Unidades --</option>';

            foreach ($unidades as $estado => $cidades) {
                $html .= '<optgroup label="' . $estado . '">';
                foreach ($cidades as $cidade) {
                    $selected = ($cidade === $selecionada) ? 'selected' : '';
                    $html .= '<option value="' . $cidade . '" ' . $selected . '>' . $cidade . '</option>';
                }
                $html .= '</optgroup>';
            }

            $html .= '</select>';
        }

        $html .= '</div>';
        return $html;
    }
}
