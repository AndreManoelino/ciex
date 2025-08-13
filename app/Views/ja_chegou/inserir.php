<div class="content-wrapper" style="margin-left: 250px; padding: 20px;">
  <div class="container-fluid">
    <h3><?= esc($title) ?></h3>

    <?php if(session()->getFlashdata('errors')): ?>
      <div class="alert alert-danger">
        <ul>
          <?php foreach(session()->getFlashdata('errors') as $error): ?>
            <li><?= esc($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('/ja_chegou/inserir') ?>">
      <?= csrf_field() ?>

      <div class="mb-3">
        <label for="nome_cidadao" class="form-label">Nome do Cidadão</label>
        <input type="text" name="nome_cidadao" id="nome_cidadao" class="form-control" required value="<?= set_value('nome_cidadao') ?>">
      </div>

      <div class="mb-3">
        <label for="cpf" class="form-label">CPF</label>
        <input type="text" name="cpf" id="cpf" class="form-control" required 
          value="<?= set_value('cpf') ?>" maxlength="14" pattern="\d{3}\.?\d{3}\.?\d{3}-?\d{2}" 
          placeholder="000.000.000-00">

        <small class="form-text text-muted">Somente números, 11 dígitos.</small>
      </div>
      <div class="col-md-4 mb-3">
        <label for="contato" class="form-label">Contato</label>
        <input type="text" id="contato" name="contato" class="form-control" required ">
      </div

      <div class="mb-3">
        <label for="tipo_documento" class="form-label">Tipo de Documento</label>
        <select name="tipo_documento" id="tipo_documento" class="form-control" required>
          <option value="">Selecione...</option>
          <option value="RG">RG</option>
          <option value="CNH">CNH</option>
          <option value="Comprovante de Endereço">Comprovante de Endereço</option>
          <option value="Certidão de Nascimento">Certidão de Nascimento</option>
          <option value="Título de Eleitor">Título de Eleitor</option>
        </select>
      </div>

      <div class="mb-3">
        <label for="codigo_entrega" class="form-label">Código de Entrega (RA / QR Code)</label>
        <input type="text" name="codigo_entrega" id="codigo_entrega" class="form-control" value="<?= set_value('codigo_entrega') ?>">
      </div>

      <button type="submit" class="btn btn-primary">Registrar Documento</button>
      <a href="<?= base_url('/ja_chegou') ?>" class="btn btn-secondary">Voltar</a>
    </form>
  </div>
</div>
