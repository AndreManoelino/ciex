<?= $this->include('templates/sidebar') ?>


<h2>Relatório de Checklists</h2>

<form method="get" action="<?= base_url('checklist/relatorio') ?>" class="row g-3">
  <div class="col-md-3">
    <label>Data Início</label>
    <input type="date" name="data_inicio" class="form-control" required>
  </div>

  <div class="col-md-3">
    <label>Data Fim</label>
    <input type="date" name="data_fim" class="form-control" required>
  </div>

  <div class="col-md-3">
    <label>Unidade</label>
    <input type="text" name="unidade" class="form-control">
  </div>

  <div class="col-md-3">
    <label>Tipo</label>
    <select name="tipo" class="form-control">
      <option value="">Todos</option>
      <option value="Sistema de CFTV">Sistema de CFTV</option>
      <option value="Atendimento">Atendimento</option>
    </select>
  </div>

  <div class="col-md-3">
    <label>&nbsp;</label>
    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
  </div>
</form>

<?php if (isset($resultados)): ?>
  <hr>
  <h4>Resultados:</h4>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Data/Hora</th>
        <th>Unidade</th>
        <th>Técnico</th>
        <th>Item</th>
        <th>Status</th>
        <th>Observação</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($resultados as $linha): ?>
        <tr>
          <td><?= $linha['data_hora'] ?></td>
          <td><?= $linha['nome_unidade'] ?></td>
          <td><?= $linha['nome_tecnico'] ?></td>
          <td><?= $linha['item'] ?></td>
          <td><?= $linha['status'] ?></td>
          <td><?= $linha['observacao'] ?></td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
<?php endif ?>

<?= $this->endSection() ?>
