<?php

namespace App\Models;
use CodeIgniter\Model;

class EstoqueModel extends Model
{
    protected $table = 'estoque';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'produto', 'especificacao', 'codigo',
        'inventariado', 'entrada', 'saida',
        'estoque_final', 'ultimo_inventario', 'proximo_inventario','responsavel','unidade','estado'
    ];
    protected $useTimestamps = true;
}
