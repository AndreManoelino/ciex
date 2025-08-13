<?php
// Migration: app/Database/Migrations/2025-07-22_CreateProjetosTable.php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjetosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nome'              => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'estado'            => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'unidade'           => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'progresso'         => [
                'type'       => 'INT',
                'default'    => 0,
            ],
            'status'            => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'PENDENTE',
            ],
            'data_conclusao'    => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at'        => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at'        => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('projetos');
    }

    public function down()
    {
        $this->forge->dropTable('projetos');
    }
}
