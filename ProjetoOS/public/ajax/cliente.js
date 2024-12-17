document.addEventListener("DOMContentLoaded", function () {
    const page = window.location.pathname.split("/").pop();
    const navLinks = document.querySelectorAll(".nav-link");
    navLinks.forEach(link => {
        if (link.getAttribute("href") === page) {
            link.classList.add("active");
        }
    });

    document.getElementById("sidebarToggle").addEventListener("click", function () {
        const sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("d-none");
    });
});

$(document).ready(function () {
    function carregarClientes() {
        $.ajax({
            url: '../controller/cliente/listar_clientes.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#clientesTable tbody').empty();
                if (data.length > 0) {
                    data.forEach(function (cliente) {
                        $('#clientesTable tbody').append(`
                            <tr>
                                <td>${cliente.cpf}</td>
                                <td>${cliente.nome}</td>
                                <td>${cliente.endereco}</td>
                                <td>${cliente.numero}</td>
                                <td>
                                    <button class="btn btn-primary editar" data-cpf="${cliente.cpf}">Editar</button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#clientesTable tbody').append(`
                        <tr>
                            <td colspan="5" class="text-center">NENHUM CLIENTE CADASTRADO</td>
                        </tr>
                    `);
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
    carregarClientes();
});

$('#clienteForm').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
        url: '../controller/cliente/cadastrar_cliente.php',
        method: 'POST',
        data: $(this).serialize(),
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
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'Erro de interação com o servidor.',
            });
        }
    });
});

$(document).on('click', '.editar', function () {
    var cpf = $(this).data('cpf');
    $.ajax({
        url: '../controller/cliente/buscar_cliente.php',
        method: 'GET',
        data: { cpf: cpf },
        success: function (data) {
            var cliente = JSON.parse(data);
            $('#cpf').val(cliente.cpf).prop('disabled', true);
            $('#nome').val(cliente.nome);
            $('#endereco').val(cliente.endereco);
            $('#numero').val(cliente.numero);
            $('#clienteForm').off('submit').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: '../controller/cliente/editar_cliente.php',
                    method: 'POST',
                    data: {
                        cpf: cliente.cpf,
                        nome: $('#nome').val(),
                        endereco: $('#endereco').val(),
                        numero: $('#numero').val()
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
                                let linha = $(`#clientesTable button[data-cpf="${cliente.cpf}"]`).closest('tr');
                                linha.find('td:nth-child(2)').text($('#nome').val());
                                linha.find('td:nth-child(3)').text($('#endereco').val());
                                linha.find('td:nth-child(4)').text($('#numero').val());

                                $('#clienteForm')[0].reset();
                                $('#cpf').prop('disabled', false);
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Erro ao editar cliente.',
                        });
                    }
                });
            });
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'Erro ao buscar cliente.',
            });
        }
    });
});
