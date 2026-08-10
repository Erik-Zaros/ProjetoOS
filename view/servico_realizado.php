<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\Autenticador;
use App\Service\FuncoesService;

Autenticador::iniciar();

$title = 'Cadastro de Serviço Realizado';
$pageTitle = 'CADASTRO DE SERVIÇO';

$usa_modulo_estoque = FuncoesService::usaModuloEstoque();

ob_start();
?>

<input type="hidden" id="usa_modulo_estoque" value="<?= $usa_modulo_estoque == true ? "true" : "false" ?>">

<div class="card shadow-sm mb-4">
    <div class="card-header">
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
                <?php if ($usa_modulo_estoque == true) { ?>
                    <div class="col-md-2">
                        <label for="usa_estoque" class="form-label d-block">Usa Estoque</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="usa_estoque" name="usa_estoque">
                            <label class="form-check-label" for="usa_estoque">Ativo</label>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <button type="submit" class="btn btn-success btn-sm mt-3">Gravar</button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <i class="bi bi-card-list"></i> Produtos Cadastrados
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover nowrap" id="servicoRealizadoTable">
            <thead>
                <tr>
                    <th class="text-center">Descrição</th>
                    <th class="text-center">Ativo</th>
                    <?php if ($usa_modulo_estoque == true) { ?>
                        <th class="text-center">Usa Estoque</th>
                    <?php } ?>
                    <th class="text-center">Ações</th>
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
