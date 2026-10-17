<?php

require_once __DIR__ . '/../vendor/autoload.php';
use App\Auth\Autenticador;

Autenticador::iniciar();
$title = 'Lista Básica';
$pageTitle = 'LISTA BÁSICA';

ob_start();
?>

<div class="card shadow-sm mb-4">
  <div class="card-header">
    <i class="bi bi-link-45deg"></i> Selecionar Produto
  </div>
  <div class="card-body">
    <div class="input-group">
      <input type="text" id="buscarProduto" class="form-control" placeholder="Clique na lupa para selecionar o produto..." readonly>
      <button class="btn btn-outline-secondary" type="button" id="btnLupaProduto" title="Buscar produtos">
        <i class="bi bi-search"></i>
      </button>
    </div>
    <input type="hidden" id="produtoSelecionado">
  </div>
</div>

<div id="areaPecas" style="display:none;">
  <div class="card shadow-sm mb-4">
    <div class="card-header">
      <i class="bi bi-boxes"></i> Peças Amarradas
    </div>
    <div class="card-body">
      <input type="text" id="buscarPeca" class="form-control mb-2" placeholder="Adicionar peça por código ou descrição...">
      <ul id="listaPecas" class="list-group" style="display:none; max-height:200px; overflow:auto;"></ul>
      <div id="produtoTitulo" class="alert alert-primary mb-3" style="display:none;">
        <strong>Produto Selecionado:</strong> <span id="nomeProduto"></span>
      </div>

      <table class="table table-bordered table-striped table-hover nowrap" id="tabelaPecas">
        <thead>
          <tr>
            <th class="text-center">Código Peça</th>
            <th class="text-center">Descrição Peça</th>
            <th class="text-center">Status Peça(Ativa)</th>
            <th class="text-center">Ações</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<div class="text-center mt-3" id="btnExcelContainer" style="display:none;">
  <a id="btnExcel" class="btn btn-success btn-sm" target="_blank"><i class="bi bi-file-earmark-spreadsheet-fill"></i> Download CSV</a>
</div>
<br>

<?php include __DIR__ . '/lupa.php'; ?>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
