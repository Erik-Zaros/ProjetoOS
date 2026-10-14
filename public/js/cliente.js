function carregarClientes() {
    MiniDataTable.destroy('#clientesTable');
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
                            <td class='text-center'>${cliente.cpf || ''}</td>
                            <td class='text-center'>${cliente.nome || ''}</td>
                            <td class='text-center'>${cepFormatado}</td>
                            <td class='text-center'>${cliente.endereco || ''}</td>
                            <td class='text-center'>${cliente.bairro || ''}</td>
                            <td class='text-center'>${cliente.numero || ''}</td>
                            <td class='text-center'>${cliente.cidade || ''}</td>
                            <td class='text-center'>${cliente.estado || ''}</td>
                            <td class="coluna-acoes text-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-acoes dropdown-toggle"
                                            data-bs-toggle="dropdown" aria-expanded="false" title="Ações">
                                        <i class="bi bi-list"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a href="javascript:void(0)" class="dropdown-item editar"
                                               data-cpf="${cliente.cpf}">
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)" class="dropdown-item btn-log-auditor"
                                               data-id="${cliente.cliente}" data-tabela="tbl_cliente">
                                                <i class="bi bi-clock-history text-info"></i> Ver Log
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    `);
                });
            }
            new MiniDataTable('#clientesTable');
        },
        error: function () {
            Swal.fire({
               toast: true,
               position: 'top-end',
               showConfirmButton: false,
               icon: 'error',
               text: 'Erro ao carregar os clientes'
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
                           toast: true,
                           position: 'top-end',
                           showConfirmButton: false,
                           icon: 'error',
                           text: 'CEP inválido!'
                         });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                       toast: true,
                       position: 'top-end',
                       showConfirmButton: false,
                       icon: 'error',
                       text: 'Erro ao buscar CEP.'
                     });
                }
            });
        } else {
            Swal.fire({
               toast: true,
               position: 'top-end',
               showConfirmButton: false,
               icon: 'error',
               text: 'CEP deve ter 8 dígitos.'
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
                       toast: true,
                       position: 'top-end',
                       showConfirmButton: false,
                       icon: 'error',
                       text: response.message
                     });
                } else {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
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
                            cliente: data.cliente,
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
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    icon: 'error',
                                    text: response.message,
                                });
                            } else if (response.status === "success") {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    timer: 2500,
                                    icon: 'success',
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
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                icon: 'error',
                                text: 'Erro ao editar cliente.',
                            });
                        }
                    });
                });
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    icon: 'error',
                    text: 'Erro ao buscar cliente.',
                });
            }
        });
    });
});
