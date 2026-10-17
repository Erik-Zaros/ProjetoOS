$(document).ready(function () {
    let editandoPeca = null;

    function carregarPecas() {
        MiniDataTable.destroy('#pecasTable');
        $.ajax({
            url: '../public/peca/listar.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#pecasTable tbody').empty();
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(function (peca) {
                        var ativo = peca.ativo == 't' || peca.ativo === true
                            ? '<i class="bi bi-check-circle-fill text-success"></i>'
                            : '<i class="bi bi-x-circle-fill text-danger"></i>';

                        $('#pecasTable tbody').append(`
                            <tr data-codigo="${peca.codigo}">
                                <td class='text-center'>${peca.codigo}</td>
                                <td class='text-center'>${peca.descricao}</td>
                                <td class='text-center'>${ativo}</td>
                                <td class="coluna-acoes text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-outline-warning btn-sm btn-acoes dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Ações">
                                            <i class="bi bi-list"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a href="javascript:void(0)" class="dropdown-item editar-peca" data-codigo="${peca.codigo}">
                                                    <i class="bi bi-pencil-square text-warning me-1"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" class="dropdown-item excluir-peca" data-peca="${peca.peca}">
                                                    <i class="bi bi-trash3-fill text-danger me-1"></i> Excluir
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" class="dropdown-item btn-log-auditor" data-id="${peca.peca}" data-tabela="tbl_peca">
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
                new MiniDataTable('#pecasTable');
            },
            error: function () {
                Alerta.error('Erro ao carregar as peças.');
            }
        });
    }

    carregarPecas();

    $('#pecaForm').on('submit', function (e) {
        e.preventDefault();

        var codigo = $('#codigo').val();
        var descricao = $('#descricao').val();
        var ativo = $('#ativo').is(':checked') ? 't' : 'f';

        if (editandoPeca) {
            $.ajax({
                url: '../public/peca/editar.php',
                method: 'POST',
                data: {
                    peca: editandoPeca,
                    codigo: codigo,
                    descricao: descricao,
                    ativo: ativo
                },
                success: function (response) {
                    let res = typeof response === 'string' ? JSON.parse(response) : response;
                    if (res.status === 'error') {
                        Alerta.error(res.message);
                    } else {
                        Alerta.success(res.message || 'Peça atualizada com sucesso.');
                        $('#pecaForm')[0].reset();
                        editandoPeca = null;
                        carregarPecas();
                    }
                },
                error: function () {
                    Alerta.error('Erro ao editar peça.');
                }
            });
        } else {
            $.ajax({
                url: '../public/peca/cadastrar.php',
                method: 'POST',
                data: {
                    codigo: codigo,
                    descricao: descricao,
                    ativo: ativo
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'error') {
                        Alerta.error(response.message);
                    } else {
                        Alerta.success(response.message || 'Peça cadastrada com sucesso.');
                        $('#pecaForm')[0].reset();
                        carregarPecas();
                    }
                },
                error: function () {
                    Alerta.error('Erro ao cadastrar peça.');
                }
            });
        }
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
                $('#ativo').prop('checked', peca.ativo == 't' || peca.ativo === true);
                editandoPeca = peca.peca;
            },
            error: function () {
                Alerta.error('Erro ao buscar peça.');
            }
        });
    });

    $(document).on('click', '.excluir-peca', function () {
        let peca = $(this).data('peca');

        Alerta.confirm({
            title: 'Excluir peça?',
            text: 'Essa ação não pode ser desfeita.',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../public/peca/excluir.php',
                    method: 'POST',
                    data: { peca: peca },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Alerta.success(response.message || 'Peça excluída com sucesso.');
                            carregarPecas();
                        } else {
                            Alerta.error(response.message || 'Erro ao excluir peça.');
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
