<?php

namespace App\Service;

use App\Auth\Autenticador;
use App\Model\Posto;
use App\Repository\PostoRepository;

class PostoService
{
    private PostoRepository $repository;
    private int $posto;

    public function __construct()
    {
        $this->posto = (int) Autenticador::getPosto();
        $this->repository = new PostoRepository();
    }

    public function buscarAtual(): ?Posto
    {
        if ($this->posto <= 0) {
            return null;
        }

        return $this->repository->buscarPorId($this->posto);
    }

    public function buscarNome(): ?string
    {
        if ($this->posto <= 0) {
            return null;
        }

        return $this->repository->buscarNome($this->posto);
    }

    public function usaModulo(string $modulo): bool
    {
        if ($this->posto <= 0) {
            return false;
        }

        return $this->repository->usaModulo(
            $this->posto,
            $modulo
        );
    }

    public function usaModuloEstoque(): bool
    {
        return $this->usaModulo('estoque');
    }

    public function listarTodos(): array
    {
        return $this->repository->listarTodos();
    }
}
