<?= $this->include('templates/sidebar') ?>
<?php
helper(['titulo', 'permissao', 'localizacao']);

$estadoSelecionado  = $estadoSelecionado ?? '';
$unidadeSelecionado = $unidadeSelecionado ?? '';
$unidades           = $unidades ?? [];
$estados            = $estados ?? [];
$solicitacoes       = $solicitacoes ?? [];

$tipoUsuario = session('tipo');

$unidadeTecnico = session('unidade') ?? '';
$nomeSupervisor = ''; 
$nomeTecnico    = session('nome') ?? '';
?>
<style>
  .content-wrapper {
    background: url('/theme/dist/img/cix_case.jpg') no-repeat center center fixed;
    background-size: cover;
  }

  /* TITULOS / LABELS / CABEÇALHO TABELA */
  h1, h2, h3, h4, h5, h6,
  label,
  .content-header,
  .table thead th {
    color: lightgrayblack !important; /* <<< COR DO TEXTO (títulos, labels e th) */
    font-weight: bold;
  }

  /* CAMPOS DE FORMULÁRIO */
  .form-control,
  .form-control-file,
  select {
    background-color: white !important; /* <<< FUNDO DOS CAMPOS */
    border: 2px solid black !important; /* <<< BORDA DOS CAMPOS */
    color:  black !important; /* <<< COR DO TEXTO DENTRO DO CAMPO */
    font-weight: bold;
    box-shadow: 0 2px 6px black; /* <<< SOMBRA DOS CAMPOS */
  }

  /* QUANDO O CAMPO ESTÁ EM FOCO */
  .form-control:focus, select:focus {
    background-color: white !important; /* <<< FUNDO DO CAMPO AO CLICAR */
    border-color: #0056b3 !important;  /* <<< COR DA BORDA AO CLICAR */
    box-shadow: 0 0 8px rgba(0,123,255,0.7); /* <<< BRILHO AO CLICAR */
  }

  /* BOTÕES */
  .btn {
    font-weight: bold;
    border-radius: 6px;
    /* você pode mudar a cor do botão via class bootstrap (btn-primary, btn-success, etc) */
  }
</style>


<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <?= titulo('Solicitação de Conserto') ?>

      <?php if(session()->getFlashdata('msg')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('msg') ?></div>
      <?php endif; ?>
      <?php if(session()->getFlashdata('erro')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('erro') ?></div>
      <?php endif; ?>

      <!-- FORMULÁRIO TÉCNICO -->
      <?php if ($tipoUsuario === 'tecnico'): ?>
        <form action="<?= base_url('/clientes/salvar') ?>" method="post" enctype="multipart/form-data" class="mb-4">
          <?= csrf_field() ?>
          <!-- Campos do técnico -->
          <div class="form-row">
            <div class="form-group col-md-3">
              <label for="nome_empresa">Nome da Empresa</label>
              <input type="text" name="nome_empresa" class="form-control" required>
            </div>
          
            <div class="form-group col-md-3">
              <label for="cidade">Cidade</label>
              <input type="text" name="cidade" class="form-control" required>
            </div>
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="estado">Estado</label>
                <input type="text" name="estado" class="form-control" value="<?= esc(session('estado')) ?>" readonly required>
              </div>
              <div class="form-group col-md-5">
                <label for="cnpj">CNPJ</label>
                <input type="text" name="cnpj" class="form-control" required>
              </div>
              </div>
          
              <div class="form-group col-md-3">
                <label for="endereco_rua">Endereço (Rua)</label>
                <input type="text" name="endereco_rua" class="form-control" required>
              </div>
              <div class="form-group col-md-3">
                <label for="bairro">Bairro</label>
                <input type="text" name="bairro" class="form-control" required>
              </div>
              <div class="form-group col-md-3">
                <label for="numero">Número</label>
                <input type="text" name="numero" class="form-control" required>
              </div>
            
              <div class="form-group col-md-3">
                <label for="nome_equipamento">Nome do Equipamento</label>
                <input type="text" name="nome_equipamento" class="form-control" required>
              </div>
              <div class="form-group col-md-3">
                <label for="unidade">Unidade</label>
                <input type="text" name="unidade" class="form-control" value="<?= esc($unidadeTecnico) ?>" readonly>
              </div>
              <div class="form-group col-md-3">
                <label for="responsavel">Responsável</label>
                <input type="text" name="responsavel" class="form-control" value="<?= esc($nomeSupervisor) ?> / <?= esc($nomeTecnico) ?>" readonly>
              </div>
              <div class="form-group col-md-3">
                <label for="documento_orcamento">Orçamento (PDF, JPG, PNG)</label>
                <input type="file" name="documento_orcamento" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png" required>
              </div>
          </div>
          <button type="submit" class="btn btn-primary">Enviar Solicitação</button>
        </form>
        <hr>
      <?php endif; ?>

      <!-- FORMULÁRIO SUPERVISOR -->
      <?php if ($tipoUsuario === 'supervisor'): ?>
        <form method="get" action="<?= base_url('/clientes/novo') ?>" class="mb-3" id="formSupervisor">
          <input type="hidden" name="estado" value="<?= esc(session('estado')) ?>">
          <label for="unidadeSupervisor">Filtrar por Unidade (<?= esc(session('estado')) ?>):</label>
          <select name="unidade" id="unidadeSupervisor" class="form-control" onchange="document.getElementById('formSupervisor').submit()">
            <option value="">Todas as unidades</option>
            <?php if (!empty($unidades)): ?>
              <?php foreach ($unidades as $unidade): ?>
                <option value="<?= esc($unidade) ?>" <?= ($unidadeSelecionado == $unidade) ? 'selected' : '' ?>>
                  <?= esc($unidade) ?>
                </option>
              <?php endforeach; ?>

            <?php else: ?>
              <option value="" disabled>Nenhuma unidade disponível</option>
            <?php endif; ?>
          </select>
        </form>
      <?php endif; ?>


      <!-- FORMULÁRIO ADMIN -->
      <?php if ($tipoUsuario === 'admin'): ?>
        <form method="get" action="<?= base_url('clientes/novo') ?>" class="form-inline mb-3" id="formAdmin">
          <div class="form-group mr-2">
            <label for="estadoAdmin" class="mr-2">Filtrar por Estado:</label>
            <select name="estado" id="estadoAdmin" class="form-control">
              <option value="">Selecione um estado</option>
              <?php foreach ($estados as $estado): ?>
                <option value="<?= esc($estado) ?>" <?= ($estadoSelecionado == $estado) ? 'selected' : '' ?>>
                    <?= esc($estado) ?>
                </option>
              <?php endforeach; ?>

            </select>
          </div>

          <?php if (!empty($estadoSelecionado)): ?>
            <div class="form-group ml-2">
              <label for="unidadeAdmin" class="mr-2">Unidade:</label>
              <select name="unidade" id="unidadeAdmin" class="form-control">
                <option value="">Todas</option>
                <?php if (!empty($unidades)): ?>
                  <?php foreach ($unidades as $unidade): ?>
                    <option value="<?= esc($unidade) ?>" <?= $unidadeSelecionado === $unidade ? 'selected' : '' ?>><?= esc($unidade) ?></option>
                  <?php endforeach; ?>
                <?php else: ?>
                  <option value="" disabled>Nenhuma unidade disponível</option>
                <?php endif; ?>
              </select>
            </div>
          <?php endif; ?>
        </form>
        <script>
          document.getElementById('estadoAdmin').addEventListener('change', function() {
            const estado = this.value;
            const url = new URL(window.location.href);
            if (estado) {
              url.searchParams.set('estado', estado);
            } else {
              url.searchParams.delete('estado');
            }
            url.searchParams.delete('unidade');
            window.location.href = url.toString();
          });

          document.getElementById('unidadeAdmin')?.addEventListener('change', function() {
            const unidade = this.value;
            const estado = document.getElementById('estadoAdmin').value;
            const url = new URL(window.location.href);
            if (estado) url.searchParams.set('estado', estado);
            if (unidade) url.searchParams.set('unidade', unidade);
            else url.searchParams.delete('unidade');
            window.location.href = url.toString();
          });
        </script>
      <?php endif; ?>


      <!-- TABELA DE SOLICITAÇÕES -->
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Empresa</th>
            <th>Cidade/Estado</th>
            <th>Equipamento</th>
            <th>Status</th>
            <th>Orçamento</th>
            <th>NF</th>
            <th>Unidade</th>
            <th>Responsável</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($solicitacoes)): ?>
            <?php foreach($solicitacoes as $item): ?>
              <tr>
                <td><?= esc($item['id']) ?></td>
                <td><?= esc($item['nome_empresa']) ?></td>
                <td><?= esc($item['cidade']) ?>/<?= esc($item['estado']) ?></td>
                <td><?= esc($item['nome_equipamento']) ?></td>
                <td>
                  <?php if ($item['status'] === \App\Models\EmpresaConcertoModel::STATUS_AGUARDANDO): ?>
                    <span class="badge badge-warning">Aguardando Aprovação</span>
                  <?php elseif ($item['status'] === \App\Models\EmpresaConcertoModel::STATUS_APROVADO): ?>
                    <span class="badge badge-success">Aprovado</span>
                  <?php elseif ($item['status'] === \App\Models\EmpresaConcertoModel::STATUS_ENVIADO): ?>
                    <span class="badge badge-info">Enviado</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($item['orcamento_path'])): ?>
                    <a href="<?= base_url('clientes/downloadOrcamento/' . $item['orcamento_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Ver</a>
                  <?php else: ?>
                    <span class="text-muted">N/A</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($item['nf_path'])): ?>
                    <a href="<?= base_url('clientes/downloadNF/' . $item['nf_path']) ?>" class="btn btn-sm btn-outline-secondary" target="_blank">Ver NF</a>
                  <?php else: ?>
                    <span class="text-muted">N/A</span>
                  <?php endif; ?>
                </td>
                <td><?= esc($item['unidade']) ?></td>
                <td><?= isset($item['tecnico']) ? esc($item['tecnico']) : '—' ?></td>

                <td>
                  <?php if (
                    $item['status'] === \App\Models\EmpresaConcertoModel::STATUS_AGUARDANDO &&
                    $tipoUsuario === 'supervisor' &&
                    strtolower(trim(session('estado'))) === strtolower(trim($item['estado']))
                  ): ?>
                    <a href="<?= base_url('/clientes/aprovar/' . $item['id']) ?>" class="btn btn-success btn-sm">Aprovar</a>
                  <?php endif; ?>

                  <?php if (
                    $item['status'] === \App\Models\EmpresaConcertoModel::STATUS_APROVADO &&
                    $tipoUsuario === 'tecnico' &&
                    session('id') === $item['usuario_id']
                  ): ?>
                    <form action="<?= base_url('/clientes/enviarNF/' . $item['id']) ?>" method="post" enctype="multipart/form-data" class="mt-2">
                      <?= csrf_field() ?>
                      <input type="date" name="data_envio" class="form-control form-control-sm mb-1" required>
                      <input type="file" name="documento_nf" class="form-control-file mb-1" required>
                      <button type="submit" class="btn btn-sm btn-primary">Enviar NF</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="10" class="text-center">Nenhuma solicitação encontrada.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
