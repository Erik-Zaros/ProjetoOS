document.addEventListener('DOMContentLoaded', function () {
  if (window.flatpickr && window.flatpickr.l10ns && window.flatpickr.l10ns.pt) {
    flatpickr.localize(flatpickr.l10ns.pt);
  }

  const sidebarWrapper = document.querySelector('.sidebar-wrapper');
  if (sidebarWrapper && window.OverlayScrollbarsGlobal?.OverlayScrollbars && window.innerWidth > 992) {
    OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
      scrollbars: {
        theme: 'os-theme-light',
        autoHide: 'leave',
        clickScroll: true
      }
    });
  }


  const searchInput = document.getElementById('sidebarSearch');
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      const term = this.value.toLowerCase().trim();
      const menuItems = document.querySelectorAll('#menuSidebar .nav-item');
      menuItems.forEach(function (item) {
        if (!term) {
          item.style.display = '';
          return;
        }
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(term) ? '' : 'none';
      });
    });
  }

  document.addEventListener('invalid', function (e) {
    const input = e.target;
    if (!input || !input.validity) return;

    if (input.validity.valueMissing) {
      input.setCustomValidity('Por favor, preencha este campo.');
    } else if (input.validity.typeMismatch) {
      if (input.type === 'email') {
        input.setCustomValidity('Por favor, insira um endereço de e-mail válido.');
      } else if (input.type === 'url') {
        input.setCustomValidity('Por favor, insira uma URL válida.');
      } else {
        input.setCustomValidity('Por favor, insira um valor válido.');
      }
    } else if (input.validity.patternMismatch) {
      input.setCustomValidity('Por favor, preencha no formato solicitado.');
    } else if (input.validity.tooShort) {
      input.setCustomValidity(`Preencha ao menos ${input.minLength} caracteres.`);
    } else if (input.validity.tooLong) {
      input.setCustomValidity(`Máximo permitido de ${input.maxLength} caracteres.`);
    } else if (input.validity.rangeUnderflow) {
      input.setCustomValidity(`O valor mínimo permitido é ${input.min}.`);
    } else if (input.validity.rangeOverflow) {
      input.setCustomValidity(`O valor máximo permitido é ${input.max}.`);
    } else if (input.validity.stepMismatch) {
      input.setCustomValidity('Por favor, insira um valor válido para este campo.');
    } else if (input.validity.badInput) {
      input.setCustomValidity('Por favor, insira um número válido.');
    }
  }, true);

  document.addEventListener('input', function (e) {
    if (e.target && typeof e.target.setCustomValidity === 'function') {
      e.target.setCustomValidity('');
    }
  }, true);

  document.querySelectorAll('.modal-dialog-centered').forEach(function (modalDialog) {
    if (!modalDialog.closest('.swal2-container') && !modalDialog.classList.contains('modal-xl')) {
      modalDialog.classList.add('modal-xl');
    }
  });
});
