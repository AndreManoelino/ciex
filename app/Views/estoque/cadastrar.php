<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper" style="margin-left: 250px; padding: 20px;">
  <div class="container-fluid">

    <!-- Título centralizado em toda largura -->
    <h2 class="text-center mb-5">Cadastro de Produto no Estoque</h2>

    <div class="card shadow-sm border-0">
      <div class="card-body">

        <form method="post" action="<?= base_url('/estoque/salvar') ?>">
          <?= csrf_field() ?>

          <div class="row">
            <!-- 3 campos por linha -->
            <div class="col-md-3 mb-2">
              <label for="produto" class="form-label">Produto</label>
              <input type="text" id="produto" name="produto" class="form-control" required>
            </div>

            <div class="col-md-3 mb-2">
              <label for="especificacao" class="form-label">Especificação</label>
              <input type="text" id="especificacao" name="especificacao" class="form-control">
            </div>

            <div class="col-md-3 mb-2">
              <label for="codigo" class="form-label">Código</label>
              <input type="text" id="codigo" name="codigo" class="form-control" required>
            </div>

            <div class="col-md-3 mb-2">
              <label for="inventariado" class="form-label">Inventariado</label>
              <input type="number" id="inventariado" name="inventariado" class="form-control" value="0">
            </div>

            <div class="col-md-3 mb-2">
              <label for="entrada" class="form-label">Entrada</label>
              <input type="number" id="entrada" name="entrada" class="form-control" value="0">
            </div>

            <div class="col-md-3 mb-2">
              <label for="saida" class="form-label">Saída</label>
              <input type="number" id="saida" name="saida" class="form-control" value="0">
            </div>
            <div class="col-md-3 mb-2">
              <label for="saida" class="form-label">Responsável</label>
              <input type="text" id="responsavel" name="responsavel" class="form-control" required>
            </div

            <div class="col-md-3 mb-2">
              <label for="saida" class="form-label">Responsável</label>
              <input type="text" id="responsavel" name="responsavel" class="form-control" required>
            </div> <!-- aqui estava faltando o fechamento do div -->


            <div class="col-md-6 mb-1">
              <label for="proximo_inventario" class="form-label">Próximo Inventário</label>
              <input type="date" id="proximo_inventario" name="proximo_inventario" class="form-control">
            </div>
          </div>

          <!-- Botões alinhados à esquerda, espaçamento entre eles -->
          <div class="mt-4">
            <button type="submit" class="btn btn-primary me-2">Salvar</button>
            <a href="<?= base_url('/estoque') ?>" class="btn btn-secondary">Voltar</a>
          </div>

        </form>

      </div>
    </div>
  </div>
</div>

<?= $this->include('templates/footer') ?>
