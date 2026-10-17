<div class="modal fade" id="modalLupaGenerica" tabindex="-1" aria-labelledby="modalLupaGenericaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content shadow border-0">
      <div class="modal-header py-2 px-3">
        <h6 class="modal-title mb-0" id="modalLupaGenericaLabel">
          <i class="bi bi-search me-2 text-primary"></i><span id="modalLupaTitulo">Pesquisar</span>
        </h6>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body p-3">
        <div class="input-group mb-3">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="search" id="modalLupaInput" class="form-control" placeholder="Digite para buscar..." autocomplete="off">
          <button type="button" id="modalLupaBtnBuscar" class="btn btn-primary btn-sm px-3">Buscar</button>
        </div>
        <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
          <table class="table table-hover table-striped table-bordered align-middle w-100" id="tabelaModalLupa">
            <thead class="table-light sticky-top" id="theadModalLupa"></thead>
            <tbody id="tbodyModalLupa">
              <tr><td colspan="10" class="text-center text-muted py-4">Clique em Buscar ou digite para pesquisar.</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer py-2 px-3">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>
