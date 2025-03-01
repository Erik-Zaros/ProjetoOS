<?php

$title = 'Detalhes da Ordem de Serviço';
$pageTitle = 'DETALHES DA ORDEM DE SERVIÇO';
require_once '../config/imports.php';
require_once '../config/rotas.php';

$os = isset($_GET['os']) && is_numeric($_GET['os']) ? intval($_GET['os']) : 0;

?>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-card-list"></i> Informações da Ordem de Serviço
    </div>
    <div class="card-body" id="detalhes-os">
        <p><strong>Número da OS:</strong> <span id="osNumero"></span></p>
        <p><strong>Data de Abertura:</strong> <span id="dataAbertura"></span></p>
        <p><strong>Nome do Consumidor:</strong> <span id="nomeConsumidor"></span></p>
        <p><strong>CPF:</strong> <span id="cpfConsumidor"></span></p>
        <p><strong>Produto:</strong> <span id="produto"></span></p>
        <p><strong>Status:</strong> <span id="status"></span></p>
        <a href="cadastra_os.php?os=<?= $os ?>" class="btn btn-primary btn-sm">Alterar</a>
        <a href="consulta_os.php" class="btn btn-secondary btn-sm">Voltar</a>
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
