<?php

namespace App\Models;

use CodeIgniter\Model;

class EmprestimoModel extends Model
{
    protected $table = 'emprestimos';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'equipamento_nome',
        'quantidade',
        'unidade_origem',
        'unidade_destino',
        'data_emprestimo',
        'data_devolucao',
        'confirmado_envio',
        'termo_envio',
        'confirmado_devolucao',
        'termo_devolucao',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false; 
}
