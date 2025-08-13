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
            <h4 class="mb-0">Novo Encaixe de Atendimento</h4>
          </div>
          <div class="card-body">

            <form method="POST" action="<?= base_url('/encaixes/salvar') ?>" class="bg-light p-4 rounded shadow-sm">
              <div class="mb-2">
                <label for="nome" class="form-label">Nome:</label>
                <input type="text" class="form-control" name="nome" id="nome" required>
              </div>

              <div class="mb-3">
                <label for="cpf" class="form-label">CPF:</label>
                <input type="text" class="form-control" name="cpf" id="cpf" required maxlength="14" placeholder="000.000.000-00">
              </div>

              <div class="mb-3">
                <label for="horario" class="form-label">Horário (07:00 - 19:00):</label>
                <input type="time" class="form-control" name="horario" id="horario" min="07:00" max="19:00" required>
              </div>

              <div class="mb-3">
                <label for="tipo" class="form-label">Tipo de Encaixe:</label>
                <select name="tipo" id="tipo" class="form-select" required>
                  <option value="">-- Selecione --</option>
                  <option value="Emissão RG">Emissão RG</option>
                  <option value="Marcação de Prova">Marcação de Prova</option>
                  <option value="Detran">Detran</option>
                  <option value="Cad Unico">Cad Único</option>
                  <option value="Procon">Procon</option>
                  <option value="Junta Militar">Junta Militar</option>
                  <option value="Assistência Social">Assistência Social</option>
                  <option value="Seguro Desemprego">Seguro Desemprego</option>
                  <option value="Ipsemg">Ipsemg</option>
                </select>
              </div>

              <button type="submit" class="btn btn-primary">Salvar</button>
              <a href="<?= base_url('/encaixes') ?>" class="btn btn-secondary ms-2">Voltar para lista</a>
            </form>

          </div>
        </div>
      </div>

    </main>
  </div>

  <?= $this->include('templates/footer') ?>

</div>
