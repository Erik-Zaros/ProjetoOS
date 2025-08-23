<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\Autenticador;

Autenticador::iniciar();

$title = 'Menu';
$pageTitle = 'MENU';
ob_start();
?>

    <div class="text-center mb-3">
        <a href="../public/menu/relatorio.php" class="btn btn-success btn-sm">Download Excel</a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card p-3 shadow-sm border-0 card-sharp">
                <h5 class="text-center">Status das OS</h5>
                <div id="grafico-pizza-os-status" style="height: 300px;"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 shadow-sm border-0 card-sharp">
                <h5 class="text-center">Status dos Produtos</h5>
                <div id="grafico-pizza-status-produto" style="height: 300px;"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 shadow-sm border-0 card-sharp">
                <h5 class="text-center">Status das Peças</h5>
                <div id="grafico-pizza-status-peca" style="height: 300px;"></div>
            </div>
        </div>
        <div class="col-md-12 mt-4">
            <div class="card p-3 shadow-sm border-0 card-sharp">
                <h5 class="text-center">Todos os Registros</h5>
                <div id="grafico-colunas" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
