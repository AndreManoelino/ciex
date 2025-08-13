<?php

namespace App\Models;

use CodeIgniter\Model;

class MovimentacaoModel extends Model
{
    protected $table = 'movimentacoes_estoque';
    protected $primaryKey = 'id';
    protected $allowedFields = ['estoque_id', 'tipo', 'quantidade', 'responsavel', 'data_movimentacao'];
    public $timestamps = false;
}
