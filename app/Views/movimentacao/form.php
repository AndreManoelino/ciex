<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="container mt-5">
    <h2>Registrar Movimentação de Estoque</h2>

    <form action="<?= base_url('/movimentacao/salvar') ?>" method="post">
        <?= csrf_field() ?>
        

        <div class="mb-3">
            <label>Tipo</label>
            <select name="tipo" class="form-control" required>
                <option value="">Selecione</option>
                <option value="ENTRADA">Entrada</option>
                <option value="SAIDA">Saída</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Quantidade</label>
            <input type="number" name="quantidade" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label>Responsável</label>
            <input type="text" name="responsavel" class="form-control" required>
        </div>
        <select name="estoque_id" class="form-control" required>
            <option value="">Selecione o produto</option>
            <?php foreach ($produtos as $produto): ?>
                <option value="<?= esc($produto['id']) ?>">
                    <?= esc($produto['produto']) ?> - <?= esc($produto['especificacao']) ?>
                </option>
            <?php endforeach; ?>
        </select>



        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="<?= base_url('/estoque') ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?= $this->include('templates/footer') ?>
