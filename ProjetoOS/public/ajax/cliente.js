document.addEventListener("DOMContentLoaded", function () {
    const page = window.location.pathname.split("/").pop();
    const navLinks = document.querySelectorAll(".nav-link");
    navLinks.forEach(link => {
        if (link.getAttribute("href") === page) {
            link.classList.add("active");
        }
    });
});

$(document).ready(function () {
    function carregarClientes() {
        $.ajax({
            url: '../controller/cliente/listar_clientes.php',
            method: 'GET',
            success: function (data) {
                $('#clientesTable tbody').html(data);
            }
        });
    }
    carregarClientes();
    $('#clienteForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '../controller/cliente/cadastrar_cliente.php',
            method: 'POST',
            data: $(this).serialize(),
            success: function (response) {
    
                    let res = JSON.parse(response);
    
                    if (res.status === 'error') {
                        alert(res.message);
                    } else {
                        alert(res.message);
                        $('#clienteForm')[0].reset();
                        carregarClientes();
                    }
            },
            error: function () {
                alert('Erro Interação Servidor');
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
                                alert(res.message);
                            } else {
                                alert(res.message);
                                $('#clienteForm')[0].reset();
                                $('#cpf').prop('disabled', false);
                                carregarClientes();
                            }
                        }
                    });
                });
            }
        });
    });
});
