<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsuarios extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'auto_increment' => true],
            'nome'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'cpf'         => ['type' => 'VARCHAR', 'constraint' => 14, 'unique' => true], // login
            'email'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'senha'       => ['type' => 'VARCHAR', 'constraint' => 255], 
            'tipo'        => ['type' => 'ENUM', 'constraint' => ['tecnico', 'supervisor'], 'default' => 'tecnico'],
            'estado'      => ['type' => 'VARCHAR', 'constraint' => 50],
            'unidade'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
