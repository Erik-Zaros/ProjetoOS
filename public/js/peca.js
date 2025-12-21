$(document).ready(function () {
    function carregarPecas() {
        if ($.fn.DataTable.isDataTable('#pecasTable')) {
            $('#pecasTable').DataTable().destroy();
        }
        $.ajax({
            url: '../public/peca/listar.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#pecasTable tbody').empty();
                if (data.length > 0) {
                    data.forEach(function (peca) {
                        var ativo = peca.ativo == 't' ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>';
                        $('#pecasTable tbody').append(`
                            <tr data-codigo="${peca.codigo}">
                                <td>${peca.codigo}</td>
                                <td>${peca.descricao}</td>
                                <td>${ativo}</td>
                                <td>
                                    <button class='btn btn-warning btn-sm editar-peca' data-codigo='${peca.codigo}'>Editar</button>
                                    <button class='btn btn-danger btn-sm excluir-peca' data-peca='${peca.peca}'>Excluir</button>
                                    <button class='btn btn-info btn-sm btn-log-auditor' data-id='${peca.peca}'data-tabela='tbl_peca'>Ver Log</button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#pecasTable tbody').append(`
                        <tr>
                            <td colspan="9" class="text-center">NENHUMA PEÇA CADASTRADO</td>
                        </tr>
                    `);
                }
                if (data.length > 0) {
                    $('#pecasTable').DataTable({
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
                    text: 'Erro ao carregar as peças.',
                });
            }
        });
    }

    carregarPecas();

    $('#pecaForm').on('submit', function (e) {
        e.preventDefault();

        var formData = {
            codigo: $('#codigo').val(),
            descricao: $('#descricao').val(),
            ativo: $('#ativo').is(':checked') ? 't' : 'f'
        };

        $.ajax({
            url: '../public/peca/cadastrar.php',
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
                        $('#pecaForm')[0].reset();
                        carregarPecas();
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao cadastrar peça.',
                });
            }
        });
    });

    $(document).on('click', '.editar-peca', function () {
        var codigo = $(this).data('codigo');

        $.ajax({
            url: '../public/peca/buscar.php',
            method: 'GET',
            data: { codigo: codigo },
            dataType: 'json',
            success: function (peca) {
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $('#codigo').val(peca.codigo);
                $('#descricao').val(peca.descricao);
                $('#ativo').prop('checked', peca.ativo == 't');

                var peca = peca.peca;

                $('#pecaForm').off('submit').on('submit', function (e) {
                    e.preventDefault();
                    $.ajax({
                        url: '../public/peca/editar.php',
                        method: 'POST',
                        data: {
                            peca: peca,
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
                                    $('#pecaForm')[0].reset();
                                    carregarPecas();
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: 'Erro ao editar peça.',
                            });
                        }
                    });
                });
            }
        });
    });

    $(document).on('click', '.excluir-peca', function () {
        let peca = $(this).data('peca');

        Swal.fire({
            title: `Deseja excluir a peça?`,
            text: 'Essa ação não pode ser desfeita.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../public/peca/excluir.php',
                    method: 'POST',
                    data: { peca: peca },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: response.message,
                            }).then(() => {
                                carregarPecas();
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
            text: 'Nenhuma peça cadastrado!',
            confirmButtonText: 'OK',
            confirmButtonColor: '#007bff'
        });
    }
});
