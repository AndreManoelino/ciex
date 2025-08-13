
<?= $this->include('templates/header') ?>
<head>
  <style>
    footer {
            background-color: orange;
            color: white;
            text-align: center;
            padding: 15px 10px;
            font-size: 14px;
            font-weight: 500;
        }
  </style>
</head>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">

      <h4 class="mb-3">Acessos Técnicos</h4>

      <?php if ($tipoUsuario === 'supervisor'): ?>
        <form method="get" action="<?= base_url('/mapeamento-rede') ?>" class="mb-2">
          <label for="unidade" class="form-label fs-6">Filtrar por Unidade:</label>
          <select name="unidade" class="form-control form-control-sm w-auto d-inline-block" onchange="this.form.submit()">
            <option value="">Todas</option>
            <?php foreach ($unidadesEstado as $uni): ?>
              <option value="<?= esc($uni) ?>" <?= ($unidadeFiltro === $uni) ? 'selected' : '' ?>><?= esc($uni) ?></option>
            <?php endforeach; ?>
          </select>
        </form>
      <?php endif; ?>

      <form method="post" action="<?= base_url('/mapeamento-rede/salvar') ?>" class="fs-6">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= isset($editarAcesso['id']) ? esc($editarAcesso['id']) : '' ?>">

        <div class="row">
          <div class="col-md-2">
            <label>Unidade</label>
            <?php if ($tipoUsuario === 'supervisor'): ?>
              <input name="unidade" class="form-control form-control-sm" required
                     value="<?= isset($editarAcesso['unidade']) ? esc($editarAcesso['unidade']) : '' ?>">
            <?php else: ?>
              <input name="unidade" class="form-control form-control-sm" value="<?= session('unidade') ?>" readonly>
            <?php endif; ?>
          </div>
          <div class="col-md-2">
            <label>Departamento</label>
            <input name="departamento" class="form-control form-control-sm"
                   value="<?= isset($editarAcesso['departamento']) ? esc($editarAcesso['departamento']) : '' ?>">
          </div>
          <div class="col-md-2">
            <label>Hostname</label>
            <input name="hostname" class="form-control form-control-sm"
                   value="<?= isset($editarAcesso['hostname']) ? esc($editarAcesso['hostname']) : '' ?>">
          </div>
          <div class="col-md-2">
            <label>Guichê</label>
            <input name="guiche" class="form-control form-control-sm"
                   value="<?= isset($editarAcesso['guiche']) ? esc($editarAcesso['guiche']) : '' ?>">
          </div>
          <div class="col-md-2">
            <label>IP de Rede</label>
            <input name="ip_rede" class="form-control form-control-sm"
                   value="<?= isset($editarAcesso['ip_rede']) ? esc($editarAcesso['ip_rede']) : '' ?>">
          </div>
          <div class="col-md-2">
            <label>Senha Desktop</label>
            <input name="senha_desktop" class="form-control form-control-sm"
                   value="<?= isset($editarAcesso['senha_desktop']) ? esc($editarAcesso['senha_desktop']) : '' ?>">
          </div>
        </div>

        <div class="row mt-2">
          <div class="col-md-2">
            <label>Senha VNC</label>
            <input name="senha_vnc" class="form-control form-control-sm"
                   value="<?= isset($editarAcesso['senha_vnc']) ? esc($editarAcesso['senha_vnc']) : '' ?>">
          </div>
          <div class="col-md-2">
            <label>Fila de Impressão</label>
            <input name="fila_impressao" class="form-control form-control-sm"
                   value="<?= isset($editarAcesso['fila_impressao']) ? esc($editarAcesso['fila_impressao']) : '' ?>">
          </div>
          <div class="col-md-2">
            <label>Switch</label>
            <input type="number" name="switch_numero" class="form-control form-control-sm"
                   value="<?= isset($editarAcesso['switch_numero']) ? esc($editarAcesso['switch_numero']) : '' ?>">
          </div>
          <div class="col-md-2">
            <label>Porta</label>
            <input name="porta_switch" class="form-control form-control-sm"
                   value="<?= isset($editarAcesso['porta_switch']) ? esc($editarAcesso['porta_switch']) : '' ?>">
          </div>
          <div class="col-md-2">
            <label>VLAN</label>
            <input name="vlan" class="form-control form-control-sm"
                   value="<?= isset($editarAcesso['vlan']) ? esc($editarAcesso['vlan']) : '' ?>">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-success btn-sm w-100" type="submit">
              <?= isset($editarAcesso) ? 'Atualizar' : 'Salvar' ?>
            </button>
          </div>
        </div>
      </form>

      <hr>

      <table class="table table-bordered table-sm mt-3">
        <thead class="table-light fs-6">
          <tr>
            <th>Unidade</th>
            <th>Depto</th>
            <th>Hostname</th>
            <th>Guichê</th>
            <th>IP</th>
            <th>Desktop</th>
            <th>VNC</th>
            <th>Fila de Impressão</th>
            <th>Switch</th>
            <th>Porta</th>
            <th>VLAN</th>
            <th>Data</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody class="fs-6">
          <?php if (!empty($acessos)): ?>
            <?php foreach ($acessos as $ac): ?>
              <tr>
                <td><?= esc($ac['unidade']) ?></td>
                <td><?= esc($ac['departamento']) ?></td>
                <td><?= esc($ac['hostname']) ?></td>
                <td><?= esc($ac['guiche']) ?></td>
                <td><?= esc($ac['ip_rede']) ?></td>
                <td><?= esc($ac['senha_desktop']) ?></td>
                <td><?= esc($ac['senha_vnc']) ?></td>
                <td><?= esc($ac['fila_impressao']) ?></td>
                <td><?= esc($ac['switch_numero']) ?></td>
                <td><?= esc($ac['porta_switch']) ?></td>
                <td><?= esc($ac['vlan']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($ac['created_at'])) ?></td>
                <td class="text-nowrap">
                  <?php if (
                      $tipoUsuario === 'supervisor' ||
                      ($tipoUsuario === 'tecnico' && trim(strtolower($ac['unidade'])) === trim(strtolower(session('unidade'))))
                  ): ?>
                    <a href="<?= base_url('/mapeamento-rede?editar=' . esc($ac['id'])) ?>" class="btn btn-sm btn-primary">✏️</a>
                  <?php endif; ?>

                  <a href="<?= base_url('mapeamento-rede/excluir/' . esc($ac['id'])) ?>"
                     onclick="return confirm('Deseja excluir este acesso?');"
                     class="btn btn-sm btn-danger">🗑️</a>
                </td>
              </tr>
            <?php endforeach ?>
          <?php else: ?>
            <tr><td colspan="13" class="text-center">Nenhum acesso registrado.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>

    </div>
  </div>
</div>

<?= $this->include('templates/footer') ?>
