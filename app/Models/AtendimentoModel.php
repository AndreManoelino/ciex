<?php namespace App\Models;

use CodeIgniter\Model;

class AtendimentoModel extends Model
{
    protected $table = 'atendimentos';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nome', 'cpf', 'numero_senha', 'codigo_atendente',
        'usuario_id', 'estado', 'unidade', 'created_at'
    ];

    public function getAtendimentosDoDia($usuarioId)
    {
        return $this->where('usuario_id', $usuarioId)
                    ->where('DATE(created_at)', date('Y-m-d'))
                    ->findAll();
    }

    public function getResumoOntem($usuarioId)
    {
        return $this->where('usuario_id', $usuarioId)
                    ->where('DATE(created_at)', date('Y-m-d', strtotime('-1 day')))
                    ->countAllResults();
    }
    public function getResumoHoje($usuarioId)
    {
        return $this->where('usuario_id', $usuarioId)
                    ->where('DATE(created_at)', date('Y-m-d'))
                    ->countAllResults();
    }


    public function getTodosDeUnidade($estado, $unidade)
    {
        return $this->where('estado', $estado)
                    ->where('unidade', $unidade)
                    ->findAll();
    }

    public function getFiltrado($estado, $unidade, $atendenteId = null, $dataInicio = null, $dataFim = null)
    {
        $builder = $this->where('estado', $estado)
                        ->where('unidade', $unidade);

        if ($atendenteId) {
            $builder->where('usuario_id', $atendenteId);
        }

        if ($dataInicio && $dataFim) {
            $builder->where('DATE(created_at) >=', $dataInicio)
                    ->where('DATE(created_at) <=', $dataFim);
        }

        return $builder->findAll();
    }
}
