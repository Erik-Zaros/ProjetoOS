<?php
include_once '../controller/login/autentica_usuario.php';

$title = 'Cadastro de Usuário';
$pageTitle = 'CADASTRO DE USUÁRIO';
$customCss = '';
$customJs = '';
ob_start();
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-person-fill"></i> Cadastro de Usuário
    </div>
    <div class="card-body">
        <form id="usuarioForm">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="login" class="form-label">Login</label>
                    <input type="text" class="form-control" id="login" name="login" required maxlength="50">
                </div>
                <div class="col-md-4">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required maxlength="50">
                </div>
                <div class="col-md-4">
                    <label for="ativo" class="form-label d-block">Ativo</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" id="ativo" name="ativo" checked>
                        <label class="form-check-label" for="ativo">Usuário Ativo</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-sm">Gravar</button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-table"></i> Usuários Cadastrados
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="usuariosTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Login</th>
                    <th>Ativo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>

