<?php

namespace App\Repository;

use App\Core\Db;
use App\Auth\Autenticador;

class OsRepository
{
    private $dados;
    private $posto;

    public function __construct(array $dados, $posto)
    {
        $this->dados = $dados;
        $this->posto = $posto;
    }

    public static function filtrarOrdens(array $filtros, $posto)
    {
        $con = Db::getConnection();

        $sql = "SELECT
                    tbl_os.os,
                    tbl_os.nome_consumidor AS cliente,
                    tbl_os.cpf_consumidor AS cpf,
                    CONCAT(tbl_produto.codigo, ' - ', tbl_produto.descricao) AS produto,
                    tbl_os.data_abertura,
                    tbl_os.finalizada,
                    tbl_os.cancelada
                FROM tbl_os
                INNER JOIN tbl_produto ON tbl_os.produto = tbl_produto.produto
                WHERE tbl_os.posto = $posto";

        if (!empty($filtros['os'])) {
            $os = (int) $filtros['os'];
            $sql .= " AND tbl_os.os = $os";
        }

        if (!empty($filtros['nomeCliente'])) {
            $nome = pg_escape_string($con, $filtros['nomeCliente']);
            $sql .= " AND tbl_os.nome_consumidor ILIKE '%$nome%'";
        }

        if (!empty($filtros['dataInicio']) && !empty($filtros['dataFim'])) {
            $dataInicio = pg_escape_string($con, $filtros['dataInicio']);
            $dataFim    = pg_escape_string($con, $filtros['dataFim']);
            $sql .= " AND tbl_os.data_abertura BETWEEN '$dataInicio' AND '$dataFim'";
        }

		if (!empty($filtros['os_finalizada']) && $filtros['os_finalizada'] == 'on') {
			$sql .= " AND tbl_os.finalizada IS TRUE ";
		}

		if (!empty($filtros['os_cancelada']) && $filtros['os_cancelada'] == 'on') {
			$sql .= " AND tbl_os.cancelada IS TRUE ";
		}

        $sql .= " ORDER BY tbl_os.os ASC";

        $result = pg_query($con, $sql);
        $ordens = [];

        if (pg_num_rows($result) > 0) {
            while ($row = pg_fetch_assoc($result)) {
                $ordens[] = [
                    'os'            => $row['os'],
                    'cliente'       => $row['cliente'],
                    'cpf'           => $row['cpf'],
                    'produto'       => $row['produto'],
                    'data_abertura' => date('d/m/Y', strtotime($row['data_abertura'])),
                    'finalizada'    => $row['finalizada'] === 't',
                    'cancelada'     => $row['cancelada'] === 't'
                ];
            }
        }

        return $ordens;
    }

    public static function finalizar($os, $posto)
    {
        $con = Db::getConnection();
        $os = intval($os);
        $posto = intval($posto);
        $sql = "UPDATE tbl_os SET finalizada = true WHERE os = {$os} AND posto = {$posto}";
        $res = pg_query($con, $sql);

        return $res
            ? ['status' => 'success', 'message' => 'OS finalizada com sucesso!']
            : ['status' => 'error', 'message' => 'Erro ao finalizar OS.'];
    }

    public static function cancelar($os, $posto)
    {
        $con = Db::getConnection();
        $sql = "UPDATE tbl_os SET cancelada = true WHERE os = $os AND posto = $posto";
        $res = pg_query($con, $sql);

        return $res
            ? ['status' => 'success', 'message' => 'OS cancelada com sucesso!']
            : ['status' => 'error', 'message' => 'Erro ao cancelar OS.'];
    }

    public static function listarTodos($posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sql = "SELECT os,
                       nome_consumidor AS cliente,
                       cpf_consumidor AS cpf,
                       to_char(data_abertura, 'DD/MM/YYYY') AS data_abertura,
                       finalizada,
                       cancelada,
                       (SELECT CONCAT(codigo, ' - ', descricao) AS codigo_descricao
                        FROM tbl_produto
                        WHERE produto = tbl_os.produto) AS produto
                FROM tbl_os
                WHERE posto = {$posto}
                ORDER BY os ASC";

        $res = pg_query($con, $sql);
        $lista = [];

        while ($row = pg_fetch_assoc($res)) {
            $row['finalizada'] = $row['finalizada'] === 't';
            $row['cancelada'] = $row['cancelada'] === 't';
            $lista[] = $row;
        }

        return $lista;
    }

    public static function buscarPorNumero($os, $posto)
    {
        $con = Db::getConnection();
        $os = intval($os);
        $posto = intval($posto);

        $sql = "
            SELECT
                os.os,
                to_char(os.data_abertura, 'DD/MM/YYYY') AS data_abertura,
                os.nome_consumidor,
                os.cpf_consumidor,
                os.cep_consumidor,
                os.endereco_consumidor,
                os.bairro_consumidor,
                os.numero_consumidor,
                os.cidade_consumidor,
                os.estado_consumidor,
                os.nota_fiscal,
                CONCAT(p.codigo, ' - ', p.descricao) AS produto_codigo_descricao,
                os.finalizada,
                os.cancelada,
                u.nome as tecnico,
                t.descricao as tipo_atendimento
            FROM tbl_os os
            INNER JOIN tbl_produto p ON os.produto = p.produto
            LEFT JOIN tbl_tipo_atendimento t ON t.tipo_atendimento = os.tipo_atendimento
            LEFT JOIN tbl_usuario u ON u.usuario = os.tecnico
            WHERE os.os = {$os} AND os.posto = {$posto}
        ";

        $res = pg_query($con, $sql);
        if (pg_num_rows($res) === 0) {
            return null;
        }

        $dados = pg_fetch_assoc($res);

        $sqlItens = "
            SELECT i.os_item, i.peca, i.quantidade, p.codigo, p.descricao, s.descricao AS descricao_servico_realizado
            FROM tbl_os_item i
            INNER JOIN tbl_peca p ON p.peca = i.peca
            LEFT JOIN tbl_servico_realizado s ON s.servico_realizado = i.servico_realizado
            WHERE i.os = {$os}
            ORDER BY p.descricao
        ";

        $resItens = pg_query($con, $sqlItens);
        $itens = [];
        if ($resItens && pg_num_rows($resItens) > 0) {
            while ($row = pg_fetch_assoc($resItens)) {
                $itens[] = $row;
            }
        }

        $dados['pecas'] = $itens;

        return $dados;
    }
}
