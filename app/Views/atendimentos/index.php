<?= $this->include('templates/header') ?>



<!-- Conteúdo principal -->
<div class="content-wrapper" style="margin-left: 250px; padding: 20px;">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
          <h2 class="mb-4">Atendimentos</h2>
      </div>

      <?php if (session()->get('tipo') == 'atendente_rg'): ?>
          <div class="col-md-6 mb-3">
              <div class="card text-white bg-primary">
                  <div class="card-body">
                      <h5 class="card-title">Resumo de Hoje</h5>
                      <p class="card-text display-6"><?= $resumo_hoje ?? 0 ?> atendimentos</p>
                  </div>
              </div>
          </div>
          <div class="col-md-6 mb-3">
              <div class="card text-white bg-secondary">
                  <div class="card-body">
                      <h5 class="card-title">Resumo de Ontem</h5>
                      <p class="card-text display-6"><?= $resumo_ontem ?? 0 ?> atendimentos</p>
                  </div>
              </div>
          </div>

          <div class="col-md-12">
              <form action="<?= base_url('atendimentos/salvar') ?>" method="post" class="row mb-4">
                  <div class="col-md-3 mb-2">
                      <input name="nome" class="form-control" placeholder="Nome da pessoa">
                  </div>
                  <div class="col-md-3 mb-2">
                      <input name="cpf" class="form-control" placeholder="CPF">
                  </div>
                  <div class="col-md-3 mb-2">
                      <input name="numero_senha" class="form-control" placeholder="Número de Senha">
                  </div>
                  <div class="col-md-3 mb-2">
                      <input name="codigo_atendente" class="form-control" placeholder="Código do Atendimento">
                  </div>
                  <div class="col-md-12 text-end">
                      <button class="btn btn-success">Registrar Atendimento</button>
                  </div>
              </form>
          </div>
      <?php endif; ?>

      <?php if (session()->get('tipo') == 'supervisor_atendimento'): ?>
          <div class="col-md-12">
              <form action="<?= base_url('atendimentos/filtrar') ?>" method="post" class="row mb-4">
                  <div class="col-md-3">
                      <label>Atendente:</label>
                      <select name="usuario_id" class="form-control">
                          <option value="">Todos</option>
                          <?php foreach ($usuarios as $u): ?>
                              <option value="<?= $u['id'] ?>"><?= $u['nome'] ?></option>
                          <?php endforeach; ?>
                      </select>
                  </div>
                  <div class="col-md-3">
                      <label>Data Início:</label>
                      <input type="date" name="data_inicio" class="form-control">
                  </div>
                  <div class="col-md-3">
                      <label>Data Fim:</label>
                      <input type="date" name="data_fim" class="form-control">
                  </div>
                  <div class="col-md-3 d-flex align-items-end">
                      <button class="btn btn-primary w-100">Filtrar</button>
                  </div>
              </form>
          </div>
      <?php endif; ?>

      <div class="col-md-12">
          <table class="table table-bordered table-hover">
              <thead class="table-dark">
                  <tr>
                      <th>Nome</th><th>CPF</th><th>Senha</th><th>Código</th><th>Data</th>
                  </tr>
              </thead>
              <tbody>
                  <?php foreach ($atendimentos as $a): ?>
                      <tr>
                          <td><?= esc($a['nome']) ?></td>
                          <td><?= esc($a['cpf']) ?></td>
                          <td><?= esc($a['numero_senha']) ?></td>
                          <td><?= esc($a['codigo_atendente']) ?></td>
                          <td><?= date('d/m/Y H:i', strtotime($a['created_at'])) ?></td>
                      </tr>
                  <?php endforeach; ?>
              </tbody>
          </table>
      </div>
    </div>
  </div>
</div>
<?= $this->include('templates/footer') ?>