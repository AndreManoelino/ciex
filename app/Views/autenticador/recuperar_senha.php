<!DOCTYPE html>
<html>
<head>
    <title>Recuperar Senha</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            background: orange;
            display: flex;
            flex-direction: column;
            font-family: Arial, sans-serif;
        }

        header {
            background-color: #ffffff;
            padding: 20px 10px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        header h2 {
            font-size: 28px;
            color: #28a745; /* Verde */
            font-weight: bold;
        }

        header h2 span {
            color: #ff8c00; /* Laranja */
        }

        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }

        .form-container {
            max-width: 420px;
            width: 100%;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 128, 0, 0.1);
        }

        .btn-warning {
            background-color: #ff8c00;
            border: none;
        }

        .btn-warning:hover {
            background-color: #28a745;
        }

        footer {
            background-color: green;
            color: white;
            text-align: center;
            padding: 15px 10px;
            font-size: 14px;
            font-weight: 500;
        }
    </style>
    <script>
        function validarSenhas() {
            const senha = document.getElementById("senha").value;
            const confirmar = document.getElementById("confirmar_senha").value;

            if (senha !== confirmar) {
                alert("As senhas não conferem. Por favor, verifique.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

<header>
    <h2>Sistema Tec <span>CIX-CITZEN</span></h2>
</header>

<div class="main-content">
    <div class="form-container">
        <h4 class="text-center text-success mb-4">Recuperar Senha</h4>

        <?php if (isset($errors)): ?>
            <div class="alert alert-danger"> <?= esc($errors) ?> </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('cpf')): ?>
            <div class="alert alert-warning text-center">
                <strong>Primeiro acesso!</strong><br>
                Defina sua nova senha para continuar.
            </div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('/recuperar-senha') ?>" onsubmit="return validarSenhas()">
            <div class="mb-3">
                <label class="form-label">CPF:</label>
                <input
                    type="text"
                    name="cpf"
                    class="form-control"
                    value="<?= session()->getFlashdata('cpf') ?? '' ?>"
                    <?= session()->getFlashdata('cpf') ? 'readonly' : '' ?>
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Nova Senha:</label>
                <input type="password" id="senha" name="senha" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirmar Nova Senha:</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success w-100">Atualizar Senha</button>
        </form>

        <?php if (!session()->getFlashdata('cpf')): ?>
            <p class="text-center mt-3">
                <a href="<?= base_url('/login') ?>" class="text-decoration-none text-success">Voltar ao Login</a>
            </p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
