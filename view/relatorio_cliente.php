<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\Autenticador;

Autenticador::iniciar();

$title = 'Relatório de Clientes';
$pageTitle = 'Relatório de Clientes';
ob_start();
?>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <i class="bi bi-search"></i> Parâmetros de Pesquisa
    </div>
    <div class="card-body">
        <form id="pesquisaForm">
            <div class="row">
                <div class="col-md-2">
                    <label for="dataInicio" class="form-label">Data Início</label>
                    <input type="text" class="form-control" id="dataInicio" name="dataInicio" placeholder="dd/mm/aaaa">
                </div>
                <div class="col-md-2">
                    <label for="dataFim" class="form-label">Data Fim</label>
                    <input type="text" class="form-control" id="dataFim" name="dataFim" placeholder="dd/mm/aaaa">
                </div>
                <div class="col-md-3">
                    <label for="cpf" class="form-label">CPF/CNPJ</label>
                    <input type="text" class="form-control" id="cpf" name="cpf">
                </div>
                <div class="col-md-3">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome">
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-2">
                    <label class="form-label" for="cliente_os">Cliente tem OS</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="cliente_os" name="cliente_os">
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-success btn-sm">Pesquisar</button>
                <button type="button" class="btn btn-secondary btn-sm" id="limparPesquisa">Limpar</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <i class="bi bi-card-list"></i> Clientes
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped" id="clienteTable">
            <thead>
                <tr>
                    <th class="text-center">Nome</th>
                    <th class="text-center">CPF</th>
                    <th class="text-center">CEP</th>
                    <th class="text-center">Endereço</th>
                    <th class="text-center">Bairro</th>
                    <th class="text-center">Cidade</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Data Cadastro</th>
                    <th class="text-center">OS's</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="text-center mt-4 mb-3" id="btnCSV" style="display:none;">
    <a href="../public/relatorio_cliente/csv.php?<?= htmlspecialchars(http_build_query($_GET)) ?>"
       class="btn btn-success btn-sm" target="_blank">
        <i class="bi bi-file-earmark-spreadsheet-fill"></i> Download CSV
    </a>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
