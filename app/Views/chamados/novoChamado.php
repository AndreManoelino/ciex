<div class="container-fluid">
  <!-- Filtro por unidade (visível apenas para supervisor) -->
  <?php if (session()->get('tipo_usuario') === 'supervisor'): ?>
    <div class="row mb-3">
      <div class="col-md-6">
        <form method="GET" action="/chamados">
          <div class="input-group">
            <label class="input-group-text" for="filtroUnidade">Unidade:</label>
            <select class="form-select" name="unidade" id="filtroUnidade" onchange="this.form.submit()">
              <option value="">Todas</option>
              <?php foreach ($unidades ?? [] as $u): ?>
                <option value="<?= esc($u) ?>" <?= (isset($_GET['unidade']) && $_GET['unidade'] === $u) ? 'selected' : '' ?>>
                  <?= esc($u) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </form>
      </div>
      <div class="col-md-6 text-end">
        <a href="/chamados/exportar<?= isset($_GET['unidade']) ? '?unidade=' . esc($_GET['unidade']) : '' ?>" class="btn btn-success">
          <i class="fas fa-file-excel"></i> Exportar para Excel
        </a>
      </div>
    </div>
  <?php endif; ?>

  <div class="row">
    <!-- Chamados Ativos -->
    <div class="col-lg-6">
      <div class="card card-warning">
        <div class="card-header">
          <h3 class="card-title">Chamados Ativos (Em Aberto)</h3>
        </div>
        <div class="card-body p-0">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>ID</th>
                <th>Sistema</th>
                <th>Técnico</th>
                <th>Início</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($ativos)): ?>
                <?php foreach ($ativos as $chamado): ?>
                  <tr>
                    <td><?= esc($chamado['id']) ?></td>
                    <td><?= esc($chamado['sistema']) ?></td>
                    <td><?= esc($chamado['tecnico']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($chamado['data_inicio'])) ?></td>
                    <td>
                      <a href="/chamados/encerrar/<?= $chamado['id'] ?>" class="btn btn-sm btn-danger">Encerrar</a>
                      <button 
                        type="button" 
                        class="btn btn-sm btn-primary" 
                        data-bs-toggle="modal" 
                        data-bs-target="#emailModal" 
                        data-chamado-id="<?= esc($chamado['id']) ?>"
                        data-sistema="<?= esc($chamado['sistema']) ?>"
                      >
                        Enviar E-mail
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="5" class="text-center">Nenhum chamado em aberto.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Chamados Recentes -->
    <div class="col-lg-6">
      <div class="card card-success">
        <div class="card-header">
          <h3 class="card-title">Chamados Recentes (últimos 30 dias)</h3>
        </div>
        <div class="card-body p-0">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>ID</th>
                <th>Sistema</th>
                <th>Início</th>
                <th>Fim</th>
                <th>Minutos</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($recentes)): ?>
                <?php foreach ($recentes as $chamado): ?>
                  <tr>
                    <td><?= esc($chamado['id']) ?></td>
                    <td><?= esc($chamado['sistema']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($chamado['data_inicio'])) ?></td>
                    <td>
                      <?= $chamado['data_fim']
                        ? date('d/m/Y H:i', strtotime($chamado['data_fim']))
                        : '<span class="text-warning">Aberto</span>' ?>
                    </td>
                    <td><?= esc($chamado['minutos_indisponibilidade'] ?? '-') ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="5" class="text-center">Nenhum chamado registrado nos últimos 30 dias.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Bootstrap para envio de e-mail -->
  <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" action="/chamados/enviarEmail" id="emailForm">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="emailModalLabel">Enviar E-mail de Indisponibilidade</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="chamado_id" id="chamado_id" value="">
            <div class="mb-3">
              <label for="senha_smtp" class="form-label">Senha SMTP</label>
              <input type="password" class="form-control" id="senha_smtp" name="senha_smtp" required placeholder="Digite sua senha de envio">
              <div class="form-text">Informe a senha da sua conta de e-mail institucional.</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Enviar E-mail</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Script JavaScript para preencher o modal -->
<script>
  const emailModal = document.getElementById('emailModal');
  emailModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const chamadoId = button.getAttribute('data-chamado-id');
    const sistema = button.getAttribute('data-sistema');

    const modalTitle = emailModal.querySelector('.modal-title');
    modalTitle.textContent = 'Enviar E-mail de Indisponibilidade: ' + sistema;

    const inputChamadoId = emailModal.querySelector('#chamado_id');
    inputChamadoId.value = chamadoId;
  });
</script>
