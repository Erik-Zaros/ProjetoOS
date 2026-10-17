<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content shadow border-0">
      <div class="modal-header">
        <h5 class="modal-title fs-5" id="modalUsuarioLabel">
          <i class="bi bi-person-circle me-2 text-primary"></i> Meu Perfil
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <small class="text-body-secondary d-block">Nome</small>
          <strong class="fs-6"><?= htmlspecialchars($usuarioNome) ?></strong>
        </div>
        <div class="mb-3">
          <small class="text-body-secondary d-block">Login</small>
          <span><?= htmlspecialchars($usuarioLogin) ?></span>
        </div>
        <div class="mb-3">
          <small class="text-body-secondary d-block">Perfil</small>
          <span><?= htmlspecialchars($usuarioTipo) ?></span>
        </div>
        <div class="mb-4">
          <small class="text-body-secondary d-block">Posto</small>
          <span><?= htmlspecialchars((string) $postoNome) ?></span>
        </div>
        <hr>
        <div class="mb-1">
          <small class="text-body-secondary d-block mb-2">Aparência</small>
          <div class="form-check form-switch d-flex align-items-center gap-2 ps-0">
            <input class="form-check-input ms-0" type="checkbox" role="switch" id="temaToggle" style="cursor: pointer; width: 2.5rem; height: 1.3rem;">
            <label class="form-check-label d-flex align-items-center gap-2 fw-medium" for="temaToggle" style="cursor: pointer;">
              <i id="temaIcon" class="bi bi-sun-fill"></i>
              <span id="temaLabel">Modo Claro</span>
            </label>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
        <a href="../logout.php" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right me-1"></i> Deslogar</a>
      </div>
    </div>
  </div>
</div>