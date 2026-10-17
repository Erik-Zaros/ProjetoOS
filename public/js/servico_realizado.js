$(document).ready(function () {
    let editandoId = null;

    function carregarServicoRealizado() {
        let usa_modulo_estoque = $("#usa_modulo_estoque").val();
        MiniDataTable.destroy('#servicoRealizadoTable');

        $.ajax({
            url: '../public/servico_realizado/listar.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#servicoRealizadoTable tbody').empty();
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(function (servico_realizado) {
                        var ativo = servico_realizado.ativo == 't' || servico_realizado.ativo === true
                            ? '<i class="bi bi-check-circle-fill text-success"></i>'
                            : '<i class="bi bi-x-circle-fill text-danger"></i>';
                        var usa_estoque = servico_realizado.usa_estoque == 't' || servico_realizado.usa_estoque === true
                            ? '<i class="bi bi-check-circle-fill text-success"></i>'
                            : '<i class="bi bi-x-circle-fill text-danger"></i>';

                        let coluna_estoque = "";
                        if (usa_modulo_estoque == "true") {
                            coluna_estoque = `<td class='text-center'>${usa_estoque}</td>`;
                        }

                        $('#servicoRealizadoTable tbody').append(`
                            <tr data-descricao="${servico_realizado.descricao}">
                                <td class='text-center'>${servico_realizado.descricao}</td>
                                <td class='text-center'>${ativo}</td>
                                ${coluna_estoque}
                                <td class="coluna-acoes text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-outline-warning btn-sm btn-acoes dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Ações">
                                            <i class="bi bi-list"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a href="javascript:void(0)" class="dropdown-item editar-servico_realizado" data-servico="${servico_realizado.servico_realizado}">
                                                    <i class="bi bi-pencil-square text-warning me-1"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" class="dropdown-item excluir-servico_realizado" data-servico="${servico_realizado.servico_realizado}">
                                                    <i class="bi bi-trash3-fill text-danger me-1"></i> Excluir
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" class="dropdown-item btn-log-auditor" data-id="${servico_realizado.servico_realizado}" data-tabela="tbl_servico_realizado">
                                                    <i class="bi bi-clock-history text-info me-1"></i> Ver Log
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        `);
                    });
                }
                new MiniDataTable('#servicoRealizadoTable');
            },
            error: function () {
                Alerta.error('Erro ao listar os Serviços Realizados.');
            }
        });
    }

    carregarServicoRealizado();

    $('#servicoRealizadoForm').on('submit', function (e) {
        e.preventDefault();

        var descricao = $('#descricao').val();
        var ativo = $('#ativo').is(':checked') ? 't' : 'f';
        var usa_estoque = $('#usa_estoque').is(':checked') ? 't' : 'f';

        if (editandoId) {
            $.ajax({
                url: '../public/servico_realizado/editar.php',
                method: 'POST',
                data: {
                    servico_realizado: editandoId,
                    descricao: descricao,
                    ativo: ativo,
                    usa_estoque: usa_estoque
                },
                success: function (response) {
                    let res = typeof response === 'string' ? JSON.parse(response) : response;
                    if (res.status === 'error') {
                        Alerta.error(res.message);
                    } else {
                        Alerta.success(res.message || 'Serviço atualizado com sucesso.');
                        $('#servicoRealizadoForm')[0].reset();
                        editandoId = null;
                        carregarServicoRealizado();
                    }
                },
                error: function () {
                    Alerta.error('Erro ao editar Serviço Realizado.');
                }
            });
        } else {
            $.ajax({
                url: '../public/servico_realizado/cadastrar.php',
                method: 'POST',
                data: {
                    descricao: descricao,
                    ativo: ativo,
                    usa_estoque: usa_estoque
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'error') {
                        Alerta.error(response.message);
                    } else {
                        Alerta.success(response.message || 'Serviço Realizado cadastrado com sucesso.');
                        $('#servicoRealizadoForm')[0].reset();
                        carregarServicoRealizado();
                    }
                },
                error: function () {
                    Alerta.error('Erro ao cadastrar Serviço Realizado.');
                }
            });
        }
    });

    $(document).on('click', '.editar-servico_realizado', function () {
        var servico = $(this).data('servico');

        $.ajax({
            url: '../public/servico_realizado/buscar.php',
            method: 'GET',
            data: { servico: servico },
            dataType: 'json',
            success: function (res) {
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $('#descricao').val(res.descricao);
                $('#ativo').prop('checked', res.ativo == 't' || res.ativo === true);
                $('#usa_estoque').prop('checked', res.usa_estoque == 't' || res.usa_estoque === true);
                editandoId = res.servico_realizado;
            },
            error: function () {
                Alerta.error('Erro ao buscar dados do serviço.');
            }
        });
    });

    $(document).on('click', '.excluir-servico_realizado', function () {
        let servico_realizado = $(this).data('servico');

        Alerta.confirm({
            title: 'Excluir Serviço Realizado?',
            text: 'Essa ação não pode ser desfeita.',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../public/servico_realizado/excluir.php',
                    method: 'POST',
                    data: { servico_realizado: servico_realizado },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Alerta.success(response.message || 'Serviço excluído com sucesso.');
                            carregarServicoRealizado();
                        } else {
                            Alerta.error(response.message || 'Erro ao excluir serviço.');
                        }
                    },
                    error: function () {
                        Alerta.error('Erro ao comunicar com o servidor.');
                    }
                });
            }
        });
    });
});
