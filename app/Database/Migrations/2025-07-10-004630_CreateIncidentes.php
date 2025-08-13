<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIncidentes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sistema' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'unidade' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'tecnico' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'numero_chamado' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'data_inicio' => [
                'type'       => 'DATETIME',
                'null'       => false,
            ],
            'data_fim' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
            ],
            'minutos_indisponibilidade' => [
                'type'       => 'INT',
                'null'       => true,
                'default'    => 0,
            ],
            'num_edicoes' => [
                'type'       => 'INT',
                'null'       => false,
                'default'    => 0,
            ],
            'email_enviado' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('incidentes');
    }

    public function down()
    {
        $this->forge->dropTable('incidentes');
    }
}
