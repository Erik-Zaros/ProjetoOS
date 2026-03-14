<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth\Autenticador;

Autenticador::iniciar();

$title     = 'Dashboard';
$pageTitle = 'DASHBOARD';
ob_start();
?>

<style>
.kpi-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e8ecf0;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.04);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    cursor: pointer;
}
.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.09);
}
.kpi-icon {
    width: 48px; height: 48px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; flex-shrink: 0;
}
.kpi-body { flex: 1; min-width: 0; }
.kpi-value {
    font-size: 26px; font-weight: 800; color: #1a202c; line-height: 1;
    font-family: 'Nunito', 'Segoe UI', sans-serif;
}
.kpi-label {
    font-size: 11px; font-weight: 700; color: #718096;
    text-transform: uppercase; letter-spacing: 0.07em;
    margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

.chart-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e8ecf0;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.04);
    padding: 4px 8px 8px;
}

#modalDashboard .modal-header {
    background: #2e3a4e;
    color: #e8edf3;
    border-radius: 10px 10px 0 0;
    padding: 14px 20px;
}
#modalDashboard .modal-title {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    font-family: 'Nunito', 'Segoe UI', sans-serif;
}
#modalDashboard .btn-close { filter: invert(1); }
#modalDashboard .modal-body { padding: 16px; }
#modalDashboard .modal-content { border-radius: 10px; border: none; box-shadow: 0 8px 32px rgba(0,0,0,0.15); }
#modalDashboard table thead th {
    background: #f5f7fa;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #4a5568;
    border-bottom: 2px solid #e8ecf0;
}
#modalDashboard table tbody td {
    font-size: 13px;
    color: #2d3748;
    vertical-align: middle;
    border-color: #f0f3f6;
}
#modalDashboard table tbody tr:hover { background: #f5f8ff; }
</style>

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
        <div class="chart-card">
            <div id="grafico-os-timeline" style="height:280px;"></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="chart-card">
            <div id="grafico-pizza-os-status" style="height:280px;"></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="chart-card">
            <div id="grafico-os-tecnico" style="height:280px;"></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="chart-card">
            <div id="grafico-estoque-mensal" style="height:280px;"></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="chart-card">
            <div id="grafico-pecas-usadas" style="height:280px;"></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="chart-card">
            <div id="grafico-pizza-status-produto" style="height:280px;"></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="chart-card">
            <div id="grafico-pizza-status-peca" style="height:280px;"></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-12">
        <div class="chart-card">
            <div id="grafico-colunas" style="height:260px;"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDashboard" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
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
