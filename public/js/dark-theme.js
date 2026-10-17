(function () {
  'use strict';

  const API_URL = '../public/tema/tema.php';
  const LS_KEY  = 'projetoos_tema';

  function applyTheme(tema) {
    const isDark = tema === 'dark';
    document.documentElement.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
    localStorage.setItem(LS_KEY, isDark ? 'dark' : 'light');
    syncToggleUI(isDark ? 'dark' : 'light');
  }

  function syncToggleUI(tema) {
    const isDark = tema === 'dark';

    const navIcon = document.getElementById('navTemaIcon');
    if (navIcon) {
      navIcon.className = isDark ? 'bi bi-sun-fill text-warning' : 'bi bi-moon-stars-fill';
    }

    const toggle = document.getElementById('temaToggle');
    if (toggle) {
      toggle.checked = isDark;
    }

    const icon  = document.getElementById('temaIcon');
    const label = document.getElementById('temaLabel');
    if (icon)  icon.className  = isDark ? 'bi bi-moon-stars-fill text-warning' : 'bi bi-sun-fill';
    if (label) label.textContent = isDark ? 'Modo Escuro' : 'Modo Claro';
  }

  function loadTemaFromServer() {
    fetch(API_URL, { method: 'GET', credentials: 'same-origin' })
      .then(r => r.json())
      .then(data => {
        if (data.tema) {
          applyTheme(data.tema);
        }
      })
      .catch(() => {
        const saved = localStorage.getItem(LS_KEY);
        if (saved) {
          applyTheme(saved);
        }
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
      const current = document.documentElement.getAttribute('data-bs-theme') || 'light';
      const novo = current === 'dark' ? 'light' : 'dark';
      applyTheme(novo);
      saveTemaToServer(novo);
    },
    set: function (tema) {
      applyTheme(tema);
      saveTemaToServer(tema);
    },
    get: function () {
      return document.documentElement.getAttribute('data-bs-theme') || 'light';
    }
  };

  document.addEventListener('DOMContentLoaded', function () {
    const currentAttr = document.documentElement.getAttribute('data-bs-theme') || localStorage.getItem(LS_KEY) || 'light';
    syncToggleUI(currentAttr);
    loadTemaFromServer();

    const btnNav = document.getElementById('btnTemaToggle');
    if (btnNav) {
      btnNav.addEventListener('click', function (e) {
        e.preventDefault();
        window.ProjetoOSTema.toggle();
      });
    }

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
