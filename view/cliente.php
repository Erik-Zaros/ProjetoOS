<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\Autenticador;

Autenticador::iniciar();

$title = 'Cadastro de Cliente';
$pageTitle = 'CADASTRO DE CLIENTE';
ob_start();
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-person-fill"></i> Cadastro de Cliente
    </div>
    <div class="card-body">
        <form id="clienteForm">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" class="form-control" id="cpf" name="cpf" required maxlength="11">
                </div>
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" required maxlength="80">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="cep" class="form-label">CEP</label>
                    <input type="text" class="form-control" id="cep" name="cep" maxlength="9">
                </div>
                <div class="col-md-8">
                    <label for="logradouro" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="logradouro" name="endereco">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text" class="form-control" id="bairro" name="bairro">
                </div>
                <div class="col-md-4">
                    <label for="numero" class="form-label">Número</label>
                    <input type="text" class="form-control" id="numero" name="numero">
                </div>
                <div class="col-md-4">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" class="form-control" id="cidade" name="cidade">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="estado" class="form-label">Estado</label>
                    <input type="text" class="form-control" id="estado" name="estado" maxlength="10">
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-sm">Gravar</button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-table"></i> Clientes Cadastrados
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="clientesTable">
            <thead>
                <tr>
                    <th>CPF</th>
                    <th>Nome</th>
                    <th>CEP</th>
                    <th>Endereço</th>
                    <th>Bairro</th>
                    <th>Número</th>
                    <th>Cidade</th>
                    <th>Estado</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="text-center mt-4 mb-3">
    <a href="../public/cliente/relatorio.php" class="btn btn-success btn-sm">Download Excel</a>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
