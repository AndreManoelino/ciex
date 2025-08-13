<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<h2><?= $item ? 'Editar' : 'Novo' ?> Registro</h2>

<form action="<?= base_url("infraestrutura/{$action}") ?>" method="post">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label>Unidade</label>
        <input type="text" name="unidade" value="<?= esc($item['unidade'] ?? '') ?>"
               class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Operadora</label>
        <input type="text" name="operadora" value="<?= esc($item['operadora'] ?? '') ?>"
               class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Banda (MB)</label>
        <input type="number" name="banda_mb" value="<?= esc($item['banda_mb'] ?? '') ?>"
               class="form-control">
    </div>
    <div class="mb-3">
        <label>Valor (R$)</label>
        <input type="text" name="valor" value="<?= esc($item['valor'] ?? '') ?>"
               class="form-control">
    </div>
    <div class="mb-3">
        <label>Tipo de Serviço</label>
        <input type="text" name="tipo_servico" value="<?= esc($item['tipo_servico'] ?? '') ?>"
               class="form-control">
    </div>
    <div class="mb-3">
        <label>Observações</label>
        <textarea name="observacoes" class="form-control"><?= esc($item['observacoes'] ?? '') ?></textarea>
    </div>
    <button class="btn btn-success" type="submit">
        <?= $item ? 'Atualizar' : 'Salvar' ?>
    </button>
    <a href="<?= base_url('infraestrutura') ?>" class="btn btn-secondary">Cancelar</a>
</form>

<?= $this->include('templates/footer') ?>
