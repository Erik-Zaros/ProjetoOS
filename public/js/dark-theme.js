(function () {
  'use strict';

  const API_URL  = '../public/tema/tema.php';
  const LS_KEY   = 'projetoos_tema';
  const DARK_CLS = 'dark-mode';

  const savedLocal = localStorage.getItem(LS_KEY);
  if (savedLocal === 'dark') {
    document.body.classList.add(DARK_CLS);
  }

  function applyTheme(tema) {
    if (tema === 'dark') {
      document.body.classList.add(DARK_CLS);
    } else {
      document.body.classList.remove(DARK_CLS);
    }
    localStorage.setItem(LS_KEY, tema);
    syncToggleUI(tema);
  }

  function syncToggleUI(tema) {
    const toggle = document.getElementById('temaToggle');
    if (toggle) {
      toggle.checked = tema === 'dark';
    }

    const icon  = document.getElementById('temaIcon');
    const label = document.getElementById('temaLabel');
    if (icon)  icon.className  = tema === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
    if (label) label.textContent = tema === 'dark' ? 'Modo Escuro' : 'Modo Claro';
  }

  function loadTemaFromServer() {
    fetch(API_URL, { method: 'GET', credentials: 'same-origin' })
      .then(r => r.json())
      .then(data => {
        applyTheme(data.tema ?? 'light');
      })
      .catch(() => {
        applyTheme(savedLocal ?? 'light');
      });
  }

  function saveTemaToServer(tema) {
    fetch(API_URL, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ tema })
    }).catch(() => { /* silencioso */ });
  }

  window.ProjetoOSTema = {
    toggle: function () {
      const isDark = document.body.classList.contains(DARK_CLS);
      const novo   = isDark ? 'light' : 'dark';
      applyTheme(novo);
      saveTemaToServer(novo);
    },
    set: function (tema) {
      applyTheme(tema);
      saveTemaToServer(tema);
    },
    get: function () {
      return document.body.classList.contains(DARK_CLS) ? 'dark' : 'light';
    }
  };

  document.addEventListener('DOMContentLoaded', function () {
    loadTemaFromServer();

    const toggle = document.getElementById('temaToggle');
    if (toggle) {
      toggle.addEventListener('change', function () {
        const tema = this.checked ? 'dark' : 'light';
        applyTheme(tema);
        saveTemaToServer(tema);
      });
    }
  });
})();
