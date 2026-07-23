const STORAGE_KEY = 'relatorioClientes';

$(document).ready(function () {

    $("#btnCSV").on("click", function () {
        $("#formCSV input[name=dataInicio]").val($("#dataInicio").val());
        $("#formCSV input[name=dataFim]").val($("#dataFim").val());
        $("#formCSV input[name=cpf]").val($("#cpf").val());
        $("#formCSV input[name=nome]").val($("#nome").val());
        $("#formCSV").submit();
    });

    $("#pesquisaForm").on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        carregarClientes(formData);
    });

    $('#limparPesquisa').on('click', function () {
        $('#pesquisaForm')[0].reset();
        sessionStorage.removeItem(STORAGE_KEY);
        if ($.fn.DataTable.isDataTable('#clienteTable')) {
            $('#clienteTable').DataTable().destroy();
            $('#clienteTable tbody').empty();
        }
        $("#btnCSV").hide();
    });

    $("#dataInicio, #dataFim").datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true
    });

    restauraSession();
});

function salvaSession(formData, dados) {
    const estado = {
        filtros: {
            dataInicio: $("#dataInicio").val(),
            dataFim: $("#dataFim").val(),
            cpf: $("#cpf").val(),
            nome: $("#nome").val()
        },
        resultado: dados
    };
    sessionStorage.setItem(STORAGE_KEY, JSON.stringify(estado));
}

function renderizarTabela(data) {
    if ($.fn.DataTable.isDataTable('#clienteTable')) {
        $('#clienteTable').DataTable().destroy();
    }

    $("#btnCSV").show();
    $('#clienteTable tbody').empty();

    data.forEach(function (cliente) {
        $('#clienteTable tbody').append(`
            <tr>
                <td>${cliente.nome || ''}</td>
                <td>${cliente.cpf || ''}</td>
                <td>${cliente.cep || ''}</td>
                <td>${cliente.endereco || ''}</td>
                <td>${cliente.bairro || ''}</td>
                <td>${cliente.cidade || ''}</td>
                <td>${cliente.estado || ''}</td>
                <td>${cliente.data_cadastro || ''}</td>
                <td>${cliente.oss || ''}</td>
            </tr>
        `);
    });

    $('#clienteTable').DataTable({
        destroy: true,
        responsive: true,
        scrollX: false,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
        }
    });
}

function carregarClientes(formData) {
    if ($.fn.DataTable.isDataTable('#clienteTable')) {
        $('#clienteTable').DataTable().destroy();
    }
    $.ajax({
        url: '../public/relatorio_cliente/pesquisar.php',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function (data) {
            if (data.status === 'alert') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: data.message
                });
                return;
            }
            renderizarTabela(data);
            salvaSession(formData, data);
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Erro ao consultar os dados'
            });
        }
    });
}

function restauraSession() {
    const salvo = sessionStorage.getItem(STORAGE_KEY);
    if (!salvo) return;

    try {
        const estado = JSON.parse(salvo);

        $("#dataInicio").val(estado.filtros.dataInicio);
        $("#dataFim").val(estado.filtros.dataFim);
        $("#cpf").val(estado.filtros.cpf);
        $("#nome").val(estado.filtros.nome);

        if (Array.isArray(estado.resultado) && estado.resultado.length > 0) {
            renderizarTabela(estado.resultado);
        }
    } catch (e) {
        console.error('Erro ao restaurar estado do relatório:', e);
        sessionStorage.removeItem(STORAGE_KEY);
    }
}