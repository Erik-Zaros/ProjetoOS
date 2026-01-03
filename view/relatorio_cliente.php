<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\Autenticador;

Autenticador::iniciar();

$title = 'Relatório de Clientes';
$pageTitle = 'Relatório de Clientes';
ob_start();
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-search"></i> Parâmetros de Pesquisa
    </div>
    <div class="card-body">
        <form id="pesquisaForm">
            <div class="row">
                <div class="col-md-2">
                    <label for="dataInicio" class="form-label">Data Início</label>
                    <input type="text" class="form-control" id="dataInicio" name="dataInicio">
                </div>
                <div class="col-md-2">
                    <label for="dataFim" class="form-label">Data Fim</label>
                    <input type="text" class="form-control" id="dataFim" name="dataFim">
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
                    <label class="form-label" for="os_finalizada">Cliente tem OS</label>
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
    <div class="card-header bg-primary text-white">
        <i class="bi bi-card-list"></i> Clientes
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped" id="clienteTable">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>CEP</th>
                    <th>Endereço</th>
                    <th>Bairro</th>
                    <th>Cidade</th>
                    <th>Estado</th>
                    <th>Data Cadastro</th>
                    <th>OS's</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<form id="formCSV" method="POST" action="../public/relatorio_cliente/relatorio.php" target="_blank">
    <input type="hidden" name="dataInicio">
    <input type="hidden" name="dataFim">
    <input type="hidden" name="cpf">
    <input type="hidden" name="nome">
</form>

<div class="text-center mt-4 mb-3">
    <button type="button" id="btnCSV" class="btn btn-success" style="display:none">
        Donwload CSV
    </button>
</div>


<?php
$content = ob_get_clean();
include 'layout.php';
?>
