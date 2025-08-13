<?php
namespace App\Models;

use CodeIgniter\Model;

class EquipamentoModel extends Model
{
    protected $table         = 'equipamentos';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'nome', 'modelo', 'quantidade_backup', 'quantidade_uso', 'unidade', 'estado',
        'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;

    public function getEquipamentosPorUsuario($tipo, $unidade = null, $estado = null, $filtroUnidade = null)
    {
        $builder = $this->builder();

        if ($tipo === 'admin') {
            // Admin pode filtrar por estado/unidade, mas se não selecionar vê tudo
            if (!empty($estado) && $estado !== 'BRASIL') {
                $builder->where('estado', $estado);
            }
            if (!empty($filtroUnidade)) {
                $builder->where('unidade', $filtroUnidade);
            }
        } elseif ($tipo === 'supervisor') {
            // Supervisor vê todas as unidades do seu estado
            $builder->where('estado', $estado);
            if (!empty($filtroUnidade)) {
                $builder->where('unidade', $filtroUnidade);
            }
        } else {
            // Outros usuários só veem sua própria unidade
            $builder->where('unidade', $unidade);
        }

        return $builder->get()->getResultArray();
    }


    public function alterarStatusUsoBackup($id, $acao)
    {
        $equip = $this->find($id);
        if (!$equip) return false;

        if ($acao === 'usar' && $equip['quantidade_backup'] > 0) {
            return $this->update($id, [
                'quantidade_backup' => $equip['quantidade_backup'] - 1,
                'quantidade_uso'    => $equip['quantidade_uso'] + 1
            ]);
        }

        if ($acao === 'liberar' && $equip['quantidade_uso'] > 0) {
            return $this->update($id, [
                'quantidade_backup' => $equip['quantidade_backup'] + 1,
                'quantidade_uso'    => $equip['quantidade_uso'] - 1
            ]);
        }

        return false;
    }

    public function adicionarQuantidade($id, $quantidadeAdicional)
    {
        $equip = $this->find($id);
        if (!$equip) return false;

        return $this->update($id, [
            'quantidade_backup' => $equip['quantidade_backup'] + $quantidadeAdicional
        ]);
    }
}
