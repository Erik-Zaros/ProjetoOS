<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\Autenticador;

Autenticador::iniciar();

$title = 'Cadastro de Peça';
$pageTitle = 'CADASTRO DE PEÇA';
ob_start();
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-wrench"></i> Cadastro de Peça
    </div>
    <div class="card-body">
        <form id="pecaForm">
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
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="ativo" name="ativo">
                <label for="ativo" class="form-check-label">Ativo</label>
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
        <table class="table table-bordered" id="pecasTable">
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
    <a href="../public/peca/relatorio.php" class="btn btn-success btn-sm">Download Excel</a>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
