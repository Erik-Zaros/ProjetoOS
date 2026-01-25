<?php

namespace App\Model;

use App\Core\Db;
use App\Auth\Autenticador;

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
        $usuario = Autenticador::getUsuario();
        pg_query($con, 'BEGIN');

        try {
            $cpf = pg_escape_string($this->dados['cpf_consumidor']);
            $cpf = preg_replace("/[^0-9]/", "", $cpf);
            $nome = pg_escape_string($this->dados['nome_consumidor']);
            $produto = intval($this->dados['produto']);
            $posto = intval($this->posto);
            $data_abertura = pg_escape_string($this->dados['data_abertura']);
            $cep = pg_escape_string($this->dados['cep_consumidor'] ?? '');
            $endereco = pg_escape_string($this->dados['endereco_consumidor'] ?? '');
            $bairro = pg_escape_string($this->dados['bairro_consumidor'] ?? '');
            $numero = pg_escape_string($this->dados['numero_consumidor'] ?? '');
            $cidade = pg_escape_string($this->dados['cidade_consumidor'] ?? '');
            $estado = pg_escape_string($this->dados['estado_consumidor'] ?? '');
            $nota_fiscal = pg_escape_string($this->dados['nota_fiscal'] ?? '');
            $tipo_atendimento = intval($this->dados['tipo_atendimento'] ?? '');
            $tecnico = !empty($this->dados['tecnico']) ? $this->dados['tecnico'] : 'NULL';

            $valida = $this->validaCamposOs($cpf, $nome, $produto, $data_abertura, $cep, $endereco, $bairro, $numero, $cidade, $estado, $nota_fiscal, $tipo_atendimento);
            if ($valida != null) {
                return ['status' => 'alert', 'message' => $valida];
            }

            $sqlCliente = "SELECT cliente, nome, cpf FROM tbl_cliente WHERE (cpf = '{$cpf}' OR nome = '{$nome}') AND posto = {$posto}";
            $res = pg_query($con, $sqlCliente);

            if (pg_num_rows($res) > 0) {
                $cliente = pg_fetch_assoc($res);
                $cliente_id = $cliente['cliente'];

                if ($cliente['nome'] !== $nome) {
                    pg_query($con, "UPDATE tbl_cliente SET nome = '{$nome}' WHERE cliente = {$cliente_id}");
                }

				if ($cliente['cpf'] !== $cpf) {
                    pg_query($con, "UPDATE tbl_cliente SET cpf = '{$cpf}' WHERE cliente = {$cliente_id}");
				}
            } else {
                $sqlInsertCliente = "INSERT INTO tbl_cliente (nome, cpf, cep, endereco, bairro, numero, cidade, estado, posto)
                                     VALUES ('{$nome}', '{$cpf}', '{$cep}', '{$endereco}', '{$bairro}', '{$numero}', '{$cidade}', '{$estado}', {$posto})
                                     RETURNING cliente";
                $res_insert = pg_query($con, $sqlInsertCliente);
                if (!$res_insert) throw new \Exception("Erro ao cadastrar cliente.");
                $cliente_row = pg_fetch_assoc($res_insert);
                $cliente_id = $cliente_row['cliente'];
            }

            $sqlInsertOS = "
                INSERT INTO tbl_os (
                    data_abertura, nome_consumidor, cpf_consumidor, produto, cliente, posto,
                    cep_consumidor, endereco_consumidor, bairro_consumidor, numero_consumidor,
                    cidade_consumidor, estado_consumidor, nota_fiscal, tecnico, tipo_atendimento
                )
                VALUES (
                    '{$data_abertura}', '{$nome}', '{$cpf}', {$produto}, {$cliente_id}, {$posto},
                    '{$cep}', '{$endereco}', '{$bairro}', '{$numero}', '{$cidade}', '{$estado}', '{$nota_fiscal}', {$tecnico}, {$tipo_atendimento}
                )
                RETURNING os
            ";
            $res_os = pg_query($con, $sqlInsertOS);

            if (!$res_os) throw new \Exception('Erro ao cadastrar OS.');

            $row_os = pg_fetch_assoc($res_os);
            $os_id = $row_os['os'];

            if (!empty($this->dados['pecas'])) {

                $pecas = json_decode($this->dados['pecas'], true);

                if (is_array($pecas)) {
                    foreach ($pecas as $item) {
                        $peca = intval($item['peca']);
                        $quantidade = intval($item['quantidade']);
                        $servico_realizado = intval($item['servico_realizado']);

                        $sqlCheckPeca = "SELECT 1
                                         FROM tbl_lista_basica
                                         WHERE produto = {$produto}
                                         AND peca = {$peca}
                                         LIMIT 1
                                        ";
                        $resCheckPeca = pg_query($con, $sqlCheckPeca);

                        if (pg_num_rows($resCheckPeca) === 0) {
                            $sqlNomePeca = "SELECT concat(codigo, ' - ', descricao) as nome_peca FROM tbl_peca WHERE peca = {$peca}";
                            $resNomePeca = pg_query($con, $sqlNomePeca);

                            if (pg_num_rows($resNomePeca) > 0) {
                                $row = pg_fetch_assoc($resNomePeca);
                                $nomePeca = $row['nome_peca'];
                                throw new \Exception("A peça '{$nomePeca}' não pertence ao produto selecionado.");
                            }
                        }

                        $usa_estoque = $this->validaServicoRealizadoUsaEstoque($servico_realizado);

                        if ($usa_estoque == true) {
                            $descricao_mov = pg_escape_string($con, "Peça lançada na OS {$os_id}");
                            $sql_estoque = "
                                SELECT fn_lanca_movimentacao_estoque(
                                    {$posto},
                                    NULL,
                                    {$peca},
                                    'S',
                                    {$quantidade},
                                    {$os_id},
                                    '{$descricao_mov}',
                                    {$usuario}
                                )
                            ";
                            $res_estoque = pg_query($con, $sql_estoque);
                        }

                        $sqlItem = "
                            INSERT INTO tbl_os_item (os, peca, quantidade, servico_realizado, posto)
                            VALUES ({$os_id}, {$peca}, {$quantidade}, {$servico_realizado}, {$posto})
                        ";
                        pg_query($con, $sqlItem);
                    }
                }
            }

            pg_query($con, 'COMMIT');
            return ['status' => 'success', 'message' => 'OS cadastrada com sucesso!', 'os' => $os_id];
        } catch (\Exception $e) {
            pg_query($con, 'ROLLBACK');
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function editar()
    {
        $con = Db::getConnection();
        $usuario = Autenticador::getUsuario();
        pg_query($con, 'BEGIN');

        try {
            $os = intval($this->dados['os']);
            $cpf = pg_escape_string($this->dados['cpf_consumidor']);
            $cpf = preg_replace("/[^0-9]/", "", $cpf);
            $nome = pg_escape_string($this->dados['nome_consumidor']);
            $produto = intval($this->dados['produto']);
            $posto = intval($this->posto);
            $data_abertura = pg_escape_string($this->dados['data_abertura']);
            $cep = pg_escape_string($this->dados['cep_consumidor'] ?? '');
            $endereco = pg_escape_string($this->dados['endereco_consumidor'] ?? '');
            $bairro = pg_escape_string($this->dados['bairro_consumidor'] ?? '');
            $numero = pg_escape_string($this->dados['numero_consumidor'] ?? '');
            $cidade = pg_escape_string($this->dados['cidade_consumidor'] ?? '');
            $estado = pg_escape_string($this->dados['estado_consumidor'] ?? '');
            $nota_fiscal = pg_escape_string($this->dados['nota_fiscal'] ?? '');
            $tipo_atendimento = intval($this->dados['tipo_atendimento'] ?? '');
            $tecnico = !empty($this->dados['tecnico']) ? $this->dados['tecnico'] : 'NULL';

            $sqlCheck = "SELECT finalizada, cancelada FROM tbl_os WHERE os = {$os}";
            $res_check = pg_query($con, $sqlCheck);

            if (pg_num_rows($res_check) === 0) throw new \Exception("OS não encontrada.");
            $row = pg_fetch_assoc($res_check);

            if ($row['finalizada'] === 't') throw new \Exception("OS já finalizada. Não é possível editar.");
            if ($row['cancelada'] === 't') throw new \Exception("OS já cancelada. Não é possível editar.");

            $valida_campo = $this->validaCamposOs($cpf, $nome, $produto, $data_abertura, $cep, $endereco, $bairro, $numero, $cidade, $estado, $nota_fiscal, $tipo_atendimento);
            if ($valida_campo != null) {
                return ['status' => 'alert', 'message' => $valida_campo];
            }

            $sqlCliente = "SELECT cliente, nome, cpf FROM tbl_cliente WHERE (cpf = '{$cpf}' OR nome = '{$nome}') AND posto = {$posto}";
            $res_cliente = pg_query($con, $sqlCliente);

            if (pg_num_rows($res_cliente) > 0) {
                $cliente = pg_fetch_assoc($res_cliente);
                $cliente_id = $cliente['cliente'];

                if ($cliente['nome'] !== $nome) {
                    pg_query($con, "UPDATE tbl_cliente SET nome = '{$nome}' WHERE cliente = {$cliente_id}");
                }

                if ($cliente['cpf'] !== $cpf) {
                    pg_query($con, "UPDATE tbl_cliente SET cpf = '{$cpf}' WHERE cliente = {$cliente_id}");
                }
            } else {

                $sqlInsertCliente = "INSERT INTO tbl_cliente (nome, cpf, cep, endereco, bairro, numero, cidade, estado, posto)
                                     VALUES ('{$nome}', '{$cpf}', '{$cep}', '{$endereco}', '{$bairro}', '{$numero}', '{$cidade}', '{$estado}', {$posto})
                                     RETURNING cliente";
                $res_insert = pg_query($con, $sqlInsertCliente);
                if (!$res_insert) throw new \Exception("Erro ao cadastrar cliente.");
                $row_new = pg_fetch_assoc($res_insert);
                $cliente_id = $row_new['cliente'];
            }

            $sqlUpdateOS = "
                UPDATE tbl_os SET
                    data_abertura = '{$data_abertura}',
                    nome_consumidor = '{$nome}',
                    cpf_consumidor = '{$cpf}',
                    produto = {$produto},
                    cliente = {$cliente_id},
                    cep_consumidor = '{$cep}',
                    endereco_consumidor = '{$endereco}',
                    bairro_consumidor = '{$bairro}',
                    numero_consumidor = '{$numero}',
                    cidade_consumidor = '{$cidade}',
                    estado_consumidor = '{$estado}',
                    nota_fiscal = '{$nota_fiscal}',
                    tecnico = {$tecnico},
                    tipo_atendimento = {$tipo_atendimento}
                WHERE os = {$os}
            ";
            $res_update = pg_query($con, $sqlUpdateOS);

            if (!$res_update) throw new \Exception("Erro ao atualizar OS.");

            if (!empty($this->dados['pecas'])) {
                $pecas = json_decode($this->dados['pecas'], true);

                if (is_array($pecas)) {
                    foreach ($pecas as $item) {
                        $peca = intval($item['peca']);
                        $quantidade = intval($item['quantidade']);
                        $servico_realizado = intval($item['servico_realizado']);

                        $sqlCheckPeca = "SELECT 1
                                         FROM tbl_lista_basica
                                         WHERE produto = {$produto}
                                         AND peca = {$peca}
                                         LIMIT 1
                                        ";
                        $resCheckPeca = pg_query($con, $sqlCheckPeca);

                        if (pg_num_rows($resCheckPeca) === 0) {
                            $sqlNomePeca = "SELECT concat(codigo, ' - ', descricao) as nome_peca FROM tbl_peca WHERE peca = {$peca}";
                            $resNomePeca = pg_query($con, $sqlNomePeca);

                            if (pg_num_rows($resNomePeca) > 0) {
                                $row = pg_fetch_assoc($resNomePeca);
                                $nomePeca = $row['nome_peca'];
                                throw new \Exception("A peça '{$nomePeca}' não pertence ao produto selecionado.");
                            }
                        }

                        $usa_estoque = $this->validaServicoRealizadoUsaEstoque($servico_realizado);

                        $sqlCheck = "SELECT os_item FROM tbl_os_item WHERE os = {$os} AND peca = {$peca}";
                        $resCheck = pg_query($con, $sqlCheck);

                        if (pg_num_rows($resCheck) > 0) {
                            $sqlUpdateItem = "
                                UPDATE tbl_os_item
                                SET quantidade = {$quantidade}, servico_realizado = {$servico_realizado}
                                WHERE os = {$os} AND peca = {$peca}
                            ";
                            $resUpdateItem = pg_query($con, $sqlUpdateItem);
                        } else {
                            $sqlInsertItem = "
                                INSERT INTO tbl_os_item (os, peca, quantidade, servico_realizado, posto)
                                VALUES ({$os}, {$peca}, {$quantidade}, {$servico_realizado}, {$posto})
                            ";
                            $resInsertIntem = pg_query($con, $sqlInsertItem);
                        }

                        if ($usa_estoque == true) {

                            $peca_lancada_anteriormente = $this->validaPecaLancadaEstoqueMovimento($peca, $os);

                            if ($peca_lancada_anteriormente == false) {
                                $descricao_mov = pg_escape_string($con, "Peça lançada/atualizada na OS {$os}");
                                $sql_estoque = "
                                    SELECT fn_lanca_movimentacao_estoque(
                                        {$posto},
                                        NULL,
                                        {$peca},
                                        'S',
                                        {$quantidade},
                                        {$os},
                                        '{$descricao_mov}',
                                        '{$usuario}'
                                    )
                                ";
                                $res_estoque = pg_query($con, $sql_estoque);
                            }
                        }
                    }
                }
            }

            pg_query($con, 'COMMIT');
            return ['status' => 'success', 'message' => 'OS atualizada com sucesso!', 'os' => $os];
        } catch (\Exception $e) {
            pg_query($con, 'ROLLBACK');
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function validaServicoRealizadoUsaEstoque($servico_realizado)
    {
        $con = Db::getConnection();
        $posto = intval($this->posto);

        $sql = "SELECT usa_estoque
                FROM tbl_servico_realizado
                WHERE posto = $posto
                AND servico_realizado = $servico_realizado
            ";
        $res = pg_query($con, $sql);

        if (pg_num_rows($res) > 0) {
            $usa_estoque = pg_fetch_result($res, 0, 'usa_estoque');

            if ($usa_estoque == 't') {
                return true;
            }
        }

        return false;
    }

    private function validaPecaLancadaEstoqueMovimento($peca, $os)
    {
        $con = Db::getConnection();
        $posto = intval($this->posto);

        $sql = "SELECT estoque_movimento
                FROM tbl_estoque_movimento
                WHERE posto = $posto
                AND peca = $peca
                AND os = $os
            ";
        $res = pg_query($con, $sql);

        if (pg_num_rows($res) > 0) {
            return true;
        }

        return false;
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

        if ($result && pg_num_rows($result) > 0) {
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

    public static function buscar($os)
    {
        $con = Db::getConnection();
        $os = intval($os);
        $sql = "SELECT os.os, os.data_abertura, os.nome_consumidor, os.cpf_consumidor,
                       os.produto, p.descricao AS produto_descricao,
                       os.cep_consumidor, os.endereco_consumidor, os.bairro_consumidor,
                       os.numero_consumidor, os.cidade_consumidor, os.estado_consumidor,
                       os.nota_fiscal, os.finalizada, os.cancelada, os.tecnico, os.tipo_atendimento
                FROM tbl_os os
                INNER JOIN tbl_produto p ON os.produto = p.produto
                LEFT JOIN tbl_usuario u ON os.tecnico = u.usuario
                WHERE os.os = {$os}
            ";

        $res = pg_query($con, $sql);
        return pg_num_rows($res) > 0 ? pg_fetch_assoc($res) : null;
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

    private static function validaCamposOs($cpf, $nome, $produto, $data_abertura, $cep, $endereco, $bairro, $numero, $cidade, $estado, $nota_fiscal, $tipo_atendimento)
    {

        $obrigatorios = [
            'CPF do consumidor' => $cpf,
            'Nome do consumidor' => $nome,
            'Produto' => $produto,
            'Data de abertura' => $data_abertura,
            'CEP' => $cep,
            'Endereço' => $endereco,
            'Bairro' => $bairro,
            'Número' => $numero,
            'Cidade' => $cidade,
            'Estado' => $estado,
            'Nota fiscal' => $nota_fiscal,
            'Tipo de Atendimento' => $tipo_atendimento
        ];

        foreach ($obrigatorios as $campo => $valor) {
            if (empty($valor) && $valor !== "0") {
                return "O campo '$campo' é obrigatório!";
            }
        }

        if (strlen($nome) >= 255) {
            return "O nome do consumidor não pode ultrapassar 255 caracteres.";
        }

        if (strlen($cpf) > 14) {
            return "O CPF não pode ultrapassar 14 caracteres.";
        }

        if (strlen($cep) > 10) {
            return "O CEP não pode ultrapassar 10 caracteres.";
        }

        if (strlen($endereco) >= 255) {
            return "O endereço não pode ultrapassar 255 caracteres.";
        }

        if (strlen($bairro) >= 255) {
            return "O bairro não pode ultrapassar 255 caracteres.";
        }

        if (strlen($numero) >= 10) {
            return "O número não pode ultrapassar 10 caracteres.";
        }

        if (strlen($cidade) >= 255) {
            return "A cidade não pode ultrapassar 255 caracteres.";
        }

        if (strlen($estado) >= 10) {
            return "O estado não pode ultrapassar 10 caracteres.";
        }

        if (strlen($nota_fiscal) >= 50) {
            return "A nota fiscal não pode ultrapassar 50 caracteres.";
        }

        if (!preg_match('/^\d{11}$|^\d{3}\.\d{3}\.\d{3}\-\d{2}$/', $cpf)) {
            return "CPF inválido.";
        }

        if (!preg_match('/^\d{5}\-?\d{3}$/', $cep)) {
            return "CEP inválido.";
        }

        if (!strtotime($data_abertura)) {
            return "Data de abertura inválida.";
        }

        return null;
    }
}
