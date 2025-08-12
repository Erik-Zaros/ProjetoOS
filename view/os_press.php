<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\Autenticador;
use App\Controller\OsController;

Autenticador::iniciar();

$title = 'Detalhes da Ordem de Serviço';
$pageTitle = 'DETALHES DA ORDEM DE SERVIÇO';

$os = $_GET['os'];

$posto = Autenticador::getPosto();
$osInfo = OsController::buscarPorNumero($os, $posto);
$osFinalizada = $osInfo['finalizada'] ?? false;
$osCancelada = $osInfo['cancelada'] ?? false;
?>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-card-list"></i> Informações da Ordem de Serviço</h5>
    </div>
    <div class="card-body" id="detalhes-os">

        <div class="row mb-3">
            <div class="col-md-12 text-center">
                <h1 class="display-4 text-warning fw-bold" id="osNumero"></h1>
                <span id="status"></span>
            </div>
        </div>

        <table class="table table-bordered align-middle">
            <tbody>
                <tr>
                    <th scope="row" style="width: 10%;">Data de Abertura</th>
                    <td colspan="1" style="width: 10%;"><span id="dataAbertura"></span></td>
                    <th scope="row" style="width: 15%;">Nome do Consumidor</th>
                    <td colspan="2" style="width: 30%;"><span id="nomeConsumidor"></span></td>
                    <th scope="row" style="width: 15%;">CPF</th>
                    <td colspan="2" style="width: 20%;"><span id="cpfConsumidor"></span></td>
                </tr>
                <tr>
                    <th scope="row" style="width: 10%;">CEP</th>
                    <td colspan="1" style="width: 10%;"><span id="cepConsumidor"></span></td>
                    <th scope="row" style="width: 15%;">Endereço</th>
                    <td colspan="2" style="width: 30%;"><span id="enderecoConsumidor"></span></td>
                    <th scope="row" style="width: 15%;">Bairro</th>
                    <td colspan="2" style="width: 20%;"><span id="bairroConsumidor"></span></td>
                </tr>
                <tr>
                    <th scope="row" style="width: 10%;">Número</th>
                    <td colspan="1" style="width: 10%;"><span id="numeroConsumidor"></span></td>
                    <th scope="row" style="width: 15%;">Cidade</th>
                    <td colspan="2" style="width: 30%;"><span id="cidadeConsumidor"></span></td>
                    <th scope="row" style="width: 15%;">Estado</th>
                    <td colspan="2" style="width: 20%;"><span id="estadoConsumidor"></span></td>
                </tr>
                <tr>
                    <th scope="row">Produto</th>
                    <td colspan="7"><span id="produtoCodigoDescricao"></span></td>
                </tr>
            </tbody>
        </table>

        <div class="text-end mt-3">

            <?php if ($osFinalizada == 't' || $osCancelada == 't') {
                $botaoAlterar = "";
            } else {
                $botaoAlterar = "<a href='cadastra_os?os=$os' class='btn btn-primary btn-sm me-2'>Alterar</a>";
            } ?>

            <?= $botaoAlterar ?>
            <a href="consulta_os.php" class="btn btn-secondary btn-sm">Voltar</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
