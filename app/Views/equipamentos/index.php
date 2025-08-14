<head>
  <style>
  footer {
      background-color:orange;
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
</style>
</head>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h2>Gerenciamento de Equipamentos</h2>

      <?php if (session()->getFlashdata('msg')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('msg') ?></div>
      <?php endif; ?>

      <?php if ($tipoUsuario === 'supervisor'): ?>
        <!-- filtro unidades -->
        <form method="get" class="mb-4">
          <label for="filtro_unidade">Filtrar por Unidade:</label>
          <select name="filtro_unidade" id="filtro_unidade" class="form-control" onchange="this.form.submit()">
            <option value="">Todas as Unidades do Estado</option>
            <?php foreach ($unidades as $uni): ?>
              <option value="<?= esc($uni) ?>" <?= ($unidadeSelecionada === $uni) ? 'selected' : '' ?>>
                <?= esc($uni) ?>
              </option>
            <?php endforeach; ?>

          </select>
        </form>
      <?php endif; ?>

      <?php if ($tipoUsuario === 'tecnico'): ?>
          <!-- Formulário técnico para adicionar equipamentos -->
          <div class="card mb-4">
              <div class="card-header">Adicionar Equipamento (Backup)</div>
              <div class="card-body">
                  <form method="post" action="<?= base_url('/equipamentos/salvar') ?>">
                      <?= csrf_field() ?>
                      <div class="row">
                          <div class="col-md-4">
                              <label>Nome do Equipamento</label>
                              <select id="nome" name="nome" class="form-control" required>
                                  <option value="">Selecione</option>
                                  <?php foreach ($equipamentosModelos as $nome => $modelos): ?>
                                      <option value="<?= $nome ?>"><?= $nome ?></option>
                                  <?php endforeach; ?>
                              </select>
                          </div>
                          <div class="col-md-4">
                              <label>Modelo</label>
                              <select id="modelo" name="modelo" class="form-control" required>
                                  <option value="">Selecione o nome primeiro</option>
                              </select>
                          </div>
                          <div class="col-md-2">
                              <label>Quantidade</label>
                              <input type="number" name="quantidade" id="quantidade" class="form-control" min="1" required>
                          </div>
                          <div class="col-md-2 d-flex align-items-end">
                              <button type="submit" class="btn btn-success w-100">Adicionar</button>
                          </div>
                      </div>
                  </form>
              </div>
          </div>

          <script>
              const equipamentosModelos = <?= json_encode($equipamentosModelos) ?>;
              const nomeSelect = document.getElementById('nome');
              const modeloSelect = document.getElementById('modelo');

              nomeSelect.addEventListener('change', function () {
                  modeloSelect.innerHTML = '<option value="">Selecione</option>';
                  if (equipamentosModelos[this.value]) {
                      equipamentosModelos[this.value].forEach(modelo => {
                          let opt = document.createElement('option');
                          opt.value = modelo;
                          opt.textContent = modelo;
                          modeloSelect.appendChild(opt);
                      });
                  }
              });
          </script>
      <?php endif; ?>

      <?php if ($tipoUsuario === 'admin'): ?>
          <form method="get">
              <label>Estado:</label>
              <select name="filtro_estado" onchange="this.form.submit()">
                  <option value="">Selecione...</option>
                  <?php foreach ($estados as $estado): ?>
                      <option value="<?= $estado ?>" <?= ($estadoSelecionado === $estado) ? 'selected' : '' ?>>
                          <?= $estado ?>
                      </option>
                  <?php endforeach; ?>
              </select>

              <?php if (!empty($unidades)): ?>
                  <label>Unidade:</label>
                  <select name="filtro_unidade" onchange="this.form.submit()">
                      <option value="">Todas</option>
                      <?php foreach ($unidades as $u): ?>
                          <option value="<?= $u ?>" <?= ($unidadeSelecionada === $u) ? 'selected' : '' ?>>
                              <?= $u ?>
                          </option>
                      <?php endforeach; ?>
                  </select>
              <?php endif; ?>
          </form>
      <?php endif; ?>


      <!-- Tabela de equipamentos -->
      <div class="card mb-4">
        <div class="card-header">Equipamentos</div>
        <div class="card-body p-0">
          <table class="table table-striped mb-0">
            <thead>
              <tr>
                <th>Nome</th>
                <th>Modelo</th>
                <th>Quantidade Total</th>
                <th>Em Uso</th>
                <th>Backup</th>
                <th>Unidade</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($equipamentos)): ?>
                <?php foreach ($equipamentos as $equip): ?>
                  <?php $quantidadeTotal = $equip['quantidade_backup'] + $equip['quantidade_uso']; ?>
                  <tr>
                    <td><?= esc($equip['nome']) ?></td>
                    <td><?= esc($equip['modelo']) ?></td>
                    <td><?= $quantidadeTotal ?></td>
                    <td><?= esc($equip['quantidade_uso']) ?></td>
                    <td><?= esc($equip['quantidade_backup']) ?></td>
                    <td><?= esc($equip['unidade']) ?></td>
                    <td>
                      <?php if ($equip['quantidade_backup'] > 0): ?>
                        <form action="<?= base_url('/equipamentos/usar/' . $equip['id']) ?>" method="post" class="d-inline">
                          <?= csrf_field() ?>
                          <button type="submit" class="btn btn-sm btn-primary">Usar</button>
                        </form>
                      <?php endif; ?>
                      <?php if ($equip['quantidade_uso'] > 0): ?>
                        <form action="<?= base_url('/equipamentos/liberar/' . $equip['id']) ?>" method="post" class="d-inline">
                          <?= csrf_field() ?>
                          <button type="submit" class="btn btn-sm btn-warning">Liberar</button>
                        </form>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="7" class="text-center">Nenhum equipamento encontrado.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>
