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

        header('Content-Type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename=relatorio_cliente.csv");

        $output = fopen('php://output', 'w');

        $cabecalho = ['Nome', 'CPF', 'Endereço', 'Bairro', 'Número', 'Cidade', 'Estado', 'Data Cadastro'];
        fputcsv($output, $cabecalho, ';');

        while ($row = pg_fetch_assoc($res)) {

            fputcsv($output, [
                $row['nome'],
                $row['cpf'],
                $row['endereco'],
                $row['bairro'],
                $row['numero'],
                $row['cidade'],
                $row['estado'],
                $row['data_input']
            ], ';');
        }

        fclose($output);
        exit;
    }
}
