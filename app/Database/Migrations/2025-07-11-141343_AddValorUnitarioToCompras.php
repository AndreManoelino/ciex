<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddValorUnitarioToCompras extends Migration
{
    public function up()
    {
        $fields = [
            'valor_unitario' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
                'after' => 'modelo',
                'null' => false,
            ],
        ];
        $this->forge->addColumn('compras', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('compras', 'valor_unitario');
    }
}
