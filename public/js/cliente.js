function carregarClientes() {
    if ($.fn.DataTable.isDataTable('#clientesTable')) {
        $('#clientesTable').DataTable().destroy();
    }
    $.ajax({
        url: '../public/cliente/listar.php',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            $('#clientesTable tbody').empty();
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(function (cliente) {
                    const cepFormatado = cliente.cep ? cliente.cep.replace(/(\d{5})(\d{3})/, '$1-$2') : '';
                    $('#clientesTable tbody').append(`
                        <tr>
                            <td>${cliente.cpf || ''}</td>
                            <td>${cliente.nome || ''}</td>
                            <td>${cepFormatado}</td>
                            <td>${cliente.endereco || ''}</td>
                            <td>${cliente.bairro || ''}</td>
                            <td>${cliente.numero || ''}</td>
                            <td>${cliente.cidade || ''}</td>
                            <td>${cliente.estado || ''}</td>
                            <td>
                                <button class="btn btn-warning btn-sm editar" data-cpf="${cliente.cpf}">Editar</button>
                                <button class='btn btn-info btn-sm btn-log-auditor' data-id='${cliente.cliente}'data-tabela='tbl_cliente'>Ver Log</button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                $('#clientesTable tbody').append(`
                    <tr>
                        <td colspan="9" class="text-center">NENHUM CLIENTE CADASTRADO</td>
                    </tr>
                `);
            }
            if (data.length > 0) {
                $('#clientesTable').DataTable({
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                    },
                    order: [[0, "asc"]],
                    stripeClasses: ['stripe1', 'stripe2'],
                });
            }
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'Erro ao carregar os clientes.',
            });
        }
    });
}

$(document).ready(function () {
    carregarClientes();

    $('#cep').on('blur', function () {
        const cep = $('#cep').val().replace('-', '');
        if (cep.length === 8) {
            $.ajax({
                url: '../public/cep/buscar.php',
                method: 'POST',
                data: { cep: cep },
                success: function (data) {
                    const endereco = JSON.parse(data);
                    if (!endereco.erro) {
                        $('#logradouro').val(endereco.logradouro || '');
                        $('#bairro').val(endereco.bairro || '');
                        $('#cidade').val(endereco.localidade || '');
                        $('#estado').val(endereco.uf || '');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'CEP inválido!',
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Erro ao buscar CEP.',
                    });
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'CEP deve ter 8 dígitos.',
            });
        }
    });

    $('#clienteForm').on('submit', function (e) {
        e.preventDefault();
        const cepSemHifen = $('#cep').val().replace('-', '');
        const formData = $(this).serializeArray();
        formData.push({ name: 'cep', value: cepSemHifen });

        $.ajax({
            url: '../public/cliente/cadastrar.php',
            method: 'POST',
            data: $.param(formData),
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
                        $('#clienteForm')[0].reset();
                        carregarClientes();
                    });
                }
            },
        });
    });

        $(document).on('click', '.editar', function () {
        var cpf = $(this).data('cpf');
        $.ajax({
            url: '../public/cliente/buscar.php',
            method: 'GET',
            data: { cpf: cpf },
            success: function (data) {
                var cliente = JSON.parse(data);

                $('#cpf').val(cliente.cpf).prop('disabled', true);
                $('#nome').val(cliente.nome || '');
                $('#cep').val(cliente.cep ? cliente.cep.replace(/(\d{5})(\d{3})/, '$1-$2') : '');
                $('#logradouro').val(cliente.endereco || '');
                $('#bairro').val(cliente.bairro || '');
                $('#numero').val(cliente.numero || '');
                $('#cidade').val(cliente.cidade || '');
                $('#estado').val(cliente.estado || '');

                $('#clienteForm').off('submit').on('submit', function (e) {
                    e.preventDefault();
                    const cepSemHifen = $('#cep').val().replace('-', '');
                    $.ajax({
                        url: '../public/cliente/editar.php',
                        method: 'POST',
                        data: {
                            cliente: cliente.cliente,
                            cpf: cliente.cpf,
                            nome: $('#nome').val(),
                            cep: cepSemHifen,
                            endereco: $('#logradouro').val(),
                            bairro: $('#bairro').val(),
                            numero: $('#numero').val(),
                            cidade: $('#cidade').val(),
                            estado: $('#estado').val()
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
                                    carregarClientes();
                                    $('#clienteForm')[0].reset();
                                    $('#cpf').prop('disabled', false);
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: 'Erro ao editar cliente.',
                            });
                        }
                    });
                });
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao buscar cliente.',
                });
            }
        });
    });
});
