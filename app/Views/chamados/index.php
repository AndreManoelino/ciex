<?= $this->include('templates/sidebar') ?>


<head><style>
  body {
    background: linear-gradient(135deg, #f0fff0, #fffbe6);
    font-family: "Segoe UI", sans-serif;
  }

  h1, h3 {
    text-align: center;
    color: #212529;
    font-weight: bold;
    margin-bottom: 25px;
  }

  .form-label, label {
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 4px;
  }

  input.form-control,
  select.form-control,
  .form-control-file {
    height: 30px !important;
    padding: 3px 8px !important;
    font-size: 13px !important;
  }

  .btn {
    font-size: 13px !important;
    padding: 5px 12px !important;
  }

  .btn-primary {
    background-color: #28a745;
    border-color: #28a745;
  }

  .btn-primary:hover {
    background-color: #ff9800;
    border-color: #ff9800;
  }

  .btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
  }

  .btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
  }

  table th, table td {
    font-size: 13px;
    vertical-align: middle;
  }

  .alert {
    font-size: 14px;
  }

  select.form-control {
    max-width: 300px;
  }
  footer {
            background-color: orange;
            color: white;
            text-align: center;
            padding: 15px 10px;
            font-size: 14px;
            font-weight: 500;
        }
</style>
</head>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1>Sistema de Chamados</h1>

            <!-- Mensagens de feedback -->
            <?php if(session()->getFlashdata('msg')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('msg') ?></div>
            <?php endif; ?>
            <?php if(session()->getFlashdata('erro')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('erro') ?></div>
            <?php endif; ?>

            <!-- Filtro por unidade (somente para supervisores) -->
            <?php if (session('tipo') === 'supervisor'): ?>
                <form method="get" class="mb-4">
                    <label for="filtro_unidade">Filtrar por Unidade:</label>
                    <select name="unidade" class="form-control" onchange="this.form.submit()">
                        <option value="">Todas as unidades</option>
                        <?php foreach ($unidades as $u): ?>
                            <?php $nomeUnidade = is_array($u) ? $u['unidade'] : $u; ?>
                            <option value="<?= esc($nomeUnidade) ?>" <?= ($unidadeFiltro === $nomeUnidade) ? 'selected' : '' ?>>
                                <?= esc($nomeUnidade) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php endif; ?>

            <!-- Formulário de abertura de chamado (somente técnico) -->
            <?php if (session('tipo') === 'tecnico'): ?>
                <form method="post" action="<?= base_url('/chamados/abrir') ?>" class="row g-3 mb-4">
                    <?= csrf_field() ?>
                    <div class="col-lg-12">
                        <label for="sistema" class="form-label">Sistema</label>
                        <select name="sistema" id="sistema" class="form-control" required>
                            <option value="">Selecione o sistema</option>
                            <?php foreach($sistemas as $key => $val): ?>
                                <option value="<?= esc($key) ?>"><?= esc($key) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label for="data_inicio" class="form-label">Data e Hora Início</label>
                        <input type="datetime-local" name="data_inicio" id="data_inicio" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
                    </div>
                    <div class="col-lg-4">
                        <label for="senha_email">Senha do e-mail (somente se desejar enviar)</label>
                        <input type="password" name="senha_email" class="form-control" placeholder="Digite sua senha do e-mail (opcional)">
                    </div>
                    <div class="col-lg-3 align-self-end">
                        <button type="submit" class="btn btn-primary">Abrir Chamado</button>
                    </div>
                </form>
                <hr>
            <?php endif; ?>
                        <?php if ($tipoUsuario === 'admin' || $tipoUsuario === 'administrador'): ?>
            <form method="get" class="mb-3">
                <label>Estado:</label>
                <select name="estado" class="form-control" onchange="this.form.submit()">
                    <option value="todos">Todos</option>
                    <?php foreach ($estados as $estado): ?>
                        <option value="<?= $estado ?>" <?= ($filtroEstado == $estado) ? 'selected' : '' ?>>
                            <?= $estado ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Unidade:</label>
                <select name="unidade" class="form-control" onchange="this.form.submit()">
                    <option value="todas">Todas</option>
                    <?php foreach ($unidades as $unidade): ?>
                        <option value="<?= $unidade ?>" <?= ($unidadeFiltro == $unidade) ? 'selected' : '' ?>>
                            <?= $unidade ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <?php endif; ?>


            <!-- Tabela de Chamados Ativos -->
            <h3>Chamados Ativos</h3>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sistema</th>
                        <th>Unidade</th>
                        <th>Técnico</th>
                        <th>Início</th>
                        <th>Número Chamado</th>
                        <th>Editar Número</th>
                        <th>Encerrar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ativos)): ?>
                        <?php foreach($ativos as $item): ?>
                            <tr>
                                <td><?= esc($item['id']) ?></td>
                                <td><?= esc($item['sistema']) ?></td>
                                <td><?= esc($item['unidade']) ?></td>
                                <td><?= esc($item['tecnico']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($item['data_inicio'])) ?></td>
                                <td><?= esc($item['numero_chamado']) ?></td>
                                <td>
                                    <a href="<?= base_url('/chamados/editarNumero/' . $item['id']) ?>" class="btn btn-warning btn-sm">Editar</a>
                                </td>
                                <td>
                                    <a href="<?= base_url('/chamados/encerrar/' . $item['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Encerrar chamado?')">Encerrar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center">Nenhum chamado ativo encontrado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Tabela de Chamados Recentes -->
            <h3 class="mt-5">Chamados Recentes do Mês</h3>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sistema</th>
                        <th>Unidade</th>
                        <th>Técnico</th>
                        <th>Início</th>
                        <th>Fim</th>
                        <th>Tempo Parado</th>
                        <th>Número Chamado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentes)): ?>
                        <?php foreach($recentes as $item): ?>
                            <tr>
                                <td><?= esc($item['id']) ?></td>
                                <td><?= esc($item['sistema']) ?></td>
                                <td><?= esc($item['unidade']) ?></td>
                                <td><?= esc($item['tecnico']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($item['data_inicio'])) ?></td>
                                <td><?= $item['data_fim'] ? date('d/m/Y H:i', strtotime($item['data_fim'])) : '-' ?></td>
                                <td>
                                    <?php
                                        if ($item['minutos_indisponibilidade'] >= 60) {
                                            $horas = floor($item['minutos_indisponibilidade'] / 60);
                                            $minutos = $item['minutos_indisponibilidade'] % 60;
                                            echo $horas . ' hora(s) ' . $minutos . ' minuto(s)';
                                        } else {
                                            echo $item['minutos_indisponibilidade'] . ' minuto(s)';
                                        }
                                    ?>
                                </td>
                                <td><?= esc($item['numero_chamado']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center">Nenhum chamado recente encontrado.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>
