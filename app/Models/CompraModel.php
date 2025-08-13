<?php
namespace App\Models;

use CodeIgniter\Model;

class CompraModel extends Model
{
    protected $table = 'compras';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nome', 'modelo', 'quantidade','valor_unitario', 'link', 'unidade', 'estado',
        'usuario_id', 'status', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;

    public function getComprasPorPermissao($tipo, $estado, $unidade, $filtroUnidade = null)
    {
        $builder = $this->builder();

        if ($tipo === 'tecnico') {
            $builder->where('unidade', $unidade);
        } elseif ($tipo === 'supervisor') {
            $builder->where('estado', $estado);
            if ($filtroUnidade) {
                $builder->where('unidade', $filtroUnidade);
            }
        } else {
            $builder->where('1', '0'); // bloqueia outros tipos
        }

        return $builder->get()->getResultArray();
    }
    public function getOrcamentoUtilizadoPorUnidadeMes($unidade, $mesAno)
    {
        return $this->select('SUM(valor_unitario * quantidade) AS total_gasto')
                    ->where('unidade', $unidade)
                    ->where('DATE_FORMAT(created_at, "%Y-%m")', $mesAno)
                    ->first()['total_gasto'] ?? 0;
    }


}
