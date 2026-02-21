<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$title = 'Cadastro de Ordem de Serviço';
$pageTitle = 'CADASTRO DE ORDEM DE SERVIÇO';
ob_start();
?>

<form id="osForm">
  <input type="hidden" id="os" name="os" value="">
<div class="card shadow-sm mb-4">
  <div class="card-header bg-primary text-white">
    <i class="bi bi-clipboard-data"></i> Informações da OS
  </div>
  <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-2">
        <label for="data_abertura" class="form-label">Data de Abertura</label>
        <input type="date" class="form-control" id="data_abertura" name="data_abertura" required>
      </div>
      <div class="col-md-2">
        <label for="nota_fiscal" class="form-label">Nota Fiscal</label>
        <input type="text" class="form-control" id="nota_fiscal" name="nota_fiscal">
      </div>
      <div class="col-md-3">
        <label for="tipo_atendimento" class="form-label">Tipo de Atendimento</label>
        <select class="form-select" name="tipo_atendimento" id="tipo_atendimento"></select>
      </div>
      <div class="col-md-4">
        <label for="tecnico" class="form-label">Técnico</label>
        <select class="form-select" name="tecnico" id="tecnico"></select>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-header bg-secondary text-white">
    <i class="bi bi-person"></i> Informações do Consumidor
  </div>
  <div class="card-body">

    <div class="row mb-3">
      <div class="col-md-6">
        <label for="nome_consumidor" class="form-label">Nome</label>
        <input type="text" class="form-control" id="nome_consumidor" name="nome_consumidor" placeholder="Nome" required>
      </div>
      <div class="col-md-6">
        <label for="cpf_consumidor" class="form-label">CPF</label>
        <input type="text" class="form-control" id="cpf_consumidor" name="cpf_consumidor" placeholder="CPF" required>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-3">
        <label for="cep_consumidor" class="form-label">CEP</label>
        <input type="text" class="form-control" id="cep_consumidor" name="cep_consumidor" placeholder="CEP">
      </div>
      <div class="col-md-6">
        <label for="endereco_consumidor" class="form-label">Endereço</label>
        <input type="text" class="form-control" id="endereco_consumidor" name="endereco_consumidor" placeholder="Endereço">
      </div>
      <div class="col-md-3">
        <label for="numero_consumidor" class="form-label">Número</label>
        <input type="text" class="form-control" id="numero_consumidor" name="numero_consumidor" placeholder="Número">
      </div>
    </div>

    <div class="row">
      <div class="col-md-5">
        <label for="bairro_consumidor" class="form-label">Bairro</label>
        <input type="text" class="form-control" id="bairro_consumidor" name="bairro_consumidor" placeholder="Bairro">
      </div>
      <div class="col-md-5">
        <label for="cidade_consumidor" class="form-label">Cidade</label>
        <input type="text" class="form-control" id="cidade_consumidor" name="cidade_consumidor" placeholder="Cidade">
      </div>
      <div class="col-md-2">
        <label for="estado_consumidor" class="form-label">UF</label>
        <input type="text" class="form-control" id="estado_consumidor" name="estado_consumidor" placeholder="UF">
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-header bg-info text-white">
    <i class="bi bi-box"></i> Informações do Produto
  </div>
  <div class="card-body">
    <select class="form-control" id="produto" name="produto" required></select>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header bg-success text-white">
    <i class="bi bi-nut"></i> Peças do Produto
  </div>
  <div class="card-body">
    <div class="row mb-3 align-items-end">
      <div class="col-md-4">
        <label for="busca_peca" class="form-label">Buscar Peça (Código ou Descrição)</label>
        <input type="text" id="busca_peca" class="form-control" placeholder="Digite para buscar..." autocomplete="off">
      </div>

      <div class="col-md-2">
        <label for="quantidade_peca" class="form-label">Quantidade</label>
        <input type="number" id="quantidade_peca" class="form-control" value="1" min="1">
      </div>

      <div class="col-md-2">
        <label for="servico_realizado" class="form-label">Serviço Realizado</label>
        <select class="form-select" id="servico_realizado">
        </select>
      </div>

      <div class="col-md-2">
        <button type="button" id="btnAdicionarPeca" class="btn btn-success w-100">
          <i class="bi bi-plus-circle" style="color: white;"></i> Adicionar
        </button>
      </div>
    </div>
    <table class="table table-bordered table-striped table-hover nowrap" id="tabelaPecas">
      <thead>
        <tr>
          <th>Código</th>
          <th>Descrição</th>
          <th>Quantidade</th>
          <th>Serviço Realizado</th>
          <th>Ação</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<div id="secaoAnexos"></div>

<div class="text-center mt-3">
  <button type="submit" class="btn btn-secondary btn-sm">Gravar</button>
</div>

<br>

</form>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
