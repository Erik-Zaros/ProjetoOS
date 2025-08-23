<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title>ProjetoOS — Gestão de Ordens de Serviço para Postos Autorizados</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="description" content="Gerencie OS, clientes, produtos e relatórios de forma simples e rápida." />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="stylesheet" href="public/css/homepage.css">
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='45' fill='%232e2e48'/%3E%3Ctext x='50' y='60' text-anchor='middle' fill='white' font-size='40' font-weight='bold'%3EOS%3C/text%3E%3C/svg%3E">
</head>
<body class="landing-body">

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
        <span class="badge bg-secondary me-2">OS</span> ProjetoOS
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="topNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="#recursos">Recursos</a></li>
          <li class="nav-item"><a class="nav-link" href="#planos">Planos</a></li>
          <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
          <li class="nav-item"><a class="nav-link" href="#contato">Suporte</a></li>
        </ul>
        <div class="d-flex gap-2">
          <a href="login.php" class="btn btn-outline-light"><i class="bi bi-box-arrow-in-right me-1"></i>Acessar o sistema</a>
        </div>
      </div>
    </div>
  </nav>

  <header class="hero-section">
    <div class="container py-5">
      <div class="row align-items-center g-4">
        <div class="col-lg-6">
          <h1 class="display-5 fw-bold text-white mb-3">Gestão de OS para <span class="grad-text">Postos Autorizados</span></h1>
          <p class="lead text-light opacity-90">
            Centralize ordens de serviço, clientes, produtos e relatórios em um único lugar. 
            Simples, rápido e feito para o seu dia a dia.
          </p>
          <div class="d-flex flex-wrap gap-2 mt-3">
            <a href="login.php" class="btn btn-success btn-lg"><i class="bi bi-play-circle me-1"></i>Acessar o sistema</a>
            <a href="#planos" class="btn btn-outline-light btn-lg"><i class="bi bi-credit-card me-1"></i>Ver planos</a>
          </div>
          <div class="d-flex gap-4 mt-4 text-light small">
            <div><i class="bi bi-shield-check me-1"></i>SSL</div>
            <div><i class="bi bi-cloud-arrow-up me-1"></i>Backup diário</div>
            <div><i class="bi bi-speedometer2 me-1"></i>Rápido</div>
          </div>
        </div>
        <div class="col-lg-6 text-center">
          <div class="hero-card shadow-lg">
            <div class="hero-stats">
              <div class="stat">
                <i class="bi bi-list-check"></i>
                <span>OS em andamento</span>
                <strong>128</strong>
              </div>
              <div class="stat">
                <i class="bi bi-people"></i>
                <span>Clientes</span>
                <strong>3.240</strong>
              </div>
              <div class="stat">
                <i class="bi bi-check2-circle"></i>
                <span>OS finalizadas</span>
                <strong>9.812</strong>
              </div>
            </div>
            <div class="hero-illu"><i class="bi bi-tools"></i></div>
          </div>
        </div>
      </div>
    </div>
    <div class="hero-blur"></div>
  </header>

  <section id="recursos" class="py-5">
    <div class="container">
      <h2 class="text-center fw-bold mb-4">Recursos que aceleram seu atendimento</h2>
      <p class="text-center text-body-secondary mb-5">Tudo o que você precisa para abrir, acompanhar e finalizar OS com qualidade.</p>
      <div class="row g-4">
        <div class="col-md-6 col-lg-4">
          <div class="feature-card shadow-sm h-100">
            <div class="icon-wrap"><i class="bi bi-clipboard2-check"></i></div>
            <h5>Abertura rápida de OS</h5>
            <p>Campos inteligentes, máscaras de CPF/CNPJ e validações para reduzir erros.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="feature-card shadow-sm h-100">
            <div class="icon-wrap"><i class="bi bi-box-seam"></i></div>
            <h5>Produtos & Peças</h5>
            <p>Cadastro simples com controle de ativos, compatibilidade e garantia.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="feature-card shadow-sm h-100">
            <div class="icon-wrap"><i class="bi bi-graph-up-arrow"></i></div>
            <h5>Relatórios</h5>
            <p>Indicadores de desempenho e exportação para CSV quando necessário.</p>
          </div>
        </div>
<!--         <div class="col-md-6 col-lg-4">
          <div class="feature-card shadow-sm h-100">
            <div class="icon-wrap"><i class="bi bi-cloud-arrow-down"></i></div>
            <h5>Anexos & Evidências</h5>
            <p>Upload de fotos e documentos com regras por fábrica, unidade e tipo.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="feature-card shadow-sm h-100">
            <div class="icon-wrap"><i class="bi bi-key"></i></div>
            <h5>Autenticação segura</h5>
            <p>Senhas com hash, sessões seguras e trilhas de auditoria.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="feature-card shadow-sm h-100">
            <div class="icon-wrap"><i class="bi bi-gear-wide-connected"></i></div>
            <h5>Integrações</h5>
            <p>ViaCEP e outras integrações prontas para agilizar cadastros e rotinas.</p>
          </div>
        </div> -->
      </div>
    </div>
  </section>

  <section id="planos" class="py-5 bg-body-tertiary">
    <div class="container">
      <h2 class="text-center fw-bold mb-4">Planos</h2>
      <p class="text-center text-body-secondary mb-5">Escolha o plano ideal para seu posto autorizado.</p>

      <div class="row g-4 align-items-stretch">
        <div class="col-md-6 col-lg-4">
          <div class="pricing-card shadow-sm h-100">
            <div class="badge-top bg-secondary">Iniciante</div>
            <h3 class="price">R$ 50<span>/mês</span></h3>
            <ul class="list-unstyled mb-4">
              <li><i class="bi bi-check2"></i> Até 50 OS/mês</li>
              <li><i class="bi bi-check2"></i> Clientes & Produtos</li>
              <li><i class="bi bi-check2"></i> Relatórios básicos</li>
              <li><i class="bi bi-x"></i> API / Integrações</li>
            </ul>
            <a href="login.php" class="w-100 btn btn-outline-dark">Começar</a>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="pricing-card shadow border-primary h-100 position-relative">
            <div class="badge-top bg-primary">Essencial</div>
            <h3 class="price">R$ 100<span>/mês</span></h3>
            <ul class="list-unstyled mb-4">
              <li><i class="bi bi-check2"></i> Até 500 OS/mês</li>
              <li><i class="bi bi-check2"></i> Anexos & Evidências</li>
              <li><i class="bi bi-check2"></i> Relatórios avançados</li>
              <li><i class="bi bi-check2"></i> Suporte por e-mail</li>
            </ul>
            <a href="login.php" class="w-100 btn btn-primary">Assinar</a>
          </div>
        </div>

        <div class="col-md-6 col-lg-4">
          <div class="pricing-card shadow-sm h-100">
            <div class="badge-top bg-dark">Pro</div>
            <h3 class="price">R$ 200<span>/mês</span></h3>
            <ul class="list-unstyled mb-4">
              <li><i class="bi bi-check2"></i> OS ilimitadas</li>
              <li><i class="bi bi-check2"></i> API & Webhooks</li>
              <li><i class="bi bi-check2"></i> SLA de suporte</li>
              <li><i class="bi bi-check2"></i> Recursos exclusivos</li>
            </ul>
            <a href="login.php" class="w-100 btn btn-outline-dark">Contratar</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="faq" class="py-5">
    <div class="container">
      <h2 class="text-center fw-bold mb-4">Perguntas frequentes</h2>
      <div class="accordion" id="faqAcc">
        <div class="accordion-item">
          <h2 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#f1">Posso cancelar quando quiser?</button></h2>
          <div id="f1" class="accordion-collapse collapse show" data-bs-parent="#faqAcc">
            <div class="accordion-body">Sim. Não há fidelidade; você pode cancelar a qualquer momento.</div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#f2">Como funciona o suporte?</button></h2>
          <div id="f2" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
            <div class="accordion-body">Atendimento por e-mail nos planos Essencial e Pro. SLA no Pro.</div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#f3">Meus dados estão seguros?</button></h2>
          <div id="f3" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
            <div class="accordion-body">Criptografia em repouso/SSL e backups diários. Você controla seus acessos.</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="contato" class="py-5 bg-body-tertiary">
    <div class="container">
      <div class="row g-4 align-items-center">
        <div class="col-lg-8">
          <h2 class="fw-bold mb-2">Precisa de ajuda para começar?</h2>
          <p class="text-body-secondary mb-0">Entre em contato e te ajudamos a configurar seu posto autorizado.</p>
        </div>
        <div class="col-lg-4 text-lg-end">
          <a href="mailto:suporte@projetoos.local" class="btn btn-dark"><i class="bi bi-envelope me-1"></i>suporte@projetoos.local</a>
        </div>
      </div>
    </div>
  </section>

  <footer class="py-4 bg-dark text-light">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
      <div class="small">© <span id="year"></span> ProjetoOS. Todos os direitos reservados.</div>
      <div class="small opacity-75">Versão 1.0 • <a href="login.php" class="link-light text-decoration-none">Acessar</a></div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="public/js/homepage.js"></script>
</body>
</html>
