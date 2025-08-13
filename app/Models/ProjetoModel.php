<?php
namespace App\Models;

use CodeIgniter\Model;

class ProjetoModel extends Model
{
    protected $table = 'projetos';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nome',
        'descricao',
        'estado',
        'unidade',
        'progresso',
        'status',
        'tecnico_responsavel',
        'acoes',
        'data_conclusao',
        'updated_at',
        'created_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $returnType = 'array';
}
