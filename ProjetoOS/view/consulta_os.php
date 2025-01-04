<?php

$title = 'Consulta de Ordem de Serviço';
$pageTitle = 'CONSULTA DE ORDEM DE SERVIÇO';
$customCss;
$customJs;
ob_start();
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-search"></i> Parâmetros de Pesquisa
    </div>
    <div class="card-body">
        <form id="filtroForm">
            <div class="row">
                <div class="col-md-3">
                    <label for="os" class="form-label">Número da Ordem:</label>
                    <input type="text" class="form-control" id="os" name="os">
                </div>
                <div class="col-md-3">
                    <label for="nomeCliente" class="form-label">Nome do Cliente:</label>
                    <input type="text" class="form-control" id="nomeCliente" name="nomeCliente">
                </div>
                <div class="col-md-3">
                    <label for="dataInicio" class="form-label">Data de Abertura (Início):</label>
                    <input type="date" class="form-control" id="dataInicio" name="dataInicio">
                </div>
                <div class="col-md-3">
                    <label for="dataFim" class="form-label">Data de Abertura (Fim):</label>
                    <input type="date" class="form-control" id="dataFim" name="dataFim">
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-success">Filtrar</button>
                <button type="button" class="btn btn-secondary" id="limparFiltros">Limpar</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-card-list"></i> Ordens de Serviço
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="osTable">
            <thead>
                <tr>
                    <th>Número da Ordem</th>
                    <th>Nome do Cliente</th>
                    <th>Produto</th>
                    <th>Data de Abertura</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
