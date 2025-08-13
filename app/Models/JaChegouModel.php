<?php

namespace App\Models;

use CodeIgniter\Model;

class JaChegouModel extends Model
{
    protected $table = 'documentos_cidadao';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nome_cidadao', 'cpf', 'tipo_documento', 'codigo_entrega', 'unidade', 
        'estado', 'data_recebimento', 'data_entrega', 'recebido_por', 'entregue_por','contato'
    ];
    protected $useTimestamps = false;


    // app/Models/JaChegouModel.php
    public function getDocumentosComNome()
    {
        return $this->select('documentos_cidadao.*, users.nome AS nome_entregador')
                    ->join('users', 'users.id = documentos_cidadao.entregue_por', 'left')
                    ->where('documentos_cidadao.unidade', session()->get('unidade'))
                    ->orderBy('documentos_cidadao.data_recebimento', 'DESC')
                    ->findAll();
    }

}
