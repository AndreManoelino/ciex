<div class="content-wrapper" style="margin-left: 250px; padding: 20px;">
  <div class="container-fluid">
    <h3><?= esc($title) ?></h3>

    <?php if(session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('info')): ?>
      <div class="alert alert-info"><?= session()->getFlashdata('info') ?></div>
    <?php endif; ?>

    <a href="<?= base_url('/ja_chegou/inserir') ?>" class="btn btn-primary mb-3">Registrar Documento</a>
    <a href="<?= base_url('/ja_chegou/buscar') ?>" class="btn btn-secondary mb-3">Buscar Documento</a>

    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Nome</th>
          <th>CPF</th>
          <th>Tipo Documento</th>
          <th>Código Entrega</th>
          <td>Contato</td>
          <th>Status</th>
          <th>Recebido em</th>
          <th>Entregue em</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($documentos as $doc): ?>
          <tr class="<?= $doc['estado'] === 'ENTREGUE' ? 'table-success' : '' ?>">
            <td><?= esc($doc['nome_cidadao']) ?></td>
            <td><?= esc($doc['cpf']) ?></td>
            <td><?= esc($doc['tipo_documento']) ?? '—' ?></td>
            <td><?= esc($doc['codigo_entrega']) ?></td>
            <td><?= esc($doc['contato']) ?></td>
            <td><?= esc($doc['estado']) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($doc['data_recebimento'])) ?></td>
            <td><?= $doc['data_entrega'] ? date('d/m/Y H:i', strtotime($doc['data_entrega'])) : '-' ?></td>
            <td>
              <?php if ($doc['estado'] === 'RECEBIDO'): ?>
                <a href="<?= base_url('/ja_chegou/entregar/' . $doc['id']) ?>" class="btn btn-success btn-sm">Entregar</a>
              <?php else: ?>
                <span class="badge badge-success">Entregue</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if(empty($documentos)): ?>
          <tr><td colspan="8" class="text-center">Nenhum documento encontrado.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<style>
  @media (max-width: 768px) {
    .content-wrapper {
      margin-left: 0 !important;
      padding: 10px !important;
    }
  }
</style>
