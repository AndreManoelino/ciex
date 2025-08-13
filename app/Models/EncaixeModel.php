<?php

namespace App\Models;

use CodeIgniter\Model;

class EncaixeModel extends Model
{
    protected $table = 'encaixes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nome', 'cpf', 'horario', 'data', 'criado_em','tipo', 'estado','unidade'];

    // Retorna encaixes do mês e ano atual
    public function getEncaixesPorMesAno($mes, $ano)
    {
        return $this->where('MONTH(data)', $mes)
                    ->where('YEAR(data)', $ano)
                    ->orderBy('data', 'DESC')
                    ->findAll();
    }

    // Busca com filtro dinâmico: mês, nome, horário
    public function buscar($mes, $nome = null, $horario = null, $estado, $unidade)
    {
        $builder = $this->where('estado', $estado)
                        ->where('unidade', $unidade)
                        ->where('MONTH(data)', $mes);

        if ($nome) {
            $builder->like('nome', $nome);
        }

        if ($horario) {
            $builder->like('horario', $horario);
        }

        return $builder->findAll();
    }


    // Conta encaixes no mês
    public function contarPorMes($mes, $ano, $estado, $unidade)
    {
        return $this->where('estado', $estado)
                    ->where('unidade', $unidade)
                    ->where('MONTH(data)', $mes)
                    ->where('YEAR(data)', $ano)
                    ->countAllResults();
    }

}
