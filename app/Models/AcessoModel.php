<?php
namespace App\Models;

use CodeIgniter\Model;

class AcessoModel extends Model
{
    protected $table = 'acesso_tecnico';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'unidade', 'departamento', 'hostname', 'guiche', 'ip_rede',
        'senha_desktop', 'senha_vnc', 'fila_impressao',
        'switch_numero', 'porta_switch', 'vlan', 'created_at'
    ];
    public $timestamps = false;
}
