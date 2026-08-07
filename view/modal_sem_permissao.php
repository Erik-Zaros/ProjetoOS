<div class="modal fade" id="modalSemPermissao" tabindex="-1"
  aria-labelledby="modalSemPermissaoLabel"
  aria-hidden="true"
  data-bs-backdrop="static"
  data-bs-keyboard="false"
>
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header py-3 px-4">
        <h5 class="modal-title mb-0" id="modalSemPermissaoLabel">
          <i class="fas fa-lock me-2 text-danger"></i>Acesso Restrito
        </h5>
      </div>
      <div class="modal-body text-center py-5">
        <i class="fas fa-triangle-exclamation text-warning mb-4" style="font-size: 4.5rem;"></i>
        <p class="mb-2" style="font-size: 1.25rem;">
          Seu usuário não possui acesso a este módulo.
        </p>
        <p class="text-muted mb-0" style="font-size: 1rem;">
          Você será redirecionado em <span id="contadorRedirect" class="fw-bold text-primary">5</span> segundos...
        </p>
      </div>
      <div class="modal-footer py-3 px-4 justify-content-center">
        <a href="menu" class="btn btn-primary btn-lg">
          <i class="fas fa-arrow-right me-1"></i>Ir agora
        </a>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const destino = 'menu';
  let segundos = 5;

  const modalEl = document.getElementById('modalSemPermissao');
  if (!modalEl) return;

  const modal = new bootstrap.Modal(modalEl);
  const contadorEl = document.getElementById('contadorRedirect');

  modal.show();

  const interval = setInterval(function () {
    segundos--;
    if (contadorEl) contadorEl.textContent = segundos;

    if (segundos <= 0) {
      clearInterval(interval);
      window.location.href = destino;
    }
  }, 1000);
});
</script>