<?= $this->include('templates/header') ?>

<div id="layoutSidenav">
    <?= $this->include('templates/sidebar') ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h4 class="mt-4 mb-4 text-primary"><?= $item ? 'Editar Registro' : 'Novo Registro' ?></h4>

                <form action="<?= base_url("infraestrutura/{$action}") ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label small" for="unidade">Unidade</label>
                            <input type="text" id="unidade" name="unidade" value="<?= esc($item['unidade'] ?? '') ?>" class="form-control form-control-sm" required>
                        </div>

                        <div class="col-lg-2 col-md-3">
                            <label class="form-label small" for="estado">Estado (UF)</label>
                            <input type="text" id="estado" name="estado" maxlength="2" value="<?= esc($item['estado'] ?? '') ?>" class="form-control form-control-sm" required>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label small" for="operadora">Operadora</label>
                            <input type="text" id="operadora" name="operadora" value="<?= esc($item['operadora'] ?? '') ?>" class="form-control form-control-sm" required>
                        </div>

                        <div class="col-lg-2 col-md-3">
                            <label class="form-label small" for="banda_mb">Banda (MB)</label>
                            <input type="number" id="banda_mb" name="banda_mb" value="<?= esc($item['banda_mb'] ?? '') ?>" class="form-control form-control-sm">
                        </div>

                        <div class="col-lg-2 col-md-3">
                            <label class="form-label small" for="valor">Valor (R$)</label>
                            <input type="text" id="valor" name="valor" value="<?= esc($item['valor'] ?? '') ?>" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label small" for="tipo_servico">Tipo de Serviço</label>
                            <input type="text" id="tipo_servico" name="tipo_servico" value="<?= esc($item['tipo_servico'] ?? '') ?>" class="form-control form-control-sm">
                        </div>

                        <div class="col-lg-6 col-md-12">
                            <label class="form-label small" for="observacoes">Observações</label>
                            <textarea id="observacoes" name="observacoes" rows="2" class="form-control form-control-sm"><?= esc($item['observacoes'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success btn-sm me-2">
                            <i class="fas fa-save me-1"></i> <?= $item ? 'Atualizar' : 'Salvar' ?>
                        </button>
                        <a href="<?= base_url('infraestrutura') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </main>

        <?= $this->include('templates/footer') ?>
    </div>
</div>
