/**
 * Componente Genérico de Lupa / Pesquisa em Modal
 */
(function (global) {
    'use strict';

    const Lupa = {
        config: null,
        debounceTimer: null,
        modalInstance: null,

        /**
         * Abre o modal de pesquisa configurado por parâmetros
         *
         * Exemplo de uso:
         * Lupa.abrir({
         *     url: '../public/lista_basica/buscar_produtos.php',
         *     titulo: 'Selecionar Produto',
         *     placeholder: 'Digite código ou descrição...',
         *     termoInicial: '',
         *     colunas: [
         *         { titulo: 'Código', campo: 'codigo', largura: '150px', classe: 'fw-bold' },
         *         { titulo: 'Descrição', campo: 'descricao' },
         *         { titulo: 'Status', campo: 'ativo', largura: '110px', tipo: 'ativo', classe: 'text-center' },
         *         { titulo: 'Ação', largura: '110px', tipo: 'acao', classe: 'text-center' }
         *     ],
         *     callback: function(item) { ... }
         * });
         */
        abrir: function (opts) {
            this.config = opts || {};
            const modalEl = document.getElementById('modalLupaGenerica');
            if (!modalEl) {
                console.error('Modal #modalLupaGenerica não encontrado no DOM. Certifique-se de dar include "lupa.php".');
                return;
            }

            this.modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);

            // Configurar título e placeholder
            $('#modalLupaTitulo').text(opts.titulo || 'Pesquisar');
            $('#modalLupaInput')
                .attr('placeholder', opts.placeholder || 'Digite para buscar...')
                .val(opts.termoInicial || '');

            // Montar cabeçalho dinâmico
            const colunas = opts.colunas || [
                { titulo: 'Código', campo: 'codigo', largura: '150px', classe: 'fw-bold' },
                { titulo: 'Descrição', campo: 'descricao' },
                { titulo: 'Ação', largura: '120px', tipo: 'acao', classe: 'text-center' }
            ];

            let theadHtml = '<tr>';
            colunas.forEach(col => {
                const style = col.largura ? `style="width:${col.largura};"` : '';
                const classe = col.classe ? `class="${col.classe}"` : '';
                theadHtml += `<th ${style} ${classe}>${col.titulo}</th>`;
            });
            theadHtml += '</tr>';
            $('#theadModalLupa').html(theadHtml);

            this.modalInstance.show();
            this.buscar(opts.termoInicial || '');
        },

        buscar: function (termo) {
            const self = this;
            const opts = this.config;
            if (!opts || !opts.url) return;

            const colunas = opts.colunas || [];
            const colCount = colunas.length || 3;

            $('#tbodyModalLupa').html(`
                <tr>
                    <td colspan="${colCount}" class="text-center text-muted py-4">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        Buscando registros...
                    </td>
                </tr>
            `);

            $.get(opts.url, { termo: termo || '' }, function (data) {
                let tbodyHtml = '';
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(item => {
                        const jsonStr = JSON.stringify(item).replace(/'/g, "&apos;");
                        tbodyHtml += `<tr class="item-lupa-linha" style="cursor: pointer;" data-json='${jsonStr}'>`;

                        colunas.forEach(col => {
                            const classe = col.classe ? `class="${col.classe}"` : '';

                            if (col.tipo === 'acao') {
                                tbodyHtml += `
                                    <td ${classe}>
                                        <button type="button" class="btn btn-primary btn-sm btn-lupa-selecionar" data-json='${jsonStr}'>
                                            <i class="bi bi-check2"></i> Selecionar
                                        </button>
                                    </td>`;
                            } else if (col.tipo === 'ativo') {
                                const val = item[col.campo];
                                const isAtivo = val === true || val === 't' || val === '1' || val === 1 || String(val).toLowerCase() === 'ativo';
                                const badge = isAtivo
                                    ? '<span class="badge bg-success">Ativo</span>'
                                    : '<span class="badge bg-danger">Inativo</span>';
                                tbodyHtml += `<td ${classe}>${badge}</td>`;
                            } else {
                                const val = item[col.campo] !== undefined && item[col.campo] !== null ? item[col.campo] : '—';
                                tbodyHtml += `<td ${classe}>${val}</td>`;
                            }
                        });

                        tbodyHtml += '</tr>';
                    });
                } else {
                    tbodyHtml = `<tr><td colspan="${colCount}" class="text-center text-muted py-4">Nenhum registro encontrado.</td></tr>`;
                }

                $('#tbodyModalLupa').html(tbodyHtml);
            }, 'json').fail(function () {
                $('#tbodyModalLupa').html(`<tr><td colspan="${colCount}" class="text-center text-danger py-4">Erro ao realizar pesquisa.</td></tr>`);
            });
        },

        selecionar: function (item) {
            if (this.modalInstance) {
                this.modalInstance.hide();
            }
            if (this.config && typeof this.config.callback === 'function') {
                this.config.callback(item);
            }
        }
    };

    // Eventos globais da Lupa
    $(document).ready(function () {
        $('#modalLupaInput').on('input', function () {
            clearTimeout(Lupa.debounceTimer);
            const termo = $(this).val().trim();
            Lupa.debounceTimer = setTimeout(function () {
                Lupa.buscar(termo);
            }, 300);
        });

        $('#modalLupaBtnBuscar').on('click', function () {
            const termo = $('#modalLupaInput').val().trim();
            Lupa.buscar(termo);
        });

        $('#modalLupaInput').on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const termo = $(this).val().trim();
                Lupa.buscar(termo);
            }
        });

        $(document).on('click', '#tbodyModalLupa .item-lupa-linha, #tbodyModalLupa .btn-lupa-selecionar', function (e) {
            e.stopPropagation();
            const item = $(this).data('json');
            Lupa.selecionar(item);
        });
    });

    global.Lupa = Lupa;

})(window);
