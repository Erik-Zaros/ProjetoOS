<?php

namespace App\Service;

use App\Model\Usuario;
use App\Auth\Autenticador;
use App\Repository\UsuarioRepository;

class UsuarioService
{
    private UsuarioRepository $repository;
    private int $posto;
    private int $usuarioLogado;

    public function __construct()
    {
        $this->posto      = (int) Autenticador::getPosto();
        $this->usuario    = (int) Autenticador::getUsuario();
        $this->repository = new UsuarioRepository($this->posto, $this->usuario);
    }

    public function cadastrar(array $dados): array
    {
        $erros = $this->validarDados($dados, exigirSenha: true);
        if (!empty($erros)) {
            return ['status' => 'error', 'message' => implode(' ', $erros)];
        }

        $usuario        = new Usuario($dados, $this->posto);
        $existente      = $this->repository->buscarPorLogin($usuario->getLogin());

        if ($existente) {
            return ['status' => 'error', 'message' => 'Login já cadastrado!'];
        }

        $novoId = $this->repository->inserir($usuario);

        if (!$novoId) {
            return ['status' => 'error', 'message' => 'Erro ao cadastrar usuário!'];
        }

        return ['status' => 'success', 'message' => 'Usuário cadastrado com sucesso!'];
    }

    public function atualizar(array $dados): array
    {
        $erros = $this->validarDados($dados, exigirId: true);
        if (!empty($erros)) {
            return ['status' => 'error', 'message' => implode(' ', $erros)];
        }

        $usuario       = new Usuario($dados, $this->posto);
        $existente     = $this->repository->buscarPorId($usuario->getId());

        if (!$existente) {
            return ['status' => 'error', 'message' => 'Usuário não encontrado.'];
        }

        $duplicado = $this->repository->buscarPorLogin($usuario->getLogin(), $usuario->getId());
        if ($duplicado) {
            return ['status' => 'error', 'message' => 'Login já cadastrado!'];
        }

        $resultado = $this->repository->atualizar($usuario);

        if (!$resultado) {
            return ['status' => 'error', 'message' => 'Erro ao atualizar usuário.'];
        }

        return ['status' => 'success', 'message' => 'Usuário atualizado com sucesso!'];
    }

    public function buscarPorId(int $id): ?array
    {
        return $this->repository->buscarPorId($id)?->toArray();
    }

    public function listarTodos(): array
    {
        return $this->repository->listarTodos();
    }

    private function validarDados(array $dados, bool $exigirId = false, bool $exigirSenha = false): array
    {
        $erros = [];

        if (empty($dados['login'])) {
            $erros[] = 'Login é obrigatório.';
        }

        if (empty($dados['nome'])) {
            $erros[] = 'Nome é obrigatório.';
        }

        if ($exigirSenha && empty($dados['senha'])) {
            $erros[] = 'Senha é obrigatória.';
        }

        if ($exigirId && empty($dados['usuario'])) {
            $erros[] = 'ID do usuário é obrigatório para atualização.';
        }

        return $erros;
    }
}