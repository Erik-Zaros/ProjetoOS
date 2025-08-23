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
      <button id='ver-mov' class="btn btn-outline-secondary"
              data-kind="${row.kind}"
              data-id="${row.id}"
              data-descricao="${row.descricao}">
        Movimentação
      </button>
    `;
  }

  function carregarTabela() {
    if ($.fn.DataTable.isDataTable('#estoqueTable')) {
      $('#estoqueTable').DataTable().destroy();
    }

    $.ajax({
      url: '../public/estoque/consultar.php',
      method: 'GET',
      dataType: 'json',
      data: parametros(),
      success: function (resp) {
        if (resp.status !== 'success') {
          Swal.fire({ icon: 'error', title: 'Erro', text: resp.message || 'Falha ao consultar estoque.' });
          return;
        }

        preencherKPIs(resp.kpi || {});
        const $tbody = $('#estoqueTable tbody').empty();
        const dados = Array.isArray(resp.rows) ? resp.rows : [];

        if (!dados.length) {
          $tbody.append('<tr><td colspan="7" class="text-center">Nenhum item encontrado</td></tr>');
        } else {
          dados.forEach(function (row) {
            $tbody.append(`
              <tr>
                <td>${row.tipo_item}</td>
                <td>${row.codigo}</td>
                <td>${row.descricao}</td>
                <td class="text-end">${row.saldo}</td>
                <td>${row.ult_entrada ?? '—'}</td>
                <td>${row.ult_saida ?? '—'}</td>
                <td>${montarAcoes(row)}</td>
              </tr>
            `);
          });
        }

        if (dados.length > 0) {
          $('#estoqueTable').DataTable({
            language: { url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json" },
            order: [[2, "asc"]],
            stripeClasses: ['stripe1', 'stripe2']
          });
        }
      },
      error: function () {
        Swal.fire({ icon: 'error', title: 'Erro', text: 'Erro ao carregar o estoque.' });
      }
    });
  }

  $('#filtroEstoque').on('submit', function (e) { e.preventDefault(); carregarTabela(); });
  $('#btnLimpar').on('click', function () {
    $('#termo').val(''); $('#somenteSaldo').prop('checked', false); $('#tpAmbos').prop('checked', true); carregarTabela();
  });
  $('#tpAmbos, #tpProduto, #tpPeca').on('change', carregarTabela);

  $(document).on('click', '#ver-mov', function () {
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
        Swal.fire({ icon: 'error', title: 'Erro', text: 'Erro ao buscar movimentações.' });
      }
    });
  });

  carregarTabela();
});
