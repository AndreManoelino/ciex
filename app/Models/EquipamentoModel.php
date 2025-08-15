<?php
namespace App\Models;

use CodeIgniter\Model;

class EquipamentoModel extends Model
{
    protected $table         = 'equipamentos';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'nome', 'modelo', 'quantidade_backup', 'quantidade_uso', 'unidade', 'estado',
        'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;

    public function getEquipamentosPorUsuario($tipo, $unidade = null, $estado = null, $filtroUnidade = null)
    {
        $builder = $this->builder();

        if ($tipo === 'admin') {
            // Admin pode filtrar por estado/unidade, mas se não selecionar vê tudo
            if (!empty($estado) && $estado !== 'BRASIL') {
                $builder->where('estado', $estado);
            }
            if (!empty($filtroUnidade)) {
                $builder->where('unidade', $filtroUnidade);
            }
        } elseif ($tipo === 'supervisor') {
            // Supervisor vê todas as unidades do seu estado
            $builder->where('estado', $estado);
            if (!empty($filtroUnidade)) {
                $builder->where('unidade', $filtroUnidade);
            }
        } else {
            // Outros usuários só veem sua própria unidade
            $builder->where('unidade', $unidade);
        }

        return $builder->get()->getResultArray();
    }


    public function alterarStatusUsoBackup($id, $acao)
    {
        $equip = $this->find($id);
        if (!$equip) return false;

        if ($acao === 'usar' && $equip['quantidade_backup'] > 0) {
            return $this->update($id, [
                'quantidade_backup' => $equip['quantidade_backup'] - 1,
                'quantidade_uso'    => $equip['quantidade_uso'] + 1
            ]);
        }

        if ($acao === 'liberar' && $equip['quantidade_uso'] > 0) {
            return $this->update($id, [
                'quantidade_backup' => $equip['quantidade_backup'] + 1,
                'quantidade_uso'    => $equip['quantidade_uso'] - 1
            ]);
        }

        return false;
    }

    public function adicionarQuantidade($id, $quantidadeAdicional)
    {
        $equip = $this->find($id);
        if (!$equip) return false;

        return $this->update($id, [
            'quantidade_backup' => $equip['quantidade_backup'] + $quantidadeAdicional
        ]);
    }
    public function getEquipamentosEModulos()
    {
        return [
            'Televisão'           => ['Samsung 43 Polegadas','Samsung 50 Polegadas','Philco 43 Polegadas'],

            'Notebook'            => ['Dell Inspiron 15','Lenovo ThinkPad'],

            'Impressora'          => ['HP LaserJet 1020','Epson EcoTank L3150'],

            'Pad Assinatura'      => ['AKYAMA AK560', 'Pad Assinatura Topaz'],

            'Monitor '            => ['Monitor RG DELL 24 Polegadas','Monitor AOC','Monitor Itaú Tec'],

            'Suporte'             => ['Televisão','Tablet','Câmera'],

            'Biombo'              => ['Para RG ou CNH'],

            'Leitor Biomêtrico'   => ['Leitor biométrico Akiyama Kojak AK06-12741','Leitor biométrico CNH','Leitor Biométrico Finger-Tech'],

            'Fonte para câmera'   => ['Fonte ACK-e10 Adaptador Ac Canon T3 A T7'],

            'Cabos de Energia'    =>['Desktop', 'Televisão','Rádio comunicador', 'Fortigate','Carregador de Tablet '],
            
            'Tablet de Avaliação' => ['Tablet A9', 'Tablet A7'],

            'Fita para Fixação'   => ['3M  dupla face 3 metros', '3M dupla face 2 metros','3M dupla face 1 metro'],

            'Pendrive'            => ['Sandisk Cruzer Blade 16GB','Sandisk Cruzer Blade 32GB','Sandisk Ultra Flair 64GB','Kingston DataTraveler 16GB','Kingston DataTraveler 32GB','Kingston DataTraveler 64GB','Multilaser Twist 16GB','Multilaser Twist 32GB','Sony MicroVault 16GB','Sony MicroVault 32GB'],

            'Switch'              => ["Aruba 2930F 48G PoE+", "Cisco Catalyst 2960X 50-Port PoE+", "Ubiquiti UniFi Switch 30-Port PoE"],
            'Patch Cord (Cabo de Rede)' => ['5 metros','4 metros','3 metros','2 metros','1 metro'],
            'Cabo de Imagem'           => ['HDMI','VGA '],
            'Desktop'             => ['HP i5', 'DEll i7'],
            'Patch Panel'        => ['Cat6 24 Portas  Rj45','Cat6 12 Portas  Rj45'],

        ];
    }
}
