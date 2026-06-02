$(document).ready(function () {

  const CAMPOS_ESPECIAIS = {
    data_input:     'Data Criação',
    data_log:       'Data Log',
    data_alteracao: 'Data Alteração',
    data_cadastro:  'Data Cadastro',
    data_nascimento:'Data Nascimento',
    id:             'ID',
    id_registro:    'ID Registro',
    posto:          'Posto',
    usuario:        'Usuário',
  };

  function traduzirCampo(campo) {
    if (CAMPOS_ESPECIAIS[campo]) return CAMPOS_ESPECIAIS[campo];
    return campo
      .split('_')
      .map(p => p.charAt(0).toUpperCase() + p.slice(1))
      .join(' ');
  }

  function traduzirValor(valor) {
    if (valor === null || valor === undefined) {
      return '<em class="text-muted">Vazio</em>';
    }
    if (valor === true  || valor === 'true'  || valor === 't') {
      return '<span class="badge bg-success">Sim</span>';
    }
    if (valor === false || valor === 'false' || valor === 'f') {
      return '<span class="badge bg-secondary">Não</span>';
    }
    if (typeof valor !== 'string') return valor;
    const v = valor.trim().toLowerCase();
    if (v === 't' || v === 'true')  return '<span class="badge bg-success">Sim</span>';
    if (v === 'f' || v === 'false') return '<span class="badge bg-secondary">Não</span>';
    if (v === '')                   return '<em class="text-muted">Vazio</em>';
    return valor;
  }

  function acaoBadge(acao) {
    const mapa = {
      insert: '<span class="badge bg-success">Inserção</span>',
      update: '<span class="badge bg-warning text-dark">Atualização</span>',
      delete: '<span class="badge bg-danger">Exclusão</span>',
    };
    return mapa[acao] ?? `<span class="badge bg-secondary">${acao}</span>`;
  }

  function montarDiff(acao, antesObj, depoisObj) {
    const IGNORAR = new Set(['id']);

    function celulaTabela(camposValores) {
      if (!camposValores || Object.keys(camposValores).length === 0) {
        return '<em class="text-muted">—</em>';
      }
      let html = '<table class="table table-sm table-bordered mb-0" style="font-size:0.78rem;">';
      for (const [campo, valor] of Object.entries(camposValores)) {
        html += `<tr>
          <td class="fw-semibold text-nowrap text-secondary" style="width:45%;">${traduzirCampo(campo)}</td>
          <td>${traduzirValor(valor)}</td>
        </tr>`;
      }
      html += '</table>';
      return html;
    }

    if (acao === 'insert') {
      const exibir = {};
      for (const k in depoisObj) {
        if (!IGNORAR.has(k)) exibir[k] = depoisObj[k];
      }
      return { antesHtml: '<em class="text-muted">—</em>', depoisHtml: celulaTabela(exibir) };
    }

    if (acao === 'delete') {
      const exibir = {};
      for (const k in antesObj) {
        if (!IGNORAR.has(k)) exibir[k] = antesObj[k];
      }
      return { antesHtml: celulaTabela(exibir), depoisHtml: '<em class="text-muted">—</em>' };
    }

    const antes  = {};
    const depois = {};

    const todasChaves = new Set([...Object.keys(antesObj  || {}), ...Object.keys(depoisObj || {}),]);

    for (const k of todasChaves) {
      if (IGNORAR.has(k)) continue;

      const valAntes  = antesObj?.[k]  ?? null;
      const valDepois = depoisObj?.[k] ?? null;

      function normalizar(v) {
        if (v === null || v === undefined) return '';
        const s = String(v).trim().toLowerCase();
        if (s === 't' || s === 'true')  return 'true';
        if (s === 'f' || s === 'false') return 'false';
        return s;
      }

      if (normalizar(valAntes) !== normalizar(valDepois)) {
        antes[k]  = valAntes;
        depois[k] = valDepois;
      }
    }

    return { antesHtml: celulaTabela(antes), depoisHtml: celulaTabela(depois) };
  }

  $(document).on('click', '.btn-log-auditor', function () {
    const tabela = $(this).data('tabela');
    const id     = $(this).data('id');
    if (!tabela || !id) return;

    const $modal   = $('#modalLogAuditor');
    const $spinner = $('#logAuditorSpinner');
    const $wrap    = $('#logAuditorTableWrap');
    const $body    = $('#logAuditorBody');

    $body.empty();
    $spinner.show();
    $wrap.hide();
    $modal.modal('show');

    $.post('../public/logAuditor/buscar.php', { tabela, id }, function (response) {
      let linhas = '';

      if (response.logs && response.logs.length > 0) {
        response.logs.forEach(log => {
          const antesObj  = log.antes  ? JSON.parse(log.antes)  : null;
          const depoisObj = log.depois ? JSON.parse(log.depois) : null;
          const { antesHtml, depoisHtml } = montarDiff(log.acao, antesObj, depoisObj);

          linhas += `
            <tr>
              <td class="text-nowrap">${log.data_log}</td>
              <td class="text-nowrap">${log.usuario_nome}</td>
              <td class="text-nowrap">${acaoBadge(log.acao)}</td>
              <td>${antesHtml}</td>
              <td>${depoisHtml}</td>
            </tr>`;
        });
      } else {
        linhas = '<tr><td colspan="5" class="text-center text-muted py-3">Nenhum log encontrado.</td></tr>';
      }

      $body.html(linhas);
      $spinner.hide();
      $wrap.show();

    }).fail(function () {
      $body.html('<tr><td colspan="5" class="text-danger text-center py-3">Erro ao buscar logs.</td></tr>');
      $spinner.hide();
      $wrap.show();
    });
  });

});