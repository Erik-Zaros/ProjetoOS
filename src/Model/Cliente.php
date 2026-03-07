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
		$cpf      = preg_replace('/[^0-9]/', '', $cpf);
        $nome     = pg_escape_string($this->dados['nome']);
        $cep      = pg_escape_string($this->dados['cep']);
        $cep      = preg_replace('/[^0-9]/', '', $cep);
        $endereco = pg_escape_string($this->dados['endereco']);
        $bairro   = pg_escape_string($this->dados['bairro']);
        $numero   = pg_escape_string($this->dados['numero']);
        $cidade   = pg_escape_string($this->dados['cidade']);
        $estado   = pg_escape_string($this->dados['estado']);
        $posto    = intval($this->posto);

        $sql_valida = "SELECT cliente, nome, cpf, cep, endereco, bairro, numero, cidade, estado FROM tbl_cliente WHERE trim(cpf) = trim('{$cpf}') AND posto = {$posto}";
        $res_valida = pg_query($con, $sql_valida);

        if (pg_num_rows($res_valida) > 0) {
			$id_cliente = pg_fetch_result($res_valida, 0, 'cliente');

            $nomeAntes = pg_fetch_result($res_valida, 0, 'nome');
            $cpfAntes = pg_fetch_result($res_valida, 0, 'cpf');
            $cepAntes = pg_fetch_result($res_valida, 0, 'cep');
            $enderecoAntes = pg_fetch_result($res_valida, 0, 'endereco');
            $bairroAntes = pg_fetch_result($res_valida, 0, 'bairro');
            $numeroAntes = pg_fetch_result($res_valida, 0, 'numero');
            $cidadeAntes = pg_fetch_result($res_valida, 0, 'cidade');
            $estadoAntes = pg_fetch_result($res_valida, 0, 'estado');

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

            $sql = "UPDATE tbl_cliente SET nome='{$nome}', cep='{$cep}', endereco='{$endereco}', bairro='{$bairro}', numero='{$numero}', cidade='{$cidade}', estado='{$estado}'
                    WHERE cpf = '{$cpf}' AND posto = {$posto}";
            $res = pg_query($con, $sql);

            if (!$res) {
                return ['status' => 'error', 'message' => 'Erro ao atualizar cliente!'];
            }

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
                    $id_cliente,
                    'update',
                    $antes,
                    $depois,
                    $usuario,
                    $posto
                );

                return ['status' => 'success', 'message' => 'Cliente atualizado com sucesso!'];
            }
        } else {
            $sql = "INSERT INTO tbl_cliente (cpf, nome, cep, endereco, bairro, numero, cidade, estado, posto)
                    VALUES ('{$cpf}','{$nome}','{$cep}','{$endereco}','{$bairro}','{$numero}','{$cidade}','{$estado}',{$posto}) RETURNING cliente";
            $res = pg_query($con, $sql);

            if (!$res) {
                return ['status' => 'error', 'message' => 'Erro ao inserir cliente!'];
            }

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
                    $usuario,
                    $posto
                );

                return ['status' => 'success', 'message' => 'Cliente cadastrado com sucesso!'];
            }
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
                $usuario,
                $posto
            );

            return ['status' => 'success', 'message' => 'Cliente atualizado com sucesso!'];
        }

        return ['status' => 'error', 'message' => 'Erro ao atualizar cliente.'];
    }
}
