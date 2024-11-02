document.addEventListener("DOMContentLoaded", function () {
    const page = window.location.pathname.split("/").pop();
    const navLinks = document.querySelectorAll(".nav-link");
    navLinks.forEach(link => {
        if (link.getAttribute("href") === page) {
            link.classList.add("active");
        }
    });

    function carregarGraficoPizza(dados) {
        const clientes = Number(dados.clientes) || 0;
        const produtos = Number(dados.produtos) || 0;
        const ordensServico = Number(dados.ordens_servico) || 0;

        Highcharts.chart('grafico-pizza', {
            chart: {
                type: 'pie'
            },
            title: {
                text: 'CADASTRO NO SISTEMA'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.y}</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '{point.name}: {point.y}'
                    }
                }
            },
            series: [{
                name: 'Total',
                colorByPoint: true,
                data: [
                    { name: 'CLIENTES', y: clientes },
                    { name: 'PRODUTOS', y: produtos },
                    { name: 'ORDENS DE SERVIÇO', y: ordensServico }
                ]
            }]
        });
    }

    $.ajax({
        url: '../controller/menu/grafico_pizza.php',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            carregarGraficoPizza(response);
        },
        error: function (error) {
            console.error("Erro ao carregar os dados do gráfico: ", error);
        }
    });
});
