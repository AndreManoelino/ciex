<?= $this->include('templates/sidebar') ?>

<h3>Histórico de Checklists</h3>

<form method="get" action="">
    <div class="row mb-3">
        <div class="col-md-3">
            <label>Unidade:</label>
            <input type="text" name="unidade" class="form-control" value="<?= esc($filtros['unidade'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label>Técnico:</label>
            <input type="text" name="tecnico" class="form-control" value="<?= esc($filtros['tecnico'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label>Data Inicial:</label>
            <input type="date" name="data_inicio" class="form-control" value="<?= esc($filtros['data_inicio'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label>Data Final:</label>
            <input type="date" name="data_fim" class="form-control" value="<?= esc($filtros['data_fim'] ?? '') ?>">
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Filtrar</button>
</form>

<hr>

<table class="table table-bordered table-sm">
    <thead class="table-light">
        <tr>
            <th>Data/Hora</th>
            <th>Unidade</th>
            <th>Técnico</th>
            <th>Item</th>
            <th>Status</th>
            <th>Observação</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($resultados as $r): ?>
            <tr>
                <td><?= date('d/m/Y H:i', strtotime($r['data_hora'])) ?></td>
                <td><?= esc($r['nome_unidade']) ?></td>
                <td><?= esc($r['nome_tecnico']) ?></td>
                <td><?= esc($r['item']) ?></td>
                <td><?= esc($r['status']) ?></td>
                <td><?= esc($r['observacao']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
