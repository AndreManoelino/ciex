<?= $this->include('templates/sidebar') ?>
<?= $this->include('templates/header') ?>

<style>
  .content-wrapper {
    margin-left: 250px; /* ajustar conforme a largura da sua sidebar */
    padding: 20px;
    overflow-x: hidden;
  }
</style>

<div class="page-wrapper">
  <div class="content-wrapper">
    <main class="main-content">

      <div class="container-fluid">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Encaixes de Atendimento</h4>
          </div>
          <div class="card-body">

            <form method="get" action="<?= base_url('/encaixes') ?>" class="row g-3 mb-4">

              <div class="col-md-4">
                <label class="form-label">Mês:</label>
                <select name="mes" class="form-select">
                  <?php for ($m = 1; $m <= 12; $m++): 
                    $selected = ($mes == $m) ? 'selected' : '';
                    $mes_nome = date('F', mktime(0, 0, 0, $m, 1));
                  ?>
                    <option value="<?= $m ?>" <?= $selected ?>><?= $mes_nome ?></option>
                  <?php endfor; ?>
                </select>
              </div>

              <div class="col-md-2">
                <label class="form-label">Nome:</label>
                <input type="text" name="nome" class="form-control" value="<?= esc($nome) ?>" placeholder="Nome do cidadão" />
              </div>

              <div class="col-md-3">
                <label class="form-label">Horário:</label>
                <input type="time" name="horario" class="form-control" value="<?= esc($horario) ?>" min="07:00" max="19:00" />
              </div>

              <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Buscar</button>
              </div>
            </form>

            <p class="mb-3">
              <strong>Total encaixes no mês selecionado:</strong> <?= $totalMes ?>
            </p>

            <div class="table-responsive">
              <table class="table table-bordered table-striped table-hover">
                <thead class="table-light text-center">
                  <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Horário</th>
                    <th>Tipo</th>
                    <th>Data</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($encaixes): ?>
                    <?php foreach ($encaixes as $encaixe): ?>
                      <tr>
                        <td><?= esc($encaixe['nome']) ?></td>
                        <td><?= esc($encaixe['cpf']) ?></td>
                        <td><?= esc($encaixe['horario']) ?></td>
                        <td><?= esc($encaixe['tipo']) ?></td>
                        <td><?= date('d/m/Y', strtotime($encaixe['data'])) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="5" class="text-center">Nenhum encaixe encontrado</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

            <a href="<?= base_url('/encaixes/criar') ?>" class="btn btn-success mt-3">Adicionar Encaixe</a>

          </div>
        </div>
      </div>

    </main>
  </div>

  <?= $this->include('templates/footer') ?>
</div>
