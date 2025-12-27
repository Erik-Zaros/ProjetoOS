<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\Autenticador;
use App\Core\Db;
use App\Service\FuncoesService;
Autenticador::iniciar();

$con = Db::getConnection();
$posto = Autenticador::getPosto();

require_once __DIR__ . '/../config/menus/rotas.php';

$arquivo_menu_posto = __DIR__ . "/../config/menus/posto/{$posto}.php";

if (file_exists($arquivo_menu_posto)) {
    $regras_posto = include $arquivo_menu_posto;

    if (!empty($regras_posto['remover'])) {
        foreach ($regras_posto['remover'] as $menuRemover) {
            unset($rotas[$menuRemover]);
        }
    }

    if (!empty($regras_posto['adicionar'])) {
        foreach ($regras_posto['adicionar'] as $key => $menuNovo) {
            $rotas[$key] = $menuNovo;
        }
    }

    if (!empty($regras_posto['alterar'])) {
        foreach ($regras_posto['alterar'] as $key => $alteracoes) {
            if (isset($rotas[$key])) {
                $rotas[$key] = array_merge($rotas[$key], $alteracoes);
            }
        }
    }
}

require_once __DIR__ . '/../config/assets/imports.php';
$current_page = basename($_SERVER['PHP_SELF']);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'ProjetoOS'; ?></title>
  <link rel="icon" type="image/x-icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='45' fill='%232e2e48'/%3E%3Ctext x='50' y='60' text-anchor='middle' fill='white' font-size='40' font-weight='bold'%3EOS%3C/text%3E%3C/svg%3E">
  <link rel="stylesheet" href="../public/adminlte/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../public/adminlte/dist/css/adminlte.min.css">

  <?php foreach ($imports["global"]["css"] as $css): ?>
    <link rel="stylesheet" href="<?= $css ?>">
  <?php endforeach; ?>

  <?php
  foreach ($imports as $key => $import) {
    if (strpos($current_page, $key) !== false && isset($import["css"])) {
      foreach ($import["css"] as $css) {
        echo '<link rel="stylesheet" href="' . $css . '">' . PHP_EOL;
      }
    }
  }
  ?>

  <?= $customCss ?? '' ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <div class="container-fluid">
      <div class="row w-100 align-items-center">
        <div class="col-3 col-sm-2 d-flex align-items-center pl-3">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button">
            <i class="fas fa-bars"></i>
          </a>
        </div>

        <div class="col-6 col-sm-8 text-center">
          <span class="navbar-text text-dark" style="font-size: 1.3rem;">
            <?= FuncoesService::buscaNomePosto($posto); ?>
          </span>
        </div>

        <div class="col-3 col-sm-2 text-right">
          <span class="navbar-text text-dark" style="font-size: 0.8rem;">
            <?= $pageTitle ?? '' ?>
          </span>
        </div>
      </div>
    </div>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">

    <a class="brand-link text-center">
      <span class="brand-text">ProjetoOS</span>
    </a>

    <div class="sidebar">

      <div class="form-inline p-2">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Buscar..." aria-label="Search" id="sidebarSearch">
          <div class="input-group-append">
            <button class="btn btn-sidebar" id="sidebarSearchIcon">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
      </div>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false" id="menuSidebar">
          <?php foreach ($rotas as $chave => $menu): ?>
            <?php if (!isset($menu['submenus'])): ?>
              <li class="nav-item">
                <a href="<?= $menu['link'] ?>" class="nav-link <?= ($current_page == $menu['link']) ? 'active' : '' ?>">
                  <i class="nav-icon <?= $menu['icone'] ?>"></i>
                  <p><?= $menu['titulo'] ?></p>
                </a>
              </li>
            <?php else: ?>
              <li class="nav-item has-treeview <?= in_array($current_page, array_column($menu['submenus'], 'link')) ? 'menu-open' : '' ?>">
                <a href="#" class="nav-link <?= in_array($current_page, array_column($menu['submenus'], 'link')) ? 'active' : '' ?>">
                  <i class="nav-icon <?= $menu['icone'] ?>"></i>
                  <p>
                    <?= $menu['titulo'] ?>
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <?php foreach ($menu['submenus'] as $submenu): ?>
                    <li class="nav-item">
                      <a href="<?= $submenu['link'] ?>" class="nav-link <?= ($current_page == $submenu['link']) ? 'active' : '' ?>">
                        <i class="far fa-circle nav-icon"></i>
                        <p><?= $submenu['titulo'] ?></p>
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>

          <li class="nav-item">
            <a href="../logout.php" class="nav-link text-danger">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>Sair</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <section class="content pt-4 px-3">
      <?= $content ?? '' ?>
    </section>
  </div>

</div>

<!-- Carrega novo layout antes -->
<script src="../public/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="../public/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../public/adminlte/dist/js/adminlte.min.js"></script>

<?php foreach ($imports["global"]["js"] as $js): ?>
  <script src="<?= $js ?>"></script>
<?php endforeach; ?>

<?php
foreach ($imports as $key => $import) {
  if (strpos($current_page, $key) !== false && isset($import["js"])) {
    foreach ($import["js"] as $js) {
      echo '<script src="' . $js . '"></script>' . PHP_EOL;
    }
  }
}
?>

<script>
  Shadowbox.init({
    overlayOpacity: 0.85,
    modal: true
  });
</script>

</body>
</html>
