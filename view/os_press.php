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
ob_start();
?>

<div class="card shadow-sm mb-4">
  <div class="card-header bg-primary text-white">
    <i class="bi bi-clipboard-data"></i> Dados da OS
  </div>
  <div class="card-body">
    <div class="row info-row">
      <div class="col-md-3 info-col">
        <div class="info-label">Nº da OS</div>
        <div class="info-value large"><?= htmlspecialchars($osInfo['os']) ?></div>
      </div>
      <div class="col-md-3 info-col">
        <div class="info-label">Data Abertura</div>
        <div class="info-value"><?= htmlspecialchars($osInfo['data_abertura']) ?></div>
      </div>
      <div class="col-md-3 info-col">
        <div class="info-label">Status</div>
        <div class="info-value">
          <?php if ($osInfo['finalizada'] === 't') { ?>
            <span class="badge bg-success fs-6 px-3 py-2">Finalizada</span>
          <?php } else if ($osInfo['cancelada'] === 't') { ?>
            <span class="badge bg-danger fs-6 px-3 py-2">Cancelada</span>
          <?php } else { ?>
            <span class="badge bg-warning fs-6 px-3 py-2">Em Aberto</span>
          <?php } ?>
        </div>
      </div>
    </div>
    <div class="row info-row">
      <div class="col-md-6 info-col">
        <div class="info-label">Tipo de Atendimento</div>
        <div class="info-value"><?= htmlspecialchars($osInfo['tipo_atendimento']) ?></div>
      </div>
      <div class="col-md-6 info-col">
        <div class="info-label">Técnico</div>
        <div class="info-value"><?= htmlspecialchars($osInfo['tecnico']) ?></div>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-header bg-primary text-white">
    <i class="bi bi-person"></i> Informações do Consumidor
  </div>
  <div class="card-body">
    <div class="row info-row">
      <div class="col-md-4 info-col">
        <div class="info-label">Nome</div>
        <div class="info-value"><?= htmlspecialchars($osInfo['nome_consumidor']) ?></div>
      </div>
      <div class="col-md-4 info-col">
        <div class="info-label">CPF</div>
        <div class="info-value"><?= FuncoesService::mascaraCpfCnpj($osInfo['cpf_consumidor']) ?></div>
      </div>
      <div class="col-md-4 info-col">
        <div class="info-label">Nota Fiscal</div>
        <div class="info-value"><?= htmlspecialchars($osInfo['nota_fiscal']) ?></div>
      </div>
    </div>
    <div class="row info-row">
      <div class="col-md-2 info-col">
        <div class="info-label">CEP</div>
        <div class="info-value"><?= htmlspecialchars($osInfo['cep_consumidor']) ?></div>
      </div>
      <div class="col-md-6 info-col">
        <div class="info-label">Endereço</div>
        <div class="info-value"><?= htmlspecialchars($osInfo['endereco_consumidor']) ?></div>
      </div>
      <div class="col-md-4 info-col">
        <div class="info-label">Bairro</div>
        <div class="info-value"><?= htmlspecialchars($osInfo['bairro_consumidor']) ?></div>
      </div>
    </div>
    <div class="row info-row">
      <div class="col-md-2 info-col">
        <div class="info-label">Número</div>
        <div class="info-value"><?= htmlspecialchars($osInfo['numero_consumidor']) ?></div>
      </div>
      <div class="col-md-4 info-col">
        <div class="info-label">Cidade</div>
        <div class="info-value"><?= htmlspecialchars($osInfo['cidade_consumidor']) ?></div>
      </div>
      <div class="col-md-2 info-col">
        <div class="info-label">UF</div>
        <div class="info-value"><?= htmlspecialchars($osInfo['estado_consumidor']) ?></div>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-header bg-primary text-white">
    <i class="bi bi-box-seam"></i> Informações do Produto
  </div>
  <div class="card-body">
    <div class="row info-row">
      <div class="col-md-12 info-col">
        <div class="info-label">Produto</div>
        <div class="info-value"><?= htmlspecialchars($osInfo['produto_codigo_descricao']) ?></div>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-header bg-primary text-white">
    <i class="bi bi-tools"></i> Peças Utilizadas
  </div>
  <div class="card-body">
    <?php if (empty($pecas)) { ?>
      <p class="text-muted text-center py-3">Nenhuma peça vinculada a esta OS.</p>
    <?php } else { ?>
      <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr>
            <th>Código</th>
            <th>Descrição</th>
            <th>Quantidade</th>
            <th>Serviço Realizado</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($pecas as $peca) {?>
          <tr>
            <td><?= htmlspecialchars($peca['codigo']) ?></td>
            <td><?= htmlspecialchars($peca['descricao']) ?></td>
            <td><?= htmlspecialchars($peca['quantidade']) ?></td>
            <td><?= htmlspecialchars($peca['descricao_servico_realizado']) ?></td>
          </tr>
        <?php } ?>
        </tbody>
      </table>
    <?php } ?>
  </div>
</div>

<div class="text-end mb-3">
  <?php
  $osFinalizadaCancelada = FuncoesService::ValidaOsFinalizadaCancelada($os);
  
  if ($osFinalizadaCancelada == false) { ?>
    <a href="cadastra_os?os=<?= $osInfo['os'] ?>" class="btn btn-warning btn-sm">
      <i class="bi bi-pencil-square"></i> Editar OS
    </a>
  <?php } ?>
  <a href="consulta_os" class="btn btn-secondary btn-sm">
    <i class="bi bi-arrow-left"></i> Voltar
  </a>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>