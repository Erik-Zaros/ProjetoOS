function renderOsRow(os) {
    var canceladaBadge = os.cancelada ? '<span class="badge bg-danger fs-7 px-2 py-1">Cancelada</span>' : '';
    var finalizadaBadge = os.finalizada ? '<span class="badge bg-success fs-7 px-2 py-1">Finalizada</span>' : '';
    var isAberta = !os.finalizada && !os.cancelada;

    return `
        <tr>
            <td class='text-center'><a href="os_press?os=${os.os}" class="link fw-semibold">${os.os}</a></td>
            <td class='text-center'>${os.cliente || ''}</td>
            <td class='text-center'>${os.cpf || ''}</td>
            <td class='text-center'>${os.produto || ''}</td>
            <td class='text-center'>${os.data_abertura || ''}</td>
            <td class="coluna-acoes text-center">
                <div class="d-flex align-items-center justify-content-center gap-1">
                    ${canceladaBadge}
                    ${finalizadaBadge}
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-warning btn-sm btn-acoes dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Ações">
                            <i class="bi bi-list"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="os_press?os=${os.os}" class="dropdown-item" target="_blank">
                                    <i class="bi bi-eye text-primary me-1"></i> Visualizar
                                </a>
                            </li>
                            ${isAberta ? `
                            <li>
                                <a href="cadastra_os?os=${os.os}" class="dropdown-item">
                                    <i class="bi bi-pencil-square text-warning me-1"></i> Editar
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" class="dropdown-item finalizar-os text-success" data-os="${os.os}">
                                    <i class="bi bi-check-all me-1"></i> Finalizar
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)" class="dropdown-item cancelar-os text-danger" data-os="${os.os}">
                                    <i class="bi bi-x-circle me-1"></i> Cancelar
                                </a>
                            </li>
                            ` : ''}
                        </ul>
                    </div>
                </div>
            </td>
        </tr>
    `;
}

function carregarOs() {
    MiniDataTable.destroy('#osTable');
    $.ajax({
        url: '../public/consulta_os/listar.php',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            $('#osTable tbody').empty();
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(function (os) {
                    $('#osTable tbody').append(renderOsRow(os));
                });
            }
            new MiniDataTable('#osTable');
        },
        error: function () {
            Alerta.error('Erro ao listar as ordens de serviço.');
        }
    });
}

function filtrarOs(formData) {
    MiniDataTable.destroy('#osTable');
    $.ajax({
        url: '../public/consulta_os/filtrar.php',
        method: 'GET',
        data: formData,
        dataType: 'json',
        success: function (data) {
            $('#osTable tbody').empty();
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(function (os) {
                    $('#osTable tbody').append(renderOsRow(os));
                });
            }
            new MiniDataTable('#osTable');
        },
        error: function () {
            Alerta.error('Erro ao aplicar os filtros!');
        }
    });
}

function atualizarUrl(formData) {
    if (window.history && window.history.pushState) {
        const url = window.location.pathname + '?' + formData;
        window.history.pushState(null, '', url);
    }
}

function restauraUrl() {
    const params = new URLSearchParams(window.location.search);
    if (!params.toString()) {
        carregarOs();
        return;
    }

    const os = params.get('os') || '';
    const nomeCliente = params.get('nomeCliente') || '';
    const dataInicio = params.get('dataInicio') || '';
    const dataFim = params.get('dataFim') || '';
    const osFinalizada = params.get('os_finalizada');
    const osCancelada = params.get('os_cancelada');

    $('#os').val(os);
    $('#nomeCliente').val(nomeCliente);

    const di = document.getElementById('dataInicio');
    if (di && di._flatpickr && dataInicio) {
        di._flatpickr.setDate(dataInicio, true);
    } else {
        $('#dataInicio').val(dataInicio);
    }

    const df = document.getElementById('dataFim');
    if (df && df._flatpickr && dataFim) {
        df._flatpickr.setDate(dataFim, true);
    } else {
        $('#dataFim').val(dataFim);
    }

    if (osFinalizada === 'on' || osFinalizada === '1' || osFinalizada === 'true') {
        $('#os_finalizada').prop('checked', true);
    }
    if (osCancelada === 'on' || osCancelada === '1' || osCancelada === 'true') {
        $('#os_cancelada').prop('checked', true);
    }

    filtrarOs($('#filtroForm').serialize());
}

$(document).ready(function () {
    if (window.flatpickr) {
        const diEl = document.getElementById('dataInicio');
        if (diEl) {
            diEl.type = 'text';
            diEl.placeholder = 'dd/mm/aaaa';
        }
        const dfEl = document.getElementById('dataFim');
        if (dfEl) {
            dfEl.type = 'text';
            dfEl.placeholder = 'dd/mm/aaaa';
        }

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

    restauraUrl();

    $('#filtroForm').on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        atualizarUrl(formData);
        filtrarOs(formData);
    });

    $('#limparFiltros').on('click', function () {
        $('#filtroForm')[0].reset();
        const di = document.getElementById('dataInicio');
        if (di && di._flatpickr) di._flatpickr.clear();
        const df = document.getElementById('dataFim');
        if (df && df._flatpickr) df._flatpickr.clear();

        history.pushState(null, '', window.location.pathname);
        carregarOs();
    });

    $(document).on('click', '.finalizar-os', function () {
        var os = $(this).data('os');

        Alerta.confirm({
            title: `Finalizar OS ${os}?`,
            text: 'Essa ação não pode ser desfeita.',
            confirmButtonText: 'Sim, finalizar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../public/consulta_os/finalizar.php',
                    method: 'POST',
                    data: { os: os },
                    dataType: 'json',
                    success: function (response) {
                        Alerta.success(response.message || 'Ordem de serviço finalizada com sucesso.');
                        if (window.location.search) {
                            filtrarOs($('#filtroForm').serialize());
                        } else {
                            carregarOs();
                        }
                    },
                    error: function () {
                        Alerta.error('Erro ao finalizar a OS!');
                    }
                });
            }
        });
    });

    $(document).on('click', '.cancelar-os', function () {
        var os = $(this).data('os');

        Alerta.confirm({
            title: `Cancelar OS ${os}?`,
            text: 'Essa ação não pode ser desfeita.',
            confirmButtonText: 'Sim, cancelar',
            cancelButtonText: 'Voltar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../public/consulta_os/cancelar.php',
                    method: 'POST',
                    data: { os: os },
                    dataType: 'json',
                    success: function (response) {
                        Alerta.success(response.message || 'Ordem de serviço cancelada com sucesso.');
                        if (window.location.search) {
                            filtrarOs($('#filtroForm').serialize());
                        } else {
                            carregarOs();
                        }
                    },
                    error: function () {
                        Alerta.error('Erro ao cancelar a OS!');
                    }
                });
            }
        });
    });
});
