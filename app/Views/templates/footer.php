<div class="wrapper">

  <?= $this->include('templates/sidebar') ?>
  <?= $this->include('templates/header') ?>

  <div class="content-wrapper">
    <!-- Seu conteúdo aqui -->
  </div>

  <footer class="main-footer">
    André Manoelino 06/06/2025 - <a href="https://www.linkedin.com/company/cixcitizenexperience">Cix-Citizen Experience</a>
  </footer>
</div>

<style>
  .content-wrapper {
    margin-left: 250px;
    padding: 20px;
    transition: margin-left 0.3s ease;
  }

  .main-footer {
    margin-left: 250px;
    width: calc(100% - 250px);
    position: fixed;
    bottom: 0;
    background-color: orange;
    color: black;
    text-align: center;
    line-height: 50px;
    padding: 0 15px;
    box-sizing: border-box;
    z-index: 1030;
    transition: margin-left 0.3s ease, width 0.3s ease;
  }

  /* Quando o menu está colapsado */
  body.sidebar-collapse .main-footer,
  body.sidebar-collapse .content-wrapper {
    margin-left: 80px;
    width: calc(100% - 80px);
  }

  body, html {
    margin: 0;
    padding: 0;
    height: 100%;
    overflow-x: hidden;
  }
</style>
