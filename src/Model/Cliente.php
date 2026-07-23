<?php

namespace App\Model;

class Cliente
{
    private ?int   $id;
    private string $cpf;
    private string $nome;
    private string $cep;
    private string $endereco;
    private string $bairro;
    private string $numero;
    private string $cidade;
    private string $estado;
    private int    $posto;

    public function __construct(array $dados, int $posto)
    {
        $this->id       = isset($dados['cliente']) ? (int) $dados['cliente'] : null;
        $this->cpf      = preg_replace('/[^0-9]/', '', $dados['cpf']   ?? '');
        $this->nome     = trim($dados['nome']     ?? '');
        $this->cep      = preg_replace('/[^0-9]/', '', $dados['cep']   ?? '');
        $this->endereco = trim($dados['endereco'] ?? '');
        $this->bairro   = trim($dados['bairro']   ?? '');
        $this->numero   = trim($dados['numero']   ?? '');
        $this->cidade   = trim($dados['cidade']   ?? '');
        $this->estado   = trim($dados['estado']   ?? '');
        $this->posto    = $posto;
    }

    public function getId(): ?int         { return $this->id; }
    public function getCpf(): string      { return $this->cpf; }
    public function getNome(): string     { return $this->nome; }
    public function getCep(): string      { return $this->cep; }
    public function getEndereco(): string { return $this->endereco; }
    public function getBairro(): string   { return $this->bairro; }
    public function getNumero(): string   { return $this->numero; }
    public function getCidade(): string   { return $this->cidade; }
    public function getEstado(): string   { return $this->estado; }
    public function getPosto(): int       { return $this->posto; }

    public function toArray(): array
    {
        return [
            'cliente'  => $this->id,
            'cpf'      => $this->cpf,
            'nome'     => $this->nome,
            'cep'      => $this->cep,
            'endereco' => $this->endereco,
            'bairro'   => $this->bairro,
            'numero'   => $this->numero,
            'cidade'   => $this->cidade,
            'estado'   => $this->estado,
            'posto'    => $this->posto,
        ];
    }
}