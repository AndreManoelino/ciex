<?php

namespace App\Models;

use CodeIgniter\Model;

class EmpresaConcertoModel extends Model
{
    protected $table = 'empresa_concerto';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'usuario_id', 'nome_empresa', 'cidade', 'estado', 'unidade',
        'endereco_rua', 'bairro', 'numero', 'cnpj',
        'nome_equipamento', 'orcamento_path', 'status', 'data_envio', 'data_retorno', 'nf_path'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public const STATUS_AGUARDANDO = 'AGUARDANDO';
    public const STATUS_APROVADO = 'APROVADO';
    public const STATUS_ENVIADO = 'ENVIADO';
}
