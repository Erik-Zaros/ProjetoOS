$(function () {

  function alternarTipo() {
    if ($('#tpProduto').is(':checked')) {
      $('#boxProduto').removeClass('d-none');
      $('#boxPeca').addClass('d-none');
      $('#peca').val(''); $('#busca_peca').val('');
      limparPainel();
    } else {
      $('#boxPeca').removeClass('d-none');
      $('#boxProduto').addClass('d-none');
      $('#produto').val(''); $('#busca_produto').val('');
      limparPainel();
    }
  }

  function limparPainel() {
    $('#sbSaldo').text('—');
    $('#sbUltEntrada').text('—');
    $('#sbUltSaida').text('—');
    $('#movTable tbody').html('<tr><td colspan="7" class="text-center text-muted">Selecione um item</td></tr>');
  }

  function carregarSaldo(params) {
    $.ajax({
      url: '../public/estoque/saldo.php',
      dataType: 'json',
      data: params,
      success: function (res) {
        $('#sbSaldo').text((res && typeof res.saldo !== 'undefined') ? res.saldo : '—');
      }
    });
  }

  function carregarMov(params) {
    $.ajax({
      url: '../public/estoque/listar.php',
      dataType: 'json',
      data: params,
      success: function (rows) {
        const $tb = $('#movTable tbody');
        $tb.empty();

        let achouE = false, achouS = false;
        if (Array.isArray(rows) && rows.length) {
          rows.forEach(function (m) {
            const t = String(m.tipo || '').toUpperCase();
            const rotulo = (t === 'E') ? 'ENTRADA' : (t === 'S') ? 'SAÍDA' : t;

            if (!achouE && rotulo === 'ENTRADA') { $('#sbUltEntrada').text(`${m.qtde} un em ${m.data}`); achouE = true; }
            if (!achouS && rotulo === 'SAÍDA')   { $('#sbUltSaida').text(`${m.qtde} un em ${m.data}`);   achouS = true; }

            $tb.append(`
              <tr>
                <td class='text-center'>${m.estoque_movimento}</td>
                <td class='text-center'>${m.data}</td>
                <td class='text-center'>${rotulo}</td>
                <td class='text-center'>${m.qtde}</td>
                <td class='text-center'>${m.os ?? ''}</td>
                <td class='text-center'>${m.motivo ?? ''}</td>
                <td class='text-center'>${m.item}</td>
              </tr>
            `);
          });
        } else {
          $tb.html('<tr><td colspan="7" class="text-center text-muted">Sem lançamentos</td></tr>');
        }
        if (!achouE) $('#sbUltEntrada').text('—');
        if (!achouS) $('#sbUltSaida').text('—');
      }
    });
  }

  $("#busca_produto").autocomplete({
    minLength: 3,
    source: function (request, response) {
      $.ajax({
        url: "../public/produto/autocomplete.php",
        dataType: "json",
        data: { term: request.term },
        success: function (data) { response(data); }
      });
    },
    select: function (event, ui) {
      $("#busca_produto").val(ui.item.value);
      $("#produto").val(ui.item.produto);
      $("#peca").val(''); $("#busca_peca").val('');
      carregarSaldo({ produto: ui.item.produto });
      carregarMov({ produto: ui.item.produto });
      return false;
    }
  });

  $("#busca_peca").autocomplete({
    minLength: 3,
    source: function (request, response) {
      $.ajax({
        url: "../public/peca/autocomplete.php",
        dataType: "json",
        data: { term: request.term },
        success: function (data) { response(data); }
      });
    },
    select: function (event, ui) {
      $("#busca_peca").val(ui.item.value);
      $("#peca").val(ui.item.peca);
      $("#produto").val(''); $("#busca_produto").val('');
      carregarSaldo({ peca: ui.item.peca });
      carregarMov({ peca: ui.item.peca });
      return false;
    }
  });

  $('#movForm').on('submit', function (e) {
    e.preventDefault();

    const tipo  = $('input[name="tipo"]:checked').val();
    const qtde  = $('#qtde').val();
    const os    = $('#os').val();
    const motivo= $('#motivo').val();
    const produto = $('#produto').val();
    const peca    = $('#peca').val();

    if (!produto && !peca) {
      Alerta.warning('Selecione um produto ou uma peça pelo autocomplete.', 'Atenção');
      return;
    }
    if (!qtde || parseInt(qtde) <= 0) {
      Alerta.warning('Quantidade deve ser maior que zero.', 'Atenção');
      return;
    }

    const dados = { tipo, qtde, os, motivo, produto: produto || '', peca: peca || '' };

    $.ajax({
      url: '../public/estoque/cadastrar.php',
      method: 'POST',
      dataType: 'json',
      data: dados,
      success: function (resp) {
        if (resp.status === 'success') {
          Alerta.success(resp.message, 'Sucesso');
          $('#qtde').val(''); $('#motivo').val('');
          if (produto) { carregarSaldo({ produto }); carregarMov({ produto }); }
          if (peca)    { carregarSaldo({ peca });    carregarMov({ peca }); }
        } else {
          Alerta.error(resp.message || 'Falha ao lançar movimentação.', resp.title || 'Erro');
        }
      },
      error: function () {
        Alerta.error('Erro ao comunicar com o servidor.');
      }
    });
  });

  // --- eventos iniciais
  $('#tpProduto, #tpPeca').on('change', alternarTipo);
  limparPainel();
  alternarTipo();
});
