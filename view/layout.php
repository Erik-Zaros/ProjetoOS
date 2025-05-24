<?php

include_once '../controller/login/autentica_usuario.php';
include '../controller/funcoes.php';
require_once '../config/rotas.php';
require_once '../config/imports.php';
$current_page = basename($_SERVER['PHP_SELF']);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../public/img/tc_2009.ico" type="image/x-icon">
    <title><?= $title ?? 'Projeto OS'; ?></title>

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

    <?= $customCss ?? ''; ?>
</head>

<body>
    <div class="d-flex">
        <nav id="sidebar" class="vh-100 sidebar-transition">
            <div class="sidebar-header p-3 text-center">
                <h4 class="text-white">Projeto OS</h4>
            </div>

            <div class="px-3">
                <input type="text" id="sidebarSearch" class="form-control" placeholder="Pesquisar">
            </div>

            <ul class="nav flex-column mt-2" id="menuSidebar">
                <?php foreach ($rotas as $chave => $menu): ?>
                    <?php if (!isset($menu['submenus'])): ?>
                        <li class="nav-item">
                            <a href="<?= $menu['link'] ?>" class="nav-link <?= ($current_page == $menu['link']) ? 'active' : '' ?>">
                                <i class="<?= $menu['icone'] ?>"></i> <?= $menu['titulo'] ?>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="#" class="nav-link toggle-menu">
                                <i class="<?= $menu['icone'] ?>"></i> <?= $menu['titulo'] ?>
                                <i class="bi bi-chevron-down float-end"></i>
                            </a>
                            <ul class="submenu <?= (in_array($current_page, array_column($menu['submenus'], 'link'))) ? 'submenu-open' : '' ?>">
                                <?php foreach ($menu['submenus'] as $submenu): ?>
                                    <li>
                                        <a href="<?= $submenu['link'] ?>" class="nav-link <?= ($current_page == $submenu['link']) ? 'active' : '' ?>">
                                            <?= $submenu['titulo'] ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
                <a href="../logout.php" class="nav-link text-danger">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>
        </nav>


        <div class="flex-grow-1">
            <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
                <div class="container-fluid">
                    <button class="btn btn-dark" id="sidebarToggle">&#9776;</button>
                     <span class="navbar-brand w-100 text-center"><?php $nomePosto = buscaNomePosto($login_posto); echo $nomePosto; ?></span>
                    <span class="navbar-brand ms-auto"><?= $pageTitle ?? ''; ?></span>
                </div>
            </nav>
            <div class="container mt-4">
                <?= $content ?? ''; ?>
            </div>
        </div>
    </div>

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

    <?= $customJs ?? ''; ?>
</body>
</html>
