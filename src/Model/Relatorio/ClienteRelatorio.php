<?php

namespace App\Model\Relatorio;

use App\Core\Db;
use App\Auth\Autenticador;

class ClienteRelatorio
{
    private $dados;
    private $posto;

    public function __construct(array $dados, $posto)
    {
        $this->dados = $dados;
        $this->posto = $posto;
    }

    public function pesquisar(array $filtros)
    {
        $con = Db::getConnection();
        $posto = intval($this->posto);

        $cond = "";
        $dataInicio = $filtros['dataInicio'];
        $dataFim = $filtros['dataFim'];
        $cpf = trim($filtros['cpf']);
        $nome = trim($filtros['nome']);
        $cliente_os = $filtros['cliente_os'];

        if (empty($dataInicio) || empty($dataFim)) {
            return ['status' => 'alert', 'message' => "A data é obrigatória para consulta"];
        }

        $cond = " AND tbl_cliente.data_input::date BETWEEN '$dataInicio' AND '$dataFim' ";

        if (!empty($cpf)) {
            $cond .= " AND tbl_cliente.cpf ILIKE '%$cpf%' ";
        }

        if (!empty($nome)) {
            $cond .= " AND tbl_cliente.nome ILIKE '%$nome%' ";
        }

        if (isset($cliente_os) && $cliente_os == 'on') {
            $condPri = " AND os_cliente.oss IS NOT NULL ";
        }

        $sql = "WITH clientes_filtrados AS (
                     SELECT tbl_cliente.cliente,
                            tbl_cliente.nome,
                            tbl_cliente.cpf,
                            tbl_cliente.cep,
                            tbl_cliente.endereco,
                            tbl_cliente.bairro,
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
            SELECT clientes_filtrados.cliente,
                   clientes_filtrados.nome,
                   clientes_filtrados.cpf,
                   clientes_filtrados.cep,
                   clientes_filtrados.endereco,
                   clientes_filtrados.bairro,
                   clientes_filtrados.cidade,
                   clientes_filtrados.estado,
                   clientes_filtrados.data_cadastro,
                   os_cliente.oss
            FROM clientes_filtrados
            LEFT JOIN os_cliente ON os_cliente.cliente = clientes_filtrados.cliente
            WHERE 1=1
            {$condPri}
            ORDER BY clientes_filtrados.nome
        ";
        $res = pg_query($con, $sql);
        $clientes = [];

        if (pg_num_rows($res) == 0) {
            return ['status' => 'alert', 'message' => "Nenhu cliente encontrado"];
        }

        if (pg_num_rows($res) > 0) {
            while ($row = pg_fetch_assoc($res)) {
                $clientes[] = [
                    'cliente'        => $row['cliente'],
                    'nome'           => $row['nome'],
                    'cpf'            => $row['cpf'],
                    'cep'            => $row['cep'],
                    'endereco'       => $row['endereco'],
                    'bairro'         => $row['bairro'],
                    'numero'         => $row['numero'],
                    'cidade'         => $row['cidade'],
                    'estado'         => $row['estado'],
                    'data_cadastro'  => $row['data_cadastro'],
                    'oss'            => $row['oss']
                ];
            }
        }

        return $clientes;
    }
}
