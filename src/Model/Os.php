<?php

namespace App\Model;

use App\Core\Db;

class Os
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

        pg_query($con, 'BEGIN');

        try {
            $sql = "SELECT cliente, nome FROM tbl_cliente WHERE cpf = $1";
            $res = pg_query_params($con, $sql, [$this->dados['cpf_consumidor']]);

            if (pg_num_rows($res) > 0) {
                $cliente = pg_fetch_assoc($res);
                $cliente_id = $cliente['cliente'];

                if ($cliente['nome'] !== $this->dados['nome_consumidor']) {
                    $update = "UPDATE tbl_cliente SET nome = $1 WHERE cliente = $2";
                    pg_query_params($con, $update, [$this->dados['nome_consumidor'], $cliente_id]);
                }
            } else {
                $insert_cliente = "INSERT INTO tbl_cliente (nome, cpf, posto) VALUES ($1, $2, $3) RETURNING cliente";
                $res_insert = pg_query_params($con, $insert_cliente, [
                    $this->dados['nome_consumidor'],
                    $this->dados['cpf_consumidor'],
                    $this->posto
                ]);

                if (!$res_insert) {
                    throw new \Exception("Erro ao cadastrar cliente.");
                }

                $cliente_row = pg_fetch_assoc($res_insert);
                $cliente_id = $cliente_row['cliente'];
            }

            $insert_os = "INSERT INTO tbl_os (data_abertura, nome_consumidor, cpf_consumidor, produto_id, cliente_id, posto)
                          VALUES ($1, $2, $3, $4, $5, $6)";

            $res_os = pg_query_params($con, $insert_os, [
                $this->dados['data_abertura'],
                $this->dados['nome_consumidor'],
                $this->dados['cpf_consumidor'],
                $this->dados['produto_id'],
                $cliente_id,
                $this->posto
            ]);

            if (!$res_os) {
                throw new \Exception("Erro ao cadastrar OS.");
            }

            pg_query($con, 'COMMIT');
            return ['status' => 'success', 'message' => 'OS cadastrada com sucesso!'];

        } catch (\Exception $e) {
            pg_query($con, 'ROLLBACK');
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function editar()
    {
        $con = Db::getConnection();

        pg_query($con, 'BEGIN');

        try {
            $os = $this->dados['os'];

            $res_check = pg_query_params($con, "SELECT finalizada FROM tbl_os WHERE os = $1", [$os]);

            if (pg_num_rows($res_check) === 0) {
                throw new \Exception("OS não encontrada.");
            }

            $row = pg_fetch_assoc($res_check);
            if ($row['finalizada'] === 't') {
                throw new \Exception("OS já finalizada. Não é possível editar.");
            }

            $res_cliente = pg_query_params($con, "SELECT cliente, nome FROM tbl_cliente WHERE cpf = $1", [$this->dados['cpf_consumidor']]);

            if (pg_num_rows($res_cliente) > 0) {
                $cliente = pg_fetch_assoc($res_cliente);
                $cliente_id = $cliente['cliente'];

                if ($cliente['nome'] !== $this->dados['nome_consumidor']) {
                    pg_query_params($con, "UPDATE tbl_cliente SET nome = $1 WHERE cliente = $2", [
                        $this->dados['nome_consumidor'],
                        $cliente_id
                    ]);
                }
            } else {
                $res_insert = pg_query_params($con, "INSERT INTO tbl_cliente (nome, cpf) VALUES ($1, $2) RETURNING cliente", [
                    $this->dados['nome_consumidor'],
                    $this->dados['cpf_consumidor']
                ]);
                $row_new = pg_fetch_assoc($res_insert);
                $cliente_id = $row_new['cliente'];
            }

            $update_os = "UPDATE tbl_os SET data_abertura = $1, nome_consumidor = $2, cpf_consumidor = $3,
                          produto_id = $4, cliente_id = $5 WHERE os = $6";

            $res_update = pg_query_params($con, $update_os, [
                $this->dados['data_abertura'],
                $this->dados['nome_consumidor'],
                $this->dados['cpf_consumidor'],
                $this->dados['produto_id'],
                $cliente_id,
                $os
            ]);

            if (!$res_update) {
                throw new \Exception("Erro ao atualizar OS.");
            }

            pg_query($con, 'COMMIT');
            return ['status' => 'success', 'message' => 'OS atualizada com sucesso!'];

        } catch (\Exception $e) {
            pg_query($con, 'ROLLBACK');
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public static function buscar($os)
    {
        $con = Db::getConnection();

        $sql = "SELECT os.os, os.data_abertura, os.nome_consumidor, os.cpf_consumidor,
                       p.descricao AS produto, os.finalizada, os.produto_id
                FROM tbl_os os
                INNER JOIN tbl_produto p ON os.produto_id = p.produto
                WHERE os.os = $1";

        $res = pg_query_params($con, $sql, [$os]);
        return pg_num_rows($res) > 0 ? pg_fetch_assoc($res) : null;
    }

    public static function finalizar($os, $posto)
    {
        $con = Db::getConnection();

        $sql = "UPDATE tbl_os SET finalizada = true WHERE os = $1 AND posto = $2";
        $res = pg_query_params($con, $sql, [$os, $posto]);

        return $res
            ? ['status' => 'success', 'message' => 'OS finalizada com sucesso!']
            : ['status' => 'error', 'message' => 'Erro ao finalizar OS.'];
    }

    public static function listarTodos($posto)
    {
        $con = Db::getConnection();

        $sql = "SELECT os, nome_consumidor AS cliente, cpf_consumidor AS cpf, data_abertura, finalizada,
                       (SELECT descricao FROM tbl_produto WHERE produto = tbl_os.produto_id) AS produto
                FROM tbl_os
                WHERE posto = $1
                ORDER BY os ASC";

        $res = pg_query_params($con, $sql, [$posto]);
        $lista = [];

        while ($row = pg_fetch_assoc($res)) {
            $row['finalizada'] = $row['finalizada'] === 't';
            $lista[] = $row;
        }

        return $lista;
    }

    public static function filtrarOrdens(array $filtros)
    {
        $con = Db::getConnection();

        $conditions = [];
        $params = [];

        $sql = "SELECT
                    os.os,
                    os.nome_consumidor AS cliente,
                    os.cpf_consumidor AS cpf,
                    p.descricao AS produto,
                    os.data_abertura,
                    os.finalizada
                FROM tbl_os os
                INNER JOIN tbl_produto p ON os.produto_id = p.produto
                WHERE 1=1";

        if (!empty($filtros['os'])) {
            $conditions[] = "os.os = $" . (count($params) + 1);
            $params[] = $filtros['os'];
        }

        if (!empty($filtros['nomeCliente'])) {
            $conditions[] = "os.nome_consumidor ILIKE $" . (count($params) + 1);
            $params[] = '%' . $filtros['nomeCliente'] . '%';
        }

        if (!empty($filtros['dataInicio']) && !empty($filtros['dataFim'])) {
            $conditions[] = "os.data_abertura BETWEEN $" . (count($params) + 1) . " AND $" . (count($params) + 2);
            $params[] = $filtros['dataInicio'];
            $params[] = $filtros['dataFim'];
        }

        if (!empty($conditions)) {
            $sql .= " AND " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY os.os ASC";

        $result = pg_query_params($con, $sql, $params);
        $ordens = [];

        if ($result && pg_num_rows($result) > 0) {
            while ($row = pg_fetch_assoc($result)) {
                $ordens[] = [
                    'os'            => $row['os'],
                    'cliente'       => $row['cliente'],
                    'cpf'           => $row['cpf'],
                    'produto'       => $row['produto'],
                    'data_abertura' => date('d/m/Y', strtotime($row['data_abertura'])),
                    'finalizada'    => $row['finalizada'] === 't'
                ];
            }
        }

        return $ordens;
    }

    public static function buscarPorNumero($os, $posto)
    {
        $con = Db::getConnection();

        $sql = "SELECT
                    os.os,
                    os.data_abertura,
                    os.nome_consumidor,
                    os.cpf_consumidor,
                    p.descricao AS produto,
                    os.finalizada
                FROM tbl_os os
                INNER JOIN tbl_produto p ON os.produto_id = p.produto
                WHERE os.os = $1 AND os.posto = $2";

        $res = pg_query_params($con, $sql, [$os, $posto]);

        return pg_num_rows($res) > 0 ? pg_fetch_assoc($res) : null;
    }
}
