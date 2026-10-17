$(document).ready(function () {
    let editandoId = null;

    function carregarProdutos() {
        MiniDataTable.destroy('#produtosTable');
        $.ajax({
            url: '../public/produto/listar.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                $('#produtosTable tbody').empty();
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(function (produto) {
                        var ativo = produto.ativo == 't' || produto.ativo === true
                            ? '<i class="bi bi-check-circle-fill text-success"></i>'
                            : '<i class="bi bi-x-circle-fill text-danger"></i>';

                        $('#produtosTable tbody').append(`
                            <tr data-produto="${produto.produto}">
                                <td class='text-center'>${produto.codigo}</td>
                                <td class='text-center'>${produto.descricao}</td>
                                <td class='text-center'>${ativo}</td>
                                <td class="coluna-acoes text-center">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-outline-warning btn-sm btn-acoes dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Ações">
                                            <i class="bi bi-list"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a href="javascript:void(0)" class="dropdown-item editar-produto" data-produto="${produto.produto}">
                                                    <i class="bi bi-pencil-square text-warning me-1"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" class="dropdown-item excluir-produto" data-produto="${produto.produto}">
                                                    <i class="bi bi-trash3-fill text-danger me-1"></i> Excluir
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0)" class="dropdown-item btn-log-auditor" data-id="${produto.produto}" data-tabela="tbl_produto">
                                                    <i class="bi bi-clock-history text-info me-1"></i> Ver Log
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        `);
                    });
                }
                new MiniDataTable('#produtosTable');
            },
            error: function () {
                Alerta.error('Erro ao carregar os produtos.');
            }
        });
    }

    carregarProdutos();

    $('#produtoForm').on('submit', function (e) {
        e.preventDefault();

        var codigo = $('#codigo').val();
        var descricao = $('#descricao').val();
        var ativo = $('#ativo').is(':checked') ? 't' : 'f';

        if (editandoId) {
            $.ajax({
                url: '../public/produto/editar.php',
                method: 'POST',
                data: {
                    produto: editandoId,
                    codigo: codigo,
                    descricao: descricao,
                    ativo: ativo
                },
                success: function (response) {
                    let res = typeof response === 'string' ? JSON.parse(response) : response;
                    if (res.status === 'error') {
                        Alerta.error(res.message);
                    } else {
                        Alerta.success(res.message || 'Produto atualizado com sucesso.');
                        $('#produtoForm')[0].reset();
                        editandoId = null;
                        carregarProdutos();
                    }
                },
                error: function () {
                    Alerta.error('Erro ao editar produto.');
                }
            });
        } else {
            $.ajax({
                url: '../public/produto/cadastrar.php',
                method: 'POST',
                data: {
                    codigo: codigo,
                    descricao: descricao,
                    ativo: ativo
                },
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'error') {
                        Alerta.error(response.message);
                    } else {
                        Alerta.success(response.message || 'Produto cadastrado com sucesso.');
                        $('#produtoForm')[0].reset();
                        carregarProdutos();
                    }
                },
                error: function () {
                    Alerta.error('Erro ao cadastrar produto.');
                }
            });
        }
    });

    $(document).on('click', '.editar-produto', function () {
        var produto = $(this).data('produto');

        $.ajax({
            url: '../public/produto/buscar.php',
            method: 'GET',
            data: { produto: produto },
            dataType: 'json',
            success: function (prod) {
                $("html, body").animate({ scrollTop: 0 }, "slow");
                $('#codigo').val(prod.codigo);
                $('#descricao').val(prod.descricao);
                $('#ativo').prop('checked', prod.ativo == 't' || prod.ativo === true);
                editandoId = prod.produto;
            },
            error: function () {
                Alerta.error('Erro ao buscar dados do produto.');
            }
        });
    });

    $(document).on('click', '.excluir-produto', function () {
        let produto = $(this).data('produto');

        Alerta.confirm({
            title: 'Excluir produto?',
            text: 'Essa ação não pode ser desfeita.',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../public/produto/excluir.php',
                    method: 'POST',
                    data: { produto: produto },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Alerta.success(response.message || 'Produto excluído com sucesso.');
                            carregarProdutos();
                        } else {
                            Alerta.error(response.message || 'Erro ao excluir produto.');
                        }
                    },
                    error: function () {
                        Alerta.error('Erro ao comunicar com o servidor.');
                    }
                });
            }
        });
    });
});
