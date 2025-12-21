<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\Autenticador;
use App\Controller\OsController;
use App\Service\FuncoesService;

Autenticador::iniciar();
$posto = Autenticador::getPosto();

$title = 'Detalhes da Ordem de Serviço';
$pageTitle = 'DETALHES DA ORDEM DE SERVIÇO';

$os = $_GET['os'] ?? null;
if (!$os) {
    die("OS não informada.");
}

$osInfo = OsController::buscarPorNumero($os, $posto);

if (isset($osInfo['error'])) {
    echo "<div class='alert alert-danger'>{$osInfo['error']}</div>";
    exit;
}

$pecas = $osInfo['pecas'] ?? [];
?>

<div class="card shadow-sm mb-4">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><i class="bi bi-clipboard-data"></i> Dados da OS</h5>
  </div>
  <div class="card-body p-3">

    <div class="table-responsive">
      <table class="table table-bordered table-sm align-middle">
        <tbody>
          <tr class="table-light">
            <th>Nº da OS</th>
            <td class="fw-bold text-primary"><?= htmlspecialchars($osInfo['os']) ?></td>

            <th>Data Abertura</th>
            <td><?= htmlspecialchars($osInfo['data_abertura']) ?></td>

            <th>Status</th>
            <td>
              <?php if ($osInfo['finalizada'] === 't'): ?>
                <span class="badge bg-success w-100">Finalizada</span>
              <?php elseif ($osInfo['cancelada'] === 't'): ?>
                <span class="badge bg-danger w-100">Cancelada</span>
              <?php else: ?>
                <span class="badge bg-warning text-dark w-100">Em Aberto</span>
              <?php endif; ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-header bg-secondary text-white">
    <h5 class="mb-0"><i class="bi bi-person"></i> Informações do Consumidor</h5>
  </div>

  <div class="card-body p-3">
    <div class="table-responsive">
      <table class="table table-bordered table-sm">
        <tbody>
          <tr>
            <th>Nome</th>
            <td><?= htmlspecialchars($osInfo['nome_consumidor']) ?></td>

            <th>CPF</th>
            <td><?= htmlspecialchars($osInfo['cpf_consumidor']) ?></td>

            <th>Nota Fiscal</th>
            <td><?= htmlspecialchars($osInfo['nota_fiscal']) ?></td>
          </tr>
          <tr>
            <th>CEP</th>
            <td><?= htmlspecialchars($osInfo['cep_consumidor']) ?></td>

            <th>Endereço</th>
            <td colspan="3"><?= htmlspecialchars($osInfo['endereco_consumidor']) ?></td>
          </tr>
          <tr>
            <th>Bairro</th>
            <td><?= htmlspecialchars($osInfo['bairro_consumidor']) ?></td>

            <th>Número</th>
            <td><?= htmlspecialchars($osInfo['numero_consumidor']) ?></td>

            <th>Cidade / UF</th>
            <td><?= htmlspecialchars($osInfo['cidade_consumidor']) ?> - <?= htmlspecialchars($osInfo['estado_consumidor']) ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-header bg-info text-white">
    <h5 class="mb-0"><i class="bi bi-box-seam"></i> Informações do Produto</h5>
  </div>
  <div class="card-body p-3">
    <div class="table-responsive">
      <table class="table table-bordered table-sm">
        <tbody>
          <tr>
            <th>Produto</th>
            <td><?= htmlspecialchars($osInfo['produto_codigo_descricao']) ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-header bg-success text-white">
    <h5 class="mb-0"><i class="bi bi-tools"></i> Peças Utilizadas</h5>
  </div>
  <div class="card-body p-3">

    <?php if (empty($pecas)): ?>
      <p class="text-muted">Nenhuma peça vinculada a esta OS.</p>
    <?php else: ?>

      <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm">
          <thead class="table-light">
            <tr>
              <th>Código</th>
              <th>Descrição</th>
              <th>Qtd</th>
              <th>Serviço Realizado</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($pecas as $peca): ?>
            <tr>
              <td><?= htmlspecialchars($peca['codigo']) ?></td>
              <td><?= htmlspecialchars($peca['descricao']) ?></td>
              <td><?= htmlspecialchars($peca['quantidade']) ?></td>
              <td><?= htmlspecialchars($peca['descricao_servico_realizado']) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    <?php endif; ?>
  </div>
</div>

<div class="text-end">
  <a href="consulta_os" class="btn btn-secondary btn-sm">Voltar</a>
<?php

$osFinalizadaCancelada = FuncoesService::ValidaOsFinalizadaCancelada($os);

if ($osFinalizadaCancelada == false) { ?>
  <a href="cadastra_os?os=<?= $osInfo['os'] ?>" class="btn btn-primary btn-sm">Editar OS</a>
<?php } ?>

</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
