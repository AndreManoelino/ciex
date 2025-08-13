<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClientes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_cliente'=> [
            
            'type'=> "INT",
            'constraint' => 9,
            'usigned' => true,
            'auto_increment' => true
            ],

            'nome' => [
                'type'=> 'VARCHAR',
                'constraint' => 128
            ],

            'data_de_nascimento' => [
                'type'=> 'DATE'
            ],
            'telefone' => [
                'type'=> 'VARCHAR',
                'constraint' => 32
            ],
            'endereco' => [
                'type'=> 'VARCHAR',
                'constraint' => 128
            ],
            
            'valor_de_servico' => [
                'type' => 'DOUBLE'
            ],
            'created_at'=> [
                'type' => 'DATETIME'
            ],
            'updated_at' => [
                'type' => 'DATETIME'
            ],
            'deleted_at' => [
                'type'=> 'DATETIME'
            ]

        ]);

        $this->forge->addKey('id_cliente', true);
        $this->forge->createTable('clientes');
    }

    public function down()
    {
        $this->forge->dropTable('clientes');
    }
}
