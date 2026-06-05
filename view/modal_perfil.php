<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalUsuarioLabel">
          <i class="fas fa-user-circle me-2"></i> Meu Perfil
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <small class="text-muted d-block">Nome</small>
          <strong><?= htmlspecialchars($usuarioNome) ?></strong>
        </div>
        <div class="mb-3">
          <small class="text-muted d-block">Login</small>
          <span><?= htmlspecialchars($usuarioLogin) ?></span>
        </div>
        <div class="mb-3">
          <small class="text-muted d-block">Perfil</small>
          <span><?= htmlspecialchars($usuarioTipo) ?></span>
        </div>
        <div class="mb-4">
          <small class="text-muted d-block">Posto</small>
          <span><?= htmlspecialchars((string) $postoNome) ?></span>
        </div>
        <hr>
        <div class="mb-1">
          <small class="text-muted d-block mb-2">Aparência</small>
          <div class="theme-toggle-wrap">
            <label class="theme-toggle" title="Alternar tema">
              <input type="checkbox" id="temaToggle">
              <span class="theme-slider"></span>
            </label>
            <span class="theme-toggle-label">
              <i id="temaIcon" class="fas fa-sun"></i>
              <span id="temaLabel">Modo Claro</span>
            </span>
          </div>
        </div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
        <a href="../logout.php" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt me-1"></i> Deslogar</a>
      </div>
    </div>
  </div>
</div>