<head>
  <!-- Font Awesome -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
  />

  <style>
    /* Estilo para o ícone hamburguer */
      nav.sidebar {
        position: fixed;
        width: 250px;
        height: 100vh;
        left: 0;
        top: 0;
        background-color: #2d3e50;
        transition: left 0.3s ease;
        z-index: 1050; /* acima do conteúdo */
      }

      body.sidebar-collapse nav.sidebar {
        left: -250px; /* esconde o menu ao adicionar classe */
      }

      main.content {
        margin-left: 250px;
        padding: 20px;
        text-align: right;
        transition: margin-left 0.3s ease;
      }

      body.sidebar-collapse main.content {
        margin-left: 0; /* conteúdo expande quando menu oculto */
      }
</style>
</head>

<body>
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <!-- Botão toggle sidebar: hamburguer -->
      <li class="nav-item">
        <a class="nav-link" id="btnToggleSidebar" href="#" role="button" title="Abrir/Fechar Sidebar">
          <i class="fas fa-bars"></i>
        </a>
      </li>

      <!-- Links Início e Contato -->
      <li class="nav-item d-none d-sm-inline-block">
        <a href="<?= base_url('/inicio') ?>" class="nav-link botao-colorido">
          <i class="fas fa-home"></i> Início
        </a>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto"></ul>
  </nav>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const toggleBtn = document.getElementById("btnToggleSidebar");
      if (toggleBtn) {
        toggleBtn.addEventListener("click", function (e) {
          e.preventDefault();
          document.body.classList.toggle("sidebar-collapse");
        });
      }
    });

  </script>
</body>
