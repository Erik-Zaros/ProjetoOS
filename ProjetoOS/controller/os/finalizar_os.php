<?php
include '../../model/conexao.php';

$numeroOs = $_POST['os'] ?? '';

if (!empty($numeroOs)) {
    $sql = "UPDATE tbl_os SET finalizada = 1 WHERE os = '$numeroOs'";

    if ($conn->query($sql) === TRUE) {
        echo "Ordem de serviço $numeroOs finalizada com sucesso!";
    } else {
        echo "Erro ao finalizar ordem de serviço: " . $conn->error;
    }
} else {
    echo "Número da ordem não especificado.";
}

$conn->close();
?>
