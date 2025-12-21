$(document).ready(function () {
    function carregarProdutos() {
        if ($.fn.DataTable.isDataTable('#produtosTable')) {
            $('#produtosTable').DataTable().destroy();
        }
        $.ajax({
            url: '../public/produto/listar.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#produtosTable tbody').empty();
                if (data.length > 0) {
                    data.forEach(function (produto) {
                        var ativo = produto.ativo == 't' ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>';
                        $('#produtosTable tbody').append(`
                            <tr data-produto="${produto.produto}">
                                <td>${produto.codigo}</td>
                                <td>${produto.descricao}</td>
                                <td>${ativo}</td>
                                <td>
                                    <button class='btn btn-warning btn-sm editar-produto' data-produto='${produto.produto}'>Editar</button>
                                    <button class='btn btn-danger btn-sm excluir-produto' data-produto='${produto.produto}'>Excluir</button>
                                    <button class='btn btn-info btn-sm btn-log-auditor' data-id='${produto.produto}'data-tabela='tbl_produto'>Ver Log</button>
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
                if (data.length > 0) {
                    $('#produtosTable').DataTable({
                        responsive: true,
                        scrollX: true,
                        autoWidth: false,
                        language: {
                            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                        }
                    });
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
            ativo: $('#ativo').is(':checked') ? 't' : 'f'
        };

        $.ajax({
            url: '../public/produto/cadastrar.php',
            method: 'POST',
            data: formData,
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
        var produto = $(this).data('produto');

        $.ajax({
            url: '../public/produto/buscar.php',
            method: 'GET',
            data: { produto: produto },
            dataType: 'json',
            success: function (produto) {
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $('#codigo').val(produto.codigo);
                $('#descricao').val(produto.descricao);
                $('#ativo').prop('checked', produto.ativo == 't');

                var produto = produto.produto;

                $('#produtoForm').off('submit').on('submit', function (e) {
                    e.preventDefault();
                    $.ajax({
                        url: '../public/produto/editar.php',
                        method: 'POST',
                        data: {
                            produto: produto,
                            codigo: $('#codigo').val(),
                            descricao: $('#descricao').val(),
                            ativo: $('#ativo').is(':checked') ? 't' : 'f'
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

    $(document).on('click', '.excluir-produto', function () {
        let produto = $(this).data('produto');

        Swal.fire({
            title: `Deseja excluir o produto?`,
            text: 'Essa ação não pode ser desfeita.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../public/produto/excluir.php',
                    method: 'POST',
                    data: { produto: produto },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: response.message,
                            }).then(() => {
                                carregarProdutos();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Atenção!',
                                text: response.message,
                            });
                        }
                    }
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
            text: 'Nenhum produto cadastrado!',
            confirmButtonText: 'OK',
            confirmButtonColor: '#007bff'
        });
    }
});
