$(document).ready(function () {
  $('#formLogin').on('submit', function (e) {
    e.preventDefault();

    const login = $('#login').val();
    const senha = $('#senha').val();

    $.ajax({
      url: 'public/auth/login.php',
      type: 'POST',
      dataType: 'json',
      data: { login, senha },
      success: function (res) {
        if (res.success) {
          window.location.href = 'view/menu';
        } else {
          showMessage(res.message, 'error');
        }
      },
      error: function () {
        $('#msgErro').text('Erro ao tentar autenticar.');
      }
    });
  });
});

function createParticles() {
  const container = document.getElementById('bgAnimation');
  const particleCount = 60;

  const toolIcons = [
    'bi-wrench',
    'bi-gear-fill',
    'bi-hammer', 
    'bi-screwdriver',
    'bi-tools',
    'bi-wrench-adjustable',
    'bi-nut-fill',
    'bi-lightning-charge-fill',
    'bi-cpu-fill',
    'bi-gear-wide-connected'
  ];
  
  for (let i = 0; i < particleCount; i++) {
    const particle = document.createElement('div');
    particle.className = 'particle';
    
    const icon = document.createElement('i');
    icon.className = `bi ${toolIcons[Math.floor(Math.random() * toolIcons.length)]}`;
    particle.appendChild(icon);
    
    particle.style.left = Math.random() * 100 + '%';
    particle.style.top = Math.random() * 100 + '%';
    particle.style.animationDelay = Math.random() * 8 + 's';
    particle.style.animationDuration = (Math.random() * 4 + 6) + 's';
    
    particle.style.transform = `rotate(${Math.random() * 360}deg)`;
    
    container.appendChild(particle);
  }
}

document.getElementById('togglePassword').addEventListener('click', function() {
  const passwordInput = document.getElementById('senha');
  const icon = this.querySelector('i');
  
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    icon.className = 'bi bi-eye-slash-fill';
  } else {
    passwordInput.type = 'password';
    icon.className = 'bi bi-eye-fill';
  }
});

function showMessage(message, type = 'error') {
  const msgDiv = document.getElementById('msgErro');
  msgDiv.textContent = message;
  msgDiv.className = type;
  msgDiv.style.display = 'block';
  
  setTimeout(() => {
    msgDiv.style.display = 'none';
  }, 10000);
}

document.addEventListener('DOMContentLoaded', function() {
  createParticles();
});
