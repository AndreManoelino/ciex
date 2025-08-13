<?= $this->include('templates/sidebar') ?>
<?= $this->extend('templates/sidebar') ?>
<?= $this->section('content') ?>

<h2>Checklist - <?= esc($menu) ?></h2>

<form method="post" action="<?= base_url('/checklist/salvar') ?>">
    <input type="hidden" name="tipo" value="<?= esc($menu) ?>">
    <input type="hidden" name="nome_tecnico" value="<?= esc($nome_tecnico) ?>">

    <div class="mb-3">
        <label>Unidade:</label>
        <input type="text" name="nome_unidade" class="form-control" required>
    </div>

    <table class="table table-bordered table-sm">
        <thead class="table-light">
            <tr>
                <th>Item</th>
                <th>Status</th>
                <th>Observação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($itens as $index => $item): ?>
                <tr>
                    <td>
                        <input type="hidden" name="item[]" value="<?= esc($item) ?>">
                        <?= esc($item) ?>
                    </td>
                    <td>
                        <select name="status[]" class="form-select form-select-sm" required>
                            <option value="Sim">Sim</option>
                            <option value="Não">Não</option>
                            <option value="N/A">N/A</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="observacao[]" class="form-control form-control-sm">
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <div class="d-flex justify-content-between mt-4">
        <button type="submit" class="btn btn-success">Salvar Checklist</button>
        <a href="<?= base_url('/checklist/historico') ?>" class="btn btn-secondary">Ver Histórico</a>
    </div>
</form>

<?= $this->endSection() ?>
