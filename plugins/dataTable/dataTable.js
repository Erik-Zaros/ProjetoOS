/*!
 * MiniDataTable 1.1
 * DataTable simples para Bootstrap 5, sem dependências (jQuery opcional).
 *
 * Recursos:
 *  - Limitador de registros por página (10, 25, 50, 100, 250, 500)
 *  - Limite máximo de 500 registros por tabela
 *  - Busca (ignora acentos e maiúsculas/minúsculas)
 *  - Ordenação por coluna (primeiro clique: maior -> menor)
 *  - "Mostrando de X até Y de Z registros" + paginador
 *  - Mensagem quando não há resultado
 *  - Em telas pequenas a tabela vira cards (ver dataTable.css)
 *
 * Uso:
 *   new MiniDataTable('#minhaTabela');
 *   new MiniDataTable('#minhaTabela', { pageLength: 25 });
 *   $('#minhaTabela').miniDataTable();           // se tiver jQuery
 *   MiniDataTable.destroy('#minhaTabela');       // restaura as linhas no tbody
 */
(function (global) {
    'use strict';

    var DEFAULTS = {
        lengthMenu: [10, 25, 50, 100, 250, 500],
        pageLength: 10,
        maxRecords: 500,            // limite máximo de registros por tabela
        searchable: true,
        sortable: true,
        firstSortDirection: 'desc', // primeiro clique no cabeçalho: maior -> menor
        order: null,                // ex.: [0, 'desc'] ordenação inicial
        noSortColumns: [],          // índices de colunas sem ordenação
        noSearchColumns: [],        // índices de colunas fora da busca
        stickyHeader: true,         // cabeçalho (th) fixo ao rolar a página
        stickyOffset: 'auto',       // 'auto' detecta a navbar fixa, ou use um número em px
        language: {
            lengthBefore: 'Mostrar',
            lengthAfter: 'registros',
            search: 'Buscar:',
            searchPlaceholder: 'Digite para buscar...',
            info: 'Mostrando de {start} até {end} de {total} {registros}',
            infoFiltered: ' (filtrado de {max} registros)',
            infoLimited: ' - limitado aos primeiros {limit} registros',
            empty: 'Nenhum resultado encontrado',
            previous: 'Anterior',
            next: 'Próximo',
            sortTitle: 'Clique para ordenar',
            sortBy: 'Ordenar por...',
            sortAsc: 'crescente',
            sortDesc: 'decrescente'
        }
    };

    var uid = 0;
    var collator = new Intl.Collator('pt-BR', { numeric: true, sensitivity: 'base' });

    /* ---------- utilitários ---------- */

    function norm(s) {
        return String(s == null ? '' : s)
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .toLowerCase();
    }

    function esc(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function resolve(target) {
        if (!target) return null;
        if (typeof target === 'string') return document.querySelector(target);
        if (target.jquery) return target[0] || null;
        return target;
    }

    // Converte o conteúdo da célula em um valor comparável (número, data ou texto).
    // Use data-order="..." na <td> para forçar o valor de ordenação.
    function parseSortValue(td) {
        var raw = td.hasAttribute('data-order') ? td.getAttribute('data-order') : td.textContent;
        var v = String(raw).replace(/\s+/g, ' ').trim();

        if (v === '') {
            // Células só com ícone (ex.: ativo sim/não)
            if (td.querySelector('.text-success, .bi-check-circle-fill')) return 1;
            if (td.querySelector('.text-danger, .bi-x-circle-fill')) return 0;
            return '';
        }

        // Data dd/mm/aaaa [hh:mm[:ss]]
        var m = v.match(/^(\d{2})\/(\d{2})\/(\d{4})(?:[ T](\d{2}):(\d{2})(?::(\d{2}))?)?$/);
        if (m) {
            return new Date(+m[3], +m[2] - 1, +m[1], +(m[4] || 0), +(m[5] || 0), +(m[6] || 0)).getTime();
        }

        // Número (aceita 1.234,56 / 12,5 / R$ 10 / 15%)
        var n = v.replace(/R\$|%|\s/g, '');
        if (/^-?\d{1,3}(\.\d{3})+(,\d+)?$/.test(n) || /^-?\d+,\d+$/.test(n)) {
            return Number(n.replace(/\./g, '').replace(',', '.'));
        }
        if (/^-?\d+(\.\d+)?$/.test(n)) return Number(n);

        return v;
    }

    /* ---------- classe principal ---------- */

    function MiniDataTable(target, options) {
        var table = resolve(target);
        if (!table) throw new Error('MiniDataTable: tabela não encontrada.');
        if (table._mdt) table._mdt.destroy();

        var o = options || {};
        this.table = table;
        this.opts = Object.assign({}, DEFAULTS, o);
        this.opts.language = Object.assign({}, DEFAULTS.language, o.language);
        this.id = ++uid;

        var max = this.opts.maxRecords;
        this.lengths = this.opts.lengthMenu.filter(function (n) { return n <= max; });
        if (!this.lengths.length) this.lengths = [Math.min(10, max)];
        this.pageLen = this.lengths.indexOf(this.opts.pageLength) > -1 ? this.opts.pageLength : this.lengths[0];

        this.searchTerm = '';
        this.sortCol = null;
        this.sortDir = 'asc';
        this.page = 1;
        this._thH = [];

        this._build();
        table._mdt = this;
    }

    MiniDataTable.prototype._build = function () {
        var self = this;
        var t = this.table;
        var L = this.opts.language;
        var max = this.opts.maxRecords;

        /* --- lê as linhas existentes no tbody --- */
        this.tbody = t.tBodies[0] || t.createTBody();
        var all = [].slice.call(this.tbody.rows).filter(function (tr) {
            // ignora linhas "placeholder" (ex.: colspan com "nenhum registro")
            return !tr.querySelector('td[colspan]') && !tr.classList.contains('mdt-empty');
        });
        this.truncated = all.length > max;

        /* --- cabeçalhos --- */
        var headRow = t.tHead && t.tHead.rows.length ? t.tHead.rows[t.tHead.rows.length - 1] : null;
        this.headers = headRow ? [].slice.call(headRow.cells) : [];
        this.labels = this.headers.map(function (th) { return th.textContent.replace(/\s+/g, ' ').trim(); });
        this.colCount = this.headers.length || (all[0] ? all[0].cells.length : 1);

        this.sortable = [];
        this.searchableCols = [];
        for (var c = 0; c < this.colCount; c++) {
            var th = this.headers[c];
            var actionLike = th && (norm(this.labels[c]) === 'acoes' || th.classList.contains('mdt-actions'));
            this.sortable[c] = !!th && this.opts.sortable && !actionLike &&
                !th.classList.contains('no-sort') && th.getAttribute('data-orderable') !== 'false' &&
                this.opts.noSortColumns.indexOf(c) === -1;
            this.searchableCols[c] = !actionLike && !(th && th.classList.contains('no-search')) &&
                this.opts.noSearchColumns.indexOf(c) === -1;
        }

        /* --- prepara linhas (texto para busca + valores de ordenação) --- */
        this.rows = all.slice(0, max).map(function (tr, i) {
            var text = [], sort = [];
            [].slice.call(tr.cells).forEach(function (td, c) {
                if (self.labels[c]) td.setAttribute('data-label', self.labels[c]);
                if (self.searchableCols[c]) text.push(td.textContent);
                sort.push(parseSortValue(td));
            });
            return { el: tr, index: i, text: norm(text.join(' ')), sort: sort };
        });

        /* --- estrutura (controles + tabela + rodapé) --- */
        var id = 'mdt' + this.id;
        var wrap = document.createElement('div');
        wrap.className = 'mdt-wrapper';

        var hasSortable = this.sortable.some(Boolean);
        var top = document.createElement('div');
        top.className = 'row g-2 align-items-center mb-3 mdt-top';
        top.innerHTML =
            '<div class="col-12 col-md-4">' +
                '<label class="d-flex align-items-center gap-2 mb-0 mdt-length" for="' + id + '-len">' +
                    esc(L.lengthBefore) +
                    ' <select id="' + id + '-len" class="form-select form-select-sm w-auto mdt-length-select"></select> ' +
                    esc(L.lengthAfter) +
                '</label>' +
            '</div>' +
            '<div class="col-12 col-md-8">' +
                (this.opts.searchable
                    ? '<div class="d-flex align-items-center justify-content-md-end gap-2">' +
                        '<label class="mb-0" for="' + id + '-search">' + esc(L.search) + '</label>' +
                        '<input id="' + id + '-search" type="search" class="form-control form-control-sm mdt-search" ' +
                            'placeholder="' + esc(L.searchPlaceholder) + '" autocomplete="off">' +
                      '</div>'
                    : '') +
            '</div>' +
            (hasSortable
                ? '<div class="col-12 d-md-none">' +
                    '<select class="form-select form-select-sm mdt-mobile-sort" aria-label="' + esc(L.sortBy) + '"></select>' +
                  '</div>'
                : '');

        var bottom = document.createElement('div');
        bottom.className = 'row g-2 align-items-center mt-2 mdt-bottom';
        bottom.innerHTML =
            '<div class="col-12 col-md-5 mdt-info" role="status" aria-live="polite"></div>' +
            '<div class="col-12 col-md-7"><nav aria-label="Paginação">' +
                '<ul class="pagination pagination-sm justify-content-md-end flex-wrap mb-0 mdt-pager"></ul>' +
            '</nav></div>';

        t.parentNode.insertBefore(wrap, t);
        wrap.appendChild(top);
        wrap.appendChild(t);
        wrap.appendChild(bottom);
        t.classList.add('mdt-table');

        this.wrap = wrap;
        this.infoEl = bottom.querySelector('.mdt-info');
        this.pagerEl = bottom.querySelector('.mdt-pager');
        this.lenSel = top.querySelector('.mdt-length-select');
        this.searchEl = top.querySelector('.mdt-search');
        this.mobileSort = top.querySelector('.mdt-mobile-sort');

        /* --- select de quantidade --- */
        this.lenSel.innerHTML = this.lengths.map(function (n) {
            return '<option value="' + n + '"' + (n === self.pageLen ? ' selected' : '') + '>' + n + '</option>';
        }).join('');
        this.lenSel.addEventListener('change', function () {
            self.pageLen = +this.value;
            self.page = 1;
            self._update();
        });

        /* --- busca (com debounce) --- */
        if (this.searchEl) {
            var timer;
            this.searchEl.addEventListener('input', function () {
                var el = this;
                clearTimeout(timer);
                timer = setTimeout(function () {
                    self.searchTerm = el.value;
                    self.page = 1;
                    self._update();
                }, 150);
            });
        }

        /* --- paginador --- */
        this.pagerEl.addEventListener('click', function (e) {
            var b = e.target.closest('button[data-page]');
            if (!b || b.disabled) return;
            self.page = +b.getAttribute('data-page');
            self._update();
        });

        /* --- ordenação pelo cabeçalho (desktop) --- */
        this.headers.forEach(function (th, c) {
            if (!self.sortable[c]) return;
            th.classList.add('mdt-sortable');
            th.setAttribute('tabindex', '0');
            th.setAttribute('aria-sort', 'none');
            th.setAttribute('title', L.sortTitle);
            var h = function (e) {
                if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') return;
                e.preventDefault();
                self._toggleSort(c);
            };
            th.addEventListener('click', h);
            th.addEventListener('keydown', h);
            self._thH.push([th, h]);
        });

        /* --- ordenação no mobile (select) --- */
        if (this.mobileSort) {
            var opts = '<option value="">' + esc(L.sortBy) + '</option>';
            this.labels.forEach(function (label, c) {
                if (!self.sortable[c]) return;
                opts += '<option value="' + c + ':desc">' + esc(label) + ' (' + esc(L.sortDesc) + ')</option>';
                opts += '<option value="' + c + ':asc">' + esc(label) + ' (' + esc(L.sortAsc) + ')</option>';
            });
            this.mobileSort.innerHTML = opts;
            this.mobileSort.addEventListener('change', function () {
                if (!this.value) {
                    self.sortCol = null;
                } else {
                    var p = this.value.split(':');
                    self.sortCol = +p[0];
                    self.sortDir = p[1];
                }
                self.page = 1;
                self._update();
            });
        }

        /* --- ordenação inicial --- */
        var ord = this.opts.order;
        if (ord && this.sortable[ord[0]]) {
            this.sortCol = ord[0];
            this.sortDir = ord[1] === 'asc' ? 'asc' : 'desc';
        }

        if (this.opts.stickyHeader) this._initSticky();

        this._update();
    };

    // Cabeçalho fixo: position: sticky no CSS + offset da navbar fixa (se houver)
    MiniDataTable.prototype._initSticky = function () {
        var self = this;
        this.table.classList.add('mdt-sticky');
        this._ovr = [];
        this._hasScrollParent = false;

        // position: sticky não funciona se um ancestral tiver overflow:hidden
        // (comum em layouts AdminLTE). "clip" tem o mesmo visual sem criar scroll.
        // NUNCA mexe em quem realmente rola (modal, offcanvas, áreas com scroll).
        var el = this.wrap.parentElement;
        while (el && el !== document.body && el !== document.documentElement) {
            var cs = getComputedStyle(el);

            if (el.classList.contains('table-responsive')) {
                // só existe para rolar na horizontal e impediria o cabeçalho fixo;
                // no celular os cards já resolvem a responsividade
                this._ovr.push({ el: el, prev: el.style.overflow });
                el.style.overflow = 'visible';
            } else if (el.matches('.modal, .offcanvas, .modal-body, .overflow-auto, .overflow-scroll') ||
                       cs.overflowY === 'scroll' ||
                       (cs.overflowY === 'auto' && el.scrollHeight > el.clientHeight + 1)) {
                // container que rola de verdade: o sticky se ancora nele
                this._hasScrollParent = true;
                break;
            } else if (cs.overflowX === 'hidden' || cs.overflowY === 'hidden') {
                this._ovr.push({ el: el, prev: el.style.overflow });
                el.style.overflow = 'clip';
            }
            el = el.parentElement;
        }

        this._onResize = function () {
            // tabela removida do DOM sem destroy(): solta o listener
            if (!document.body.contains(self.table)) {
                window.removeEventListener('resize', self._onResize);
                return;
            }
            self._applyStickyOffset();
        };
        window.addEventListener('resize', this._onResize);
        this._applyStickyOffset();
    };

    MiniDataTable.prototype._applyStickyOffset = function () {
        var off = this.opts.stickyOffset;
        if (typeof off !== 'number') {
            off = 0;
            if (!this._hasScrollParent) {
                var sels = ['.main-header', '.navbar.fixed-top', '.navbar.sticky-top', 'header.fixed-top', 'header.sticky-top'];
                for (var i = 0; i < sels.length; i++) {
                    var n = document.querySelector(sels[i]);
                    if (!n) continue;
                    var pos = getComputedStyle(n).position;
                    if (pos === 'fixed' || pos === 'sticky') {
                        off = Math.round(n.getBoundingClientRect().height);
                        break;
                    }
                }
            } 
        }
        this.table.style.setProperty('--mdt-sticky-top', off + 'px');
    };

    MiniDataTable.prototype._toggleSort = function (c) {
        if (this.sortCol === c) {
            this.sortDir = this.sortDir === 'desc' ? 'asc' : 'desc';
        } else {
            this.sortCol = c;
            this.sortDir = this.opts.firstSortDirection === 'asc' ? 'asc' : 'desc';
        }
        this.page = 1;
        this._update();
    };

    MiniDataTable.prototype._compare = function (a, b) {
        var c = this.sortCol;
        var x = a.sort[c], y = b.sort[c];
        var xe = x === '' || x == null, ye = y === '' || y == null;
        if (xe && ye) return 0;
        if (xe) return 1;   // vazios sempre no final
        if (ye) return -1;

        var r;
        if (typeof x === 'number' && typeof y === 'number') r = x - y;
        else if (typeof x === 'number') r = -1;
        else if (typeof y === 'number') r = 1;
        else r = collator.compare(x, y);

        return this.sortDir === 'asc' ? r : -r;
    };

    MiniDataTable.prototype._update = function () {
        var self = this;

        /* filtra */
        var terms = norm(this.searchTerm).trim().split(/\s+/).filter(Boolean);
        var list = terms.length
            ? this.rows.filter(function (r) { return terms.every(function (t) { return r.text.indexOf(t) > -1; }); })
            : this.rows.slice();

        /* ordena */
        if (this.sortCol !== null) {
            list.sort(function (a, b) { return self._compare(a, b) || a.index - b.index; });
        }
        this.filtered = list;

        /* pagina */
        var total = list.length;
        var pages = Math.max(1, Math.ceil(total / this.pageLen));
        if (this.page > pages) this.page = pages;
        if (this.page < 1) this.page = 1;
        var from = (this.page - 1) * this.pageLen;
        var slice = list.slice(from, from + this.pageLen);

        /* desenha */
        if (slice.length) {
            this.tbody.replaceChildren.apply(this.tbody, slice.map(function (r) { return r.el; }));
        } else {
            var tr = document.createElement('tr');
            tr.className = 'mdt-empty';
            tr.innerHTML = '<td colspan="' + this.colCount + '" class="mdt-empty-cell text-center text-muted py-4">' +
                esc(this.opts.language.empty) + '</td>';
            this.tbody.replaceChildren(tr);
        }

        this._renderInfo(total, from, slice.length);
        this._renderPager(pages);
        this._renderSortState();
    };

    MiniDataTable.prototype._renderInfo = function (total, from, shown) {
        var L = this.opts.language;
        var start = total ? from + 1 : 0;
        var end = total ? from + shown : 0;
        var txt = L.info
            .replace('{start}', start)
            .replace('{end}', end)
            .replace('{total}', total)
            .replace('{registros}', total === 1 ? 'registro' : 'registros');

        if (norm(this.searchTerm).trim() && total !== this.rows.length) {
            txt += L.infoFiltered.replace('{max}', this.rows.length);
        }
        if (this.truncated) {
            txt += L.infoLimited.replace('{limit}', this.opts.maxRecords);
        }
        this.infoEl.textContent = txt;
    };

    MiniDataTable.prototype._renderPager = function (pages) {
        var L = this.opts.language;
        var cur = this.page;

        function item(label, page, opts) {
            opts = opts || {};
            return '<li class="page-item' + (opts.disabled ? ' disabled' : '') + (opts.active ? ' active' : '') +
                (opts.hideXs ? ' mdt-hide-xs' : '') + '">' +
                '<button type="button" class="page-link" data-page="' + page + '"' +
                (opts.disabled ? ' disabled tabindex="-1"' : '') +
                (opts.active ? ' aria-current="page"' : '') + '>' + label + '</button></li>';
        }

        var html = item(esc(L.previous), cur - 1, { disabled: cur === 1 });

        // páginas visíveis: primeira, última e vizinhas da atual
        var show = {};
        [1, pages, cur - 2, cur - 1, cur, cur + 1, cur + 2].forEach(function (p) {
            if (p >= 1 && p <= pages) show[p] = true;
        });
        var nums = Object.keys(show).map(Number).sort(function (a, b) { return a - b; });
        var prev = 0;
        nums.forEach(function (p) {
            if (p - prev > 1) {
                html += '<li class="page-item disabled mdt-hide-xs"><span class="page-link">&hellip;</span></li>';
            }
            html += item(p, p, { active: p === cur, hideXs: p !== cur });
            prev = p;
        });

        html += item(esc(L.next), cur + 1, { disabled: cur === pages });
        this.pagerEl.innerHTML = html;
    };

    MiniDataTable.prototype._renderSortState = function () {
        var self = this;
        this.headers.forEach(function (th, c) {
            if (!self.sortable[c]) return;
            th.classList.remove('mdt-sort-asc', 'mdt-sort-desc');
            th.setAttribute('aria-sort', 'none');
            if (self.sortCol === c) {
                th.classList.add(self.sortDir === 'asc' ? 'mdt-sort-asc' : 'mdt-sort-desc');
                th.setAttribute('aria-sort', self.sortDir === 'asc' ? 'ascending' : 'descending');
            }
        });
        if (this.mobileSort) {
            this.mobileSort.value = this.sortCol === null ? '' : this.sortCol + ':' + this.sortDir;
        }
    };

    /* ---------- API pública ---------- */

    MiniDataTable.prototype.search = function (term) {
        this.searchTerm = term || '';
        if (this.searchEl) this.searchEl.value = this.searchTerm;
        this.page = 1;
        this._update();
        return this;
    };

    MiniDataTable.prototype.sort = function (col, dir) {
        if (!this.sortable[col]) return this;
        this.sortCol = col;
        this.sortDir = dir === 'asc' ? 'asc' : 'desc';
        this.page = 1;
        this._update();
        return this;
    };

    MiniDataTable.prototype.goTo = function (page) {
        this.page = page;
        this._update();
        return this;
    };

    // Remove o plugin e devolve TODAS as linhas ao tbody.
    MiniDataTable.prototype.destroy = function () {
        if (!this.table._mdt) return;

        this._thH.forEach(function (p) {
            p[0].removeEventListener('click', p[1]);
            p[0].removeEventListener('keydown', p[1]);
        });
        this.headers.forEach(function (th) {
            th.classList.remove('mdt-sortable', 'mdt-sort-asc', 'mdt-sort-desc');
            th.removeAttribute('tabindex');
            th.removeAttribute('aria-sort');
            th.removeAttribute('title');
        });

        if (this._onResize) window.removeEventListener('resize', this._onResize);
        (this._ovr || []).forEach(function (o) { o.el.style.overflow = o.prev; });
        this.table.classList.remove('mdt-sticky');
        this.table.style.removeProperty('--mdt-sticky-top');

        this.tbody.replaceChildren.apply(this.tbody, this.rows.map(function (r) { return r.el; }));

        this.wrap.parentNode.insertBefore(this.table, this.wrap);
        this.wrap.parentNode.removeChild(this.wrap);
        this.table.classList.remove('mdt-table');
        delete this.table._mdt;
    };

    MiniDataTable.destroy = function (target) {
        var t = resolve(target);
        if (t && t._mdt) t._mdt.destroy();
    };

    MiniDataTable.isInitialized = function (target) {
        var t = resolve(target);
        return !!(t && t._mdt);
    };

    MiniDataTable.getInstance = function (target) {
        var t = resolve(target);
        return (t && t._mdt) || null;
    };

    MiniDataTable.defaults = DEFAULTS;

    /* ---------- atalho jQuery (opcional) ---------- */
    if (global.jQuery) {
        global.jQuery.fn.miniDataTable = function (options) {
            return this.each(function () { new MiniDataTable(this, options); });
        };
    }

    global.MiniDataTable = MiniDataTable;

})(window);