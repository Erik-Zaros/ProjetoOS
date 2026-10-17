function carregarUsuarios() {
    MiniDataTable.destroy('#usuariosTable');
    $.ajax({
        url: '../public/usuario/listar.php',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            $('#usuariosTable tbody').empty();
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(function (usuario) {
                    $('#usuariosTable tbody').append(`
                        <tr>
                            <td class='text-center'>${usuario.usuario}</td>
                            <td class='text-center'>${usuario.login}</td>
                            <td class='text-center'>${usuario.nome}</td>
                            <td class='text-center'>
                                ${usuario.ativo === 't' || usuario.ativo === true ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>'}
                            </td>
                            <td class='text-center'>
                                ${usuario.tecnico === 't' || usuario.tecnico === true ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>'}
                            </td>
                            <td class='text-center'>
                                ${usuario.master === 't' || usuario.master === true ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>'}
                            </td>
                            <td class="coluna-acoes text-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-warning btn-sm btn-acoes dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Ações">
                                        <i class="bi bi-list"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a href="javascript:void(0)"
                                               class="dropdown-item editar"
                                               data-usuario="${usuario.usuario}">
                                                <i class="bi bi-pencil-square text-warning me-1"></i>
                                                Editar
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)"
                                               class="dropdown-item btn-log-auditor"
                                               data-id="${usuario.usuario}"
                                               data-tabela="tbl_usuario">
                                                <i class="bi bi-clock-history text-info me-1"></i>
                                                Ver Log
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    `);
                });
            }
            new MiniDataTable('#usuariosTable');
        },
        error: function () {
            Alerta.error('Erro ao carregar os usuários.');
        }
    });
}

$(document).ready(function () {
    carregarUsuarios();

    $('#usuarioForm').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serializeArray();

        $.ajax({
            url: '../public/usuario/cadastrar.php',
            method: 'POST',
            data: $.param(formData),
            dataType: 'json',
            success: function (response) {
                if (response.status === 'error') {
                    Alerta.error(response.message);
                } else {
                    Alerta.success(response.message || 'Usuário cadastrado com sucesso.');
                    $('#usuarioForm')[0].reset();
                    carregarUsuarios();
                }
            },
            error: function () {
                Alerta.error('Erro ao cadastrar usuário.');
            }
        });
    });

    $(document).on('click', '.editar', function () {
        var usuario_id = $(this).data('usuario');
        $.ajax({
            url: '../public/usuario/buscar.php',
            method: 'GET',
            data: { usuario: usuario_id },
            success: function (usuario) {
                $('#login').val(usuario.login);
                $('#nome').val(usuario.nome);
                $('#senha').val('');
                $('#ativo').prop('checked', usuario.ativo === 't' || usuario.ativo === true);
                $('#tecnico').prop('checked', usuario.tecnico === 't' || usuario.tecnico === true);
                $('#master').prop('checked', usuario.master === 't' || usuario.master === true);

                $('#login').prop('readonly', true);
                $("html, body").animate({ scrollTop: 0 }, "slow");

                $('#usuarioForm').off('submit').on('submit', function (e) {
                    e.preventDefault();

                    var formData = $(this).serializeArray();
                    formData.push({ name: 'usuario', value: usuario.usuario });

                    $.ajax({
                        url: '../public/usuario/editar.php',
                        method: 'POST',
                        dataType: 'json',
                        data: $.param(formData),
                        success: function (res) {
                            if (res.status === 'error') {
                                Alerta.error(res.message);
                            } else {
                                Alerta.success(res.message || 'Usuário editado com sucesso.');
                                carregarUsuarios();
                                $('#usuarioForm')[0].reset();
                                $('#login').prop('readonly', false);
                            }
                        },
                        error: function () {
                            Alerta.error('Erro ao editar usuário.');
                        }
                    });
                });
            },
            error: function () {
                Alerta.error('Erro ao buscar usuário.');
            }
        });
    });
});
