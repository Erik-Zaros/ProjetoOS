<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login - ProjetoOS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="public/css/login.css">
  <link rel="shortcut icon" href="public/img/tc_2009.ico" type="image/x-icon">
</head>

<body>

  <div class="login-card">
    <img src="public/img/tc_2009.ico" alt="Logo" class="logo">
    <h2>Projeto OS</h2>

    <div id="msgErro"></div>

    <form id="formLogin">
      <div class="form-group">
        <input type="text" name="login" id="login" class="form-control" placeholder="Login" required>
      </div>
      <div class="form-group">
        <input type="password" name="senha" id="senha" class="form-control" placeholder="Senha" required>
      </div>
      <button type="submit" class="btn btn-primary btn-login mt-2">
        <i class="bi bi-box-arrow-in-right"></i> Entrar
      </button>
    </form>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="public/ajax/login.js"></script>
</body>
</html>
