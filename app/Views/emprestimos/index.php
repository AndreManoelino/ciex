<?= $this->include('templates/sidebar') ?>

<style>
  input.form-control,
  select.form-control,
  .form-control-file {
    height: 3;
    padding: 3;
    font-size: 3;
  }

  button.btn {
    padding: 5px 12px !important;
    font-size: 14px !important;
  }

  label {
    font-size: 14px !important;
    margin-bottom: 4px !important;
  }

  footer {
      background-color: orange;
      color: white;
      text-align: center;
      padding: 12px;
      font-size: 13px;
      font-weight: bold;
      box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.15);
      position: fixed;
      width: 100%;
      bottom: 0;
  }

  .row > [class*='col-'] {
    padding-left: 8px !important;
    padding-right: 8px !important;
  }
</style>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">

      <h2>Gerenciamento de Empréstimos</h2>

      <?php if ($tipoUsuario === 'supervisor'): ?>
        <form method="get" action="<?= base_url('/emprestimos') ?>" class="mb-2">
          <label for="unidade">Filtrar por Unidade:</label>
          <select name="unidade" id="unidade" class="form-control" onchange="this.form.submit()">
            <option value="">Todas as unidades</option>
            <?php foreach($unidadesEstado as $unidade): ?>
              <option value="<?= esc($unidade) ?>" <?= (!empty($unidadeFiltro) && $unidadeFiltro === $unidade) ? 'selected' : '' ?>>
                <?= esc($unidade) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </form>   
      <?php endif; ?> 

      <?php if ($tipoUsuario === 'admin'): ?>
        <form method="get" class="mb-1">
          <label for="estado_filtro">Estado:</label>
          <select name="estado_filtro" id="estado_filtro" class="form-control" onchange="this.form.submit()">
              <option value="">-- Todos --</option>
              <?php foreach (array_keys($todosEstados) as $estado): ?>
                  <option value="<?= $estado ?>" <?= ($estadoFiltro === $estado) ? 'selected' : '' ?>><?= $estado ?></option>
              <?php endforeach; ?>
          </select>

          <?php if (!empty($estadoFiltro)): ?>
            <label for="unidade_filtro">Unidade:</label>
            <select name="unidade_filtro" id="unidade_filtro" class="form-control" onchange="this.form.submit()">
                <option value="">-- Todas --</option>
                <?php foreach ($unidadesEstado as $unidade): ?>
                    <option value="<?= $unidade ?>" <?= ($unidadeFiltro === $unidade) ? 'selected' : '' ?>><?= $unidade ?></option>
                <?php endforeach; ?>
            </select>
          <?php endif; ?>
        </form>
      <?php endif; ?>

      <?php if (session()->getFlashdata('msg')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('msg') ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('erro')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('erro') ?></div>
      <?php endif; ?>

      <?php if (session('tipo') === 'tecnico'): ?>
        <div class="card mb-4">
          <div class="card-header">Novo Empréstimo</div>
          <div class="card-body">
            <form method="post" action="<?= base_url('/emprestimos/registrar') ?>" enctype="multipart/form-data">
              <?= csrf_field() ?>
              <div class="row">
                <div class="col-md-4">
                  <label>Equipamento</label>
                  <select name="equipamento_nome" class="form-control" required>
                    <option value="">Selecione</option>
                    <?php foreach($equipamentos as $equip): ?>
                      <option value="<?= esc($equip['nome']) ?>"><?= esc($equip['nome']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-2">
                  <label>Quantidade</label>
                  <input type="number" name="quantidade" class="form-control" min="1" value="1" required>
                </div>

                <div class="col-md-3">
                  <label>Unidade de Destino</label>
                  <select name="unidade_destino" class="form-control" required>
                    <option value="">Selecione</option>
                    <?php foreach ($unidadesEstado as $uni): ?>
                      <option value="<?= esc($uni) ?>"><?= esc($uni) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-md-3">
                  <label>Termo de Envio (PDF ou imagem)</label>
                  <input type="file" name="termo_envio" class="form-control-file" accept=".pdf,image/*" required>
                </div>
              </div>

              <button type="submit" class="btn btn-success mt-3">Registrar Empréstimo</button>
            </form>
          </div>
        </div>
      <?php endif; ?>

      <div class="card">
        <div class="card-header">Histórico de Empréstimos</div>
        <div class="card-body p-0">
          <table class="table table-bordered table-striped mb-0">
            <thead>
              <tr>
                <th>Equipamento</th>
                <th>Qtd</th>
                <th>Origem</th>
                <th>Destino</th>
                <th>Data Empréstimo</th>
                <th>Data Devolução</th>
                <th>Termo Envio</th>
                <th>Confirm. Envio</th>
                <th>Termo Devolução</th>
                <th>Confirm. Devolução</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($emprestimos)): ?>
                <?php foreach ($emprestimos as $e): ?>
                  <tr>
                    <td><?= esc($e['equipamento_nome']) ?></td>
                    <td><?= esc($e['quantidade']) ?></td>
                    <td><?= esc($e['unidade_origem']) ?></td>
                    <td><?= esc($e['unidade_destino']) ?></td>
                    <td><?= esc($e['data_emprestimo']) ?></td>
                    <td><?= esc($e['data_devolucao'] ?? '-') ?></td>
                    <td>
                      <?php if (!empty($e['termo_envio'])): ?>
                        <a href="<?= base_url('writable/uploads/termos/' . $e['termo_envio']) ?>" target="_blank">Ver Termo</a>
                      <?php else: ?>-
                      <?php endif; ?>
                    </td>
                    <td><?= $e['confirmado_envio'] ? 'Sim' : 'Não' ?></td>
                    <td>
                      <?php if (!empty($e['termo_devolucao'])): ?>
                        <a href="<?= base_url('writable/uploads/termos/' . $e['termo_devolucao']) ?>" target="_blank">Ver Termo</a>
                      <?php else: ?>-
                      <?php endif; ?>
                    </td>
                    <td><?= $e['confirmado_devolucao'] ? 'Sim' : 'Não' ?></td>
                    <td>
                      <?php
                        $sessUnidade = session()->get('unidade');
                        $sessTipo = session()->get('tipo');

                        if (!$e['confirmado_devolucao'] && $sessUnidade === $e['unidade_destino']): ?>
                          <form action="<?= base_url('/emprestimos/registrarDevolucao/' . $e['id']) ?>" method="post" enctype="multipart/form-data" style="margin-bottom:5px;">
                            <?= csrf_field() ?>
                            <input type="file" name="termo_devolucao" accept=".pdf,image/*" required style="margin-bottom:5px;">
                            <button type="submit" class="btn btn-warning btn-sm">Registrar Devolução</button>
                          </form>
                        <?php elseif (!$e['devolucao_resolvida'] && $sessUnidade === $e['unidade_origem'] && $e['confirmado_devolucao']): ?>
                          <form action="<?= base_url('/emprestimos/confirmarResolucaoDevolucao/' . $e['id']) ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success btn-sm">Confirmar Recebimento</button>
                          </form>
                        <?php else: ?>-
                        <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="11" class="text-center">Nenhum empréstimo encontrado.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>
