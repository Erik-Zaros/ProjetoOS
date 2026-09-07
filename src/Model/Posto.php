<?php

namespace App\Model;

class Posto
{
    private ?int $id;
    private string $nome;
    private bool $ativo;
    private array $modulo;

    public function __construct(array $dados)
    {
        $this->id = isset($dados['posto']) ? (int) $dados['posto'] : null;

        $this->nome = trim($dados['nome'] ?? '');

        $this->ativo = isset($dados['ativo']) ? (bool) $dados['ativo'] : false;

        $this->modulo = $this->normalizarModulo($dados['modulo'] ?? []);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getAtivo(): bool
    {
        return $this->ativo;
    }

    public function getModulo(): array
    {
        return $this->modulo;
    }

    public function toArray(): array
    {
        return [
            'posto'  => $this->id,
            'nome'   => $this->nome,
            'ativo'  => $this->ativo,
            'modulo' => $this->modulo,
        ];
    }

    private function normalizarModulo(mixed $modulo): array
    {
        if (is_array($modulo)) {
            return $modulo;
        }

        if (is_string($modulo)) {
            $resultado = json_decode($modulo, true);

            return is_array($resultado) ? $resultado : [];
        }

        return [];
    }
}