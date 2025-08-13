<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<style>
    .content-wrapper {
        margin-left: 250px;
        padding: 20px;
        font-size: 0.9rem;
        overflow-x: hidden;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .form-control, .form-select, textarea {
        font-size: 0.85rem;
        padding: 6px 8px;
    }

    .btn-sm {
        padding: 3px 8px;
        font-size: 0.75rem;
    }

    .table td, .table th {
        word-break: break-word;
        vertical-align: middle;
        white-space: normal;
        font-size: 0.8rem;
    }

    .acoes-log {
        max-height: 70px;
        overflow-y: auto;
        background-color: #f8f9fa;
        padding: 6px;
        border: 1px solid #ccc;
        font-size: 12px;
        white-space: pre-wrap;
    }

    .form-atualizar textarea {
        resize: vertical;
        font-size: 12px;
    }

    .form-atualizar .form-control,
    .form-atualizar .form-select {
        font-size: 12px;
        margin-bottom: 4px;
    }

    .progress {
        height: 12px;
        margin: 0;
    }

    .progress-bar {
        font-size: 10px;
    }

    td.min-width {
        max-width: 80px;
    }

    td.very-small {
        width: 60px;
    }

    td.acoes-coluna {
        max-width: 140px;
        overflow-wrap: break-word;
    }

    .btn-toggle {
        padding: 2px 6px;
        font-size: 11px;
        margin-top: 2px;
    }

    /* Remove qualquer scroll lateral da página */
    body {
        overflow-x: hidden !important;
    }
    .projeto-finalizado {
    background-color: #e6ffe6 !important; /* Verde claro */
    }

</style>

<div class="container-fluid content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="text-center mb-4">Gestão de Projetos</h2>

            <?php if (session()->getFlashdata('msg')): ?>
                <div class="alert alert-info"><?= session()->getFlashdata('msg') ?></div>
            <?php endif; ?>
            <?php if ($tipo === 'admin'): ?>
                <form method="get" class="mb-3">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label for="estado">Estado:</label>
                            <select name="estado" id="estado" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Selecione um Estado --</option>
                                <?php foreach ($estados as $est): ?>
                                    <option value="<?= esc($est) ?>" <?= ($estadoFiltro == $est) ? 'selected' : '' ?>>

                                        <?= esc($est) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="mostrar_concluidos">Projetos concluídos:</label>
                            <select name="mostrar_concluidos" id="mostrar_concluidos" class="form-select" onchange="this.form.submit()">
                                <option value="1" <?= ($mostrarConcluidos ?? '') == '1' ? 'selected' : '' ?>>Mostrar</option>
                                <option value="0" <?= ($mostrarConcluidos ?? '') == '0' ? 'selected' : '' ?>>Ocultar</option>
                            </select>
                        </div>


                        <?php if (!empty($unidades_estado)): ?>
                            <div class="col-md-4 mb-2">
                                <label for="unidade">Unidade:</label>
                                <select name="unidade" id="unidade" class="form-select" onchange="this.form.submit()">
                                    <option value="">-- Todas --</option>
                                    <?php foreach ($unidades_estado as $uni): ?>
                                        <option value="<?= esc($uni) ?>" <?= ($unidadeFiltro == $uni) ? 'selected' : '' ?>>

                                            <?= esc($uni) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>
                </form>

                <form action="<?= base_url('projetos/salvar') ?>" method="post" class="mb-4">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label>Nome do Projeto:</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Descrição:</label>
                            <textarea name="descricao" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Estado:</label>
                            <select name="estado" class="form-select" required>
                                <option value="">Selecione</option>
                                <?php foreach ($estados as $est): ?>
                                    <option value="<?= esc($est) ?>"><?= esc($est) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Unidade:</label>
                            <select name="unidade" class="form-select" required>
                                <option value="">Selecione</option>
                                <?php foreach ($unidades_estado ?? [] as $u): ?>
                                    <option value="<?= esc($u) ?>"><?= esc($u) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <input type="hidden" name="progresso" value="0">
                    <input type="hidden" name="status" value="EM ANDAMENTO">
                    <input type="hidden" name="acoes" value="">
                    <input type="hidden" name="data_conclusao" value="">

                    <button type="submit" class="btn btn-success btn-sm">Salvar Projeto</button>
                </form>
            <?php endif; ?>

            <?php if ($tipo === 'supervisor'): ?>
                <!-- formulário original do supervisor -->
                <form action="<?= base_url('projetos/salvar') ?>" method="post" class="mb-4">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label>Nome do Projeto:</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Descrição:</label>
                            <textarea name="descricao" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label>Selecionar Unidade:</label>
                            <select name="unidade" class="form-select" required>
                                <option value="">Selecione</option>
                                <?php foreach ($unidades_estado ?? [] as $u): ?>
                                    <option value="<?= esc($u) ?>"><?= esc($u) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <input type="hidden" name="estado" value="<?= esc(session()->get('estado')) ?>">
                    <input type="hidden" name="progresso" value="0">
                    <input type="hidden" name="status" value="EM ANDAMENTO">
                    <input type="hidden" name="acoes" value="">
                    <input type="hidden" name="data_conclusao" value="">

                    <button type="submit" class="btn btn-success btn-sm">Salvar Projeto</button>
                </form>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-bordered table-sm table-striped text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Projeto</th>
                            <th>Unidade</th>
                            <th>Estado</th>
                            <th class="very-small">Progresso</th>
                            <th>Status</th>
                            <th class="min-width">Conclusão</th>
                            <th>Descrição</th>
                            <th class="acoes-coluna">Ações</th>
                            <?php if ($tipo === 'tecnico'): ?>
                                <th>Atualizar</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projetos as $projeto): ?>
                            <?php
                                // Se usuário pediu para ocultar concluídos
                                if ($mostrarConcluidos == '0' && $projeto['status'] === 'CONCLUÍDO') {
                                    continue;
                                }

                                // Classe para destacar finalizados
                                $classeLinha = ($projeto['status'] === 'CONCLUÍDO') ? 'projeto-finalizado' : '';
                            ?>
                            <tr class="<?= $classeLinha ?>">
                                <td><?= esc($projeto['nome']) ?></td>
                                <td><?= esc($projeto['unidade']) ?></td>
                                <td><?= esc($projeto['estado']) ?></td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar <?= $projeto['progresso'] < 100 ? 'bg-warning' : 'bg-success' ?>"
                                            style="width: <?= esc($projeto['progresso']) ?>%;">
                                            <?= esc($projeto['progresso']) ?>%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge <?= $projeto['status'] === 'CONCLUÍDO' ? 'bg-success' : ($projeto['status'] === 'PAUSADO' ? 'bg-secondary' : 'bg-warning') ?>">
                                        <?= esc($projeto['status']) ?>
                                    </span>
                                </td>
                                <td><?= esc($projeto['data_conclusao'] ?? '-') ?></td>
                                <td><?= esc($projeto['descricao']) ?></td>
                                <td class="acoes-coluna">
                                    <?php if (!empty($projeto['acoes'])): ?>
                                        <button class="btn btn-outline-secondary btn-sm btn-toggle" onclick="toggleAcoes(this)">Ver</button>
                                        <div class="acoes-log d-none"><?= esc($projeto['acoes']) ?></div>
                                    <?php else: ?>
                                        <span class="text-muted">Nenhuma ação</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($tipo === 'tecnico'): ?>
                                    <td>
                                        <form method="post" action="<?= base_url('/projetos/atualizar/'.$projeto['id']) ?>" class="form-atualizar">
                                            <textarea name="acoes" rows="2" class="form-control" placeholder="Ação..."></textarea>
                                            <input type="number" name="progresso" class="form-control" min="0" max="100"
                                                value="<?= esc($projeto['progresso']) ?>" required>
                                            <select name="status" class="form-select">
                                                <option value="EM ANDAMENTO" <?= $projeto['status'] === 'EM ANDAMENTO' ? 'selected' : '' ?>>EM ANDAMENTO</option>
                                                <option value="PAUSADO" <?= $projeto['status'] === 'PAUSADO' ? 'selected' : '' ?>>PAUSADO</option>
                                                <option value="CONCLUÍDO" <?= $projeto['status'] === 'CONCLUÍDO' ? 'selected' : '' ?>>CONCLUÍDO</option>
                                            </select>
                                            <button class="btn btn-primary btn-sm w-100 mt-1">Atualizar</button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->include('templates/footer') ?>

<script>
    function toggleAcoes(button) {
        const log = button.nextElementSibling;
        log.classList.toggle('d-none');
        button.innerText = log.classList.contains('d-none') ? 'Ver' : 'Fechar';
    }
</script>
