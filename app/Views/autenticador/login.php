<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    html, body {
      height: 100%;
      margin: 0;
    }

    body {
      background: darkorange;
      display: flex;
      flex-direction: column;
      font-family: Arial, sans-serif;
    }

    .main-content {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      padding-top: 190px;
    }

    .title-box {
      background-color: #ffffff;
      border: 3px solid #ffa500;
      border-left: 8px solid green;
      padding: 15px 50px;
      border-radius: 10px;
      margin-bottom: 30px;
      box-shadow: 0 0 15px rgba(0,0,0,0.08);
    }

    .form-container {
      background: #ffffff;
      border-radius: 12px;
      padding: 30px;
      max-width: 420px;
      box-shadow: 0 10px 30px rgba(0, 128, 0, 0.1);
      width: 100%;
      margin-bottom: 40px;
    }

    .btn-custom {
      background-color: orange;
      border: none;
      color: white;
    }

    .btn-custom:hover {
      background-color: green;
    }
    footer {
            background-color:whitesmoke;
            color: black;
            text-align: center;
            padding: 15px 10px;
            font-size: 14px;
            font-weight: 500;
        }
  </style>
</head>
<body>

  <div class="main-content">

    <div class="title-box">
      <h2 class="text-center m-0">Sistema Tec CIX-CITZEN</h2>
    </div>

    <div class="form-container">
      <h4 class="text-center text-warning">Login</h4>

      <?php if (session()->getFlashdata('msg')): ?>
        <div class="alert alert-success"> <?= session()->getFlashdata('msg') ?> </div>
      <?php endif; ?>

      <?php if (isset($errors)): ?>
        <div class="alert alert-danger"> <?= esc($errors) ?> </div>
      <?php endif; ?>

      <form method="post" action="<?= base_url('/login') ?>">
        <div class="mb-3">
          <label for="cpf" class="form-label">CPF:</label>
          <input type="text" name="cpf" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="senha" class="form-label">Senha:</label>
          <input type="password" name="senha" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Entrar</button>
      </form>

      <hr>
      <p class="text-center">
        <!--<a href="<?= base_url('/registrar') ?>" class="text-decoration-none text-warning">Cadastrar</a> -->
        <!--<a href="<?= base_url('/recuperar-senha') ?>" class="text-decoration-none text-warning">Recuperar Senha</a> -->
      </p>
    </div>

  </div>


</body>
</html>

