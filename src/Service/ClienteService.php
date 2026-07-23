<?php

namespace App\Service;

use App\Model\Cliente;
use App\Auth\Autenticador;
use App\Repository\ClienteRepository;

class ClienteService
{
    private ClienteRepository $repository;
    private int $posto;

    public function __construct()
    {
        $this->posto       = (int) Autenticador::getPosto();
        $this->repository  = new ClienteRepository($this->posto);
    }

    public function cadastrar(array $dados): array
    {
        $erros = $this->validarDados($dados);
        if (!empty($erros)) {
            return ['status' => 'error', 'message' => implode(' ', $erros)];
        }

        $cliente   = new Cliente($dados, $this->posto);
        $usuario   = Autenticador::getUsuario();
        $existente = $this->repository->buscarPorCpf($cliente->getCpf());

        if ($existente) {
            $resultado = $this->repository->atualizar($cliente);

            if (!$resultado) {
                return ['status' => 'error', 'message' => 'Erro ao atualizar cliente!'];
            }

            return ['status' => 'success', 'message' => 'Cliente atualizado com sucesso!'];
        }

        $idNovo = $this->repository->inserir($cliente);

        if (!$idNovo) {
            return ['status' => 'error', 'message' => 'Erro ao cadastrar cliente!'];
        }

        return ['status' => 'success', 'message' => 'Cliente cadastrado com sucesso!'];
    }

    public function atualizar(array $dados): array
    {
        $erros = $this->validarDados($dados, exigirId: true);
        if (!empty($erros)) {
            return ['status' => 'error', 'message' => implode(' ', $erros)];
        }

        $cliente   = new Cliente($dados, $this->posto);
        $usuario   = Autenticador::getUsuario();
        $existente = $this->repository->buscarPorId($cliente->getId());

        if (!$existente) {
            return ['status' => 'error', 'message' => 'Cliente não encontrado.'];
        }

        $resultado = $this->repository->atualizar($cliente);

        if (!$resultado) {
            return ['status' => 'error', 'message' => 'Erro ao atualizar cliente.'];
        }

        return ['status' => 'success', 'message' => 'Cliente atualizado com sucesso!'];
    }

    public function buscarPorCpf(string $cpf): ?array
    {
        return $this->repository->buscarPorCpf($cpf)?->toArray();
    }

    public function listarTodos(): array
    {
        return $this->repository->listarTodos();
    }

    public function autocompleteClientes($termo): array
    {
        return $this->repository->autocompleteClientes($termo);
    }

    private function validarDados(array $dados, bool $exigirId = false): array
    {
        $erros = [];

        if (empty($dados['cpf'])) {
            $erros[] = 'CPF é obrigatório.';
        } elseif (!$this->cpfValido($dados['cpf'])) {
            $erros[] = 'CPF inválido.';
        }

        if (empty($dados['nome'])) {
            $erros[] = 'Nome é obrigatório.';
        }

        if ($exigirId && empty($dados['cliente'])) {
            $erros[] = 'ID do cliente é obrigatório para atualização.';
        }

        return $erros;
    }

    public function relatorio(array $filtros): array
    {
        if (empty($filtros['dataInicio']) || empty($filtros['dataFim'])) {
            return ['status' => 'alert', 'message' => 'A data é obrigatória para consulta'];
        }
        return $this->repository->relatorio($filtros);
    }

    private function cpfValido(string $cpf): bool
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += (int) $cpf[$i] * ($t + 1 - $i);
            }
            $digito = (10 * $soma) % 11 % 10;
            if ((int) $cpf[$t] !== $digito) {
                return false;
            }
        }

        return true;
    }
}
