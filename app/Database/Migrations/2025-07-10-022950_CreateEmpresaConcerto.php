<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmpresaConcerto extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'usuario_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => false],
            'nome_empresa'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'cidade'           => ['type' => 'VARCHAR', 'constraint' => 100],
            'estado'           => ['type' => 'VARCHAR', 'constraint' => 50],
            'unidade'          => ['type' => 'VARCHAR', 'constraint' => 100], // campo unidade
            'endereco_rua'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'bairro'           => ['type' => 'VARCHAR', 'constraint' => 150],
            'numero'           => ['type' => 'VARCHAR', 'constraint' => 20],
            'cnpj'             => ['type' => 'VARCHAR', 'constraint' => 20],
            'nome_equipamento' => ['type' => 'VARCHAR', 'constraint' => 255],
            'orcamento_path'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'           => ['type' => 'ENUM', 'constraint' => ['AGUARDANDO', 'APROVADO', 'ENVIADO'], 'default' => 'AGUARDANDO'],
            'data_envio'       => ['type' => 'DATE', 'null' => true],
            'data_retorno'     => ['type' => 'DATE', 'null' => true],
            'nf_path'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('empresa_concerto');
    }

    public function down()
    {
        $this->forge->dropTable('empresa_concerto');
    }
}
