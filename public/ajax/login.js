$(document).ready(function () {
  $('#formLogin').on('submit', function (e) {
    e.preventDefault();

    const login = $('#login').val();
    const senha = $('#senha').val();

    $.ajax({
      url: 'controller/login/valida_login.php',
      type: 'POST',
      dataType: 'json',
      data: { login, senha },
      success: function (res) {
        if (res.success) {
          window.location.href = 'view/menu.php';
        } else {
          $('#msgErro').text(res.message);
        }
      },
      error: function () {
        $('#msgErro').text('Erro ao tentar autenticar.');
      }
    });
  });
});
