<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\Autenticador;

Autenticador::iniciar();
$title = 'Movimentação de Estoque';
$pageTitle = 'CADASTRA MOVIMENTAÇÃO';

ob_start();
?>

<div class="row">
  <div class="col-lg-4 col-12">
    <div class="small-box bg-primary">
      <div class="inner">
        <h3 id="sbSaldo">—</h3>
        <p>Saldo Atual do Item</p>
      </div>
      <div class="icon"><i class="bi bi-graph-up-arrow icone"></i></div>
      <a href="consulta_estoque" class="small-box-footer icone">Ver estoque <i class="bi bi-arrow-right icone"></i></a>
    </div>
  </div>
  <div class="col-lg-4 col-12">
    <div class="small-box bg-success">
      <div class="inner">
        <h3 id="sbUltEntrada">—</h3>
        <p>Última Entrada</p>
      </div>
      <div class="icon"><i class="bi bi-box-arrow-in-down icone"></i></div>
      <span class="small-box-footer">&nbsp;</span>
    </div>
  </div>
  <div class="col-lg-4 col-12">
    <div class="small-box bg-danger">
      <div class="inner">
        <h3 id="sbUltSaida">—</h3>
        <p>Última Saída</p>
      </div>
      <div class="icon"><i class="bi bi-box-arrow-up icone"></i></div>
      <span class="small-box-footer">&nbsp;</span>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-header bg-primary text-white">
    <i class="bi bi-box-seam"></i> Lançar Movimentação (Entrada/Saída)
  </div>
  <div class="card-body">
    <form id="movForm">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label d-block">Tipo</label>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="tipo" id="tipoE" value="E" checked>
            <label class="form-check-label" for="tipoE">Entrada</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="tipo" id="tipoS" value="S">
            <label class="form-check-label" for="tipoS">Saída</label>
          </div>
        </div>

        <div class="col-md-3">
          <label class="form-label d-block">Item</label>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="tipo_item" id="tpProduto" value="produto" checked>
            <label class="form-check-label" for="tpProduto">Produto</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="tipo_item" id="tpPeca" value="peca">
            <label class="form-check-label" for="tpPeca">Peça</label>
          </div>
        </div>

        <div class="col-md-6" id="boxProduto">
          <label for="busca_produto" class="form-label">Produto</label>
          <input type="text" class="form-control" id="busca_produto" placeholder="Digite código ou descrição (mín. 3 letras)">
          <input type="hidden" id="produto" name="produto">
          <div class="form-text">Exibe: Descrição (Código)</div>
        </div>

        <div class="col-md-6 d-none" id="boxPeca">
          <label for="busca_peca" class="form-label">Peça</label>
          <input type="text" class="form-control" id="busca_peca" placeholder="Digite código ou descrição (mín. 3 letras)">
          <input type="hidden" id="peca" name="peca">
          <div class="form-text">Exibe: Descrição (Código)</div>
        </div>

        <div class="col-md-3">
          <label for="qtde" class="form-label">Quantidade</label>
          <input type="number" min="1" class="form-control" id="qtde" name="qtde" required>
        </div>

        <div class="col-md-3">
          <label for="os" class="form-label">OS (opcional)</label>
          <input type="number" class="form-control" id="os" name="os" placeholder="nº da OS">
        </div>

        <div class="col-md-6">
          <label for="motivo" class="form-label">Motivo (opcional)</label>
          <input type="text" class="form-control" id="motivo" name="motivo" maxlength="200">
        </div>
      </div>

      <div class="mt-3">
        <button type="submit" class="btn btn-success btn-sm"> Gravar</button>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header bg-primary text-white">
    <i class="bi bi-clock-history"></i> Últimos Lançamentos do Item
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-striped table-hover nowrap" id="movTable">
        <thead>
          <tr>
            <th>#</th>
            <th>Data</th>
            <th>Tipo</th>
            <th>Qtde</th>
            <th>OS</th>
            <th>Motivo</th>
            <th>Item</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
