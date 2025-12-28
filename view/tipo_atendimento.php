<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\Autenticador;

Autenticador::iniciar();

$title = 'Cadastro de Tipo de Atendimento';
$pageTitle = 'CADASTRO DE TIPO DE ATENDIMENTO';
ob_start();
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-box-fill"></i> Cadastro Tipo de Atendimento
    </div>
    <div class="card-body">
        <form id="tipoAtendimentoForm">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="codigo" class="form-label">Código</label>
                    <input type="text" class="form-control" id="codigo" name="codigo" required
                        maxlength="50">
                </div>
                <div class="col-md-6">
                    <label for="descricao" class="form-label">Descrição</label>
                    <input type="text" class="form-control" id="descricao" name="descricao" required
                        maxlength="120">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label" for="ativo">Ativo</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="ativo" name="ativo">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-sm mt-3">Gravar</button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-card-list"></i> Tipo de Atendimentos Cadastrados
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped" id="tipoAtendimentoTable">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descrição</th>
                    <th>Ativo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="text-center mt-4 mb-3">
    <a href="../public/tipo_atendimento/relatorio.php" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-spreadsheet-fill"></i> Download CSV</a>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
