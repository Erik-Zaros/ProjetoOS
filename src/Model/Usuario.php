<?php

namespace App\Model;

class Usuario
{
    private ?int   $id;
    private string $login;
    private string $nome;
    private ?string $senha;
    private bool   $ativo;
    private bool   $tecnico;
    private bool   $master;
    private int    $posto;

    public function __construct(array $dados, int $posto)
    {
        $this->id      = isset($dados['usuario']) ? (int) $dados['usuario'] : null;
        $this->login   = trim($dados['login'] ?? '');
        $this->nome    = trim($dados['nome']  ?? '');
        $this->senha   = !empty($dados['senha']) ? $dados['senha'] : null;
        $this->ativo   = ($dados['ativo']   ?? null) === 'on';
        $this->tecnico = ($dados['tecnico'] ?? null) === 'on';
        $this->master  = ($dados['master']  ?? null) === 'on';
        $this->posto   = $posto;
    }

    public function getId(): ?int        { return $this->id; }
    public function getLogin(): string   { return $this->login; }
    public function getNome(): string    { return $this->nome; }
    public function getSenha(): ?string  { return $this->senha; }
    public function isAtivo(): bool      { return $this->ativo; }
    public function isTecnico(): bool    { return $this->tecnico; }
    public function isMaster(): bool     { return $this->master; }
    public function getPosto(): int      { return $this->posto; }

    public function toArray(): array
    {
        return [
            'usuario' => $this->id,
            'login'   => $this->login,
            'nome'    => $this->nome,
            'ativo'   => $this->ativo,
            'tecnico' => $this->tecnico,
            'master'  => $this->master,
            'posto'   => $this->posto,
        ];
    }
}