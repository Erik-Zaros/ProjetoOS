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

        if ($dados['cliente'] === 0) {
            header("Location: ../../view/cliente.php?alerta=true");
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
        header('Content-Disposition: attachment; filename=Relatorio_Cliente.csv');

        $output = fopen('php://output', 'w');

        $cabecalho = ['NOME',
                      'CPF',
                      'CEP',
                      'ENDERECO',
                      'BAIRRO',
                      'NUMERO',
                      'CIDADE',
                      'ESTADO',
                      'DATA CADASTRO'];

        fputcsv($output, $cabecalho, ';');

        while ($row = pg_fetch_assoc($res)) {
            fputcsv($output, [
                $row['nome'],
                $row['cpf'],
                $row['cep'],
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
