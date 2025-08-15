<?php
// app/Helpers/localizacaoHelper.php

if (! function_exists('getEstados')) {
    function getEstados()
    {
        return ['Minas Gerais', 'São Paulo', 'Rio de Janeiro','Ceara', 'Parana'];
    }
}

if (! function_exists('getUnidadesPorEstado')) {
    function getUnidadesPorEstado($estado = null)
    {
        $lista = [
            'Minas Gerais' => [
                'Barreiro','Betim','Contagem','Contagem Avançada','Curvelo',
                'Governador Valadares','Ipatinga','Juiz de Fora','Montes Claros',
                'Poços de Caldas','Pouso Alegre','Praça Sete','São Sebastião do Paraiso',
                'Sete Lagoas','Sete Lagoas Avançada','Teofilo Otoni','Uberlândia',
                'Uberlândia Avançada','Varginha',
            ],
            'São Paulo' => ['Poupatempo Sé', 'Poupatempo Santo Amaro', 'Poupatempo Itaquera','Poupatempo Luz','Poupatempo Móvel (Cidade Tiradentes)','Poupatempo Móvel (Ipiranga)','Poupatempo Guarulhos','Poupatempo Campinas'],
            'Rio de Janeiro' => ['Poupa Tempo Recreio dos Bandeirantes', 'Poupa Tempo Zona Oeste','Poupa Tempo Baixada','Poupa Tempo São Gonçalo','Poupa Tempo Bangu'],
            'Ceara' => ['Unidade 1', 'Unidade 2', 'Unidade 3'],
            'Parana' => ['Unidade A', 'Unidade B', 'Unidade C'],
        ];

        if ($estado === null) {
            return $lista;
        }

        return $lista[$estado] ?? [];
    }
}
