<?php

namespace App\Model;

use App\Core\Db;

class Cliente
{
    private $dados;
    private $posto;

    public function __construct(array $dados, $posto)
    {
        $this->dados = $dados;
        $this->posto = $posto;
    }

    public function salvar()
    {
        $con = Db::getConnection();

        $sql = "SELECT cpf FROM tbl_cliente WHERE cpf = $1 AND posto = $2";
        $check = pg_query_params($con, $sql, [$this->dados['cpf'], $this->posto]);

        if (pg_num_rows($check) > 0) {
            return ['status' => 'error', 'message' => 'Cliente já cadastrado com esse CPF!'];
        }

        $sql = "INSERT INTO tbl_cliente (cpf, nome, cep, endereco, bairro, numero, cidade, estado, posto)
                VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9)";
        $result = pg_query_params($con, $sql, [
            $this->dados['cpf'],
            $this->dados['nome'],
            str_replace('-', '', $this->dados['cep']),
            $this->dados['endereco'],
            $this->dados['bairro'],
            $this->dados['numero'],
            $this->dados['cidade'],
            $this->dados['estado'],
            $this->posto
        ]);

        return $result
            ? ['status' => 'success', 'message' => 'Cliente cadastrado com sucesso!']
            : ['status' => 'error', 'message' => 'Erro ao cadastrar cliente.'];
    }

    public static function buscarPorCpf($cpf, $posto)
    {
        $con = Db::getConnection();

        $sql = "SELECT cpf, nome, cep, endereco, bairro, numero, cidade, estado
                FROM tbl_cliente WHERE cpf = $1 AND posto = $2";

        $result = pg_query_params($con, $sql, [$cpf, $posto]);
        return pg_num_rows($result) > 0 ? pg_fetch_assoc($result) : null;
    }

    public function atualizar()
    {
        $con = Db::getConnection();

        $sql = "UPDATE tbl_cliente SET nome=$1, cep=$2, endereco=$3, bairro=$4, numero=$5, cidade=$6, estado=$7
                WHERE cpf = $8 AND posto = $9";

        $res = pg_query_params($con, $sql, [
            $this->dados['nome'],
            str_replace('-', '', $this->dados['cep']),
            $this->dados['endereco'],
            $this->dados['bairro'],
            $this->dados['numero'],
            $this->dados['cidade'],
            $this->dados['estado'],
            $this->dados['cpf'],
            $this->posto
        ]);

        return $res
            ? ['status' => 'success', 'message' => 'Cliente atualizado com sucesso!']
            : ['status' => 'error', 'message' => 'Erro ao atualizar cliente.'];
    }

    public static function listarTodos($posto)
    {
        $con = Db::getConnection();

        $sql = "SELECT cpf, nome, cep, endereco, bairro, numero, cidade, estado
                FROM tbl_cliente WHERE posto = $1 ORDER BY cpf ASC";

        $res = pg_query_params($con, $sql, [$posto]);
        $clientes = [];

        while ($row = pg_fetch_assoc($res)) {
            $clientes[] = $row;
        }

        return $clientes;
    }

    public static function autocompleteClientes($termo, $posto)
    {
        $con = Db::getConnection();

        $sql = "SELECT nome, cpf, cep FROM tbl_cliente 
                WHERE cep IS NOT NULL AND cep <> '' 
                AND nome ILIKE $1 
                AND posto = $2
                ORDER BY nome LIMIT 20";
        $res = pg_query_params($con, $sql, ["%$termo%", $posto]);

        $sugestoes = [];
        while ($row = pg_fetch_assoc($res)) {
            $sugestoes[] = [
                'label' => $row['nome'] . " (" . $row['cpf'] . ")",
                'value' => $row['nome'],
                'cpf'   => $row['cpf'],
                'cep'   => $row['cep']
            ];
        }
        return $sugestoes;
    }
}
