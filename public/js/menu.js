$(document).ready(function () {
    let dashboardData = null;
    let chartInstances = {};

    function isDarkMode() {
        return document.documentElement.getAttribute('data-bs-theme') === 'dark';
    }

    function getChartTheme() {
        const dark = isDarkMode();
        return {
            textColor: dark ? '#cbd5e1' : '#495057',
            gridColor: dark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)',
            borderColor: dark ? '#373b3e' : '#dee2e6'
        };
    }

    $.ajax({
        url: '../public/menu/menu.php',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            dashboardData = response;
            preencherKPIs(response);
            renderAllCharts(response);
        },
        error: function () {
            console.error('Erro ao carregar dados do dashboard.');
            if (window.Alerta) {
                Alerta.error('Erro ao carregar os dados do painel.');
            }
        }
    });

    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            if (mutation.attributeName === 'data-bs-theme' && dashboardData) {
                renderAllCharts(dashboardData);
            }
        });
    });
    observer.observe(document.documentElement, { attributes: true });

    function destroyChart(key) {
        if (chartInstances[key]) {
            chartInstances[key].destroy();
            delete chartInstances[key];
        }
    }

    function renderAllCharts(response) {
        carregarOsTimeline(response);
        carregarGraficoPizzaStatusOS(response);
        carregarOsPorTecnico(response);
        carregarEstoqueMensal(response);
        carregarPecasMaisUsadas(response);
        carregarGraficoPizzaStatusProduto(response);
        carregarGraficoPizzaStatusPeca(response);
    }

    function preencherKPIs(d) {
        function animCount(el, target) {
            if (!el) return;
            var start = 0;
            var val = Number(target) || 0;
            var step = Math.ceil(val / 30) || 1;
            var timer = setInterval(function () {
                start = Math.min(start + step, val);
                el.textContent = start.toLocaleString('pt-BR');
                if (start >= val) clearInterval(timer);
            }, 25);
        }

        animCount(document.getElementById('kpi-os'),         d.ordens_servico);
        animCount(document.getElementById('kpi-os-abertas'), d.os_abertas);
        animCount(document.getElementById('kpi-clientes'),   d.clientes);
        animCount(document.getElementById('kpi-produtos'),   d.produtos);
        animCount(document.getElementById('kpi-pecas'),      d.pecas);
        animCount(document.getElementById('kpi-usuarios'),   d.usuarios);

        $('#kpi-os').closest('.kpi-card').css('cursor', 'pointer').off('click').on('click', function () { abrirModal('os'); });
        $('#kpi-os-abertas').closest('.kpi-card').css('cursor', 'pointer').off('click').on('click', function () { abrirModal('os_abertas'); });
        $('#kpi-clientes').closest('.kpi-card').css('cursor', 'pointer').off('click').on('click', function () { abrirModal('clientes'); });
        $('#kpi-produtos').closest('.kpi-card').css('cursor', 'pointer').off('click').on('click', function () { abrirModal('produtos'); });
        $('#kpi-pecas').closest('.kpi-card').css('cursor', 'pointer').off('click').on('click', function () { abrirModal('pecas'); });
        $('#kpi-usuarios').closest('.kpi-card').css('cursor', 'pointer').off('click').on('click', function () { abrirModal('usuarios'); });
    }

    function carregarOsTimeline(dados) {
        var canvas = document.getElementById('grafico-os-timeline');
        if (!canvas || !window.Chart) return;
        destroyChart('os_timeline');

        var timeline = dados.os_timeline || [];
        var labels = timeline.map(function (d) { return d.dia; });
        var valores = timeline.map(function (d) { return Number(d.total) || 0; });
        var theme = getChartTheme();

        chartInstances['os_timeline'] = new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'OS Abertas',
                    data: valores,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.15)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2,
                    pointBackgroundColor: '#3b82f6',
                    pointRadius: 3,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onClick: function (evt, elements) {
                    if (elements && elements.length > 0) {
                        abrirModal('os_abertas');
                    }
                },
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        ticks: { color: theme.textColor, font: { size: 10 } },
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: theme.textColor,
                            precision: 0,
                            font: { size: 10 }
                        },
                        grid: { color: theme.gridColor }
                    }
                }
            }
        });
    }

    function carregarGraficoPizzaStatusOS(dados) {
        var canvas = document.getElementById('grafico-pizza-os-status');
        if (!canvas || !window.Chart) return;
        destroyChart('os_status');

        var labels = ['Abertas', 'Finalizadas', 'Canceladas'];
        var valores = [
            Number(dados.os_abertas) || 0,
            Number(dados.os_finalizadas) || 0,
            Number(dados.os_canceladas) || 0
        ];
        var theme = getChartTheme();

        chartInstances['os_status'] = new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: valores,
                    backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
                    borderWidth: 2,
                    borderColor: theme.borderColor
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onClick: function (evt, elements) {
                    if (elements && elements.length > 0) {
                        var idx = elements[0].index;
                        var status = labels[idx];
                        if (status) abrirModal('os_status', status);
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: theme.textColor, boxWidth: 12, font: { size: 11 } }
                    }
                }
            }
        });
    }

    function carregarOsPorTecnico(dados) {
        var canvas = document.getElementById('grafico-os-tecnico');
        if (!canvas || !window.Chart) return;
        destroyChart('os_tecnico');

        var tecnicos = dados.os_por_tecnico || [];
        var nomes = tecnicos.map(function (t) { return t.nome; });
        var totais = tecnicos.map(function (t) { return Number(t.total) || 0; });
        var theme = getChartTheme();

        chartInstances['os_tecnico'] = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: nomes,
                datasets: [{
                    label: 'OS',
                    data: totais,
                    backgroundColor: ['#3b82f6', '#0ea5e9', '#6366f1', '#8b5cf6', '#ec4899', '#14b8a6'],
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                onClick: function (evt, elements) {
                    if (elements && elements.length > 0) {
                        var idx = elements[0].index;
                        var tecnico = nomes[idx];
                        if (tecnico) abrirModal('os_tecnico', tecnico);
                    }
                },
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { color: theme.textColor, precision: 0, font: { size: 10 } },
                        grid: { color: theme.gridColor }
                    },
                    y: {
                        ticks: { color: theme.textColor, font: { size: 11 } },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    function carregarEstoqueMensal(dados) {
        var canvas = document.getElementById('grafico-estoque-mensal');
        if (!canvas || !window.Chart) return;
        destroyChart('estoque_mensal');

        var meses = (dados.estoque_mensal || []).map(function (d) { return d.mes; });
        var entradas = (dados.estoque_mensal || []).map(function (d) { return Number(d.entradas) || 0; });
        var saidas = (dados.estoque_mensal || []).map(function (d) { return Number(d.saidas) || 0; });
        var theme = getChartTheme();

        chartInstances['estoque_mensal'] = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: meses,
                datasets: [
                    { label: 'Entradas', data: entradas, backgroundColor: '#10b981', borderRadius: 3 },
                    { label: 'Saídas', data: saidas, backgroundColor: '#ef4444', borderRadius: 3 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { color: theme.textColor, boxWidth: 12, font: { size: 11 } }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: theme.textColor, font: { size: 10 } },
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { color: theme.textColor, precision: 0, font: { size: 10 } },
                        grid: { color: theme.gridColor }
                    }
                }
            }
        });
    }

    function carregarPecasMaisUsadas(dados) {
        var canvas = document.getElementById('grafico-pecas-usadas');
        if (!canvas || !window.Chart) return;
        destroyChart('pecas_usadas');

        var pecas = dados.pecas_mais_usadas || [];
        var nomes = pecas.map(function (p) { return p.descricao; });
        var totais = pecas.map(function (p) { return Number(p.total) || 0; });
        var theme = getChartTheme();

        chartInstances['pecas_usadas'] = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: nomes,
                datasets: [{
                    label: 'Qtd. Utilizada',
                    data: totais,
                    backgroundColor: '#4f46e5',
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                onClick: function (evt, elements) {
                    if (elements && elements.length > 0) {
                        var idx = elements[0].index;
                        var peca = nomes[idx];
                        if (peca) abrirModal('pecas_usadas', peca);
                    }
                },
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { color: theme.textColor, precision: 0, font: { size: 10 } },
                        grid: { color: theme.gridColor }
                    },
                    y: {
                        ticks: { color: theme.textColor, font: { size: 11 } },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    function carregarGraficoPizzaStatusProduto(dados) {
        var canvas = document.getElementById('grafico-pizza-status-produto');
        if (!canvas || !window.Chart) return;
        destroyChart('status_produto');

        var inativo = Number(dados.produto_inativo) || 0;
        var ativo   = Number(dados.produto_ativo)   || 0;
        if (ativo === 0 && inativo === 0) return;

        var labels = ['Inativos', 'Ativos'];
        var valores = [inativo, ativo];
        var theme = getChartTheme();

        chartInstances['status_produto'] = new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: valores,
                    backgroundColor: ['#ef4444', '#10b981'],
                    borderWidth: 2,
                    borderColor: theme.borderColor
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onClick: function (evt, elements) {
                    if (elements && elements.length > 0) {
                        var idx = elements[0].index;
                        var status = labels[idx];
                        if (status) abrirModal('produtos', status);
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: theme.textColor, boxWidth: 12, font: { size: 11 } }
                    }
                }
            }
        });
    }

    function carregarGraficoPizzaStatusPeca(dados) {
        var canvas = document.getElementById('grafico-pizza-status-peca');
        if (!canvas || !window.Chart) return;
        destroyChart('status_peca');

        var inativa = Number(dados.peca_inativa) || 0;
        var ativa   = Number(dados.peca_ativa)   || 0;
        if (ativa === 0 && inativa === 0) return;

        var labels = ['Inativas', 'Ativas'];
        var valores = [inativa, ativa];
        var theme = getChartTheme();

        chartInstances['status_peca'] = new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: valores,
                    backgroundColor: ['#ef4444', '#10b981'],
                    borderWidth: 2,
                    borderColor: theme.borderColor
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onClick: function (evt, elements) {
                    if (elements && elements.length > 0) {
                        var idx = elements[0].index;
                        var status = labels[idx];
                        if (status) abrirModal('pecas', status);
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: theme.textColor, boxWidth: 12, font: { size: 11 } }
                    }
                }
            }
        });
    }
});

function abrirModal(tipo, filtro) {
    filtro = filtro || null;

    $('#modalDashboardLabel').text('Carregando...');
    $('#modalCorpo').html(`
        <div class="d-flex align-items-center justify-content-center py-5 gap-2 text-muted">
            <div class="spinner-border spinner-border-sm" role="status"></div>
            <span style="font-size:13px;">Buscando registros...</span>
        </div>
    `);
    $('#modalDashboard').modal('show');

    var params = { tipo: tipo };
    if (filtro) params.filtro = filtro;

    $.ajax({
        url: '../public/menu/modal_dashboard.php',
        method: 'GET',
        data: params,
        dataType: 'json',
        success: function (res) {
            if (res.erro) {
                $('#modalCorpo').html('<div class="alert alert-danger m-3">' + res.erro + '</div>');
                return;
            }

            $('#modalDashboardLabel').text(res.titulo);

            var thead = '<thead><tr>';
            res.colunas.forEach(function (col) {
                thead += '<th class="text-center">' + col + '</th>';
            });
            thead += '</tr></thead>';

            var tbody = '<tbody>';
            if (!res.dados || res.dados.length === 0) {
                tbody += '<tr><td colspan="' + res.colunas.length + '" class="text-center text-muted py-4">Nenhum registro encontrado.</td></tr>';
            } else {
                res.dados.forEach(function (row) {
                    tbody += '<tr>';
                    res.campos.forEach(function (campo) {
                        var val = row[campo] !== null && row[campo] !== undefined ? row[campo] : '—';
                        if (campo === 'status' || campo === 'situacao') {
                            var mapa = {
                                'Aberta':    'warning',
                                'Finalizada':'success',
                                'Cancelada': 'danger',
                                'Ativo':     'success',
                                'Inativo':   'secondary',
                                'Ativa':     'success',
                                'Inativa':   'secondary',
                                'Sim':       'info',
                                'Não':       'light'
                            };
                            var cor = mapa[val] || 'secondary';
                            val = '<span class="badge bg-' + cor + '">' + val + '</span>';
                        }
                        if (campo === 'os') {
                            val = '<a class="link" target="_blank" href="os_press?os=' + val + '">' + val + '</a>';
                        }
                        tbody += '<td class="text-center">' + val + '</td>';
                    });
                    tbody += '</tr>';
                });
            }
            tbody += '</tbody>';

            $('#modalCorpo').html(
                '<div class="table-responsive px-1">' +
                    '<table id="modalTabela" class="table table-hover table-sm w-100">' +
                        thead + tbody +
                    '</table>' +
                '</div>'
            );
            new MiniDataTable('#modalTabela');
        },
        error: function () {
            $('#modalCorpo').html('<div class="alert alert-danger m-3">Erro ao buscar dados.</div>');
        }
    });
}
