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
    function carregarProdutos() {
        $.ajax({
            url: '../controller/produto/listar_produtos.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#produtosTable tbody').empty();
                data.forEach(function (produto) {
                    var ativo = produto.ativo == 1 ? 'Sim' : 'Não';
                    $('#produtosTable tbody').append(`
                        <tr>
                            <td>${produto.codigo}</td>
                            <td>${produto.descricao}</td>
                            <td>${ativo}</td>
                            <td>
                                <button class='btn btn-primary editar-produto' data-codigo='${produto.codigo}'>Editar</button>
                            </td>
                        </tr>
                    `);
                });
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    carregarProdutos();

    $('#produtoForm').on('submit', function (e) {
        e.preventDefault();

        var formData = {
            codigo: $('#codigo').val(),
            descricao: $('#descricao').val(),
            ativo: $('#ativo').is(':checked') ? 1 : 0
        };

        $.ajax({
            url: '../controller/produto/cadastrar_produto.php',
            method: 'POST',
            data: formData,
            success: function (response) {
                alert('Produto cadastrado com sucesso!');
                $('#produtoForm')[0].reset();
                carregarProdutos();
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    $(document).on('click', '.editar-produto', function () {
        var codigo = $(this).data('codigo');

        $.ajax({
            url: '../controller/produto/buscar_produto.php',
            method: 'GET',
            data: { codigo: codigo },
            dataType: 'json',
            success: function (produto) {
                $('#codigo').val(produto.codigo);
                $('#descricao').val(produto.descricao);
                $('#ativo').prop('checked', produto.ativo == '1');

                var id = produto.id;

                $('#produtoForm').off('submit').on('submit', function (e) {
                    e.preventDefault();
                    $.ajax({
                        url: '../controller/produto/editar_produto.php',
                        method: 'POST',
                        data: {
                            id: id,
                            codigo: $('#codigo').val(),
                            descricao: $('#descricao').val(),
                            ativo: $('#ativo').is(':checked') ? 1 : 0
                        },
                        success: function (response) {
                            alert('Produto atualizado com sucesso!');
                            $('#produtoForm')[0].reset();
                            carregarProdutos();
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                });
            },
        });
    });
});
