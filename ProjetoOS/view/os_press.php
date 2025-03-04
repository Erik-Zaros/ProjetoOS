<?php
$title = 'Detalhes da Ordem de Serviço';
$pageTitle = 'DETALHES DA ORDEM DE SERVIÇO';
require_once '../config/imports.php';
require_once '../config/rotas.php';

$os = isset($_GET['os']) && is_numeric($_GET['os']) ? intval($_GET['os']) : 0;
?>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-card-list"></i> Informações da Ordem de Serviço</h5>
    </div>
    <div class="card-body" id="detalhes-os">

        <div class="row mb-3">
            <div class="col-md-12 text-center">
                <h1 class="display-4 text-warning fw-bold" id="osNumero">OS 68653922</h1>
                <span id="status">Aberto</span>
            </div>
        </div>

        <table class="table table-bordered align-middle">
            <tbody>
                <tr>
                    <th scope="row" style="width: 20%;">Data de Abertura</th>
                    <td style="width: 30%;"><span id="dataAbertura"></span></td>
                    <th scope="row" style="width: 20%;">Nome do Consumidor</th>
                    <td style="width: 30%;"><span id="nomeConsumidor"></span></td>
                </tr>
                <tr>
                    <th scope="row">CPF</th>
                    <td><span id="cpfConsumidor"></span></td>
                    <th scope="row">Produto</th>
                    <td><span id="produto"></span></td>
                </tr>
                <tr>
            </tbody>
        </table>

        <div class="text-end mt-3">
            <a href="cadastra_os.php?os=<?= $os ?>" class="btn btn-primary btn-sm me-2">Alterar</a>
            <a href="consulta_os.php" class="btn btn-secondary btn-sm">Voltar</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        carregarDetalhesOS(<?= $os ?>);
    });
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
