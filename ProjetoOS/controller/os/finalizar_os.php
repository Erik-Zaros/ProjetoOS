<?php

include '../../model/conexao.php';

$os = $_POST['os'] ?? '';

if (!empty($os)) {
    $sql = "UPDATE tbl_os SET finalizada = 1 WHERE os = '$os'";

    if ($conn->query($sql) === TRUE) {
        echo "Ordem de serviço $os finalizada com sucesso!";
    } else {
        echo "Erro ao finalizar ordem de serviço: " . $conn->error;
    }
}

$conn->close();
?>
