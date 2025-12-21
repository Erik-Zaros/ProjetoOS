function carregarUsuarios() {
    if ($.fn.DataTable.isDataTable('#usuariosTable')) {
        $('#usuariosTable').DataTable().destroy();
    }
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
                            <td>${usuario.usuario}</td>
                            <td>${usuario.login}</td>
                            <td>${usuario.nome}</td>
                            <td>
                                ${usuario.ativo === 't' || usuario.ativo === true ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-check-circle-fill text-danger"></i>'}
                            </td>
                            <td> ${usuario.tecnico === 't' || usuario.tecnico === true ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-check-circle-fill text-danger"></i>'}
                            </td>
                            <td> ${usuario.master === 't' || usuario.master === true ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-check-circle-fill text-danger"></i>'}
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm editar" data-usuario="${usuario.usuario}">Editar</button>
                                <button class='btn btn-info btn-sm btn-log-auditor' data-id='${usuario.usuario}'data-tabela='tbl_usuario'>Ver Log</button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                $('#usuariosTable tbody').append(`
                    <tr>
                        <td colspan="4" class="text-center">NENHUM USUÁRIO CADASTRADO</td>
                    </tr>
                `);
            }
            $('#usuariosTable').DataTable({
                responsive: true,
                scrollX: true,
                autoWidth: false,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                }
            });
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'Erro ao carregar os usuários.'
            });
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
                        $('#usuarioForm')[0].reset();
                        carregarUsuarios();
                    });
                }
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
                                    carregarUsuarios();
                                    $('#usuarioForm')[0].reset();
                                    $('#login').prop('readonly', false);
                                });
                            }
                        }
                    });
                });
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao buscar usuário.',
                });
            }
        });
    });
});

