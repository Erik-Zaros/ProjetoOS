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
                <td>${m.estoque_movimento}</td>
                <td>${m.data}</td>
                <td>${rotulo}</td>
                <td>${m.qtde}</td>
                <td>${m.os ?? ''}</td>
                <td>${m.motivo ?? ''}</td>
                <td>${m.item}</td>
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
      Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Selecione um produto ou uma peça pelo autocomplete.' });
      return;
    }
    if (!qtde || parseInt(qtde) <= 0) {
      Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Quantidade deve ser maior que zero.' });
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
          Swal.fire({ icon: 'success', title: 'Sucesso', text: resp.message });
          $('#qtde').val(''); $('#motivo').val('');
          if (produto) { carregarSaldo({ produto }); carregarMov({ produto }); }
          if (peca)    { carregarSaldo({ peca });    carregarMov({ peca }); }
        } else {
          Swal.fire({ icon: 'error', title: resp.title || 'Erro', text: resp.message || 'Falha ao lançar movimentação.' });
        }
      },
      error: function () {
        Swal.fire({ icon: 'error', title: 'Erro', text: 'Erro ao comunicar com o servidor.' });
      }
    });
  });

  // --- eventos iniciais
  $('#tpProduto, #tpPeca').on('change', alternarTipo);
  limparPainel();
  alternarTipo();
});
