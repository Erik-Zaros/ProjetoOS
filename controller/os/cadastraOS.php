<?php

include '../../model/dbconfig.php';

function cadastraOS() {
    global $con;

    $data_abertura   = $_POST['data_abertura'];
    $nome_consumidor = $_POST['nome_consumidor'];
    $cpf_consumidor  = $_POST['cpf_consumidor'];
    $produto_id      = $_POST['produto_id'];

    pg_query($con, 'BEGIN');

    try {
        $sql = "SELECT cliente, nome FROM tbl_cliente WHERE cpf = $1";
        $result_cliente = pg_query_params($con, $sql, [$cpf_consumidor]);

        if (pg_num_rows($result_cliente) > 0) {
            $row_cliente = pg_fetch_assoc($result_cliente);
            $cliente_id = $row_cliente['id'];

            if ($row_cliente['nome'] !== $nome_consumidor) {
                $update_sql = "UPDATE tbl_cliente SET nome = $1 WHERE cliente = $2";
                pg_query_params($con, $update_sql, [$nome_consumidor, $cliente_id]);
            }

        } else {
            $insert_cliente_sql = "INSERT INTO tbl_cliente (nome, cpf) VALUES ($1, $2) RETURNING cliente";
            $insert_cliente_result = pg_query_params($con, $insert_cliente_sql, [$nome_consumidor, $cpf_consumidor]);

            if (!$insert_cliente_result) {
                throw new Exception("Erro ao cadastrar o cliente: " . pg_last_error($con));
            }

            $row = pg_fetch_assoc($insert_cliente_result);
            $cliente_id = $row['id'];
        }

        $insert_os_sql = "INSERT INTO tbl_os (data_abertura, nome_consumidor, cpf_consumidor, produto_id, cliente_id) 
                          VALUES ($1, $2, $3, $4, $5)";
        $insert_os_result = pg_query_params($con, $insert_os_sql, [
            $data_abertura,
            $nome_consumidor,
            $cpf_consumidor,
            $produto_id,
            $cliente_id
        ]);

        if (!$insert_os_result) {
            throw new Exception("Erro ao cadastrar ordem de serviço: " . pg_last_error($con));
        }

        pg_query($con, 'COMMIT');
        echo json_encode(['status' => 'success', 'message' => 'Ordem de serviço cadastrada com sucesso!']);

    } catch (Exception $e) {
        pg_query($con, 'ROLLBACK');
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

cadastraOS();

pg_close($con);
?>
