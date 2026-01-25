<?php

namespace App\Service\Export;

use App\Core\Db;

class ProdutoCsvExportService
{
    public function gerar($posto)
    {
        $con = Db::getConnection();
        $posto = intval($posto);

        $sqlVerifica = "SELECT COUNT(1) AS produto FROM tbl_produto WHERE posto = {$posto}";
        $resVerifica = pg_query($con, $sqlVerifica);

        if (pg_num_rows($resVerifica) === 0) {
            header("Location: ../../view/produto.php?alerta=true");exit;

        }

        $sql = " SELECT tbl_produto.codigo,
                        tbl_produto.descricao,
                        CASE WHEN tbl_produto.ativo IS TRUE
                             THEN 'Ativo'
                             ELSE 'Inativo'
                        END AS ativo,
                        to_char(tbl_produto.data_input, 'DD/MM/YYYY') AS data_input
                    FROM tbl_produto
                    WHERE posto = $posto
                    ORDER BY codigo ASC
                ";
        $res = pg_query($con, $sql);


        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=relatorio_produto.csv');

        $output = fopen('php://output', 'w');

        $cabecalho = ['Código Produto', 'Descrição Produto', 'Status Produto', 'Data Cadastro'];
        fputcsv($output, $cabecalho, ';');

        while ($row = pg_fetch_assoc($res)) {

            fputcsv($output, [$row['codigo'], $row['descricao'], $row['ativo'], $row['data_input']], ';');
        }

        fclose($output);
        exit;
    }
}
