<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper" style="margin-left: 250px; padding: 20px;">
  <div class="container-fluid">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h2 class="text-center mb-5">Controle de Estoque</h2>
          <a href="<?= base_url('/estoque/exportarExcel') ?>" class="btn btn-outline-primary me-2">
            Exportar Excel
          </a>
          <a href="<?= base_url('/estoque/cadastrar') ?>" class="btn btn-success">Novo Produto</a>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-striped table-sm">
            <thead class="table-light">
              <tr>
                <th>Produto</th>
                <th>Especificação</th>
                <th>Código</th>
                <th>Inventariado</th>
                <th>Entrada</th>
                <th>Saída</th>
                <th>Responsável</th>
                <th>Estoque Final</th>
                <th>Último Inventário</th>
                <th>Próximo Inventário</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($estoques as $item): ?>
                <?php
                    $movModel = new \App\Models\MovimentacaoModel();
                    $entrada = $movModel->where(['estoque_id' => $item['id'], 'tipo' => 'ENTRADA'])->selectSum('quantidade')->first()['quantidade'] ?? 0;
                    $saida   = $movModel->where(['estoque_id' => $item['id'], 'tipo' => 'SAIDA'])->selectSum('quantidade')->first()['quantidade'] ?? 0;
                    $responsavel = $movModel->where(['estoque_id' => $item['id']])->orderBy('id', 'DESC')->first()['responsavel'] ?? '';
                    $estoqueFinal = $item['inventariado'] + $entrada - $saida;
                ?>
                <tr>
                  <td><?= esc($item['produto']) ?></td>
                  <td><?= esc($item['especificacao']) ?></td>
                  <td><?= esc($item['codigo']) ?></td>
                  <td><?= esc($item['inventariado']) ?></td>
                  <td><?= $entrada ?></td>
                  <td><?= $saida ?></td>
                  <td><?= esc($responsavel) ?></td>
                  <td><?= $estoqueFinal ?></td>
                  <td><?= esc($item['ultimo_inventario'] ?? '—') ?></td>
                  <td><?= esc($item['proximo_inventario'] ?? '—') ?></td>
                  <td>
                    <a href="<?= base_url('/movimentacao/registrar/' . $item['id']) ?>" class="btn btn-sm btn-outline-info">
                      Movimentar
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>

<?= $this->include('templates/footer') ?>
