<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InfraConectividade extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'auto_increment' => true],
            'unidade'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'estado'       => ['type' => 'VARCHAR', 'constraint' => 2],
            'operadora'    => ['type' => 'VARCHAR', 'constraint' => 50],
            'banda_mb'     => ['type' => 'INT', 'null' => true],
            'valor'        => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'tipo_servico' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'observacoes'  => ['type' => 'TEXT', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('infra_conectividade');
    }

    public function down()
    {
        $this->forge->dropTable('infra_conectividade');
    }
}
