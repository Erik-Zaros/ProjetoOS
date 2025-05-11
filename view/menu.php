<?php

include_once '../controller/login/autentica_usuario.php';

$regra_arquivo = '../controller/regras_posto/posto_'. $login_posto . '/regras.php';
include_once file_exists($regra_arquivo) ? $regra_arquivo : '../controller/regras_posto/default.php';

$title = 'Menu';
$pageTitle = 'MENU';
$customCss;
$customJs;
ob_start();
?>

    <div class="text-center mt-4 mb-3">
        <a href="../controller/geraCsv.php" class="btn btn-outline-success btn-sm">Download Excel</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card p-3 shadow-sm border-0 card-sharp">
                <h5 class="text-center">Gráfico de Pizza</h5>
                <div id="grafico-pizza" style="height: 300px;"></div>
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
