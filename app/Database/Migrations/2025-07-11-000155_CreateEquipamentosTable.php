<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEquipamentosTable extends Migration
{

    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'auto_increment' => true],
            'nome'          => ['type' => 'VARCHAR', 'constraint' => 100],
            'modelo'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'quantidade'    => ['type' => 'INT', 'default' => 0], // total
            'quantidade_em_uso' => ['type' => 'INT', 'default' => 0],
            'unidade'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'estado'        => ['type' => 'ENUM', 'constraint' => ['backup', 'uso'], 'default' => 'backup'],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('equipamentos');
}

}
