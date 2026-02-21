const TdocsWidget = (function () {

  let _cfg       = {};
  let _hashesTemp = [];

  function init(cfg) {
    _cfg        = cfg;
    _hashesTemp = [];
    _renderCard();
    _carregarTipos();
    if (_cfg.referenciaId) {
      _listar();
    }
  }

  function getHashesTemp() {
    return [..._hashesTemp];
  }

  function vincular(referenciaId, hashes) {
    if (!hashes || hashes.length === 0) return;
    $.ajax({
      url: '../public/tdocs/vincular_temp.php',
      method: 'POST',
      data: {
        hashes:         JSON.stringify(hashes),
        referencia_id:  referenciaId,
        contexto_anexo: _cfg.contextoAnexo,
      },
      dataType: 'json',
    });
  }

  function _renderCard() {
    $(_cfg.container).html(`
      <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
          <i class="bi bi-paperclip"></i> Anexos
        </div>
        <div class="card-body">
          <div class="row mb-3 align-items-end">
            <div class="col-md-4">
              <label class="form-label">Tipo de Anexo</label>
              <select class="form-select" id="tdocsTipo"></select>
            </div>
            <div class="col-md-5">
              <label class="form-label">Arquivo</label>
              <input type="file" class="form-control" id="tdocsArquivo">
            </div>
            <div class="col-md-3">
              <button type="button" class="btn btn-warning w-100" id="btnEnviarAnexo">
                <i class="bi bi-upload"></i> Enviar
              </button>
            </div>
          </div>
          <small class="text-muted">Máx. 15 MB — PDF, JPG, PNG, DOCX, XLSX, TXT</small>
          <table class="table table-bordered table-striped table-hover mt-3" id="tabelaAnexos">
            <thead>
              <tr>
                <th>Tipo</th>
                <th>Arquivo</th>
                <th>Tamanho</th>
                <th>Data</th>
                <th>Ação</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>

      <!-- Modal visualização de imagem (Shadowbox) -->
      <div class="modal fade" id="tdocsImagemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width:85vw;">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="tdocsImagemModalLabel">Visualizar Anexo</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" style="min-height:60vh;">
              <img id="tdocsImagemPreview" src="" alt="" class="img-fluid rounded" style="max-height:75vh;">
            </div>
            <div class="modal-footer">
              <a id="tdocsImagemDownload" href="#" target="_blank" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-download"></i> Baixar
              </a>
              <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
            </div>
          </div>
        </div>
      </div>
    `);

    $('#btnEnviarAnexo').on('click', _upload);
  }

  function _carregarTipos() {
    $.ajax({
      url: '../public/tipo_anexo/listar.php',
      method: 'GET',
      data: { contexto_anexo: _cfg.contextoAnexo },
      dataType: 'json',
      success: function (tipos) {
        $('#tdocsTipo').empty().append('<option value="">Selecione o tipo</option>');
        tipos.forEach(function (t) {
          if (t.ativo === 't' || t.ativo === true) {
            $('#tdocsTipo').append(`<option value="${t.tipo_anexo}">${t.descricao}</option>`);
          }
        });
      },
    });
  }

  function _listar() {
    $.ajax({
      url: '../public/tdocs/listar.php',
      method: 'GET',
      data: { referencia_id: _cfg.referenciaId, contexto_anexo: _cfg.contextoAnexo },
      dataType: 'json',
      success: function (docs) {
        _renderLinhas(docs);
      },
    });
  }

  function _renderLinhas(docs) {
    const $tbody = $('#tabelaAnexos tbody');
    $tbody.empty();

    if (!docs.length) {
      $tbody.append('<tr><td colspan="5" class="text-center text-muted">Nenhum anexo.</td></tr>');
      return;
    }

    docs.forEach(function (d) {
      const tamanho    = _formatarTamanho(parseInt(d.tamanho));
      const data       = (d.data_input || '').substring(0, 16).replace('T', ' ');
      const ehImagem   = ['jpg','jpeg','png','gif','webp'].includes((d.extensao || '').toLowerCase());
      const urlDownload = `../public/tdocs/download.php?tdocs=${d.tdocs}`;

      const linkNome = ehImagem
        ? `<a href="#" class="tdocs-ver-imagem text-decoration-none"
              data-url="${urlDownload}&inline=1"
              data-nome="${d.nome_original}"
              data-download="${urlDownload}">
             <i class="bi ${_iconeExt(d.extensao)}"></i> ${d.nome_original}
           </a>`
        : `<a href="${urlDownload}" target="_blank" class="text-decoration-none">
             <i class="bi ${_iconeExt(d.extensao)}"></i> ${d.nome_original}
           </a>`;

      $tbody.append(`
        <tr>
          <td>${d.tipo_descricao}</td>
          <td>${linkNome}</td>
          <td>${tamanho}</td>
          <td>${data}</td>
          <td>
            <a href="${urlDownload}" class="btn btn-sm btn-primary" title="Baixar">
              <i class="bi bi-download"></i>
            </a>
            <button type="button" class="btn btn-sm btn-danger ms-1 btnRemoverAnexo"
                    data-id="${d.tdocs}" title="Remover">
              <i class="bi bi-trash3-fill"></i>
            </button>
          </td>
        </tr>
      `);
    });

    $(document).off('click', '.tdocs-ver-imagem').on('click', '.tdocs-ver-imagem', function (e) {
      e.preventDefault();
      $('#tdocsImagemPreview').attr('src', $(this).data('url'));
      $('#tdocsImagemModalLabel').text($(this).data('nome'));
      $('#tdocsImagemDownload').attr('href', $(this).data('download'));
      $('#tdocsImagemModal').modal('show');
    });

    $(document).off('click', '.btnRemoverAnexo').on('click', '.btnRemoverAnexo', function () {
      const id  = $(this).data('id');
      const $tr = $(this).closest('tr');
      Swal.fire({
        title: 'Remover anexo?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim',
        cancelButtonText: 'Cancelar',
      }).then((result) => {
        if (!result.isConfirmed) return;
        $.ajax({
          url: '../public/tdocs/remover.php',
          method: 'POST',
          data: { tdocs: id },
          dataType: 'json',
          success: function (res) {
            if (res.status === 'success') {
              $tr.remove();
            } else {
              Swal.fire('Erro', res.message, 'error');
            }
          },
        });
      });
    });
  }

  function _upload() {
    const tipo      = $('#tdocsTipo').val();
    const fileInput = document.getElementById('tdocsArquivo');

    if (!fileInput.files.length) {
      Swal.fire('Atenção', 'Selecione um arquivo.', 'warning');
      return;
    }

    if (!tipo) {
      Swal.fire('Atenção', 'Selecione o tipo de anexo.', 'warning');
      return;
    }

    const formData = new FormData();
    formData.append('arquivo',        fileInput.files[0]);
    formData.append('tipo_anexo',     tipo);
    formData.append('contexto_anexo', _cfg.contextoAnexo);

    const isEdicao = !!_cfg.referenciaId;
    const url      = isEdicao
      ? '../public/tdocs/upload_direto.php'
      : '../public/tdocs/upload_temp.php';

    if (isEdicao) formData.append('referencia_id', _cfg.referenciaId);

    $('#btnEnviarAnexo').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

    $.ajax({
      url: url, method: 'POST', data: formData,
      processData: false, contentType: false, dataType: 'json',
      success: function (res) {
        if (res.status === 'success') {
          if (!isEdicao) {
            _hashesTemp.push(res.hash_temp);
            _adicionarLinhaTemp(res);
          } else {
            _listar();
          }
          $('#tdocsArquivo').val('');
          Swal.fire({ icon: 'success', title: 'Enviado!', timer: 1500, showConfirmButton: false });
        } else {
          Swal.fire('Erro', res.message, 'error');
        }
      },
      error: function () {
        Swal.fire('Erro', 'Falha na comunicação com o servidor.', 'error');
      },
      complete: function () {
        $('#btnEnviarAnexo').prop('disabled', false).html('<i class="bi bi-upload"></i> Enviar');
      },
    });
  }

  function _adicionarLinhaTemp(res) {
    const $tbody   = $('#tabelaAnexos tbody');
    const tipoDesc = $('#tdocsTipo option:selected').text();
    const ehImagem = ['jpg','jpeg','png','gif','webp'].includes((res.extensao || '').toLowerCase());

    $tbody.find('tr td[colspan]').parent().remove();

    const nomeHtml = `<i class="bi ${_iconeExt(res.extensao)}"></i> ${res.nome}`;

    $tbody.append(`
      <tr data-hash="${res.hash_temp}">
        <td>${tipoDesc}</td>
        <td>${nomeHtml}</td>
        <td>—</td>
        <td>agora</td>
        <td>
          <button type="button" class="btn btn-sm btn-danger btnRemoverTemp">
            <i class="bi bi-trash3-fill"></i>
          </button>
        </td>
      </tr>
    `);

    $(document).off('click', '.btnRemoverTemp').on('click', '.btnRemoverTemp', function () {
      const hash = $(this).closest('tr').data('hash');
      _hashesTemp = _hashesTemp.filter(h => h !== hash);
      $(this).closest('tr').remove();
    });
  }

  function _formatarTamanho(bytes) {
    if (!bytes) return '—';
    if (bytes < 1024)    return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
  }

  function _iconeExt(ext) {
    const mapa = {
      pdf:  'bi-file-earmark-pdf text-danger',
      jpg:  'bi-file-earmark-image text-success',
      jpeg: 'bi-file-earmark-image text-success',
      png:  'bi-file-earmark-image text-success',
      gif:  'bi-file-earmark-image text-success',
      webp: 'bi-file-earmark-image text-success',
      doc:  'bi-file-earmark-word text-primary',
      docx: 'bi-file-earmark-word text-primary',
      xls:  'bi-file-earmark-excel text-success',
      xlsx: 'bi-file-earmark-excel text-success',
      txt:  'bi-file-earmark-text',
    };
    return mapa[(ext || '').toLowerCase()] || 'bi-file-earmark';
  }

  return { init, getHashesTemp, vincular };

})();

