<?php

namespace App\Repository;

use App\Core\Db;
use App\Model\Cliente;
use App\Model\LogAuditor;

class ClienteRepository
{
    private $posto;
    private $usuario;

    public function __construct($posto, $usuario)
    {
        $this->posto   = $posto;
        $this->usuario = $usuario;
    }

    public function inserir(Cliente $cliente): ?int
    {
        $con = Db::getConnection();

        $cpf      = pg_escape_string($cliente->getCpf());
        $nome     = pg_escape_string($cliente->getNome());
        $cep      = pg_escape_string($cliente->getCep());
        $endereco = pg_escape_string($cliente->getEndereco());
        $bairro   = pg_escape_string($cliente->getBairro());
        $numero   = pg_escape_string($cliente->getNumero());
        $cidade   = pg_escape_string($cliente->getCidade());
        $estado   = pg_escape_string($cliente->getEstado());
        $posto    = $this->posto;
        $usuario  = $this->usuario;

        $sql = "INSERT INTO tbl_cliente (cpf, nome, cep, endereco, bairro, numero, cidade, estado, posto)
                VALUES ('{$cpf}','{$nome}','{$cep}','{$endereco}','{$bairro}','{$numero}','{$cidade}','{$estado}',{$posto})
                RETURNING cliente";

        $res = pg_query($con, $sql);

        if (!$res || pg_num_rows($res) === 0) {
            return null;
        }

        $novoId = (int) pg_fetch_result($res, 0, 'cliente');

        LogAuditor::registrar(
            'tbl_cliente',
            $novoId,
            'insert',
            null,
            [
                'cpf'      => $cpf,
                'nome'     => $nome,
                'cep'      => $cep,
                'endereco' => $endereco,
                'bairro'   => $bairro,
                'numero'   => $numero,
                'cidade'   => $cidade,
                'estado'   => $estado
            ],
            $usuario,
            $posto
        );

        return $novoId;
    }

    public function atualizar(Cliente $cliente): bool
    {
        $con = Db::getConnection();

        $cliente_id  = pg_escape_string($cliente->getId());
        $cpf         = pg_escape_string($cliente->getCpf());
        $nome        = pg_escape_string($cliente->getNome());
        $cep         = pg_escape_string($cliente->getCep());
        $endereco    = pg_escape_string($cliente->getEndereco());
        $bairro      = pg_escape_string($cliente->getBairro());
        $numero      = pg_escape_string($cliente->getNumero());
        $cidade      = pg_escape_string($cliente->getCidade());
        $estado      = pg_escape_string($cliente->getEstado());
        $posto       = $this->posto;
        $usuario     = $this->usuario;

        $antes = null;
        $sqlAntes = "SELECT cpf, nome, cep, endereco, bairro, numero, cidade, estado
                     FROM tbl_cliente WHERE cliente = {$cliente_id} AND posto = {$posto}";
        $resAntes = pg_query($con, $sqlAntes);
        if (pg_num_rows($resAntes) > 0) {
            $antes = pg_fetch_assoc($resAntes);
        }

        $sql = "UPDATE tbl_cliente
                SET nome='{$nome}', cep='{$cep}', endereco='{$endereco}',
                    bairro='{$bairro}', numero='{$numero}', cidade='{$cidade}', estado='{$estado}'
                WHERE cliente = '{$cliente_id}' AND posto = {$posto}";
        $res = pg_query($con, $sql);

        if ($res) {
            LogAuditor::registrar(
                'tbl_cliente',
                $cliente_id,
                'update',
                $antes,
                [
                    'cpf'      => $cpf,
                    'nome'     => $nome,
                    'cep'      => $cep,
                    'endereco' => $endereco,
                    'bairro'   => $bairro,
                    'numero'   => $numero,
                    'cidade'   => $cidade,
                    'estado'   => $estado
                ],
                $usuario,
                $posto
            );
        }

        return (bool) $res;
    }

    public function buscarPorCpf(string $cpf): ?Cliente
    {
         $con = Db::getConnection();
         $cpf = pg_escape_string(preg_replace('/[^0-9]/', '', $cpf));

         $sql = "SELECT cliente, cpf, nome, cep, endereco, bairro, numero, cidade, estado
                 FROM tbl_cliente
                 WHERE trim(cpf) = trim('{$cpf}') AND posto = {$this->posto}";

         $res = pg_query($con, $sql);

         if (!$res || pg_num_rows($res) === 0) {
             return null;
         }

         return $this->tratar(pg_fetch_assoc($res));
    }

    public function buscarPorId(int $id): ?Cliente
    {
        $con = Db::getConnection();

        $sql = "SELECT cliente, cpf, nome, cep, endereco, bairro, numero, cidade, estado
                FROM tbl_cliente
                WHERE cliente = {$id} AND posto = {$this->posto}";

        $res = pg_query($con, $sql);

        if (!$res || pg_num_rows($res) === 0) {
            return null;
        }

        return $this->tratar(pg_fetch_assoc($res));
    }

    public function listarTodos(): array
    {
        $con = Db::getConnection();

        $sql = "SELECT cliente, cpf, nome, cep, endereco, bairro, numero, cidade, estado
                FROM tbl_cliente
                WHERE posto = {$this->posto}
                ORDER BY cpf ASC
                LIMIT 500
            ";
        $res = pg_query($con, $sql);
        $clientes = [];

        while ($row = pg_fetch_assoc($res)) {
            $clientes[] = $this->tratar($row)->toArray();
        }

        return $clientes;
    }

    private function tratar(array $row): Cliente
    {
        return new Cliente([
            'cliente'  => $row['cliente']  ?? null,
            'cpf'      => $row['cpf']      ?? '',
            'nome'     => $row['nome']     ?? '',
            'cep'      => $row['cep']      ?? '',
            'endereco' => $row['endereco'] ?? '',
            'bairro'   => $row['bairro']   ?? '',
            'numero'   => $row['numero']   ?? '',
            'cidade'   => $row['cidade']   ?? '',
            'estado'   => $row['estado']   ?? '',
        ], $this->posto);
    }

    public function autocompleteClientes($termo)
    {
        $con = Db::getConnection();
        $termo = pg_escape_string($termo);

        $sql = "SELECT nome,
                       cpf,
                       cep,
                       endereco,
                       bairro,
                       numero,
                       cidade,
                       estado
                FROM tbl_cliente
                WHERE cep IS NOT NULL
                AND nome ILIKE '%{$termo}%'
                AND posto = {$this->posto}
                ORDER BY nome LIMIT 20
             ";
        $res = pg_query($con, $sql);

        $sugestoes = [];
        while ($row = pg_fetch_assoc($res)) {
            $sugestoes[] = [
                'label' => $row['nome'] . " (" . $row['cpf'] . ")",
                'value' => $row['nome'],
                'cpf'   => $row['cpf'],
                'cep'   => $row['cep'],
                'endereco'  => $row['endereco'],
                'bairro'    => $row['bairro'],
                'numero'    => $row['numero'],
                'cidade'    => $row['cidade'],
                'estado'    => $row['estado'],
            ];
        }
        return $sugestoes;
    }

    public function relatorio(array $filtros, bool $limit)
    {
        $con = Db::getConnection();
        $posto = intval($this->posto);

        $dataInicio = $filtros['dataInicio'] ?? '';
        $dataFim    = $filtros['dataFim'] ?? '';
        $cpf        = pg_escape_string($con, trim($filtros['cpf'] ?? ''));
        $nome       = pg_escape_string($con, trim($filtros['nome'] ?? ''));
        $clienteOs  = $filtros['cliente_os'] ?? null;

        if (empty($dataInicio) || empty($dataFim)) {
            return ['status' => 'alert', 'message' => "A data é obrigatória para consulta"];
        }

        $dataInicio = pg_escape_string($con, $dataInicio);
        $dataFim    = pg_escape_string($con, $dataFim);

        $cond = " AND tbl_cliente.data_input::date BETWEEN '{$dataInicio}' AND '{$dataFim}' ";

        if ($cpf !== '') {
            $cond .= " AND tbl_cliente.cpf ILIKE '%{$cpf}%' ";
        }
        if ($nome !== '') {
            $cond .= " AND tbl_cliente.nome ILIKE '%{$nome}%' ";
        }

        $condPri = "";
        if ($clienteOs === 'on') {
            $condPri = " AND os_cliente.oss IS NOT NULL ";
        }

        $cond_limit = "";
        if ($limit == true) {
            $cond_limit = "LIMIT 500";
        }

        $sql = "WITH clientes_filtrados AS (
                     SELECT tbl_cliente.cliente,
                            tbl_cliente.nome,
                            tbl_cliente.cpf,
                            tbl_cliente.cep,
                            tbl_cliente.endereco,
                            tbl_cliente.bairro,
                            tbl_cliente.numero,
                            tbl_cliente.cidade,
                            tbl_cliente.estado,
                            to_char(tbl_cliente.data_input, 'DD/MM/YYYY') AS data_cadastro
                     FROM tbl_cliente
                     WHERE tbl_cliente.posto = {$posto}
                     {$cond}
            ),
            os_cliente AS (
                SELECT tbl_os.cliente,
                       string_agg(tbl_os.os::text, ', ' ORDER BY tbl_os.os) AS oss
                FROM tbl_os
                WHERE tbl_os.posto = {$posto}
                GROUP BY tbl_os.cliente
            )
            SELECT clientes_filtrados.*, os_cliente.oss
            FROM clientes_filtrados
            LEFT JOIN os_cliente ON os_cliente.cliente = clientes_filtrados.cliente
            WHERE 1=1
            {$condPri}
            ORDER BY clientes_filtrados.nome
            {$cond_limit}
        ";

        $res = pg_query($con, $sql);

        if (!$res) {
            return ['status' => 'error', 'message' => "Erro ao consultar relatório."];
        }

        if (pg_num_rows($res) === 0) {
            return ['status' => 'alert', 'message' => "Nenhum cliente encontrado"];
        }

        $clientes = [];
        while ($row = pg_fetch_assoc($res)) {
            $clientes[] = [
                'cliente'       => $row['cliente'],
                'nome'          => $row['nome'],
                'cpf'           => $row['cpf'],
                'cep'           => $row['cep'],
                'endereco'      => $row['endereco'],
                'bairro'        => $row['bairro'],
                'numero'        => $row['numero'],
                'cidade'        => $row['cidade'],
                'estado'        => $row['estado'],
                'data_cadastro' => $row['data_cadastro'],
                'oss'           => $row['oss'],
            ];
        }

        return $clientes;
    }
}
