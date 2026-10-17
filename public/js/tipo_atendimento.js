$(document).ready(function () {
    let editandoId = null;

    function carregarTipoAtendimento() {
        MiniDataTable.destroy('#tipoAtendimentoTable');
        $.ajax({
            url: '../public/tipo_atendimento/listar.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#tipoAtendimentoTable tbody').empty();
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(function (tipo_atendimento) {
                        var ativo = tipo_atendimento.ativo == 't' || tipo_atendimento.ativo === true
                            ? '<i class="bi bi-check-circle-fill text-success"></i>'
                            : '<i class="bi bi-x-circle-fill text-danger"></i>';

                        $('#tipoAtendimentoTable tbody').append(`
                            <tr data-tipo_atendimento="${tipo_atendimento.tipo_atendimento}">
                                <td class='text-center'>${tipo_atendimento.codigo}</td>
                                <td class='text-center'>${tipo_atendimento.descricao}</td>
                                <td class='text-center'>${ativo}</td>
                                <td class="coluna-acoes text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-outline-warning btn-sm btn-acoes dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Ações">
                                            <i class="bi bi-list"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a href="javascript:void(0)" class="dropdown-item editar-tipo_atendimento" data-tipo_atendimento="${tipo_atendimento.tipo_atendimento}">
                                                    <i class="bi bi-pencil-square text-warning me-1"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" class="dropdown-item btn-log-auditor" data-id="${tipo_atendimento.tipo_atendimento}" data-tabela="tbl_tipo_atendimento">
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
                new MiniDataTable('#tipoAtendimentoTable');
            },
            error: function () {
                Alerta.error('Erro ao carregar os tipos de atendimento.');
            }
        });
    }

    carregarTipoAtendimento();

    $('#tipoAtendimentoForm').on('submit', function (e) {
        e.preventDefault();

        var codigo = $('#codigo').val();
        var descricao = $('#descricao').val();
        var ativo = $('#ativo').is(':checked') ? 't' : 'f';

        if (editandoId) {
            $.ajax({
                url: '../public/tipo_atendimento/editar.php',
                method: 'POST',
                data: {
                    tipo_atendimento: editandoId,
                    codigo: codigo,
                    descricao: descricao,
                    ativo: ativo
                },
                success: function (response) {
                    let res = typeof response === 'string' ? JSON.parse(response) : response;
                    if (res.status === 'error') {
                        Alerta.error(res.message);
                    } else {
                        Alerta.success(res.message || 'Tipo de atendimento atualizado com sucesso.');
                        $('#tipoAtendimentoForm')[0].reset();
                        editandoId = null;
                        carregarTipoAtendimento();
                    }
                },
                error: function () {
                    Alerta.error('Erro ao editar tipo de atendimento.');
                }
            });
        } else {
            $.ajax({
                url: '../public/tipo_atendimento/cadastrar.php',
                method: 'POST',
                data: {
                    codigo: codigo,
                    descricao: descricao,
                    ativo: ativo
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === "alert") {
                        Alerta.warning(response.message);
                    } else if (response.status === 'error') {
                        Alerta.error(response.message);
                    } else {
                        Alerta.success(response.message || 'Tipo de atendimento cadastrado com sucesso.');
                        $('#tipoAtendimentoForm')[0].reset();
                        carregarTipoAtendimento();
                    }
                },
                error: function () {
                    Alerta.error('Erro ao cadastrar tipo de atendimento.');
                }
            });
        }
    });

    $(document).on('click', '.editar-tipo_atendimento', function () {
        var tipo_atendimento = $(this).data('tipo_atendimento');

        $.ajax({
            url: '../public/tipo_atendimento/buscar.php',
            method: 'GET',
            data: { tipo_atendimento: tipo_atendimento },
            dataType: 'json',
            success: function (res) {
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $('#codigo').val(res.codigo);
                $('#descricao').val(res.descricao);
                $('#ativo').prop('checked', res.ativo == 't' || res.ativo === true);
                editandoId = res.tipo_atendimento;
            },
            error: function () {
                Alerta.error('Erro ao buscar tipo de atendimento.');
            }
        });
    });

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('alerta') === 'true') {
        Alerta.warning('Nenhum tipo de atendimento cadastrado!');
    }
});
