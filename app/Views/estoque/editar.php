<!-- app/Views/estoque/editar.php -->
<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>
<div class="container mt-5">
    <h2>Editar Produto</h2>

    <form action="<?= base_url('estoque/atualizar/' . $produto['id']) ?>" method="post">
        <div class="mb-3">
            <label>Produto</label>
            <input type="text" name="produto" class="form-control" value="<?= esc($produto['produto']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Especificação</label>
            <input type="text" name="especificacao" class="form-control" value="<?= esc($produto['especificacao']) ?>">
        </div>
        <div class="mb-3">
            <label>Código</label>
            <input type="text" name="codigo" class="form-control" value="<?= esc($produto['codigo']) ?>">
        </div>
        <div class="mb-3">
            <label>Inventariado</label>
            <input type="number" name="inventariado" class="form-control" value="<?= esc($produto['inventariado']) ?>">
        </div>
        <div class="mb-3">
            <label>Entrada</label>
            <input type="number" name="entrada" class="form-control" value="<?= esc($produto['entrada']) ?>">
        </div>
        <div class="mb-3">
            <label>Saída</label>
            <input type="number" name="saida" class="form-control" value="<?= esc($produto['saida']) ?>">
        </div>
        <div class="mb-3">
            <label>Responsável</label>
            <input type="text" name="responsavel" class="form-control" value="<?= esc($produto['responsavel']) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="<?= base_url('estoque') ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>


