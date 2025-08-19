<?php

namespace App\Models;
use CodeIgniter\Model;


class UsuarioModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id','nome', 'cpf', 'email', 'senha','senha_smtp', 'tipo', 'estado','unidade','ativo'];

    protected $useTimestamps = true;


    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    public function getNomeSupervisorByUnidade($unidade)
    {
        return $this->where('unidade', $unidade)
                    ->where('tipo', 'supervisor')
                    ->select('nome')
                    ->first()['nome'] ?? 'Supervisor não encontrado';
    }
    public function findByCpfNormalized(string $cpfLimpo)
    {
        // Somente para teste porque esta query pode variar conforme o banco de dados que for usado como postgres etc 
        return $this->where("REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '')", $cpfLimpo)->first();
    }

}
