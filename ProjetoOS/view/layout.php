<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../public/img/tc_2009.ico" type="image/x-icon">
    <title><?= $title ?? 'Projeto OS'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/menu.css">
    <link rel="stylesheet" href="../public/css/cliente.css">
    <link rel="stylesheet" href="../public/css/produto.css">
    <link rel="stylesheet" href="../public/css/cadastraOS.css">
    <link rel="stylesheet" href="../public/css/consultaOS.css">
    <?= $customCss ?? ''; ?>
</head>

<body>
    <div class="d-flex">
        <nav id="sidebar" class="flex-column vh-100 sidebar-transition">
            <div class="sidebar-header p-3 text-center">
                <h4 class="text-white">Projeto OS</h4>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="menu.php" class="nav-link text-white"><i class="bi bi-pie-chart-fill"></i> Dashboard</a></li>
                <li class="nav-item"><a href="cliente.php" class="nav-link text-white"><i class="bi bi-people-fill"></i> Clientes</a></li>
                <li class="nav-item"><a href="produto.php" class="nav-link text-white"><i class="bi bi-box-fill"></i> Produtos</a></li>
                <li class="nav-item"><a href="cadastra_os.php" class="nav-link text-white"><i class="bi bi-tools"></i> Cadastrar OS</a></li>
                <li class="nav-item"><a href="consulta_os.php" class="nav-link text-white"><i class="bi bi-search"></i> Consultar OS</a></li>
            </ul>
        </nav>

        <div class="flex-grow-1">
            <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
                <div class="container-fluid">
                    <button class="btn btn-dark" id="sidebarToggle">&#9776;</button>
                    <span class="navbar-brand ms-3"><?= $pageTitle ?? ''; ?></span>
                </div>
            </nav>
            <div class="container mt-4">
                <?= $content ?? ''; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.highcharts.com/9.1.2/highcharts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../public/ajax/main.js"></script>
    <script src="../public/ajax/menu.js"></script>
    <script src="../public/ajax/cliente.js"></script>
    <script src="../public/ajax/produto.js"></script>
    <script src="../public/ajax/os.js"></script>
    <?= $customJs ?? ''; ?>

</body>
</html>
