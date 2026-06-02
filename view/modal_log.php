<div class="modal fade" id="modalLogAuditor" tabindex="-1"
  aria-labelledby="modalLogAuditorLabel"
  aria-hidden="true"
>
  <div class="modal-dialog modal-dialog modal-xl ">
    <div class="modal-content">

      <div class="modal-header py-2 px-3">
        <h6 class="modal-title mb-0" id="modalLogAuditorLabel">
          <i class="fas fa-history me-2 text-primary"></i>Histórico de Alterações
        </h6>
        <button type="button" class="close ms-auto" data-bs-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body p-0" style="max-height: 65vh; overflow-y: auto;">

        <div id="logAuditorSpinner" class="text-center py-4">
          <div class="spinner-border spinner-border-sm text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
          </div>
          <p class="text-muted mt-2 mb-0" style="font-size:0.78rem;">Buscando registros...</p>
        </div>

        <div id="logAuditorTableWrap" class="table-responsive" style="display:none;">
          <table class="table table-bordered table-striped table-hover table-sm mb-0" style="font-size:0.78rem;">
            <thead class="table-dark">
              <tr>
                <th style="min-width:110px; white-space:nowrap;">Data</th>
                <th style="min-width:100px; white-space:nowrap;">Usuário</th>
                <th style="min-width:80px;  white-space:nowrap;">Ação</th>
                <th style="min-width:180px;">Antes</th>
                <th style="min-width:180px;">Depois</th>
              </tr>
            </thead>
            <tbody id="logAuditorBody"></tbody>
          </table>
        </div>

      </div>

      <div class="modal-footer py-2 px-3">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i>Fechar
        </button>
      </div>

    </div>
  </div>
</div>

<style>
  #modalLogAuditor .badge {
    font-size: 0.75rem;
    padding: 0.35em 0.6em;
  }
  #modalLogAuditor .table-sm td {
    padding: 0.25rem 0.4rem !important;
    vertical-align: middle;
  }
  #modalLogAuditor .modal-body {
    overscroll-behavior: contain;
  }
</style>