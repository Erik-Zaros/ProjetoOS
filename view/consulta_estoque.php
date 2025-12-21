<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\Autenticador;

Autenticador::iniciar();

$title     = 'Consulta de Estoque';
$pageTitle = 'CONSULTA DE ESTOQUE';
ob_start();
?>

<div class="row">
  <div class="col-lg-4 col-12">
    <div class="small-box bg-primary">
      <div class="inner">
        <h3 id="kpiTotal">—</h3>
        <p>Total de Itens</p>
      </div>
      <div class="icon"><i class="bi bi-box-seam icone"></i></div>
      <span class="small-box-footer">&nbsp;</span>
    </div>
  </div>
  <div class="col-lg-4 col-12">
    <div class="small-box bg-success">
      <div class="inner">
        <h3 id="kpiComSaldo">—</h3>
        <p>Com Saldo</p>
      </div>
      <div class="icon"><i class="bi bi-graph-up icone"></i></div>
      <span class="small-box-footer">&nbsp;</span>
    </div>
  </div>
  <div class="col-lg-4 col-12">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3 id="kpiZerados">—</h3>
        <p>Zerados</p>
      </div>
      <div class="icon"><i class="bi bi-slash-circle icone"></i></div>
      <span class="small-box-footer">&nbsp;</span>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-header bg-primary text-white">
    <i class="bi bi-funnel-fill me-1"></i> Filtros
  </div>
  <div class="card-body">
    <form id="filtroEstoque" class="row g-3">

      <div class="col-md-4">
        <label class="form-label">Tipo de Item</label>
        <div class="d-flex gap-3">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="tipo_item" id="tpAmbos" value="ambos" checked>
            <label class="form-check-label" for="tpAmbos">Ambos</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="tipo_item" id="tpProduto" value="produto">
            <label class="form-check-label" for="tpProduto">Produto</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="tipo_item" id="tpPeca" value="peca">
            <label class="form-check-label" for="tpPeca">Peça</label>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <label for="termo" class="form-label">Pesquisar (código/descrição)</label>
        <input type="text" class="form-control" id="termo" name="termo" placeholder="Ex.: BICO 123 | ÓLEO 10W40">
      </div>

      <div class="col-md-2 d-flex align-items-end">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="somenteSaldo" name="somente_saldo">
          <label class="form-check-label" for="somenteSaldo">Somente com saldo</label>
        </div>
      </div>

      <div class="col-md-2 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-search me-1 icone"></i>Pesquisar</button>
        <button type="button" id="btnLimpar" class="btn btn-secondary btn-sm"><i class="bi bi-eraser me-1 icone"></i>Limpar</button>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header bg-primary text-white">
    <i class="bi bi-table me-1"></i> Resultado
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-striped table-hover nowrap" id="estoqueTable">
        <thead>
          <tr>
            <th>Tipo</th>
            <th>Código</th>
            <th>Descrição</th>
            <th class="text-end">Saldo</th>
            <th>Última Entrada</th>
            <th>Última Saída</th>
            <th style="width: 110px">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr><td colspan="7" class="text-center text-muted">Use os filtros acima e pesquise.</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal fade" id="movModal" tabindex="-1" aria-labelledby="movModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header text-white">
        <h5 class="modal-title" id="movModalLabel"><i class="bi bi-clock-history me-1"></i> Movimentações do Item</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover nowrap" id="movModalTable">
            <thead>
              <tr>
                <th>#</th>
                <th>Data</th>
                <th>Tipo</th>
                <th>Qtde</th>
                <th>OS</th>
                <th>Motivo</th>
              </tr>
            </thead>
            <tbody>
              <tr><td colspan="6" class="text-center text-muted">Sem dados</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
