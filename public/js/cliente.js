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
                                <button class="btn btn-warning btn-sm editar" data-cpf="${cliente.cpf}"><i class="bi bi-pencil-square"></i> Editar</button>
                                <button class='btn btn-info btn-sm btn-log-auditor' data-id='${cliente.cliente}'data-tabela='tbl_cliente'><i class="bi bi-clock-history"></i> Ver Log</button>
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
                    responsive: true,
                    scrollX: true,
                    autoWidth: false,
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                    }
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
                dataType: 'json',
                data: { cep: cep },
                success: function (data) {

                    if (!data.erro) {
                        $('#logradouro').val(data.logradouro || '');
                        $('#bairro').val(data.bairro || '');
                        $('#cidade').val(data.localidade || '');
                        $('#estado').val(data.uf || '');
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
            dataType: 'json',
            data: $.param(formData),
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
            dataType: 'json',
            data: { cpf: cpf },
            success: function (data) {
                $("html, body").animate({ scrollTop: 0 }, "slow");

                $('#cpf').val(data.cpf).prop('disabled', true);
                $('#nome').val(data.nome || '');
                $('#cep').val(data.cep ? data.cep.replace(/(\d{5})(\d{3})/, '$1-$2') : '');
                $('#logradouro').val(data.endereco || '');
                $('#bairro').val(data.bairro || '');
                $('#numero').val(data.numero || '');
                $('#cidade').val(data.cidade || '');
                $('#estado').val(data.estado || '');

                $('#clienteForm').off('submit').on('submit', function (e) {
                    e.preventDefault();
                    const cepSemHifen = $('#cep').val().replace('-', '');
                    $.ajax({
                        url: '../public/cliente/editar.php',
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            data: data.cliente,
                            cpf: data.cpf,
                            nome: $('#nome').val(),
                            cep: cepSemHifen,
                            endereco: $('#logradouro').val(),
                            bairro: $('#bairro').val(),
                            numero: $('#numero').val(),
                            cidade: $('#cidade').val(),
                            estado: $('#estado').val()
                        },
                        success: function (response) {

                            if (response.status === 'error') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro!',
                                    text: response.message,
                                });
                            } else if (response.status === "success") {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sucesso!',
                                    text: response.message,
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

    const urlParams = new URLSearchParams(window.location.search);
    const alerta = urlParams.get('alerta');

    if (alerta === 'true') {
        Swal.fire({
            icon: 'warning',
            title: 'Atenção',
            text: 'Nenhum cliente cadastrado!',
            confirmButtonText: 'OK',
            confirmButtonColor: '#007bff'
        });
    }
});
