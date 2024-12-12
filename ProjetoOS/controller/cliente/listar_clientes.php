<?php

include '../../model/conexao.php';

$sql = "SELECT cpf, nome, endereco, numero FROM tbl_cliente";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["cpf"]. "</td>
                <td>" . $row["nome"]. "</td>
                <td>" . $row["endereco"]. "</td>
                <td>" . $row["numero"]. "</td>
                <td><button class='btn btn-primary editar' data-cpf='" . $row["cpf"]. "'>Editar</button></td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5'>Nenhum cliente encontrado</td></tr>";
}

$conn->close();
?>
