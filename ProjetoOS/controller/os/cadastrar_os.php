<?php
include '../../model/conexao.php';

$data_abertura = $_POST['data_abertura'];
$nome_consumidor = $_POST['nome_consumidor'];
$cpf_consumidor = $_POST['cpf_consumidor'];
$produto_codigo = $_POST['produto_codigo'];

$sql_cliente = "SELECT id FROM tbl_cliente WHERE cpf = '$cpf_consumidor'";
$result_cliente = $conn->query($sql_cliente);

if ($result_cliente->num_rows > 0) {
    $row_cliente = $result_cliente->fetch_assoc();
    $cliente_id = $row_cliente['id'];
} else {
    $sql_insert_cliente = "INSERT INTO tbl_cliente (nome, cpf) VALUES ('$nome_consumidor', '$cpf_consumidor')";
    if ($conn->query($sql_insert_cliente) === TRUE) {
        $cliente_id = $conn->insert_id;
    } else {
        echo "Erro ao cadastrar cliente: " . $conn->error;
        exit();
    }
}

$sql_insert_os = "INSERT INTO tbl_os (data_abertura, nome_consumidor, cpf_consumidor, produto_codigo, cliente_id) 
                  VALUES ('$data_abertura', '$nome_consumidor', '$cpf_consumidor', '$produto_codigo', '$cliente_id')";

if ($conn->query($sql_insert_os) === TRUE) {
    echo "Ordem de serviço cadastrada com sucesso!";
} else {
    echo "Erro ao cadastrar ordem de serviço: " . $conn->error;
}

$conn->close();
?>
