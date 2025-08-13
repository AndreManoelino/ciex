
<style>
  .content-wrapper {
    margin-left: 250px; /* ajustar conforme a largura da sua sidebar */
    padding: 20px;
    overflow-x: hidden;
  }
</style>

<?= $this->include('templates/header') ?>
<?= $this->include('templates/sidebar') ?>

<div class="content-wrapper">
  <div class="container-fluid">
    <h3><?= esc($title) ?></h3>

    <form method="get" action="<?= base_url('/ja_chegou/buscar') ?>" class="mb-4">
      <div class="row g-3 align-items-end">
        <div class="col-md-5">
          <label for="nome" class="form-label">Nome do Cidadão</label>
          <input type="text" name="nome" id="nome" class="form-control" value="<?= esc($nome ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label for="cpf" class="form-label">CPF (com pontos e traço)</label>
            <input type="text" name="cpf" id="cpf" class="form-control" maxlength="14" placeholder="000.000.000-00" value="<?= esc($cpf ?? '') ?>">
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">Buscar</button>
        </div>
      </div>
    </form>

    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Nome</th>
          <th>CPF</th>
          <th>Tipo Documento</th>
          <th>Código Entrega</th>
          <th>Status</th>
          <th>Contato</th>
          <th>Recebido em</th>
          <th>Entregue em</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($documentos)): ?>
          <?php foreach ($documentos as $doc): ?>
            <tr class="<?= $doc['estado'] === 'ENTREGUE' ? 'table-success' : '' ?>">
              <td><?= esc($doc['nome_cidadao']) ?></td>
              <td><?= esc($doc['cpf']) ?></td>
              <td><?= esc($doc['tipo_documento']) ?></td>
              <td><?= esc($doc['codigo_entrega']) ?></td>
              <td><?= esc($doc['estado']) ?></td>
              <td><?= esc($doc['contato']) ?></td>
              
              <td><?= date('d/m/Y H:i', strtotime($doc['data_recebimento'])) ?></td>
              <td><?= $doc['data_entrega'] ? date('d/m/Y H:i', strtotime($doc['data_entrega'])) : '-' ?></td>
              <td>
                <?php if ($doc['estado'] === 'RECEBIDO'): ?>
                  <a href="<?= base_url('/ja_chegou/entregar/' . $doc['id']) ?>" class="btn btn-success btn-sm">Entregar</a>
                <?php else: ?>
                  <span class="badge bg-success">Entregue</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" class="text-center">Nenhum documento encontrado.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <a href="<?= base_url('/ja_chegou') ?>" class="btn btn-secondary mt-3">Voltar</a>
  </div>
</div>

<?= $this->include('templates/footer') ?>
