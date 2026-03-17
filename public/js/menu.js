$(document).ready(function () {
    $.ajax({
        url: '../public/menu/menu.php',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            preencherKPIs(response);
            carregarOsTimeline(response);
            carregarGraficoPizzaStatusOS(response);
            carregarOsPorTecnico(response);
            carregarEstoqueMensal(response);
            carregarPecasMaisUsadas(response);
            carregarGraficoPizzaStatusProduto(response);
            carregarGraficoPizzaStatusPeca(response);
            carregarGraficoColunas(response);
        },
        error: function () {
            console.error('Erro ao carregar dados do dashboard.');
        }
    });

    $('#modalDashboard').on('hidden.bs.modal', function () {
        if ($.fn.DataTable.isDataTable('#modalTabela')) {
            $('#modalTabela').DataTable().destroy();
        }
        $('#modalTabela').empty();
    });
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
                thead += '<th>' + col + '</th>';
            });
            thead += '</tr></thead>';

            var tbody = '<tbody>';
            if (res.dados.length === 0) {
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
							val = '<a class="link" target="_blank" href="os_press?os='+val+'">'+val+'</a>';
						}
                        tbody += '<td>' + val + '</td>';
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

            $('#modalTabela').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                },
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [],
                autoWidth: false,
                columnDefs: [{ targets: '_all', className: 'align-middle' }]
            });
        },
        error: function () {
            $('#modalCorpo').html('<div class="alert alert-danger m-3">Erro ao buscar dados.</div>');
        }
    });
}

var THEME = {
    font: "'Nunito', 'Segoe UI', sans-serif",
    title: { fontSize: '12px', fontWeight: '700', color: '#2d3748', textTransform: 'uppercase', letterSpacing: '0.07em' },
    subtitle: { fontSize: '11px', color: '#a0aec0', fontWeight: '500' },
    grid: '#f0f3f6',
    axis: '#a0aec0',
    label: '#4a5568',
    bg: 'transparent',
    anim: 700,
    colors: {
        os:     ['#f6c90e', '#27ae60', '#e74c3c'],
        status: ['#e74c3c', '#27ae60'],
        bar:    ['#4a6fa5', '#5b8dd9', '#3a5a8a', '#2d4a7a', '#1a3060', '#7c9dc5', '#2c5282', '#1a365d'],
        entrada:'#27ae60',
        saida:  '#e74c3c',
        line:   '#4a6fa5'
    }
};

var TOOLTIP_BASE = {
    backgroundColor: '#2d3748',
    borderWidth: 0,
    borderRadius: 6,
    shadow: false,
    style: { color: '#fff', fontSize: '12px', fontFamily: THEME.font }
};

function preencherKPIs(d) {
    function animCount(el, target) {
        var start = 0;
        var step = Math.ceil(target / 40);
        var timer = setInterval(function () {
            start = Math.min(start + step, target);
            el.textContent = start.toLocaleString('pt-BR');
            if (start >= target) clearInterval(timer);
        }, 20);
    }

    animCount(document.getElementById('kpi-os'),         d.ordens_servico);
    animCount(document.getElementById('kpi-os-abertas'), d.os_abertas);
    animCount(document.getElementById('kpi-clientes'),   d.clientes);
    animCount(document.getElementById('kpi-produtos'),   d.produtos);
    animCount(document.getElementById('kpi-pecas'),      d.pecas);
    animCount(document.getElementById('kpi-usuarios'),   d.usuarios);

    $('#kpi-os').closest('.kpi-card').css('cursor', 'pointer').on('click', function () { abrirModal('os'); });
    $('#kpi-os-abertas').closest('.kpi-card').css('cursor', 'pointer').on('click', function () { abrirModal('os_abertas'); });
    $('#kpi-clientes').closest('.kpi-card').css('cursor', 'pointer').on('click', function () { abrirModal('clientes'); });
    $('#kpi-produtos').closest('.kpi-card').css('cursor', 'pointer').on('click', function () { abrirModal('produtos'); });
    $('#kpi-pecas').closest('.kpi-card').css('cursor', 'pointer').on('click', function () { abrirModal('pecas'); });
    $('#kpi-usuarios').closest('.kpi-card').css('cursor', 'pointer').on('click', function () { abrirModal('usuarios'); });
}

function buildPizza(elementId, titulo, subtitulo, dados, cores, tipoModal) {
    Highcharts.chart(elementId, {
        chart: {
            type: 'pie',
            backgroundColor: THEME.bg,
            style: { fontFamily: THEME.font },
            animation: { duration: THEME.anim },
            margin: [24, 8, 8, 8]
        },
        title:    { text: titulo,    style: THEME.title,    margin: 14 },
        subtitle: { text: subtitulo, style: THEME.subtitle },
        colors: cores,
        tooltip: {
            backgroundColor: TOOLTIP_BASE.backgroundColor,
            borderWidth: TOOLTIP_BASE.borderWidth,
            borderRadius: TOOLTIP_BASE.borderRadius,
            shadow: TOOLTIP_BASE.shadow,
            style: TOOLTIP_BASE.style,
            headerFormat: '',
            pointFormat: '<span style="color:{point.color}">●</span> <b>{point.name}</b>: {point.y} ({point.percentage:.1f}%)'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                borderWidth: 0,
                borderRadius: 3,
                cursor: 'pointer',
                innerSize: '48%',
                size: '82%',
                dataLabels: {
                    enabled: true,
                    format: '<b style="color:{point.color}">{point.name}</b><br><span style="color:#718096">{point.y} ({point.percentage:.1f}%)</span>',
                    distance: 16,
                    style: { fontSize: '10.5px', fontWeight: '600', fontFamily: THEME.font, textOutline: 'none' }
                },
                states: { hover: { halo: { size: 5 } } },
                point: {
                    events: {
                        click: function () {
                            abrirModal(tipoModal, this.name);
                        }
                    }
                }
            }
        },
        series: [{ animation: { duration: THEME.anim }, colorByPoint: true, data: dados }],
        credits: { enabled: false },
        legend:  { enabled: false },
        accessibility: { enabled: false }
    });
}

function carregarOsTimeline(dados) {
    var timeline = dados.os_timeline || [];
    var dias     = timeline.map(function (d) { return d.dia; });
    var totais   = timeline.map(function (d) { return d.total; });

    Highcharts.chart('grafico-os-timeline', {
        chart: {
            type: 'areaspline',
            backgroundColor: THEME.bg,
            style: { fontFamily: THEME.font },
            animation: { duration: THEME.anim }
        },
        title:    { text: 'OS Abertas — Últimos 30 Dias', style: THEME.title, margin: 16 },
        subtitle: { text: 'Clique em um ponto para ver as OS do dia', style: THEME.subtitle },
        xAxis: {
            categories: dias,
            lineColor: THEME.grid,
            tickColor: THEME.grid,
            labels: { step: Math.ceil(dias.length / 10), style: { fontSize: '11px', color: THEME.label, fontFamily: THEME.font } }
        },
        yAxis: {
            title: { text: null },
            gridLineColor: THEME.grid,
            min: 0,
            allowDecimals: false,
            labels: { style: { fontSize: '11px', color: THEME.axis, fontFamily: THEME.font } }
        },
        tooltip: {
            backgroundColor: TOOLTIP_BASE.backgroundColor,
            borderWidth: TOOLTIP_BASE.borderWidth,
            borderRadius: TOOLTIP_BASE.borderRadius,
            shadow: TOOLTIP_BASE.shadow,
            style: TOOLTIP_BASE.style,
            pointFormat: '<b>{point.y}</b> OS abertas'
        },
        plotOptions: {
            areaspline: {
                color: THEME.colors.line,
                fillColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                    stops: [[0, 'rgba(74,111,165,0.25)'], [1, 'rgba(74,111,165,0.02)']]
                },
                lineWidth: 2.5,
                cursor: 'pointer',
                marker: { enabled: false, symbol: 'circle', radius: 4, states: { hover: { enabled: true } } },
                states: { hover: { lineWidth: 3 } },
                point: {
                    events: {
                        click: function () {
                            abrirModal('os_abertas');
                        }
                    }
                }
            }
        },
        series: [{ name: 'OS Abertas', animation: { duration: THEME.anim }, data: totais }],
        credits: { enabled: false },
        legend:  { enabled: false },
        accessibility: { enabled: false }
    });
}

function carregarGraficoPizzaStatusOS(dados) {
    buildPizza(
        'grafico-pizza-os-status',
        'Status das OS',
        'Clique em uma fatia para ver detalhes',
        [
            { name: 'Abertas',     y: Number(dados.os_abertas)     || 0 },
            { name: 'Finalizadas', y: Number(dados.os_finalizadas) || 0 },
            { name: 'Canceladas',  y: Number(dados.os_canceladas)  || 0 }
        ],
        THEME.colors.os,
        'os_status'
    );
}

function carregarOsPorTecnico(dados) {
    var tecnicos = dados.os_por_tecnico || [];
    var nomes    = tecnicos.map(function (t) { return t.nome; });
    var totais   = tecnicos.map(function (t) { return t.total; });

    Highcharts.chart('grafico-os-tecnico', {
        chart: {
            type: 'bar',
            backgroundColor: THEME.bg,
            style: { fontFamily: THEME.font },
            animation: { duration: THEME.anim }
        },
        title:    { text: 'OS por Técnico', style: THEME.title, margin: 16 },
        subtitle: { text: 'Clique em uma barra para ver as OS do técnico', style: THEME.subtitle },
        xAxis: {
            categories: nomes,
            lineColor: THEME.grid,
            tickColor: THEME.grid,
            labels: { style: { fontSize: '11px', color: THEME.label, fontFamily: THEME.font } }
        },
        yAxis: {
            title: { text: null },
            gridLineColor: THEME.grid,
            allowDecimals: false,
            labels: { style: { fontSize: '11px', color: THEME.axis, fontFamily: THEME.font } }
        },
        tooltip: {
            backgroundColor: TOOLTIP_BASE.backgroundColor,
            borderWidth: TOOLTIP_BASE.borderWidth,
            borderRadius: TOOLTIP_BASE.borderRadius,
            shadow: TOOLTIP_BASE.shadow,
            style: TOOLTIP_BASE.style,
            pointFormat: '<b>{point.y}</b> ordens de serviço'
        },
        plotOptions: {
            bar: {
                borderWidth: 0,
                borderRadius: 4,
                colorByPoint: true,
                colors: THEME.colors.bar,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '11px', fontWeight: '700', fontFamily: THEME.font, color: THEME.label, textOutline: 'none' }
                },
                states: { hover: { brightness: 0.08 } },
                point: {
                    events: {
                        click: function () {
                            abrirModal('os_tecnico', this.category);
                        }
                    }
                }
            }
        },
        series: [{ name: 'OS', animation: { duration: THEME.anim }, data: totais }],
        credits: { enabled: false },
        legend:  { enabled: false },
        accessibility: { enabled: false }
    });
}

function carregarEstoqueMensal(dados) {
    var meses    = (dados.estoque_mensal || []).map(function (d) { return d.mes; });
    var entradas = (dados.estoque_mensal || []).map(function (d) { return d.entradas; });
    var saidas   = (dados.estoque_mensal || []).map(function (d) { return d.saidas; });

    Highcharts.chart('grafico-estoque-mensal', {
        chart: {
            type: 'column',
            backgroundColor: THEME.bg,
            style: { fontFamily: THEME.font },
            animation: { duration: THEME.anim }
        },
        title:    { text: 'Movimentação de Estoque', style: THEME.title, margin: 16 },
        subtitle: { text: 'Entradas e saídas nos últimos 6 meses', style: THEME.subtitle },
        xAxis: {
            categories: meses,
            lineColor: THEME.grid,
            tickColor: THEME.grid,
            labels: { style: { fontSize: '11px', color: THEME.label, fontFamily: THEME.font } }
        },
        yAxis: {
            title: { text: null },
            gridLineColor: THEME.grid,
            allowDecimals: false,
            labels: { style: { fontSize: '11px', color: THEME.axis, fontFamily: THEME.font } }
        },
        tooltip: {
            backgroundColor: TOOLTIP_BASE.backgroundColor,
            borderWidth: TOOLTIP_BASE.borderWidth,
            borderRadius: TOOLTIP_BASE.borderRadius,
            shadow: TOOLTIP_BASE.shadow,
            style: TOOLTIP_BASE.style,
            shared: true,
            pointFormat: '<span style="color:{series.color}">●</span> {series.name}: <b>{point.y}</b><br>'
        },
        plotOptions: {
            column: {
                borderWidth: 0,
                borderRadius: 3,
                groupPadding: 0.12,
                dataLabels: { enabled: false },
                states: { hover: { brightness: 0.08 } }
            }
        },
        legend: {
            enabled: true,
            align: 'right',
            verticalAlign: 'top',
            itemStyle: { fontSize: '11px', fontWeight: '600', color: THEME.label, fontFamily: THEME.font }
        },
        series: [
            { name: 'Entradas', color: THEME.colors.entrada, animation: { duration: THEME.anim },       data: entradas },
            { name: 'Saídas',   color: THEME.colors.saida,   animation: { duration: THEME.anim + 100 }, data: saidas }
        ],
        credits: { enabled: false },
        accessibility: { enabled: false }
    });
}

function carregarPecasMaisUsadas(dados) {
    var pecas  = dados.pecas_mais_usadas || [];
    var nomes  = pecas.map(function (p) { return p.descricao; });
    var totais = pecas.map(function (p) { return p.total; });

    Highcharts.chart('grafico-pecas-usadas', {
        chart: {
            type: 'bar',
            backgroundColor: THEME.bg,
            style: { fontFamily: THEME.font },
            animation: { duration: THEME.anim }
        },
        title:    { text: 'Peças Mais Utilizadas', style: THEME.title, margin: 16 },
        subtitle: { text: 'Clique em uma barra para ver as OS com essa peça', style: THEME.subtitle },
        xAxis: {
            categories: nomes,
            lineColor: THEME.grid,
            tickColor: THEME.grid,
            labels: { style: { fontSize: '11px', color: THEME.label, fontFamily: THEME.font } }
        },
        yAxis: {
            title: { text: null },
            gridLineColor: THEME.grid,
            allowDecimals: false,
            labels: { style: { fontSize: '11px', color: THEME.axis, fontFamily: THEME.font } }
        },
        tooltip: {
            backgroundColor: TOOLTIP_BASE.backgroundColor,
            borderWidth: TOOLTIP_BASE.borderWidth,
            borderRadius: TOOLTIP_BASE.borderRadius,
            shadow: TOOLTIP_BASE.shadow,
            style: TOOLTIP_BASE.style,
            pointFormat: '<b>{point.y}</b> unidades utilizadas'
        },
        plotOptions: {
            bar: {
                borderWidth: 0,
                borderRadius: 4,
                color: '#3a5a8a',
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '11px', fontWeight: '700', fontFamily: THEME.font, color: THEME.label, textOutline: 'none' }
                },
                states: { hover: { brightness: 0.08 } },
                point: {
                    events: {
                        click: function () {
                            abrirModal('pecas_usadas', this.category);
                        }
                    }
                }
            }
        },
        series: [{ name: 'Peças', animation: { duration: THEME.anim }, data: totais }],
        credits: { enabled: false },
        legend:  { enabled: false },
        accessibility: { enabled: false }
    });
}

function carregarGraficoPizzaStatusProduto(dados) {
    var inativo = Number(dados.produto_inativo) || 0;
    var ativo   = Number(dados.produto_ativo)   || 0;
    if (ativo === 0 && inativo === 0) return;
    buildPizza(
        'grafico-pizza-status-produto',
        'Produtos',
        'Ativos · Inativos',
        [{ name: 'Inativos', y: inativo }, { name: 'Ativos', y: ativo }],
        THEME.colors.status,
        'produtos'
    );
}

function carregarGraficoPizzaStatusPeca(dados) {
    var inativa = Number(dados.peca_inativa) || 0;
    var ativa   = Number(dados.peca_ativa)   || 0;
    if (ativa === 0 && inativa === 0) return;
    buildPizza(
        'grafico-pizza-status-peca',
        'Peças',
        'Ativas · Inativas',
        [{ name: 'Inativas', y: inativa }, { name: 'Ativas', y: ativa }],
        THEME.colors.status,
        'pecas'
    );
}

function carregarGraficoColunas(dados) {
    var mapTipo  = ['usuarios', 'clientes', 'produtos', 'pecas', 'os'];
    var categorias = ['Usuários', 'Clientes', 'Produtos', 'Peças', 'Ordens de Serviço'];
    var valores = [
        Number(dados.usuarios)       || 0,
        Number(dados.clientes)       || 0,
        Number(dados.produtos)       || 0,
        Number(dados.pecas)          || 0,
        Number(dados.ordens_servico) || 0
    ];

    Highcharts.chart('grafico-colunas', {
        chart: {
            type: 'column',
            backgroundColor: THEME.bg,
            style: { fontFamily: THEME.font },
            animation: { duration: THEME.anim }
        },
        title:    { text: 'Resumo Geral de Cadastros', style: THEME.title, margin: 16 },
        subtitle: { text: 'Clique em uma coluna para ver os registros', style: THEME.subtitle },
        xAxis: {
            categories: categorias,
            lineColor: THEME.grid,
            tickColor: THEME.grid,
            labels: { style: { fontSize: '12px', color: THEME.label, fontFamily: THEME.font } }
        },
        yAxis: {
            title: { text: null },
            gridLineColor: THEME.grid,
            allowDecimals: false,
            labels: { style: { fontSize: '11px', color: THEME.axis, fontFamily: THEME.font } }
        },
        tooltip: {
            backgroundColor: TOOLTIP_BASE.backgroundColor,
            borderWidth: TOOLTIP_BASE.borderWidth,
            borderRadius: TOOLTIP_BASE.borderRadius,
            shadow: TOOLTIP_BASE.shadow,
            style: TOOLTIP_BASE.style,
            pointFormat: '<b>{point.y}</b> registros'
        },
        plotOptions: {
            column: {
                borderWidth: 0,
                borderRadius: 5,
                colorByPoint: true,
                colors: THEME.colors.bar,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '12px', fontWeight: '700', fontFamily: THEME.font, color: THEME.label, textOutline: 'none' }
                },
                states: { hover: { brightness: 0.08 } },
                point: {
                    events: {
                        click: function () {
                            abrirModal(mapTipo[this.index]);
                        }
                    }
                }
            }
        },
        series: [{ name: 'Registros', animation: { duration: THEME.anim }, data: valores }],
        credits: { enabled: false },
        legend:  { enabled: false },
        accessibility: { enabled: false }
    });
}
