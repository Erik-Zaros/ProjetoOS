<?php

namespace App\Model;

use App\Core\Db;
use App\Auth\Autenticador;
use App\Model\LogAuditor;

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
        $usuario = Autenticador::getUsuario();

        $cpf      = pg_escape_string($this->dados['cpf']);
        $nome     = pg_escape_string($this->dados['nome']);
        $cep      = pg_escape_string(str_replace('-', '', $this->dados['cep']));
        $endereco = pg_escape_string($this->dados['endereco']);
        $bairro   = pg_escape_string($this->dados['bairro']);
        $numero   = pg_escape_string($this->dados['numero']);
        $cidade   = pg_escape_string($this->dados['cidade']);
        $estado   = pg_escape_string($this->dados['estado']);
        $posto    = intval($this->posto);

        $sql = "SELECT cpf FROM tbl_cliente WHERE cpf = '{$cpf}' AND posto = {$posto}";
        $check = pg_query($con, $sql);

        if (pg_num_rows($check) > 0) {
            return ['status' => 'error', 'message' => 'Cliente já cadastrado com esse CPF!'];
        }

        $sql = "INSERT INTO tbl_cliente (cpf, nome, cep, endereco, bairro, numero, cidade, estado, posto)
                VALUES ('{$cpf}','{$nome}','{$cep}','{$endereco}','{$bairro}','{$numero}','{$cidade}','{$estado}',{$posto}) RETURNING cliente";
        $res = pg_query($con, $sql);

        if (pg_num_rows($res) > 0) {
            $cliente = pg_fetch_result($res, 0, 'cliente');
            $depois = [
                'cpf'    => $cpf,
                'nome' => $nome,
                'cep'     => $cep,
                'endereco' => $endereco,
                'bairro'   => $bairro,
                'numero'   => $numero,
                'cidade'   => $cidade,
                'estado'   => $estado
            ];

            $antes = null;

            LogAuditor::registrar(
                'tbl_cliente',
                $cliente,
                'insert',
                $antes,
                $depois,
                $usuario
            );

            return ['status' => 'success', 'message' => 'Cliente cadastrado com sucesso!'];
        }

        return ['status' => 'error', 'message' => 'Erro ao cadastrar cliente!'];
    }

    public static function buscarPorCpf($cpf, $posto)
    {
        $con = Db::getConnection();
        $cpf = pg_escape_string($cpf);
        $posto = intval($posto);

        $sql = "SELECT cliente, cpf, nome, cep, endereco, bairro, numero, cidade, estado
                FROM tbl_cliente WHERE cpf = '{$cpf}' AND posto = {$posto}";

        $res = pg_query($con, $sql);
        return pg_num_rows($res) > 0 ? pg_fetch_assoc($res) : null;
    }

    public function atualizar()
    {
        $con = Db::getConnection();
        $usuario = Autenticador::getUsuario();

        $nome  = pg_escape_string($this->dados['nome']);
        $cep   = pg_escape_string(str_replace('-', '', $this->dados['cep']));
        $endereco = pg_escape_string($this->dados['endereco']);
        $bairro   = pg_escape_string($this->dados['bairro']);
        $numero   = pg_escape_string($this->dados['numero']);
        $cidade   = pg_escape_string($this->dados['cidade']);
        $estado   = pg_escape_string($this->dados['estado']);
        $cpf      = pg_escape_string($this->dados['cpf']);
        $posto    = intval($this->posto);
        $cliente = intval($this->dados['cliente']);

        $sqlAntes = "SELECT nome, cpf, cep, endereco, bairro, numero, cidade, estado FROM tbl_cliente WHERE cliente = $cliente AND posto = $posto";
        $resAntes = pg_query($con, $sqlAntes);

        if (pg_num_rows($resAntes) > 0) {
            $nomeAntes = pg_fetch_result($resAntes, 0, 'nome');
            $cpfAntes = pg_fetch_result($resAntes, 0, 'cpf');
            $cepAntes = pg_fetch_result($resAntes, 0, 'cep');
            $enderecoAntes = pg_fetch_result($resAntes, 0, 'endereco');
            $bairroAntes = pg_fetch_result($resAntes, 0, 'bairro');
            $numeroAntes = pg_fetch_result($resAntes, 0, 'numero');
            $cidadeAntes = pg_fetch_result($resAntes, 0, 'cidade');
            $estadoAntes = pg_fetch_result($resAntes, 0, 'estado');

            $antes = [
                'nome'     => $nomeAntes,
                'cpf'      => $cpfAntes,
                'cep'      => $cepAntes,
                'endereco' => $enderecoAntes,
                'bairro'   => $bairroAntes,
                'numero'   => $numeroAntes,
                'cidade'   => $cidadeAntes,
                'estado'   => $estadoAntes
            ];
        }

        $sql = "UPDATE tbl_cliente SET nome='{$nome}', cep='{$cep}', endereco='{$endereco}', bairro='{$bairro}', numero='{$numero}', cidade='{$cidade}', estado='{$estado}'
                WHERE cpf = '{$cpf}' AND posto = {$posto}";

        $res = pg_query($con, $sql);

        if ($res) {
            $depois = [
                'nome'     => $nome,
                'cpf'      => $cpf,
                'cep'      => $cep,
                'endereco' => $endereco,
                'bairro'   => $bairro,
                'numero'   => $numero,
                'cidade'   => $cidade,
                'estado'   => $estado
            ];

            LogAuditor::registrar(
                'tbl_cliente',
                $cliente,
                'update',
                $antes,
                $depois,
                $usuario
            );

        return ['status' => 'success', 'message' => 'Cliente atualizado com sucesso!'];
        }

        return ['status' => 'error', 'message' => 'Erro ao atualizar cliente.'];
    }

    public static function listarTodos($posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sql = "SELECT cliente, cpf, nome, cep, endereco, bairro, numero, cidade, estado
                FROM tbl_cliente WHERE posto = {$posto} ORDER BY cpf ASC";

        $res = pg_query($con, $sql);
        $clientes = [];

        while ($row = pg_fetch_assoc($res)) {
            $clientes[] = $row;
        }

        return $clientes;
    }

    public static function autocompleteClientes($termo, $posto)
    {
        $con = Db::getConnection();
        $termo = pg_escape_string($termo);
        $posto = intval($posto);

        $sql = "SELECT nome, cpf, cep FROM tbl_cliente
                WHERE cep IS NOT NULL AND cep <> ''
                AND nome ILIKE '%{$termo}%'
                AND posto = {$posto}
                ORDER BY nome LIMIT 20";
        $res = pg_query($con, $sql);

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
