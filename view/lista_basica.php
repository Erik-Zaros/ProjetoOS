<?php

require_once __DIR__ . '/../vendor/autoload.php';
use App\Auth\Autenticador;

Autenticador::iniciar();
$title = 'Lista Básica';
$pageTitle = 'LISTA BÁSICA';

ob_start();
?>

<div class="card shadow-sm mb-4">
  <div class="card-header bg-primary text-white">
    <i class="bi bi-link-45deg"></i> Selecionar Produto
  </div>
  <div class="card-body">
    <input type="text" id="buscarProduto" class="form-control" placeholder="Pesquisar produto por código ou descrição...">
    <ul id="listaProdutos" class="list-group mt-2" style="display:none; max-height:200px; overflow:auto;"></ul>
    <input type="hidden" id="produtoSelecionado">
  </div>
</div>

<div id="areaPecas" style="display:none;">
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-success text-white">
      <i class="bi bi-boxes"></i> Peças Amarradas
    </div>
    <div class="card-body">
      <input type="text" id="buscarPeca" class="form-control mb-2" placeholder="Adicionar peça por código ou descrição...">
      <ul id="listaPecas" class="list-group" style="display:none; max-height:200px; overflow:auto;"></ul>

      <table class="table table-bordered table-striped table-hover nowrap" id="tabelaPecas">
        <thead>
          <tr>
            <th>Código Peça</th>
            <th>Descrição Peça</th>
            <th>Status Peça(Ativa)</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<div class="text-center mt-3" id="btnExcelContainer" style="display:none;">
  <a id="btnExcel" class="btn btn-success btn-sm" target="_blank">Download Excel</a>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
