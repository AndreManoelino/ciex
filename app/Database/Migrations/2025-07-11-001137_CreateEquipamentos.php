<?php
// Migration: 2025-07-10_create_equipamentos.php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEquipamentos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'modelo' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'quantidade_backup' => [
                'type' => 'INT',
                'default' => 0
            ],
            'quantidade_uso' => [
                'type' => 'INT',
                'default' => 0
            ],
            'unidade' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'estado' => [
                'type' => 'VARCHAR',
                'constraint' => 2
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('equipamentos');
    }

    public function down()
    {
        $this->forge->dropTable('equipamentos');
    }
}
