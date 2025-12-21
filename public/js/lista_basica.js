$(document).ready(function () {

    $('#buscarProduto').on('input', function () {
        const termo = $(this).val();
        if (termo.length < 2) return;

        $.get('../public/lista_basica/buscar_produtos.php', { termo }, function (data) {
            let html = '';
            data.forEach(p => {
                html += `<li class="list-group-item list-produto" data-id="${p.produto}">
                            <strong>${p.codigo}</strong> - ${p.descricao}
                         </li>`;
            });
            $('#listaProdutos').html(html).show();
        }, 'json');
    });

    $(document).on('click', '.list-produto', function () {
        const produtoId = $(this).data('id');
        const produtoNome = $(this).text().trim();

        $('#produtoSelecionado').val(produtoId);
        $('#listaProdutos').hide();
        $('#areaPecas').fadeIn();

        $('#btnExcelContainer').fadeIn();
        $('#btnExcel').attr('href', `../public/lista_basica/relatorio.php?produto=${produtoId}`);

        carregarPecas(produtoId);

        Swal.fire({
            icon: 'info',
            title: 'Produto selecionado',
            text: produtoNome,
            timer: 1500,
            showConfirmButton: false
        });
    });

    function carregarPecas(produtoId) {
        $.get('../public/lista_basica/buscar_pecas_produto.php', { produto: produtoId }, function (data) {
            let html = '';
            data.forEach(p => {
            let ativo = p.ativo == 't' ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-x-circle-fill text-danger"></i>';
                html += `
                    <tr>
                        <td>${p.codigo}</td>
                        <td>${p.descricao}</td>
                        <td>${ativo}</td>
                        <td>
                            <button class="btn btn-danger btn-sm removerPeca" data-id="${p.lista_basica}">Excluir</button>
                        </td>
                    </tr>`;
            });
            $('#tabelaPecas tbody').html(html);
        }, 'json');
    }

    $('#buscarPeca').on('input', function () {
        const termo = $(this).val();
        if (termo.length < 2) return;

        $.get('../public/lista_basica/buscar_pecas.php', { termo }, function (data) {
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

        $.post('../public/lista_basica/adicionar_peca.php', { produto, peca }, function (resp) {
            if (resp.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Peça adicionada!',
                    text: 'A peça foi amarrada com sucesso ao produto.',
                    timer: 1500,
                    showConfirmButton: false
                });
                carregarPecas(produto);
                $('#buscarPeca').val('');
                $('#listaPecas').hide();
            } else if (resp.error) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Já existe!',
                    text: 'Esta peça já está amarrada ao produto não é possível lançar novamente!',
                    timer: 2500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Falha',
                    text: 'Falha ao processar',
                    timer: 1800,
                    showConfirmButton: false
                });
            }
        }, 'json');
    });

    $(document).on('click', '.removerPeca', function () {
        const id = $(this).data('id');
        const produto = $('#produtoSelecionado').val();

        Swal.fire({
            title: 'Remover peça?',
            text: 'Essa amarração será excluída permanentemente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, remover!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../public/lista_basica/remover_peca.php', { id }, function (resp) {
                    if (resp.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Removida!',
                            text: 'A amarração foi removida com sucesso.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        carregarPecas(produto);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Não foi possível remover a peça.',
                        });
                    }
                }, 'json');
            }
        });
    });
});
