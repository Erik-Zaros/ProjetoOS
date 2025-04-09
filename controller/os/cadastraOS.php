<?php

include '../../model/conexao.php';

function cadastraOS() {

    global $conn;

    $data_abertura = $_POST['data_abertura'];
    $nome_consumidor = $_POST['nome_consumidor'];
    $cpf_consumidor = $_POST['cpf_consumidor'];
    $produto_id = $_POST['produto_id'];

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("SELECT id, nome FROM tbl_cliente WHERE cpf = ?");
        $stmt->bind_param("s", $cpf_consumidor);
        $stmt->execute();
        $result_cliente = $stmt->get_result();
        
        if ($result_cliente->num_rows > 0) {
            $row_cliente = $result_cliente->fetch_assoc();
            $cliente_id = $row_cliente['id'];
            
            if ($row_cliente['nome'] !== $nome_consumidor) {
                $stmt = $conn->prepare("UPDATE tbl_cliente SET nome = ? WHERE id = ?");
                $stmt->bind_param("si", $nome_consumidor, $cliente_id);
                $stmt->execute();
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO tbl_cliente (nome, cpf) VALUES (?, ?)");
            $stmt->bind_param("ss", $nome_consumidor, $cpf_consumidor);
            if (!$stmt->execute()) {
                throw new Exception("Erro ao cadastrar o cliente");
            }
            $cliente_id = $conn->insert_id;
        }

        $stmt = $conn->prepare("INSERT INTO tbl_os (data_abertura, nome_consumidor, cpf_consumidor, produto_id, cliente_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $data_abertura, $nome_consumidor, $cpf_consumidor, $produto_id, $cliente_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Erro ao cadastrar ordem de serviço");
        }

        $os_id = $conn->insert_id;
        $conn->commit();
        echo "Ordem de serviço cadastrada com sucesso!";

    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }

}

cadastraOS();

$conn->close();
?>
