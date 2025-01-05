<?php

$title = 'Menu';
$pageTitle = 'MENU';
$customCss;
$customJs;
ob_start();
?>

    <h2 class="mb-4">Bem-vindo ao Sistema</h2>

    <div class="row">
        <div class="col-md-6">
            <div class="card p-3 shadow-sm border-0 card-sharp">
                <h5 class="text-center">Gráfico de Pizza</h5>
                <div id="grafico-pizza" style="height: 300px;"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 shadow-sm border-0 card-sharp">
                <h5 class="text-center">Gráfico de Colunas</h5>
                <div id="grafico-colunas" style="height: 300px;"></div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="../controller/gerar_csv.php" class="btn btn-outline-success">Download Excel</a>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
