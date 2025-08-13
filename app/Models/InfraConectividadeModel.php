<?php



namespace App\Models;

use CodeIgniter\Model;

class InfraConectividadeModel extends Model
{
    protected $table = 'infra_conectividade';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unidade', 'estado', 'operadora',
        'banda_mb', 'valor', 'tipo_servico',
        'observacoes', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = false;
}
