<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChecklistsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'auto_increment' => true],
            'tipo'           => ['type' => 'VARCHAR', 'constraint' => 100],
            'item'           => ['type' => 'TEXT'],
            'status'         => ['type' => 'VARCHAR', 'constraint' => 20],
            'observacao'     => ['type' => 'TEXT', 'null' => true],
            'nome_tecnico'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'nome_unidade'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'data_hora'      => ['type' => 'DATETIME'],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('checklists');
    }

    public function down()
    {
        $this->forge->dropTable('checklists');
    }
}
