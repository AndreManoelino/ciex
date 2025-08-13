<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>
<?= titulo_centralizado('Meu Formulário Bonito') ?>

<div class="content-wrapper">
  <div class="container-fluid">


    <form action="<?= base_url('formulario/enviar') ?>" method="post" enctype="multipart/form-data" class="row">
      <?= gerar_formulario($campos) ?>
      <div class="form-group col-12 mt-3">
        <button type="submit" class="btn btn-primary">Enviar</button>
      </div>
    </form>
  </div>
</div>

<?= $this->include('templates/footer') ?>

