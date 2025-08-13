<?php

namespace App\Models;

use CodeIgniter\Model;

class ChecklistModel extends Model
{
    protected $table            = 'checklists';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'tipo', 'item', 'status', 'observacao',
        'nome_tecnico', 'nome_unidade', 'data_hora'
    ];

    protected $useTimestamps    = true;
    protected $useSoftDeletes   = true;
}
