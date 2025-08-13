<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEstadoStatusToIncidentes extends Migration
{
    public function up()
    {
        $fields = [
            'estado' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'null'       => false,
                'default'    => ''
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => false,
                'default'    => 'ativo'
            ],
        ];
        $this->forge->addColumn('incidentes', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('incidentes', 'estado');
        $this->forge->dropColumn('incidentes', 'status');
    }
}
