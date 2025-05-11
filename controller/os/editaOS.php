<?php

include '../../model/dbconfig.php';

function editaOS() {

    global $con;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(["status" => "error", "message" => "Método inválido."]);
        exit;
    }

    $os              = $_POST['os'];
    $data_abertura   = $_POST['data_abertura'];
    $nome_consumidor = $_POST['nome_consumidor'];
    $cpf_consumidor  = $_POST['cpf_consumidor'];
    $produto_id      = $_POST['produto_id'];

    pg_query($con, 'BEGIN');

    try {

        $check_sql = "SELECT finalizada FROM tbl_os WHERE os = $1";
        $result = pg_query_params($con, $check_sql, [$os]);

        if (pg_num_rows($result) === 0) {
            echo json_encode(["status" => "error", "message" => "Ordem de serviço não encontrada."]);
            exit;
        }

        $row = pg_fetch_assoc($result);
        if ($row['finalizada'] === 't') {
            echo json_encode(["status" => "error", "message" => "Ordem de serviço finalizada. Não é possível editar!"]);
            exit;
        }

        $cliente_sql = "SELECT cliente, nome FROM tbl_cliente WHERE cpf = $1";
        $result_cliente = pg_query_params($con, $cliente_sql, [$cpf_consumidor]);

        if (pg_num_rows($result_cliente) > 0) {
            $row_cliente = pg_fetch_assoc($result_cliente);
            $cliente_id = $row_cliente['id'];

            if ($row_cliente['nome'] !== $nome_consumidor) {
                $update_nome_sql = "UPDATE tbl_cliente SET nome = $1 WHERE id = $2";
                pg_query_params($con, $update_nome_sql, [$nome_consumidor, $cliente_id]);
            }
        } else {
            $insert_cliente_sql = "INSERT INTO tbl_cliente (nome, cpf) VALUES ($1, $2) RETURNING cliente";
            $result_insert = pg_query_params($con, $insert_cliente_sql, [$nome_consumidor, $cpf_consumidor]);

            if (!$result_insert) {
                throw new Exception("Erro ao cadastrar cliente: " . pg_last_error($con));
            }

            $row_new = pg_fetch_assoc($result_insert);
            $cliente_id = $row_new['id'];
        }

        $update_os_sql = "UPDATE tbl_os
                          SET data_abertura = $1,
                              nome_consumidor = $2,
                              cpf_consumidor = $3,
                              produto_id = $4,
                              cliente_id = $5
                          WHERE os = $6";

        $update_result = pg_query_params($con, $update_os_sql, [
            $data_abertura,
            $nome_consumidor,
            $cpf_consumidor,
            $produto_id,
            $cliente_id,
            $os
        ]);

        if (!$update_result) {
            throw new Exception("Erro ao atualizar ordem de serviço: " . pg_last_error($con));
        }

        pg_query($con, 'COMMIT');
        echo json_encode(["status" => "success", "message" => "Ordem de Serviço Atualizada!"]);

    } catch (Exception $e) {
        pg_query($con, 'ROLLBACK');
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}

editaOS();

pg_close($con);
?>
