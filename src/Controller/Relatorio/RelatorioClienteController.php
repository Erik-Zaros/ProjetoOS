<?php

namespace App\Controller\Relatorio;

use App\Core\Db;

class RelatorioClienteController
{
    public static function gerarCSV($posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sqlVerifica = "SELECT COUNT(1) AS cliente FROM tbl_cliente WHERE posto = $posto";
        $resVerifica = pg_query($con, $sqlVerifica);

        $dados = pg_fetch_assoc($resVerifica);

        if (in_array(0, $dados)) {
            header("Location: ../../view/cliente?alerta=true");
            exit;
        }

        $sql = "SELECT tbl_cliente.nome,
                       tbl_cliente.cpf,
                       tbl_cliente.cep,
                       tbl_cliente.endereco,
                       tbl_cliente.bairro,
                       tbl_cliente.numero,
                       tbl_cliente.cidade,
                       tbl_cliente.estado,
                       to_char(tbl_cliente.data_input, 'DD/MM/YYYY') as data_input
                    FROM tbl_cliente
                    WHERE posto = $posto
                    ORDER BY tbl_cliente.nome ASC
                ";
        $res = pg_query($con, $sql);

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=Relatorio_Cliente.xls");
        header("Cache-Control: max-age=0");

        echo "<table border='1'>";
        echo "<tr bgcolor='#2e2e48' style='color: #ffffff; font-weight: bold;'>
                <th>Nome</th>
                <th>CPF</th>
                <th>Endereço</th>
                <th>Bairro</th>
                <th>Número</th>
                <th>Cidade</th>
                <th>Estado</th>
                <th>Data Cadastro</th>
              </tr>";

        while ($row = pg_fetch_assoc($res)) {

            echo "<tr>";
            echo "<td>{$row['nome']}</td>";
            echo "<td>{$row['cpf']}</td>";
            echo "<td>{$row['endereco']}</td>";
            echo "<td>{$row['bairro']}</td>";
            echo "<td>{$row['numero']}</td>";
            echo "<td>{$row['cidade']}</td>";
            echo "<td>{$row['estado']}</td>";
            echo "<td>{$row['data_input']}</td>";
            echo "</tr>";
        }

        echo "</table>";
        exit;
    }
}
