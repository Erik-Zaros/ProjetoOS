$(document).ready(function () {
    function carregarServicoRealizado() {
        if ($.fn.DataTable.isDataTable('#servicoRealizadoTable')) {
            $('#servicoRealizadoTable').DataTable().destroy();
        }
        $.ajax({
            url: '../public/servico_realizado/listar.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#servicoRealizadoTable tbody').empty();
                if (data.length > 0) {
                    data.forEach(function (servico_realizado) {
                        var ativo = servico_realizado.ativo == 't' ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>';
                        var usa_estoque = servico_realizado.usa_estoque == 't' ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>';
                        $('#servicoRealizadoTable tbody').append(`
                            <tr data-descricao="${servico_realizado.descricao}">
                                <td>${servico_realizado.descricao}</td>
                                <td>${ativo}</td>
                                <td>${usa_estoque}</td>
                                <td>
                                    <button class='btn btn-warning btn-sm editar-servico_realizado' data-descricao='${servico_realizado.descricao}'>Editar</button>
                                    <button class='btn btn-danger btn-sm excluir-servico_realizado' data-servico='${servico_realizado.servico_realizado}'>Excluir</button>
                                    <button class='btn btn-info btn-sm btn-log-auditor' data-id='${servico_realizado.servico_realizado}'data-tabela='tbl_servico_realizado'>Ver Log</button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#servicoRealizadoTable tbody').append(`
                        <tr>
                            <td colspan="9" class="text-center">NENHUM SERVIÇO REALIZADO CADASTRADO</td>
                        </tr>
                    `);
                }
                if (data.length > 0) {
                    $('#servicoRealizadoTable').DataTable({
                        responsive: true,
                        scrollX: true,
                        autoWidth: false,
                        language: {
                            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                        }
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao listar as Serviço Realizado.',
                });
            }
        });
    }

    carregarServicoRealizado();

    $('#servicoRealizadoForm').on('submit', function (e) {
        e.preventDefault();

        var formData = {
            descricao: $('#descricao').val(),
            ativo: $('#ativo').is(':checked') ? 't' : 'f',
            usa_estoque: $('#usa_estoque').is(':checked') ? 't' : 'f'
        };

        $.ajax({
            url: '../public/servico_realizado/cadastrar.php',
            method: 'POST',
            data: formData,
            success: function (response) {

                if (response.status === 'error') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: response.message,
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: response.message,
                    }).then(() => {
                        $('#servicoRealizadoForm')[0].reset();
                        carregarServicoRealizado();
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao cadastrar Serviço Realizado.',
                });
            }
        });
    });

    $(document).on('click', '.editar-servico_realizado', function () {
        var descricao = $(this).data('descricao');

        $.ajax({
            url: '../public/servico_realizado/buscar.php',
            method: 'GET',
            data: { descricao: descricao },
            dataType: 'json',
            success: function (servico_realizado) {
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $('#descricao').val(servico_realizado.descricao);
                $('#ativo').prop('checked', servico_realizado.ativo == 't');
                $('#usa_estoque').prop('checked', servico_realizado.usa_estoque == 't');

                var servico_realizado = servico_realizado.servico_realizado;

                $('#servicoRealizadoForm').off('submit').on('submit', function (e) {
                    e.preventDefault();
                    $.ajax({
                        url: '../public/servico_realizado/editar.php',
                        method: 'POST',
                        data: {
                            servico_realizado: servico_realizado,
                            descricao: $('#descricao').val(),
                            ativo: $('#ativo').is(':checked') ? 't' : 'f',
                            usa_estoque: $('#usa_estoque').is(':checked') ? 't' : 'f'
                        },
                        success: function (response) {
                            let res = JSON.parse(response);

                            if (res.status === 'error') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro!',
                                    text: res.message,
                                });
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sucesso!',
                                    text: res.message,
                                }).then(() => {
                                    $('#servicoRealizadoForm')[0].reset();
                                    carregarServicoRealizado();
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: 'Erro ao editar Serviço Realizado.',
                            });
                        }
                    });
                });
            }
        });
    });

    $(document).on('click', '.excluir-servico_realizado', function () {
        let servico_realizado = $(this).data('servico');

        Swal.fire({
            title: `Deseja excluir o Serviço Realizado?`,
            text: 'Essa ação não pode ser desfeita.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../public/servico_realizado/excluir.php',
                    method: 'POST',
                    data: { servico_realizado: servico_realizado },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: response.message,
                            }).then(() => {
                                carregarServicoRealizado();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Atenção!',
                                text: response.message,
                            });
                        }
                    }
                });
            }
        });
    });


    const urlParams = new URLSearchParams(window.location.search);
    const alerta = urlParams.get('alerta');

    if (alerta === 'true') {
        Swal.fire({
            icon: 'warning',
            title: 'Atenção',
            text: 'Nenhum Serviço Realizado cadastrado!',
            confirmButtonText: 'OK',
            confirmButtonColor: '#007bff'
        });
    }
});
