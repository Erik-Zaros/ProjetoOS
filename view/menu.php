<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\Autenticador;

Autenticador::iniciar();

$title = 'Menu';
$pageTitle = 'MENU';
$customCss;
$customJs;
ob_start();
?>

    <div class="text-center mt-4 mb-3">
        <a href="../public/relatorio/relatorio.php" class="btn btn-success btn-sm">Download Excel</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card p-3 shadow-sm border-0 card-sharp">
                <h5 class="text-center">Status das OS</h5>
                <div id="grafico-pizza-os-status" style="height: 300px;"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 shadow-sm border-0 card-sharp">
                <h5 class="text-center">Gráfico de Pizza</h5>
                <div id="grafico-pizza-status-produto" style="height: 300px;"></div>
            </div>
        </div>
        <div class="col-md-12 mt-4">
            <div class="card p-3 shadow-sm border-0 card-sharp">
                <h5 class="text-center">Gráfico de Colunas</h5>
                <div id="grafico-colunas" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
