<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\Autenticador;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$title = 'Cadastro de Ordem de Serviço';
$pageTitle = 'CADASTRO DE ORDEM DE SERVIÇO';
ob_start();
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-tools"></i> Cadastro de Ordem de Serviço
    </div>
    <div class="card-body">
        <form id="osForm">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="os" class="form-label">Número da Ordem de Serviço</label>
                    <input type="text" class="form-control" id="os" name="os" readonly>
                </div>
                <div class="col-md-6">
                    <label for="data_abertura" class="form-label">Data de Abertura</label>
                    <input type="date" class="form-control" id="data_abertura" name="data_abertura"
                        required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nome_consumidor" class="form-label">Nome do Consumidor</label>
                    <input type="text" class="form-control" id="nome_consumidor" name="nome_consumidor"
                        required maxlength="50">
                </div>
                <div class="col-md-6">
                    <label for="cpf_consumidor" class="form-label">CPF do Consumidor</label>
                    <input type="text" class="form-control" id="cpf_consumidor" name="cpf_consumidor"
                        required maxlength="11">
                </div>
            </div>
            <div class="mb-3">
                <label for="produto" class="form-label">Produto</label>
                <select class="form-control" id="produto" name="produto" required></select>
            </div>
            <button type="submit" class="btn btn-success btn-sm">Gravar</button>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
