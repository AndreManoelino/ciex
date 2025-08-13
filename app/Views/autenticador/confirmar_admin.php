<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Confirmação de Administrador</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
        body {
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h4 class="text-success mb-3 text-center">Confirmação de Administrador</h4>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= base_url('/confirmar_admin') ?>">
            <div class="mb-3">
                <input type="text" name="cpf_confirm" class="form-control" placeholder="CPF do Administrador" required>
            </div>
            <div class="mb-3">
                <input type="password" name="senha_confirm" class="form-control" placeholder="Senha" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Confirmar</button>
        </form>

        <a href="<?= base_url('/login') ?>" class="btn btn-link mt-3 w-100 text-center">Voltar ao Login</a>
    </div>
</body>
</html>
