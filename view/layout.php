<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Db;
use App\Auth\Autenticador;
use App\Service\FuncoesService;
use App\Controller\PostoController;
use App\Service\Cache;

Autenticador::iniciar();

$usuario = Autenticador::getUsuario();
$posto   = Autenticador::getPosto();

$layoutContext = FuncoesService::buscaInfoUsuario($usuario);
$usuarioNome   = $layoutContext['usuarioNome'] ?? '';
$usuarioLogin  = $layoutContext['usuarioLogin'] ?? '';
$usuarioTipo   = $layoutContext['usuarioTipo'] ?? '';
$postoNome     = PostoController::buscarNome($posto);

$temaAtual = 'light';
try {
    $temaCache = new Cache('usuario', (string) $usuario);
    $raw       = $temaCache->getFromCache();
    if ($raw !== '') {
        $temaData  = json_decode($raw, true);
        $temaAtual = ($temaData['tema'] ?? 'light') === 'dark' ? 'dark' : 'light';
    }
} catch (\Throwable $e) {
}

require_once __DIR__ . '/../config/menus/rotas.php';
if (file_exists(__DIR__ . "/../config/menus/posto/{$posto}/rotas.php")) {
    include __DIR__ . "/../config/menus/posto/{$posto}/rotas.php";
}

require_once __DIR__ . '/../config/assets/imports.php';
if (file_exists(__DIR__ . "/../config/assets/posto/{$posto}/imports.php")) {
    include __DIR__ . "/../config/assets/posto/{$posto}/imports.php";
}

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="<?= $temaAtual ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'ProjetoOS' ?></title>

  <link rel="icon" type="image/x-icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='45' fill='%232e2e48'/%3E%3Ctext x='50' y='60' text-anchor='middle' fill='white' font-size='40' font-weight='bold'%3EOS%3C/text%3E%3C/svg%3E">

  <?php foreach ($imports["global"]["css"] as $css): ?>
    <link rel="stylesheet" href="<?= $css ?>">
  <?php endforeach; ?>

  <?php
  foreach ($imports as $key => $import) {
      if ($current_page === $key && isset($import["css"])) {
          foreach ($import["css"] as $css) {
              echo '    <link rel="stylesheet" href="' . $css . '">' . PHP_EOL;
          }
      }
  }
  ?>
</head>

<body class="layout-fixed fixed-header sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

  <nav class="app-header navbar navbar-expand bg-body shadow-sm">
    <div class="container-fluid">
      <ul class="navbar-nav align-items-center">
        <li class="nav-item">
          <a class="nav-link d-inline-flex align-items-center justify-content-center" data-lte-toggle="sidebar" href="#" role="button" aria-label="Alternar Menu">
            <i class="bi bi-list fs-4"></i>
          </a>
        </li>
      </ul>

      <div class="mx-auto text-center px-2">
        <span class="navbar-text fw-bold fs-5 text-body">
          <?= htmlspecialchars((string) $postoNome) ?>
        </span>
      </div>

      <ul class="navbar-nav align-items-center gap-1">
        <li class="nav-item">
          <a class="nav-link d-inline-flex align-items-center justify-content-center" href="#" data-lte-toggle="fullscreen" role="button" aria-label="Tela Cheia" title="Tela Cheia">
            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit d-none"></i>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link d-inline-flex align-items-center justify-content-center" href="#" id="btnTemaToggle" role="button" aria-label="Alternar Modo Escuro/Claro" title="Alternar Modo Escuro/Claro">
            <i id="navTemaIcon" class="bi bi-<?= $temaAtual === 'dark' ? 'sun-fill text-warning' : 'moon-stars-fill' ?>"></i>
          </a>
        </li>

        <li class="nav-item">
          <button
            type="button"
            class="btn btn-link nav-link d-inline-flex align-items-center justify-content-center text-decoration-none border-0"
            data-bs-toggle="modal"
            data-bs-target="#modalUsuario"
            aria-label="Abrir Perfil"
            title="Perfil: <?= htmlspecialchars($usuarioNome) ?>"
          >
            <i class="bi bi-person-circle"></i>
          </button>
        </li>
      </ul>
    </div>
  </nav>

  <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
      <a href="menu" class="brand-link text-decoration-none">
        <span class="brand-text fw-bold fs-4 text-white">ProjetoOS</span>
      </a>
    </div>

    <div class="sidebar-wrapper">
      <div class="px-3 pt-3 pb-1">
        <div class="input-group input-group-sm">
          <input
            type="search"
            class="form-control"
            id="sidebarSearch"
            placeholder="Filtrar menu..."
            aria-label="Filtrar menu"
            autocomplete="off"
          >
          <span class="input-group-text"><i class="bi bi-search"></i></span>
        </div>
      </div>

      <nav class="mt-2" aria-label="Navegação Principal">
        <ul
          class="nav sidebar-menu flex-column"
          data-lte-toggle="treeview"
          role="menu"
          data-accordion="false"
          id="menuSidebar"
        >
          <?php foreach ($rotas as $chave => $menu) { ?>
            <?php if (!isset($menu['submenus'])) { ?>
              <li class="nav-item">
                <a href="<?= $menu['link'] ?>" class="nav-link <?= ($current_page == $menu['link']) ? 'active' : '' ?>">
                  <i class="nav-icon <?= $menu['icone'] ?>"></i>
                  <p><?= $menu['titulo'] ?></p>
                </a>
              </li>
            <?php } else { ?>
              <?php $isOpen = in_array($current_page, array_column($menu['submenus'], 'link')); ?>
              <li class="nav-item <?= $isOpen ? 'menu-open' : '' ?>">
                <a href="#" class="nav-link <?= $isOpen ? 'active' : '' ?>">
                  <i class="nav-icon <?= $menu['icone'] ?>"></i>
                  <p>
                    <?= $menu['titulo'] ?>
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <?php foreach ($menu['submenus'] as $submenu) { ?>
                    <li class="nav-item">
                      <a href="<?= $submenu['link'] ?>" class="nav-link <?= ($current_page == $submenu['link']) ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-circle fs-8 me-2"></i>
                        <p><?= $submenu['titulo'] ?></p>
                      </a>
                    </li>
                  <?php } ?>
                </ul>
              </li>
            <?php } ?>
          <?php } ?>
        </ul>
      </nav>
    </div>
  </aside>

  <main class="app-main">
    <?php if (!empty($pageTitle) && 1==2) { ?>
      <div class="app-content-header pt-3 px-3 pb-0">
        <div class="container-fluid">
          <div class="row align-items-center">
            <div class="col-12">
              <h4 class="mb-0 fw-bold text-body"><?= htmlspecialchars((string) $pageTitle) ?></h4>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
    <div class="app-content py-3 px-3">
      <div class="container-fluid">
        <?= $content ?? '' ?>
      </div>
    </div>
  </main>

</div>

<?php include __DIR__ . '/modal_perfil.php'; ?>
<?php include __DIR__ . '/modal_log.php'; ?>

<?php foreach ($imports["global"]["js"] as $js): ?>
  <script src="<?= $js ?>"></script>
<?php endforeach; ?>

<?php
foreach ($imports as $key => $import) {
    if ($current_page === $key && isset($import["js"])) {
        foreach ($import["js"] as $js) {
            echo '<script src="' . $js . '"></script>' . PHP_EOL;
        }
    }
}
?>

</body>
</html>
