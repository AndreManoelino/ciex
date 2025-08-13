<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCompras extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'auto_increment' => true],
            'nome'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'modelo'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'quantidade'  => ['type' => 'INT'],
            'link'        => ['type' => 'TEXT', 'null' => true],
            'unidade'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'estado'      => ['type' => 'VARCHAR', 'constraint' => 2],
            'usuario_id'  => ['type' => 'INT'],
            'status'      => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pendente'],
            'nf_path'     => ['type' => 'TEXT', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('compras');
    }

    public function down()
    {
        $this->forge->dropTable('compras');
    }
}
