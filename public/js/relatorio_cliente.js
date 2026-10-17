$(document).ready(function () {

    if (window.flatpickr) {
        flatpickr('#dataInicio', {
            locale: 'pt',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            allowInput: true
        });
        flatpickr('#dataFim', {
            locale: 'pt',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd/m/Y',
            allowInput: true
        });
    }

    $("#pesquisaForm").on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        atualizarUrl(formData);
        carregarClientes(formData);
    });

    $('#limparPesquisa').on('click', function () {
        $('#pesquisaForm')[0].reset();
        const di = document.getElementById('dataInicio');
        if (di && di._flatpickr) di._flatpickr.clear();
        const df = document.getElementById('dataFim');
        if (df && df._flatpickr) df._flatpickr.clear();
        
        history.pushState(null, '', window.location.pathname);
        MiniDataTable.destroy('#clienteTable');
        $('#clienteTable tbody').empty();
        $("#btnCSV").hide();
    });

    restauraUrl();
});

function atualizarUrl(formData) {
    if (window.history && window.history.pushState) {
        const url = window.location.pathname + '?' + formData;
        window.history.pushState(null, '', url);
    }
}

function renderizarTabela(data) {
    MiniDataTable.destroy('#clienteTable');
    $('#clienteTable tbody').empty();

    if (Array.isArray(data)) {
        $("#btnCSV").show();
        data.forEach(function (cliente) {
            $('#clienteTable tbody').append(`
                <tr>
                    <td>${cliente.nome || ''}</td>
                    <td class='text-center'>${cliente.cpf || ''}</td>
                    <td class='text-center'>${cliente.cep || ''}</td>
                    <td>${cliente.endereco || ''}</td>
                    <td>${cliente.bairro || ''}</td>
                    <td>${cliente.cidade || ''}</td>
                    <td class='text-center'>${cliente.estado || ''}</td>
                    <td class='text-center'>${cliente.data_cadastro || ''}</td>
                    <td class='text-center'>${cliente.oss || ''}</td>
                </tr>
            `);
        });
    }

    new MiniDataTable('#clienteTable');
}

function carregarClientes(formData) {
    MiniDataTable.destroy('#clienteTable');
    $.ajax({
        url: '../public/relatorio_cliente/pesquisar.php',
        method: 'POST',
        data: formData,
        dataType: 'json',
        success: function (data) {
            if (data.status === 'alert') {
                Alerta.warning(data.message, 'Atenção');
                return;
            }
            renderizarTabela(data);
        },
        error: function () {
            Alerta.error('Erro ao consultar os dados.');
        }
    });
}

function restauraUrl() {
    const params = new URLSearchParams(window.location.search);
    if (!params.toString()) return;

    const dataInicio = params.get('dataInicio') || '';
    const dataFim = params.get('dataFim') || '';
    const cpf = params.get('cpf') || '';
    const nome = params.get('nome') || '';
    const clienteOs = params.get('cliente_os');

    const di = document.getElementById('dataInicio');
    if (di && di._flatpickr && dataInicio) {
        di._flatpickr.setDate(dataInicio, true);
    } else {
        $("#dataInicio").val(dataInicio);
    }

    const df = document.getElementById('dataFim');
    if (df && df._flatpickr && dataFim) {
        df._flatpickr.setDate(dataFim, true);
    } else {
        $("#dataFim").val(dataFim);
    }

    $("#cpf").val(cpf);
    $("#nome").val(nome);
    if (clienteOs === 'on' || clienteOs === '1' || clienteOs === 'true') {
        $("#cliente_os").prop('checked', true);
    }

    if (dataInicio && dataFim) {
        carregarClientes($('#pesquisaForm').serialize());
    }
}