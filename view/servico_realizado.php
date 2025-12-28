<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\Autenticador;

Autenticador::iniciar();

$title = 'Cadastro de Serviço Realizado';
$pageTitle = 'CADASTRO DE SERVIÇO';
ob_start();
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-box-fill"></i> Cadastro de Serviço Realizado
    </div>
    <div class="card-body">
        <form id="servicoRealizadoForm">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="descricao" class="form-label">Descrição</label>
                    <input type="text" class="form-control" id="descricao" name="descricao" required
                        maxlength="120">
                </div>
                <div class="col-md-2">
                    <label for="ativo" class="form-label d-block">Ativo</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="ativo" name="ativo">
                        <label class="form-check-label" for="ativo">Ativo</label>
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="usa_estoque" class="form-label d-block">Usa Estoque</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="usa_estoque" name="usa_estoque">
                        <label class="form-check-label" for="usa_estoque">Ativo</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-sm mt-3">Gravar</button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-card-list"></i> Produtos Cadastrados
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover nowrap" id="servicoRealizadoTable">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Ativo</th>
                    <th>Usa Estoque</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="text-center mt-4 mb-3">
    <a href="../public/servico_realizado/relatorio.php" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-spreadsheet-fill"></i> Download CSV</a>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
