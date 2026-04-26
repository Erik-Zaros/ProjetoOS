<div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalUsuarioLabel">
          <i class="fas fa-user-circle me-2"></i> Meu Perfil
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
          <small class="text-muted d-block">Nome</small>
          <strong><?= htmlspecialchars($usuarioNome) ?></strong>
        </div>
        <div class="mb-2">
          <small class="text-muted d-block">Login</small>
          <span><?= htmlspecialchars($usuarioLogin) ?></span>
        </div>
        <div class="mb-2">
          <small class="text-muted d-block">Perfil</small>
          <span><?= htmlspecialchars($usuarioTipo) ?></span>
        </div>
        <div>
          <small class="text-muted d-block">Posto</small>
          <span><?= htmlspecialchars((string) $postoNome); ?></span>
        </div>
      </div>
      <div class="modal-footer">
        <a href="../logout.php" class="btn btn-outline-secondary">Sair</a>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>
