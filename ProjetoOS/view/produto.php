<?php

$title = 'Cadastro de Produto';
$pageTitle = 'CADASTRO DE PRODUTO';
$customCss;
$customJs;
ob_start();
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-box-fill"></i> Cadastro de Produto
    </div>
    <div class="card-body">
        <form id="produtoForm">
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
            <button type="submit" class="btn btn-success mt-3">Gravar</button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-card-list"></i> Produtos Cadastrados
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="produtosTable">
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

<?php
$content = ob_get_clean();
include 'layout.php';
?>