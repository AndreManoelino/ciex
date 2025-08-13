<?php

namespace App\Models;

use CodeIgniter\Model;

class ChamadoModel extends Model
{
    protected $table = 'incidentes';
    protected $primaryKey = 'id';
    protected $allowedFields = [
    'sistema', 'unidade', 'tecnico', 'data_inicio', 'estado', 'status', 'email_enviado', 'num_edicoes', 'data_fim', 'minutos_indisponibilidade', 'numero_chamado'];


    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    
}
