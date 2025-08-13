<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateChamados extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                        => ['type' => 'INT', 'auto_increment' => true],
            'sistema'                   => ['type' => 'VARCHAR', 'constraint' => 100],
            'unidade'                   => ['type' => 'VARCHAR', 'constraint' => 100],
            'tecnico'                   => ['type' => 'VARCHAR', 'constraint' => 100],
            'data_inicio'               => ['type' => 'DATETIME'],
            'data_fim'                  => ['type' => 'DATETIME', 'null' => true],
            'minutos_indisponibilidade' => ['type' => 'INT', 'null' => true],
            'numero_chamado'            => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'num_edicoes'               => ['type' => 'TINYINT', 'default' => 0],
            'email_enviado'             => ['type' => 'BOOLEAN', 'default' => false],
            'created_at'                => ['type' => 'DATETIME', 'null' => true],
            'updated_at'                => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('chamados');
    }

    public function down()
    {
        $this->forge->dropTable('chamados');
    }
}
