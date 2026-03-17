<?php

require '../../vendor/autoload.php';

use App\Auth\Autenticador;
use App\Core\Db;
use App\Service\FuncoesService;

Autenticador::iniciar();

$posto = Autenticador::getPosto();
$tipo  = $_GET['tipo'];
$filtro = $_GET['filtro'];

$con = Db::getConnection();
$rows = [];

switch ($tipo) {

    case 'os':
        $sql = "
            SELECT o.os, to_char(o.data_abertura, 'DD/MM/YYYY') as data_abertura,
                   o.nome_consumidor, o.cpf_consumidor,
                   p.descricao AS produto,
                   u.nome AS tecnico,
                   CASE
                       WHEN o.cancelada  THEN 'Cancelada'
                       WHEN o.finalizada THEN 'Finalizada'
                       ELSE 'Aberta'
                   END AS status
            FROM tbl_os o
            LEFT JOIN tbl_produto p ON p.produto = o.produto
            LEFT JOIN tbl_usuario u ON u.usuario = o.tecnico
            WHERE o.posto = $1
            ORDER BY o.os ASC
        ";
        $res = pg_query_params($con, $sql, [$posto]);

		$rows = [];

		while ($r = pg_fetch_assoc($res)) {
			$r['cpf_consumidor'] = FuncoesService::mascaraCpfCnpj($r['cpf_consumidor']);
			$rows[] = $r;
		}

        echo json_encode([
            'titulo'  => 'Ordens de Serviço',
            'colunas' => ['OS', 'Data Abertura', 'Consumidor', 'CPF', 'Produto', 'Técnico', 'Status'],
            'campos'  => ['os', 'data_abertura', 'nome_consumidor', 'cpf_consumidor', 'produto', 'tecnico', 'status'],
            'dados'   => $rows
        ]);
        break;

    case 'os_abertas':
        $sql = "
            SELECT o.os, to_char(o.data_abertura, 'DD/MM/YYYY') as data_abertura,
                   o.nome_consumidor, o.cpf_consumidor,
                   p.descricao AS produto,
                   u.nome AS tecnico
            FROM tbl_os o
            LEFT JOIN tbl_produto p ON p.produto = o.produto
            LEFT JOIN tbl_usuario u ON u.usuario = o.tecnico
            WHERE o.posto = $1
              AND (o.finalizada IS FALSE OR o.finalizada IS NULL)
              AND (o.cancelada  IS FALSE OR o.cancelada  IS NULL)
            ORDER BY o.os ASC
        ";
        $res = pg_query_params($con, $sql, [$posto]);

		$rows = [];

		while ($r = pg_fetch_assoc($res)) {
			$r['cpf_consumidor'] = FuncoesService::mascaraCpfCnpj($r['cpf_consumidor']);
			$rows[] = $r;
		}

        echo json_encode([
            'titulo'  => 'OS Abertas',
            'colunas' => ['OS', 'Data Abertura', 'Consumidor', 'CPF', 'Produto', 'Técnico'],
            'campos'  => ['os', 'data_abertura', 'nome_consumidor', 'cpf_consumidor', 'produto', 'tecnico'],
            'dados'   => $rows
        ]);
        break;

    case 'clientes':
        $sql = "
            SELECT nome, cpf, cidade, estado, bairro, endereco, numero
            FROM tbl_cliente
            WHERE posto = $1
            ORDER BY nome ASC
        ";
        $res = pg_query_params($con, $sql, [$posto]);

		$rows = [];

		while ($r = pg_fetch_assoc($res)) {
			$r['cpf'] = FuncoesService::mascaraCpfCnpj($r['cpf']);
			$rows[] = $r;
		}

        echo json_encode([
            'titulo'  => 'Clientes Cadastrados',
            'colunas' => ['Nome', 'CPF', 'Cidade', 'Estado', 'Bairro', 'Endereço', 'Número'],
            'campos'  => ['nome', 'cpf', 'cidade', 'estado', 'bairro', 'endereco', 'numero'],
            'dados'   => $rows
        ]);
        break;

    case 'produtos':
        $sql = "
            SELECT codigo, descricao,
                   CASE WHEN ativo THEN 'Ativo' ELSE 'Inativo' END AS situacao
            FROM tbl_produto
            WHERE posto = $1
            ORDER BY descricao ASC
        ";
        $res = pg_query_params($con, $sql, [$posto]);
        while ($r = pg_fetch_assoc($res)) $rows[] = $r;
        echo json_encode([
            'titulo'  => 'Produtos Cadastrados',
            'colunas' => ['Código', 'Descrição', 'Situação'],
            'campos'  => ['codigo', 'descricao', 'situacao'],
            'dados'   => $rows
        ]);
        break;

    case 'pecas':
        $sql = "
            SELECT codigo, descricao,
                   CASE WHEN ativo THEN 'Ativa' ELSE 'Inativa' END AS situacao
            FROM tbl_peca
            WHERE posto = $1
            ORDER BY descricao ASC
        ";
        $res = pg_query_params($con, $sql, [$posto]);
        while ($r = pg_fetch_assoc($res)) $rows[] = $r;
        echo json_encode([
            'titulo'  => 'Peças Cadastradas',
            'colunas' => ['Código', 'Descrição', 'Situação'],
            'campos'  => ['codigo', 'descricao', 'situacao'],
            'dados'   => $rows
        ]);
        break;

    case 'usuarios':
        $sql = "
            SELECT nome, login,
                   CASE WHEN tecnico THEN 'Sim' ELSE 'Não' END AS tecnico,
                   CASE WHEN master  THEN 'Sim' ELSE 'Não' END AS master,
                   CASE WHEN ativo   THEN 'Ativo' ELSE 'Inativo' END AS situacao
            FROM tbl_usuario
            WHERE posto = $1
            ORDER BY nome ASC
        ";
        $res = pg_query_params($con, $sql, [$posto]);
        while ($r = pg_fetch_assoc($res)) $rows[] = $r;
        echo json_encode([
            'titulo'  => 'Usuários do Sistema',
            'colunas' => ['Nome', 'Login', 'Técnico', 'Master', 'Situação'],
            'campos'  => ['nome', 'login', 'tecnico', 'master', 'situacao'],
            'dados'   => $rows
        ]);
        break;

    case 'os_status':
        $condicao = match($filtro) {
            'Finalizadas' => "o.finalizada IS TRUE",
            'Canceladas'  => "o.cancelada IS TRUE AND (o.finalizada IS FALSE OR o.finalizada IS NULL)",
            default       => "(o.finalizada IS FALSE OR o.finalizada IS NULL) AND (o.cancelada IS FALSE OR o.cancelada IS NULL)"
        };
        $sql = "
            SELECT o.os, to_char(o.data_abertura, 'DD/MM/YYYY') as data_abertura,
                   o.nome_consumidor, o.cpf_consumidor,
                   p.descricao AS produto,
                   u.nome AS tecnico
            FROM tbl_os o
            LEFT JOIN tbl_produto p ON p.produto = o.produto
            LEFT JOIN tbl_usuario u ON u.usuario = o.tecnico
            WHERE o.posto = $1 AND {$condicao}
            ORDER BY o.data_abertura DESC
        ";
        $res = pg_query_params($con, $sql, [$posto]);

		$rows = [];

		while ($r = pg_fetch_assoc($res)) {
			$r['cpf_consumidor'] = FuncoesService::mascaraCpfCnpj($r['cpf_consumidor']);
			$rows[] = $r;
		}
        while ($r = pg_fetch_assoc($res)) $rows[] = $r;
        echo json_encode([
            'titulo'  => 'OS — ' . ($filtro ?? 'Abertas'),
            'colunas' => ['OS', 'Data Abertura', 'Consumidor', 'CPF', 'Produto', 'Técnico'],
            'campos'  => ['os', 'data_abertura', 'nome_consumidor', 'cpf_consumidor', 'produto', 'tecnico'],
            'dados'   => $rows
        ]);
        break;

    case 'os_tecnico':
        $sql = "
            SELECT o.os, to_char(o.data_abertura, 'DD/MM/YYYY') as data_abertura,
                   o.nome_consumidor, o.cpf_consumidor,
                   p.descricao AS produto,
                   CASE
                       WHEN o.cancelada  THEN 'Cancelada'
                       WHEN o.finalizada THEN 'Finalizada'
                       ELSE 'Aberta'
                   END AS status
            FROM tbl_os o
            LEFT JOIN tbl_produto p ON p.produto = o.produto
            LEFT JOIN tbl_usuario u ON u.usuario = o.tecnico
            WHERE o.posto = $1
              AND u.nome = $2
            ORDER BY o.data_abertura DESC
        ";
        $res = pg_query_params($con, $sql, [$posto, $filtro]);

		$rows = [];

		while ($r = pg_fetch_assoc($res)) {
			$r['cpf_consumidor'] = FuncoesService::mascaraCpfCnpj($r['cpf_consumidor']);
			$rows[] = $r;
		}

        echo json_encode([
            'titulo'  => 'OS — Técnico: ' . $filtro,
            'colunas' => ['OS', 'Data Abertura', 'Consumidor', 'CPF', 'Produto', 'Status'],
            'campos'  => ['os', 'data_abertura', 'nome_consumidor', 'cpf_consumidor', 'produto', 'status'],
            'dados'   => $rows
        ]);
        break;

    case 'pecas_usadas':
        $sql = "
            SELECT o.os, to_char(o.data_abertura, 'DD/MM/YYYY') as data_abertura, o.nome_consumidor,
                   oi.quantidade,
                   u.nome AS tecnico,
                   CASE
                       WHEN o.cancelada  THEN 'Cancelada'
                       WHEN o.finalizada THEN 'Finalizada'
                       ELSE 'Aberta'
                   END AS status
            FROM tbl_os_item oi
            JOIN tbl_os   o ON o.os     = oi.os
            JOIN tbl_peca p ON p.peca   = oi.peca
            LEFT JOIN tbl_usuario u ON u.usuario = o.tecnico
            WHERE o.posto = $1
              AND p.descricao = $2
            ORDER BY o.data_abertura DESC
        ";
        $res = pg_query_params($con, $sql, [$posto, $filtro]);
        while ($r = pg_fetch_assoc($res)) $rows[] = $r;
        echo json_encode([
            'titulo'  => 'OS com peça: ' . $filtro,
            'colunas' => ['OS', 'Data Abertura', 'Consumidor', 'Qtde', 'Técnico', 'Status'],
            'campos'  => ['os', 'data_abertura', 'nome_consumidor', 'quantidade', 'tecnico', 'status'],
            'dados'   => $rows
        ]);
        break;

    default:
        echo json_encode(['erro' => 'Tipo inválido']);
}
