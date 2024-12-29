<?php

include '../../model/conexao.php';

$os = $_POST['os'];
$data_abertura = $_POST['data_abertura'];
$nome_consumidor = $_POST['nome_consumidor'];
$cpf_consumidor = $_POST['cpf_consumidor'];
$produto_id = $_POST['produto_id'];

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("SELECT finalizada FROM tbl_os WHERE os = ?");
    $stmt->bind_param("i", $os);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['finalizada'] == 1) {
            echo json_encode(["status" => "error", "message" => "Ordem de serviço finalizada. Não é possível editar!"]);
            exit;
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Ordem de serviço não encontrada."]);
        exit;
    }

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
            if ($conn->errno == 1062) {
                echo json_encode(["status" => "error", "message" => "CPF já cadastrado para outro cliente."]);
                exit;
            }
            echo json_encode(["status" => "error", "message" => "Erro ao cadastrar cliente."]);
            exit;
        }
        $cliente_id = $conn->insert_id;
    }

    $stmt = $conn->prepare("UPDATE tbl_os SET 
        data_abertura = ?,
        nome_consumidor = ?,
        cpf_consumidor = ?,
        produto_id = ?,
        cliente_id = ?
        WHERE os = ?");
    $stmt->bind_param("sssiii", $data_abertura, $nome_consumidor, $cpf_consumidor, $produto_id, $cliente_id, $os);
    
    if (!$stmt->execute()) {
        echo json_encode(["status" => "error", "message" => "Erro ao atualizar ordem de serviço."]);
        exit;
    }

    $conn->commit();
    echo json_encode(["status" => "success", "message" => "Ordem de Serviço Atualizada!"]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$conn->close();
?>
