<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterEstadoComprasToVarchar255 extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('compras', [
            'estado' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        // Reverte de volta para VARCHAR(2), caso necessário
        $this->forge->modifyColumn('compras', [
            'estado' => [
                'type'       => 'VARCHAR',
                'constraint' => 2,
                'null'       => false,
            ],
        ]);
    }
}
