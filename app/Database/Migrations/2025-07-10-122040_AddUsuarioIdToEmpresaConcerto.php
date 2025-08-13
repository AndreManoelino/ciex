<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUsuarioIdToEmpresaConcerto extends Migration
{
   public function up()
    {
        $this->forge->addColumn('empresa_concerto', [
            'usuario_id' => ['type' => 'INT', 'unsigned' => true],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('empresa_concerto', 'usuario_id');
    }

}
