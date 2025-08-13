<?= $this->include('templates/sidebar') ?>

<style>
  body {
    background: linear-gradient(135deg, #f5fff5, #fff7e0);
    font-family: "Segoe UI", sans-serif;
  }

  h2 {
    text-align: center;
    color: #212529;
    font-weight: bold;
    margin-bottom: 25px;
  }

  input.form-control,
  select.form-control,
  .form-control-file {
    height: 30px !important;
    padding: 4px 8px !important;
    font-size: 14px !important;
  }

  label {
    font-size: 14px !important;
    font-weight: 600;
    margin-bottom: 4px !important;
  }

  button.btn {
    padding: 5px 14px !important;
    font-size: 14px !important;
    font-weight: bold;
  }

  .btn-success {
    background-color: #28a745;
    border-color: #28a745;
  }

  .btn-success:hover {
    background-color: #ff9800;
    border-color: #ff9800;
  }

  .btn-primary {
    background-color: #ff9800;
    border-color: #ff9800;
  }

  .btn-primary:hover {
    background-color: #28a745;
    border-color: #28a745;
  }

  .form-control-file {
    font-size: 13px;
  }

  .card-header {
    background-color: #f0fdf4;
    font-weight: bold;
  }

  .badge-warning {
    background-color: #ffc107;
  }

  .badge-success {
    background-color: #28a745;
  }

  .badge-info {
    background-color: #17a2b8;
  }

  footer {
    margin-top: 30px;
    background-color: green;
    color: white;
    text-align: center;
    padding: 12px;
    font-size: 13px;
    font-weight: bold;
    box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.15);
  }
</style>


<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">

      <h2>Compras Mensais</h2>

      <!-- Mensagens de feedback -->
      <?php if (session()->getFlashdata('msg')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('msg') ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('erro')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('erro') ?></div>
      <?php endif; ?>

      <!-- Filtro por unidade para supervisor -->
      <?php if (session('tipo') === 'supervisor'): ?>
        <form method="get" class="mb-4">
          <label for="filtro_unidade">Filtrar por Unidade:</label>
          <select name="unidade" class="form-control" onchange="this.form.submit()">
            <option value="">Todas as unidades</option>
            <?php foreach ($unidades as $u): ?>
                <?php $nomeUnidade = is_array($u) ? $u['unidade'] : $u; ?>
                <option value="<?= esc($nomeUnidade) ?>" <?= ($unidadeFiltro === $nomeUnidade) ? 'selected' : '' ?>>
                    <?= esc($nomeUnidade) ?>
                </option>
            <?php endforeach; ?>
          </select>
        </form>
      <?php endif; ?>

      <!-- Formulário de nova compra para técnico -->
      <?php if (session('tipo') === 'tecnico'): ?>
        <div class="card mb-4">
          <div class="card-header">Nova Solicitação de Compra</div>
          <div class="card-body">
            <form method="post" action="<?= base_url('/compras/salvar') ?>">
              <?= csrf_field() ?>
              <div class="row">
                <div class="col-md-3">
                  <label>Nome do Equipamento</label>
                  <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="col-md-3">
                  <label>Modelo</label>
                  <input type="text" name="modelo" class="form-control" required>
                </div>
                <div class="col-md-2">
                  <label>Quantidade</label>
                  <input type="number" name="quantidade" class="form-control" min="1" required>
                </div>
                <div class="col-md-2">
                  <label>Valor Unitário (R$)</label>
                  <input type="number" name="valor_unitario" class="form-control" min="0" step="0.01" required>
                </div>
                <div class="col-md-2">
                  <label>Link de Referência</label>
                  <input type="url" name="link" class="form-control" placeholder="URL">
                </div>
              </div>
              <button class="btn btn-success mt-3">Enviar Solicitação</button>
            </form>
          </div>
        </div>
      <?php endif; ?>
      <?php if ($tipoUsuario === 'admin'): ?>
        <form method="get" action="">
            <label>Estado:</label>
            <select name="estado" onchange="this.form.submit()">
                <option value="">Todos</option>
                <?php foreach ($estados as $estado): ?>
                    <option value="<?= $estado ?>" <?= ($estadoFiltro == $estado) ? 'selected' : '' ?>>
                        <?= $estado ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if (!empty($unidades)): ?>
                <label>Unidade:</label>
                <select name="unidade" onchange="this.form.submit()">
                    <option value="">Todas</option>
                    <?php foreach ($unidades as $unidade): ?>
                        <option value="<?= $unidade ?>" <?= ($unidadeFiltro == $unidade) ? 'selected' : '' ?>>
                            <?= $unidade ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </form>
        <?php endif; ?>


      <!-- Tabela de compras -->
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span>Solicitações de Compra</span>
          <?php if (session('tipo') === 'supervisor'): ?>
            <a href="<?= base_url('/compras/exportar') ?>" class="btn btn-primary btn-sm">Exportar Excel</a>
          <?php endif; ?>
        </div>
        <div class="card-body p-0">
          <table class="table table-bordered table-striped mb-0">
            <thead>
              <tr>
                <th>Nome</th>
                <th>Modelo</th>
                <th>Qtd</th>
                <th>Valor Unitário (R$)</th>
                <th>Valor Total (R$)</th>
                <th>Status</th>
                <th>Unidade</th>
                <th>Link</th>
                <th>NF</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($compras)): ?>
                <?php foreach ($compras as $compra): ?>
                  <tr>
                    <td><?= esc($compra['nome']) ?></td>
                    <td><?= esc($compra['modelo']) ?></td>
                    <td><?= esc($compra['quantidade']) ?></td>
                    <td><?= number_format($compra['valor_unitario'], 2, ',', '.') ?></td>
                    <td><?= number_format($compra['quantidade'] * $compra['valor_unitario'], 2, ',', '.') ?></td>
                    <td>
                      <?php if ($compra['status'] === 'pendente'): ?>
                        <?php if (session('tipo') === 'tecnico'): ?>
                          <span class="badge badge-warning">Pendente</span>
                        <?php else: ?>
                          <a href="<?= base_url('/compras/entregar/' . $compra['id']) ?>" class="badge badge-warning">Pendente</a>
                        <?php endif; ?>
                      <?php elseif ($compra['status'] === 'entregue'): ?>
                        <span class="badge badge-success">Entregue</span>
                      <?php elseif ($compra['status'] === 'enviado'): ?>
                        <span class="badge badge-info">NF Enviada</span>
                      <?php endif; ?>
                    </td>
                    <td><?= esc($compra['unidade']) ?></td>
                    <td>
                      <?php if ($compra['link']): ?>
                        <a href="<?= esc($compra['link']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Abrir</a>
                      <?php else: ?>
                        <span class="text-muted">N/A</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if (!empty($compra['nf_path'])): ?>
                        <a href="<?= base_url('compras/downloadNF/' . $compra['nf_path']) ?>" target="_blank" class="btn btn-sm btn-secondary">Ver NF</a>
                      <?php else: ?>
                        <span class="text-muted">N/A</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if (
                        $compra['status'] === 'pendente' &&
                        session('tipo') === 'tecnico' &&
                        session('user_id') === $compra['usuario_id']
                      ): ?>
                        <a href="<?= base_url('/compras/entregar/' . $compra['id']) ?>" class="btn btn-sm btn-success">Marcar como Entregue</a>
                      <?php endif; ?>

                      <?php if (
                        $compra['status'] === 'entregue' &&
                        session('tipo') === 'tecnico' &&
                        session('user_id') === $compra['usuario_id']
                      ): ?>
                        <form action="<?= base_url('/compras/enviarNF/' . $compra['id']) ?>" method="post" enctype="multipart/form-data" class="mt-2">
                          <?= csrf_field() ?>
                          <input type="file" name="documento_nf" class="form-control-file mb-1" required>
                          <button type="submit" class="btn btn-sm btn-primary">Enviar NF</button>
                        </form>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="10" class="text-center">Nenhuma compra encontrada.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>
