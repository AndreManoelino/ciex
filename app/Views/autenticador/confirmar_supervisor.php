<!-- app/Views/autenticador/confirmar_supervisor.php -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Validação Supervisor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
        body {
            background: linear-gradient(135deg, #e5fbe5, #fff7e6);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
        }
        .container {
            background: white;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,128,0,0.15);
            max-width: 400px;
            width: 100%;
        }
        h3 {
            color: #28a745;
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
            text-shadow: 0 0 8px #28a745aa;
        }
        input.form-control {
            border: 1.8px solid #ffa500;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 15px;
        }
        input:focus {
            border-color: #28a745;
            box-shadow: 0 0 6px #28a745aa;
            outline: none;
        }
        .btn-success {
            background-color: darkgreen;
            border: none;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
        }
        .btn-success:hover {
            background-color: #ff8c00;
        }
        .alert {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Confirmação Supervisor</h3>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-warning"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= base_url('/confirmar') ?>">
            <input type="text" name="cpf_confirm" placeholder="Digite seu CPF" class="form-control" required />
            <input type="password" name="senha_confirm" placeholder="Digite sua senha" class="form-control" required />
            <button type="submit" class="btn btn-success">Validar</button>
            <a href="<?= base_url('/logout') ?>" class="btn btn-danger">Sair</a>
        </form>

    </div>
</body>
</html>
