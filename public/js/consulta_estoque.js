$(document).ready(function () {
  function tipoSelecionado() {
    return $('input[name="tipo_item"]:checked').val() || 'ambos';
  }
  function parametros() {
    return {
      tipo_item: tipoSelecionado(),
      termo: $('#termo').val(),
      somente_saldo: $('#somenteSaldo').is(':checked') ? '1' : ''
    };
  }
  function preencherKPIs(k) {
    $('#kpiTotal').text(k.total ?? '—');
    $('#kpiComSaldo').text(k.com_saldo ?? '—');
    $('#kpiZerados').text(k.zerados ?? '—');
    $('#kpiNegativos').text(k.negativos ?? '—');
  }
  function montarAcoes(row) {
    return `
      <div class="btn-group">
        <button type="button" class="btn btn-outline-warning btn-sm btn-acoes dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Ações">
          <i class="bi bi-list"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a href="javascript:void(0)" class="dropdown-item ver-mov"
               data-kind="${row.kind}"
               data-id="${row.id}"
               data-descricao="${row.descricao}">
              <i class="bi bi-clock-history text-info me-1"></i> Movimentação
            </a>
          </li>
        </ul>
      </div>
    `;
  }

  function carregarTabela() {
    MiniDataTable.destroy('#estoqueTable');

    $.ajax({
      url: '../public/estoque/consultar.php',
      method: 'GET',
      dataType: 'json',
      data: parametros(),
      success: function (resp) {
        if (resp.status !== 'success') {
          Alerta.error(resp.message || 'Falha ao consultar estoque.');
          return;
        }

        preencherKPIs(resp.kpi || {});
        const $tbody = $('#estoqueTable tbody').empty();
        const dados = Array.isArray(resp.rows) ? resp.rows : [];

        if (dados.length > 0) {
          dados.forEach(function (row) {
            $tbody.append(`
              <tr>
                <td class='text-center'>${row.tipo_item}</td>
                <td class='text-center'>${row.codigo}</td>
                <td class='text-center'>${row.descricao}</td>
                <td class="text-center">${row.saldo}</td>
                <td class='text-center'>${row.ult_entrada ?? '—'}</td>
                <td class='text-center'>${row.ult_saida ?? '—'}</td>
                <td class="coluna-acoes text-center">${montarAcoes(row)}</td>
              </tr>
            `);
          });
        }
        new MiniDataTable('#estoqueTable');
      },
      error: function () {
        Alerta.error('Erro ao carregar o estoque.');
      }
    });
  }

  $('#filtroEstoque').on('submit', function (e) { e.preventDefault(); carregarTabela(); });
  $('#btnLimpar').on('click', function () {
    $('#termo').val(''); $('#somenteSaldo').prop('checked', false); $('#tpAmbos').prop('checked', true); carregarTabela();
  });
  $('#tpAmbos, #tpProduto, #tpPeca').on('change', carregarTabela);

  $(document).on('click', '.ver-mov', function () {
    const kind = $(this).data('kind');
    const id   = $(this).data('id');
    const desc = $(this).data('descricao');

    $('#movModalLabel').text(`Movimentações do ${kind.toUpperCase()}: ${desc}`);
    const params = (kind === 'produto') ? { produto: id } : { peca: id };

    $.ajax({
      url: '../public/estoque/listar.php',
      dataType: 'json',
      data: params,
      success: function (rows) {
        const $tb = $('#movModalTable tbody').empty();
        if (Array.isArray(rows) && rows.length) {
          rows.forEach(function (m) {
            const t = String(m.tipo || '').toUpperCase();
            const rot = (t === 'E') ? 'ENTRADA' : (t === 'S') ? 'SAÍDA' : t;
            $tb.append(`
              <tr>
                <td>${m.estoque_movimento}</td>
                <td>${m.data}</td>
                <td>${rot}</td>
                <td>${m.qtde}</td>
                <td>${m.os ?? ''}</td>
                <td>${m.motivo ?? ''}</td>
              </tr>
            `);
          });
        } else {
          $tb.append('<tr><td colspan="6" class="text-center text-muted">Sem lançamentos</td></tr>');
        }
        new bootstrap.Modal(document.getElementById('movModal')).show();
      },
      error: function () {
        Alerta.error('Erro ao buscar movimentações.');
      }
    });
  });

  carregarTabela();
});
