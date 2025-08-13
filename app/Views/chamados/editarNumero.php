<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Editar Número do Chamado</h1>

            <form action="<?= base_url('/chamados/salvarChamado') ?>" method="post" class="col-lg-6">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= esc($chamado['id']) ?>">
                <div class="mb-3">
                    <label for="numero_chamado" class="form-label">Número do Chamado</label>
                    <input type="text" id="numero_chamado" name="numero_chamado" class="form-control" value="<?= esc($chamado['numero_chamado']) ?>" maxlength="100" required>
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
                <a href="<?= base_url('/chamados') ?>" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>
