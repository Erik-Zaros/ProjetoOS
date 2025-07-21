<style>
  .modal-custom-size {
    max-width: 85vw;
    height: 90vh;
  }

  .modal-header {
    background-color: #2e2e48 !important;
  }

</style>

<div class="modal fade" id="logAuditorModal" tabindex="-1" aria-labelledby="modalLogLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable modal-custom-size">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalLogLabel">Histórico de Alterações</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-striped table-hover">
          <thead class="table-secondary">
            <tr>
              <th>Data</th>
              <th>Usuário</th>
              <th>Ação</th>
              <th>Antes</th>
              <th>Depois</th>
            </tr>
          </thead>
          <tbody id="logAuditorBody">
            <tr><td colspan="5" class="text-center">Carregando...</td></tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>
