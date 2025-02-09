$(document).ready(function () {
    function carregarProdutos() {
        $.ajax({
            url: '../controller/produto/listaProduto.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#produtosTable tbody').empty();
                if (data.length > 0) {
                    data.forEach(function (produto) {
                        var ativo = produto.ativo == 1 ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>';
                        $('#produtosTable tbody').append(`
                            <tr data-codigo="${produto.codigo}">
                                <td>${produto.codigo}</td>
                                <td>${produto.descricao}</td>
                                <td>${ativo}</td>
                                <td>
                                    <button class='btn btn-warning editar-produto' data-codigo='${produto.codigo}'>Editar</button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#produtosTable tbody').append(`
                        <tr>
                            <td colspan="9" class="text-center">NENHUM PRODUTO CADASTRADO</td>
                        </tr>
                    `);
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao carregar os produtos.',
                });
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
            url: '../controller/produto/cadastraProduto.php',
            method: 'POST',
            data: formData,
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
                        $('#produtoForm')[0].reset();
                        carregarProdutos();
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao cadastrar produto.',
                });
            }
        });
    });

    $(document).on('click', '.editar-produto', function () {
        var codigo = $(this).data('codigo');

        $.ajax({
            url: '../controller/produto/buscaProduto.php',
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
                        url: '../controller/produto/editaProduto.php',
                        method: 'POST',
                        data: {
                            id: id,
                            codigo: $('#codigo').val(),
                            descricao: $('#descricao').val(),
                            ativo: $('#ativo').is(':checked') ? 1 : 0
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
                                    $('#produtoForm')[0].reset();
                                    carregarProdutos();
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: 'Erro ao editar produto.',
                            });
                        }
                    });
                });
            }
        });
    });
});
