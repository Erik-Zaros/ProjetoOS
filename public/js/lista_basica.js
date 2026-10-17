$(document).ready(function () {

    window.retorna_produto = function (p) {
        if (!p) return;
        const produtoId = p.produto || p.id;
        const codigo = p.codigo || '';
        const descricao = p.descricao || '';
        const rotulo = (codigo ? codigo + ' - ' : '') + descricao;

        $('#produtoSelecionado').val(produtoId);
        $('#buscarProduto').val(rotulo);
        $('#areaPecas').fadeIn();

        $('#nomeProduto').text(rotulo);
        $('#produtoTitulo').fadeIn();

        $('#btnExcelContainer').fadeIn();
        $('#btnExcel').attr('href', `../public/lista_basica/relatorio.php?produto=${produtoId}`);

        carregarPecas(produtoId);
        Alerta.info(rotulo, 'Produto Selecionado');
    };

    $('#btnLupaProduto').on('click', function () {
        if (!window.Lupa) {
            console.error('Módulo Lupa não encontrado.');
            return;
        }

        Lupa.abrir({
            url: '../public/lista_basica/buscar_produtos.php',
            titulo: 'Selecionar Produto',
            placeholder: 'Digite código ou descrição para buscar...',
            colunas: [
                { titulo: 'Código', campo: 'codigo', largura: '150px', classe: 'fw-bold' },
                { titulo: 'Descrição', campo: 'descricao' },
                { titulo: 'Status', campo: 'ativo', largura: '110px', tipo: 'ativo', classe: 'text-center' },
                { titulo: 'Ação', largura: '120px', tipo: 'acao', classe: 'text-center' }
            ],
            callback: window.retorna_produto
        });
    });

    function carregarPecas(produtoId) {
        MiniDataTable.destroy('#tabelaPecas');
        $.get('../public/lista_basica/buscar_pecas_produto.php', { produto: produtoId }, function (data) {
            let html = '';
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(p => {
                    let ativo = p.ativo == 't' || p.ativo === true
                        ? '<i class="bi bi-check-circle-fill text-success"></i>'
                        : '<i class="bi bi-x-circle-fill text-danger"></i>';

                    html += `
                        <tr>
                            <td class='text-center'>${p.codigo}</td>
                            <td class='text-center'>${p.descricao}</td>
                            <td class='text-center'>${ativo}</td>
                            <td class="coluna-acoes text-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-warning btn-sm btn-acoes dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Ações">
                                        <i class="bi bi-list"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a href="javascript:void(0)" class="dropdown-item removerPeca" data-id="${p.lista_basica}">
                                                <i class="bi bi-trash3-fill text-danger me-1"></i> Excluir
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>`;
                });
            }
            $('#tabelaPecas tbody').html(html);
            new MiniDataTable('#tabelaPecas');
        }, 'json');
    }

    $('#buscarPeca').on('input', function () {
        const termo = $(this).val();
        if (termo.length < 2) return;

        $.get('../public/lista_basica/buscar_pecas.php', { termo: termo }, function (data) {
            let html = '';
            data.forEach(p => {
                html += `<li class="list-group-item list-peca" data-id="${p.peca}">
                            <strong>${p.codigo}</strong> - ${p.descricao}
                         </li>`;
            });
            $('#listaPecas').html(html).show();
        }, 'json');
    });

    $(document).on('click', '.list-peca', function () {
        const produto = $('#produtoSelecionado').val();
        const peca = $(this).data('id');

        $.post('../public/lista_basica/adicionar_peca.php', { produto: produto, peca: peca }, function (resp) {
            if (resp.success) {
                Alerta.success('A peça foi amarrada com sucesso ao produto.', 'Peça adicionada!');
                carregarPecas(produto);
                $('#buscarPeca').val('');
                $('#listaPecas').hide();
            } else if (resp.error) {
                Alerta.warning('Esta peça já está amarrada ao produto.', 'Já existe!');
            } else {
                Alerta.warning('Falha ao processar solicitação.', 'Atenção');
            }
        }, 'json');
    });

    $(document).on('click', '.removerPeca', function () {
        const id = $(this).data('id');
        const produto = $('#produtoSelecionado').val();

        Alerta.confirm({
            title: 'Remover peça?',
            text: 'Essa amarração será excluída permanentemente.',
            confirmButtonText: 'Sim, remover',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../public/lista_basica/remover_peca.php', { id: id }, function (resp) {
                    if (resp.success) {
                        Alerta.success('A amarração foi removida com sucesso.', 'Removida!');
                        carregarPecas(produto);
                    } else {
                        Alerta.error('Não foi possível remover a peça.');
                    }
                }, 'json');
            }
        });
    });
});
