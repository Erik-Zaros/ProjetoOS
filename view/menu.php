<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\Autenticador;

Autenticador::iniciar();

$title = 'Dashboard';
$pageTitle = 'DASHBOARD';
ob_start(); ?>

<div class="row g-3 mb-3">
    <div class="col-6 col-sm-4 col-lg-2">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#eef2ff; color:#4a6fa5;"><i class="fas fa-file-alt"></i></div>
            <div class="kpi-body">
                <div class="kpi-value" id="kpi-os">–</div>
                <div class="kpi-label">Ordens de Serviço</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-lg-2">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#fff8e1; color:#f6c90e;"><i class="fas fa-folder-open"></i></div>
            <div class="kpi-body">
                <div class="kpi-value" id="kpi-os-abertas">–</div>
                <div class="kpi-label">OS Abertas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-lg-2">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#e6f9f0; color:#27ae60;"><i class="fas fa-users"></i></div>
            <div class="kpi-body">
                <div class="kpi-value" id="kpi-clientes">–</div>
                <div class="kpi-label">Clientes</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-lg-2">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#fef3f2; color:#e74c3c;"><i class="fas fa-box"></i></div>
            <div class="kpi-body">
                <div class="kpi-value" id="kpi-produtos">–</div>
                <div class="kpi-label">Produtos</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-lg-2">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#f0f4ff; color:#3a5a8a;"><i class="fas fa-cogs"></i></div>
            <div class="kpi-body">
                <div class="kpi-value" id="kpi-pecas">–</div>
                <div class="kpi-label">Peças</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-lg-2">
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#f5f0ff; color:#7c3aed;"><i class="fas fa-user-tie"></i></div>
            <div class="kpi-body">
                <div class="kpi-value" id="kpi-usuarios">–</div>
                <div class="kpi-label">Usuários</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-8">
        <div class="chart-card p-3">
            <h6 class="fw-bold mb-1 text-body">OS ABERTAS — ÚLTIMOS 30 DIAS</h6>
            <small class="text-body-secondary d-block mb-3">Clique nos pontos para visualizar ordens de serviço</small>
            <div style="position: relative; height: 260px;">
                <canvas id="grafico-os-timeline"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="chart-card p-3">
            <h6 class="fw-bold mb-1 text-body">STATUS DAS OS</h6>
            <small class="text-body-secondary d-block mb-3">Clique em uma fatia para ver detalhes</small>
            <div style="position: relative; height: 260px;">
                <canvas id="grafico-pizza-os-status"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="chart-card p-3">
            <h6 class="fw-bold mb-1 text-body">OS POR TÉCNICO</h6>
            <small class="text-body-secondary d-block mb-3">Clique na barra para ver as OS do técnico</small>
            <div style="position: relative; height: 260px;">
                <canvas id="grafico-os-tecnico"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-card p-3">
            <h6 class="fw-bold mb-1 text-body">MOVIMENTAÇÃO DE ESTOQUE</h6>
            <small class="text-body-secondary d-block mb-3">Entradas e saídas nos últimos 6 meses</small>
            <div style="position: relative; height: 260px;">
                <canvas id="grafico-estoque-mensal"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="chart-card p-3">
            <h6 class="fw-bold mb-1 text-body">PEÇAS MAIS UTILIZADAS</h6>
            <small class="text-body-secondary d-block mb-3">Clique na barra para ver as OS correspondentes</small>
            <div style="position: relative; height: 260px;">
                <canvas id="grafico-pecas-usadas"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="chart-card p-3">
            <h6 class="fw-bold mb-1 text-body">PRODUTOS</h6>
            <small class="text-body-secondary d-block mb-3">Ativos · Inativos</small>
            <div style="position: relative; height: 260px;">
                <canvas id="grafico-pizza-status-produto"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="chart-card p-3">
            <h6 class="fw-bold mb-1 text-body">PEÇAS</h6>
            <small class="text-body-secondary d-block mb-3">Ativas · Inativas</small>
            <div style="position: relative; height: 260px;">
                <canvas id="grafico-pizza-status-peca"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDashboard" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title" id="modalDashboardLabel">Registros</span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body" id="modalCorpo">
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
