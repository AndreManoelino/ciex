<!-- Main Sidebar Container -->


<aside class="main-sidebar sidebar-dark-primary elevation-4" style="display: flex; flex-direction: column; height: 100vh; position: fixed;">
  <!-- Sidebar -->
  <div class="sidebar d-flex flex-column flex-grow-1" style="overflow-y: auto;">

    <!-- Sidebar user panel -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="<?= base_url('theme/dist/img/fundo.jpg') ?>" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block" style="font-size: 14px;">
            <span>CIX-CITZEN</span><br>
            <span></span>
        </a>
      </div>
    </div>

    <!-- SidebarSearch Form -->
    <div class="form-inline px-3 mb-3">
      <div class="input-group" data-widget="sidebar-search">
        <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search" style="font-size: 14px;">
        <div class="input-group-append">
          <button class="btn btn-sidebar" style="padding: 6px 10px;">
            <i class="fas fa-search fa-fw"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2 flex-grow-1">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false" style="font-size: 14px;">

        <?php if (!in_array(session()->get('tipo'), ['atendente', 'supervisor_atendimento','atendente_rg'])): ?>
          <li class="nav-item menu-open">
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?= base_url('clientes/novo') ?>" class="nav-link">
                  <i class="fas fa-network-wired nav-icon"></i>
                  <p>Clientes</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url('/chamados') ?>" class="nav-link">
                  <i class="fas fa-network-wired nav-icon"></i>
                  <p>Sistema de Chamados</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('compras') ?>" class="nav-link">          
              <i class="fas fa-network-wired nav-icon"></i>
              <p>Compras Mensais</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('equipamentos') ?>" class="nav-link">
              <i class="fas fa-network-wired nav-icon"></i>
              <p>Equipamentos de Backup</p>
            </a>
          </li>
          <li class="nav-item">        
            <a href="<?= base_url('emprestimos') ?>" class="nav-link">
              <i class="fas fa-network-wired nav-icon"></i>
              <p>Empréstimos</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('mapeamento-rede') ?>" class="nav-link">
              <i class="fas fa-network-wired nav-icon"></i>
              <p>Infra de Rede</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('projetos') ?>" class="nav-link">
              <i class="fas fa-network-wired nav-icon"></i>
              <p>Gestão de Projetos</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('/formulario') ?>" class="nav-link">
              <i class="fas fa-network-wired nav-icon"></i>
              <p>Teste de Formulário</p>
            </a>
          </li>
        <?php endif; ?>

        <?php if (in_array(session()->get('tipo'), ['atendente', 'supervisor_atendimento','atendente_rg'])): ?>
          <li class="nav-item">
            <a href="<?= base_url('/ja_chegou') ?>" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Já Chegou</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('/estoque') ?>" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Estoque</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('/encaixes') ?>" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Encaixes de Atendimento</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('/atendimentos') ?>" class="nav-link">
              <i class="far fa-circle nav-icon"></i>
              <p>Atendimentos RG </p>
            </a>
          </li>
        <?php endif; ?>

      </ul>
    </nav>


    <!-- Botão sair -->
    <div style="padding: 10px; border-top: 1px solid orange;">
      <a href="<?= base_url('/logout') ?>" class="btn btn-orange btn-block" style="font-size: 13px; padding: 8px 0;">
        <i class="fas fa-sign-out-alt"></i> Sair
      </a>
    </div>
  </div>
</aside>

<style>
  .main-sidebar {
    width: 250px;
  }

  @media (max-width: 768px) {
    .main-sidebar {
      width: 100%;
      position: relative;
      height: auto;
      flex-direction: row;
    }
    .main-sidebar .sidebar {
      flex-direction: row;
      overflow-x: auto;
      white-space: nowrap;
    }
    .main-sidebar .nav-sidebar > li {
      display: inline-block;
      float: none;
    }
    .main-sidebar .nav-sidebar > li > a {
      padding: 10px 15px;
    }
    .main-sidebar .btn-danger {
      font-size: 12px !important;
      padding: 6px !important;
      width: 100%;
    }
  }
</style>
