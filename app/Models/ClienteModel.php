<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table = "clientes";
    protected $primaryKey = "id_cliente";

    protected $allowedFields = [
        'id_cliente',
        'nome',
        'data_de_nascimento',
        'telefone',
        'endereco',
        'valor_de_servico',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Status possíveis
    public const STATUS_AGUARDANDO = 'AGUARDANDO';
    public const STATUS_APROVADO = 'APROVADO';
    public const STATUS_ENVIADO = 'ENVIADO';
}