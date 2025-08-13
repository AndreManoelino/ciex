
<header>
  <style>



  .row > [class*='col-'] {
    padding-left: 8px !important;
    padding-right: 8px !important;
  }
  </style>


</header>

<?= $this->include('templates/sidebar') ?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="alert alert-success">
        <h4 class="mb-1" style="font-size: 1.25rem;">Bem-vindo ao Sistema de Tecnologia da Informação da CIX, <?= session('nome') ?>!</h4>
        <p class="mb-0" style="font-size: 0.95rem;"><?= session('unidade') ?> - <?= session('estado') ?></p>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="container-fluid">

      <!-- Abas -->
      <ul class="nav nav-tabs" id="infoTabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="lgpd-tab" data-toggle="tab" href="#lgpd" role="tab">LGPD</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="conduta-tab" data-toggle="tab" href="#conduta" role="tab">Conduta em TI</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="institucional-tab" data-toggle="tab" href="#institucional" role="tab">Sobre a CiX</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="eventos-tab" data-toggle="tab" href="#eventos" role="tab">Eventos Internacionais</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="estrutura-tab" data-toggle="tab" href="#estrutura" role="tab">Estrutura Organizacional</a>
        </li>
      </ul>

      <!-- Conteúdo das abas -->
      <div class="tab-content p-3 border bg-light" id="infoTabsContent">

        <!-- LGPD -->
        <div class="tab-pane fade show active" id="lgpd" role="tabpanel">
          <div class="row">
            <div class="col-md-9">
              <h5 class="mb-2">Lei Geral de Proteção de Dados (LGPD)</h5>
              <p class="small">
                A LGPD (Lei nº 13.709/2018) estabelece diretrizes para o uso, tratamento e proteção dos dados pessoais no Brasil.
                Todos os colaboradores devem estar atentos à coleta, uso e armazenamento correto das informações.
              </p>
              <ul class="small">
                <li>Não compartilhe dados de terceiros sem consentimento.</li>
                <li>Evite armazenar senhas em arquivos de texto ou planilhas.</li>
                <li>Respeite os princípios de necessidade, segurança e finalidade dos dados.</li>
              </ul>
            </div>
            <div class="col-md-3 text-right">
              <img src="<?= base_url('theme/dist/img/lgpd.jpg') ?>" alt="LGPD" class="img-fluid" style="max-height: 140px;">
            </div>
          </div>
        </div>

        <!-- Conduta -->
        <div class="tab-pane fade" id="conduta" role="tabpanel">
          <div class="row">
            <div class="col-md-9">
              <h5 class="mb-2">Boas Práticas em Informática</h5>
              <ul class="small">
                <li>Utilize senhas fortes e troque-as regularmente.</li>
                <li>Evite clicar em links suspeitos ou de fontes desconhecidas.</li>
                <li>Faça logout ao sair do sistema.</li>
                <li>Não instale softwares não autorizados.</li>
                <li>Mantenha os sistemas atualizados e seguros.</li>
              </ul>
            </div>
            <div class="col-md-3 text-right">
              <img src="<?= base_url('theme/dist/img/seguranca.jpg') ?>" alt="Segurança" class="img-fluid" style="max-height: 140px;">
            </div>
          </div>
        </div>

        <!-- Institucional -->
        <div class="tab-pane fade" id="institucional" role="tabpanel">
          <div class="row">
            <div class="col-md-9">
              <h5 class="mb-2">Sobre a CiX Citizen Experience</h5>
              <p class="small">
                A CiX é referência nacional em soluções digitais para cidadania. Com atuação em diversos estados, oferecemos serviços que colocam o cidadão no centro da experiência pública.
              </p>
              <p class="small">Entre os pilares da nossa atuação estão:</p>
              <ul class="small">
                <li>Transformação digital com foco no cidadão</li>
                <li>Serviços phygitais (físico + digital)</li>
                <li>Segurança, escalabilidade e inovação</li>
              </ul>
            </div>
            <div class="col-md-3 text-right">
              <img src="<?= base_url('theme/dist/img/cix_logo.png') ?>" alt="CiX" class="img-fluid" style="max-height: 100px;">
            </div>
          </div>
        </div>

        <!-- Eventos -->
        <div class="tab-pane fade" id="eventos" role="tabpanel">
          <div class="row">
            <div class="col-md-9">
              <h5 class="mb-2">Eventos Internacionais</h5>
              <div class="small">
                <p><strong>Barcelona:</strong> Participamos da maior feira de Smart Cities do mundo...</p>
                <p><strong>Dubai:</strong> Apresentamos soluções de IA voltadas ao cidadão...</p>
                <p><strong>Barcelona:</strong> Participamos da maior feira de Smart Cities do mundo...</p>
                <p><strong>Dubai:</strong> Apresentamos soluções de IA voltadas ao cidadão...</p>
                <p><strong>Barcelona:</strong> Participamos da maior feira de Smart Cities do mundo...</p>
         

              </div>
            </div>
            <div class="col-md-3 text-right">
              <img src="<?= base_url('theme/dist/img/eventos.jpg') ?>" alt="Eventos" class="img-fluid" style="max-height: 140px;">
            </div>
          </div>
        </div>

        <!-- Estrutura Organizacional (com visual em árvore hierárquica) -->
        <div class="tab-pane fade" id="estrutura" role="tabpanel">
         

          <style>
            .org-chart {
              text-align: center;
              font-size: 15px;
              line-height: 1.6;
            }
            .org-level {
              margin-bottom: 25px;
            }
            .org-title {
              font-weight: bold;
              font-size: 1.2rem;
              color: #0c63e4;
            }
            .org-boxes {
              display: flex;
              justify-content: center;
              flex-wrap: wrap;
              gap: 15px;
              margin-top: 10px;
            }
            .org-box {
              background-color: #f8f9fa;
              border: 1px solid #dee2e6;
              border-radius: 6px;
              padding: 12px 18px;
              min-width: 180px;
              box-shadow: 1px 1px 5px rgba(0,0,0,0.05);
            }
            .org-box strong {
              display: block;
              color: #343a40;
            }
            .org-box a {
              color: #0c63e4;
              font-weight: 500;
              text-decoration: none;
            }
          </style>

          <div class="org-chart">

            <!-- Nível 1: Supervisores -->
            <div class="org-level">
              <div class="org-title">Supervisores Minas Gerais</div>
              <div class="org-boxes">
                <div class="org-box">
                  <img src="<?= base_url('theme/dist/img/fundo.jpg') ?>" alt="Foto Roberta" style="width: 30px; height: 30px; border-radius: 50%; vertical-align: middle; margin-right: 8px;">
                  <strong>Cristiano Guimarães</strong>
                  Supervisor Geral<br>
                  <a href="https://wa.me/5599777777777" target="_blank">WhatsApp</a>
                </div>
                <div class="org-box">
                  <img src="<?= base_url('theme/dist/img/fundo.jpg') ?>" alt="Foto Roberta" style="width: 30px; height: 30px; border-radius: 50%; vertical-align: middle; margin-right: 8px;">
                  <strong>Roberta Guedes</strong><br>
                  Supervisora Regional<br>
                  <a href="https://wa.me/5599777777777" target="_blank">WhatsApp</a>
                </div>

            </div>

            <!-- Nível 2: Analistas -->
            <div class="org-level">
              <div class="org-title">Analistas de Infraestrutura</div>
              <div class="org-boxes">
                <div class="org-box">
                  <img src="<?= base_url('theme/dist/img/fundo.jpg') ?>" alt="Foto Roberta" style="width: 30px; height: 30px; border-radius: 50%; vertical-align: middle; margin-right: 8px;">
                  <strong>Luiz Amorin</strong>
                  Analista de Infraestrutura<br>
                  <a href="https://wa.me/5599666666666" target="_blank">WhatsApp</a>
                </div>
                <div class="org-box">
                  <img src="<?= base_url('theme/dist/img/fundo.jpg') ?>" alt="Foto Roberta" style="width: 30px; height: 30px; border-radius: 50%; vertical-align: middle; margin-right: 8px;">
                  <strong>Ruan Louzada</strong>
                  Analista de Suporte Pleno<br>
                  <a href="https://wa.me/5599777777777" target="_blank">WhatsApp</a>
                </div>
                <div class="org-box">
                  <img src="<?= base_url('theme/dist/img/fundo.jpg') ?>" alt="Foto Roberta" style="width: 30px; height: 30px; border-radius: 50%; vertical-align: middle; margin-right: 8px;">
                  <strong>Yuri Figueiredo</strong>
                  Analista de Suporte Pleno<br>
                  <a href="https://wa.me/5599666666666" target="_blank">WhatsApp</a>
                </div>
              </div>
            </div>

            <!-- Nível 3: Técnicos -->
            <div class="org-level">
              <div class="org-title">Técnicos de Informática por Estado</div>
              <div class="org-boxes">
                <div class="org-box">
                  <img src="<?= base_url('theme/dist/img/fundo.jpg') ?>" alt="Foto Roberta" style="width: 30px; height: 30px; border-radius: 50%; vertical-align: middle; margin-right: 8px;">
                  <strong>André Manoelino</strong>
                  Técnico – Minas Gerais<br>
                  <a href="https://wa.me/5599777777777" target="_blank">WhatsApp</a>
                </div>
                <div class="org-box">
                  <img src="<?= base_url('theme/dist/img/fundo.jpg') ?>" alt="Foto Roberta" style="width: 30px; height: 30px; border-radius: 50%; vertical-align: middle; margin-right: 8px;">
                  <strong>André Manoelino</strong>
                  Técnico – São Paulo<br>
                  <a href="https://wa.me/5599777777777" target="_blank">WhatsApp</a>
                </div>
                <div class="org-box">
                  <img src="<?= base_url('theme/dist/img/fundo.jpg') ?>" alt="Foto Roberta" style="width: 30px; height: 30px; border-radius: 50%; vertical-align: middle; margin-right: 8px;">
                  <strong>André Manoelino</strong>
                  Técnico – Rio de Janeiro<br>
                  <a href="https://wa.me/5599777777777" target="_blank">WhatsApp</a>
                </div>
                <div class="org-boxes">
                <div class="org-box">
                  <img src="<?= base_url('theme/dist/img/fundo.jpg') ?>" alt="Foto Roberta" style="width: 30px; height: 30px; border-radius: 50%; vertical-align: middle; margin-right: 8px;">
                  <strong>André Manoelino</strong>
                  Técnico – Minas Gerais<br>
                  <a href="https://wa.me/5599777777777" target="_blank">WhatsApp</a>
                </div>
                <div class="org-box">
                  <img src="<?= base_url('theme/dist/img/fundo.jpg') ?>" alt="Foto Roberta" style="width: 30px; height: 30px; border-radius: 50%; vertical-align: middle; margin-right: 8px;">
                  <strong>André Manoelino</strong>
                  Técnico – São Paulo<br>
                  <a href="https://wa.me/5599777777777" target="_blank">WhatsApp</a>
                </div>
                <div class="org-box">
                  <img src="<?= base_url('theme/dist/img/fundo.jpg') ?>" alt="Foto Roberta" style="width: 30px; height: 30px; border-radius: 50%; vertical-align: middle; margin-right: 8px;">
                  <strong>André Manoelino</strong>
                  Técnico – Rio de Janeiro<br>
                  <a href="https://wa.me/5599777777777" target="_blank">WhatsApp</a>
                </div>
              </div>

            </div>
             <div class="org-level">
              <div class="org-title">Técnicos de Informática por Estado</div>
              <div class="org-boxes">
                <div class="org-box">
                  <img src="<?= base_url('theme/dist/img/fundo.jpg') ?>" alt="Foto Roberta" style="width: 30px; height: 30px; border-radius: 50%; vertical-align: middle; margin-right: 8px;">
                  <strong>André Manoelino</strong>
                  Técnico – Minas Gerais<br>
                  <a href="https://wa.me/5599777777777" target="_blank">WhatsApp</a>
                </div>
                <div class="org-box">
                  <img src="<?= base_url('theme/dist/img/fundo.jpg') ?>" alt="Foto Roberta" style="width: 30px; height: 30px; border-radius: 50%; vertical-align: middle; margin-right: 8px;">
                  <strong>André Manoelino</strong>
                  Técnico – São Paulo<br>
                  <a href="https://wa.me/5599777777777" target="_blank">WhatsApp</a>
                </div>
                <div class="org-box">
                  <img src="<?= base_url('theme/dist/img/fundo.jpg') ?>" alt="Foto Roberta" style="width: 30px; height: 30px; border-radius: 50%; vertical-align: middle; margin-right: 8px;">
                  <strong>André Manoelino</strong>
                  Técnico – Rio de Janeiro<br>
                  <a href="https://wa.me/5599777777777" target="_blank">WhatsApp</a>
                </div>
              </div>
            </div>

          </div>
        </div>


      </div>

      <hr>

    </div>
  </div>
</div>


<!-- SCRIPTS NECESSÁRIOS PARA FUNCIONAMENTO DAS ABAS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
