$(document).ready(function () {
    function carregarTipoAtendimento() {
        if ($.fn.DataTable.isDataTable('#tipoAtendimentoTable')) {
            $('#tipoAtendimentoTable').DataTable().destroy();
        }
        $.ajax({
            url: '../public/tipo_atendimento/listar.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#tipoAtendimentoTable tbody').empty();
                if (data.length > 0) {
                    data.forEach(function (tipo_atendimento) {
                        var ativo = tipo_atendimento.ativo == 't' ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>';
                        $('#tipoAtendimentoTable tbody').append(`
                            <tr data-tipo_atendimento="${tipo_atendimento.tipo_atendimento}">
                                <td>${tipo_atendimento.codigo}</td>
                                <td>${tipo_atendimento.descricao}</td>
                                <td>${ativo}</td>
                                <td>
                                    <button class='btn btn-warning btn-sm editar-tipo_atendimento' data-tipo_atendimento='${tipo_atendimento.tipo_atendimento}'><i class="bi bi-pencil-square"></i> Editar</button>
                                    <button class='btn btn-info btn-sm btn-log-auditor' data-id='${tipo_atendimento.tipo_atendimento}'data-tabela='tbl_tipo_atendimento'><i class="bi bi-clock-history"></i> Ver Log</button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#tipoAtendimentoTable tbody').append(`
                        <tr>
                            <td colspan="9" class="text-center">NENHUM TIPO DE ATENDIMENTO CADASTRADO</td>
                        </tr>
                    `);
                }
                if (data.length > 0) {
                    $('#tipoAtendimentoTable').DataTable({
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
                    text: 'Erro ao carregar os tipos de atendimento.',
                }); 
            }
        });
    }

    carregarTipoAtendimento();

    $('#tipoAtendimentoForm').on('submit', function (e) {
        e.preventDefault();

        var formData = {
            codigo: $('#codigo').val(),
            descricao: $('#descricao').val(),
            ativo: $('#ativo').is(':checked') ? 't' : 'f'
        };

        $.ajax({
            url: '../public/tipo_atendimento/cadastrar.php',
            method: 'POST',
            data: formData,
            success: function (response) {

                if (response.status === "alert") {
                  Swal.fire({
                    icon: "warning",
                    title: "Atenção",
                    text: response.message,
                  });
                }else if (response.status === 'error') {
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
                        $('#tipoAtendimentoForm')[0].reset();
                        carregarTipoAtendimento();
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao cadastrar tipo de atendimento.',
                });
            }
        });
    });

    $(document).on('click', '.editar-tipo_atendimento', function () {
        var tipo_atendimento = $(this).data('tipo_atendimento');

        $.ajax({
            url: '../public/tipo_atendimento/buscar.php',
            method: 'GET',
            data: { tipo_atendimento: tipo_atendimento },
            dataType: 'json',
            success: function (tipo_atendimento) {
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $('#codigo').val(tipo_atendimento.codigo);
                $('#descricao').val(tipo_atendimento.descricao);
                $('#ativo').prop('checked', tipo_atendimento.ativo == 't');

                var tipo_atendimento = tipo_atendimento.tipo_atendimento;

                $('#tipoAtendimentoForm').off('submit').on('submit', function (e) {
                    e.preventDefault();
                    $.ajax({
                        url: '../public/tipo_atendimento/editar.php',
                        method: 'POST',
                        data: {
                            tipo_atendimento: tipo_atendimento,
                            codigo: $('#codigo').val(),
                            descricao: $('#descricao').val(),
                            ativo: $('#ativo').is(':checked') ? 't' : 'f'
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
                                    $('#tipoAtendimentoForm')[0].reset();
                                    carregarTipoAtendimento();
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: 'Erro ao editar tipo de atendimento.',
                            });
                        }
                    });
                });
            }
        });
    });

    const urlParams = new URLSearchParams(window.location.search);
    const alerta = urlParams.get('alerta');

    if (alerta == 'true') {
        Swal.fire({
            icon: 'warning',
            title: 'Atenção',
            text: 'Nenhum tipo de atendimento cadastrado!',
            confirmButtonText: 'OK',
            confirmButtonColor: '#007bff'
        });
    }
});
