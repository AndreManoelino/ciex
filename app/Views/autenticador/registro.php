<!DOCTYPE html>
<html>
<head>
    <title>Cadastro de Usuário</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            background: linear-gradient(135deg, #e5fbe5, #fff7e6);
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
        }
        body {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 15px 80px;
        }
        .container {
            max-width: 650px;
            width: 100%;
            background: #fff;
            border-radius: 10px;
            padding: 30px 40px;
            box-shadow: 0 0 15px rgba(0,128,0,0.15);
        }
        h3 {
            color: #28a745;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
            text-shadow: 0 0 8px #28a745aa;
        }
        input.form-control, select.form-control {
            border: 1.8px solid #ffa500;
            border-radius: 6px;
            padding: 8px 12px;
        }
        input:focus, select:focus {
            border-color: #28a745;
            box-shadow: 0 0 6px #28a745aa;
            outline: none;
        }
        .btn-success {
            background-color: #28a745;
            border: none;
            font-weight: 600;
            font-size: 16px;
        }
        .btn-success:hover {
            background-color: #ff8c00;
        }
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: orange;
            color: white;
            text-align: center;
            padding: 12px 10px;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 -3px 6px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
<div class="container">
    <h3>Cadastro de Usuário</h3>

    <?php if (isset($errors) && is_array($errors) && count($errors) > 0): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?= esc($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('/registrar') ?>">
        <?= form_input_group('Nome', 'nome', 'text', ['value' => old('nome')]) ?>
        <?= form_input_group('CPF', 'cpf', 'text', ['value' => old('cpf')]) ?>
        <?= form_input_group('Email', 'email', 'email', ['value' => old('email')]) ?>
        <?= form_input_group('Senha', 'senha', 'password') ?>
        <?= form_input_group('Senha SMTP (para envio de e-mails)', 'senha_smtp', 'password', [
            'required' => false,
            'value' => old('senha_smtp') ?? ($usuario['senha_smtp'] ?? '')
        ]) ?>

        <div class="mb-3 form-group">
            <label for="tipo">Tipo de Usuário</label>
            <select name="tipo" id="tipo" class="form-control" required>
                <option value="">Selecione...</option>
                <option value="admin" <?= old('tipo') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                <option value="supervisor" <?= old('tipo') === 'supervisor' ? 'selected' : '' ?>>Supervisor</option>
                <option value="tecnico" <?= old('tipo') === 'tecnico' ? 'selected' : '' ?>>Técnico</option>
                <option value="atendente" <?= old('tipo') === 'atendente' ? 'selected' : '' ?>>Atendente</option>
            </select>
        </div>

        <div id="estado-group" class="mb-3">
            <?= form_select_group('Estado', 'estado', array_combine($estados, $estados), ['value' => old('estado')]) ?>
        </div>

        <div class="mb-3" id="unidade-group">
            <label for="unidade">Unidade:</label>
            <select name="unidade" id="unidade" class="form-control" required>
                <option value="">Selecione o estado primeiro</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success w-100">Cadastrar</button>
    </form>

    <p class="text-center mt-3">
        <a href="<?= base_url('/login') ?>">Voltar ao Login</a>
    </p>
</div>



<script>
    const unidades = <?= json_encode($unidades) ?>;
    const tipoSelect = document.getElementById('tipo');
    const estadoSelect = document.getElementById('estado');
    const unidadeSelect = document.getElementById('unidade');
    const unidadeGroup = document.getElementById('unidade-group');
    const estadoGroup = document.getElementById('estado-group');

    function atualizarUnidades() {
        const estado = estadoSelect.value;
        unidadeSelect.innerHTML = '<option value="">Selecione</option>';

        if (unidades[estado]) {
            unidades[estado].forEach(function(u) {
                const option = document.createElement('option');
                option.value = u;
                option.textContent = u;
                unidadeSelect.appendChild(option);
            });
        }

        if (tipoSelect.value === 'supervisor') {
            unidadeGroup.style.display = 'none';
            unidadeSelect.innerHTML = '<option value="TODAS_DO_ESTADO">Todas do Estado</option>';
        } else {
            unidadeGroup.style.display = 'block';
        }
        if (tipoSelect.value === 'admin' || tipoSelect.value === 'administrador') {
            estadoSelect.disabled = true;
            unidadeSelect.disabled = true;
            estadoGroup.style.display = 'none';
            unidadeGroup.style.display = 'none';

            estadoSelect.innerHTML = '<option value="BRASIL">BRASIL</option>';
            unidadeSelect.innerHTML = '<option value="TODAS">TODAS AS UNIDADES</option>';
        } else {
            estadoSelect.disabled = false;
            unidadeSelect.disabled = false;
            estadoGroup.style.display = 'block';
            unidadeGroup.style.display = 'block';
        }

    }

    function atualizarCamposPorTipo() {
        const tipo = tipoSelect.value;

        if (tipo === 'admin') {
            estadoSelect.disabled = true;
            unidadeSelect.disabled = true;
            unidadeGroup.style.display = 'none';
            estadoGroup.style.display = 'none';
        } else if (tipo === 'supervisor') {
            estadoSelect.disabled = false;
            estadoGroup.style.display = 'block';
            unidadeGroup.style.display = 'none';
            unidadeSelect.innerHTML = '<option value="TODAS_DO_ESTADO">Todas do Estado</option>';
        } else if (tipo === 'tecnico' || tipo === 'atendente' || tipo === 'supervisor_atendimento') {
            estadoSelect.disabled = false;
            unidadeSelect.disabled = false;
            estadoGroup.style.display = 'block';
            unidadeGroup.style.display = 'block';
            atualizarUnidades();
        } else {
            estadoGroup.style.display = 'none';
            unidadeGroup.style.display = 'none';
        }
    }

    tipoSelect.addEventListener('change', atualizarCamposPorTipo);
    estadoSelect.addEventListener('change', atualizarUnidades);

    window.addEventListener('DOMContentLoaded', () => {
        atualizarCamposPorTipo();

        // Preencher unidade se estado já estiver selecionado
        const estado = estadoSelect.value;
        const unidadeAntiga = "<?= old('unidade') ?>";
        if (estado && unidades[estado]) {
            atualizarUnidades();
            if (unidadeAntiga) {
                unidadeSelect.value = unidadeAntiga;
            }
        }
    });
</script>
</body>
</html>
