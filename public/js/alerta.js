(function (global) {
  'use strict';

  const Alerta = {
    useToast: true, // alerta canto da tela

    timer: 3000,

    show: function (opts) {
      if (typeof opts === 'string') {
        opts = { message: opts, type: 'info' };
      }

      const icon = (opts.icon || opts.type || 'info') === 'danger' ? 'error' : (opts.icon || opts.type || 'info');
      const title = opts.title || '';
      const text = opts.text || opts.message || '';

      if (global.Swal) {
        if (this.useToast) {
          return global.Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: title ? title + (text ? ': ' + text : '') : text,
            showConfirmButton: false,
            timer: opts.timer || this.timer,
            timerProgressBar: true
          });
        } else {
          return global.Swal.fire({
            position: 'center',
            icon: icon,
            title: title || (icon === 'success' ? 'Sucesso' : icon === 'error' ? 'Erro' : 'Atenção'),
            text: text,
            confirmButtonText: 'OK',
            confirmButtonColor: '#0d6efd',
            timer: icon === 'success' ? (opts.timer || 2500) : null
          });
        }
      } else {
        alert((title ? title + '\n' : '') + text);
        return Promise.resolve({ isConfirmed: true });
      }
    },

    confirm: function (opts) {
      opts = opts || {};
      const title = opts.title || 'Confirmar ação';
      const text = opts.text || opts.message || 'Deseja continuar?';
      const confirmText = opts.confirmButtonText || opts.confirmText || 'Confirmar';
      const cancelText = opts.cancelButtonText || opts.cancelText || 'Cancelar';
      const icon = opts.icon || 'warning';

      if (global.Swal) {
        if (this.useToast) {
          return global.Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: title,
            text: text,
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            confirmButtonColor: icon === 'error' ? '#dc3545' : '#0d6efd',
            cancelButtonColor: '#6c757d',
            reverseButtons: true
          });
        } else {
          return global.Swal.fire({
            position: 'center',
            icon: icon,
            title: title,
            text: text,
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            confirmButtonColor: icon === 'error' ? '#dc3545' : '#0d6efd',
            cancelButtonColor: '#6c757d',
            reverseButtons: true
          });
        }
      } else {
        const ok = confirm(title + '\n' + text);
        return Promise.resolve({ isConfirmed: ok, isDismissed: !ok });
      }
    },

    success: function (msg, title) {
      return this.show({ message: msg, title: title, type: 'success' });
    },

    error: function (msg, title) {
      return this.show({ message: msg, title: title, type: 'error' });
    },

    warning: function (msg, title) {
      return this.show({ message: msg, title: title, type: 'warning' });
    },

    info: function (msg, title) {
      return this.show({ message: msg, title: title, type: 'info' });
    }
  };

  global.Alerta = Alerta;
  global.Toast = Alerta;
  global.AppAlert = Alerta;
  global.AppToast = Alerta;

})(window);
