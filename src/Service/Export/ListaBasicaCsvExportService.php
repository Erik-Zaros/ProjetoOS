<?php

namespace App\Service\Export;

use App\Core\Db;

class ListaBasicaCsvExportService
{
    public function gerar($produtoId, $posto)
    {
        $con = Db::getConnection();
        $produtoId = intval($produtoId);
        $posto = intval($posto);

        $sqlProduto = "
            SELECT codigo, descricao 
            FROM tbl_produto 
            WHERE produto = $produtoId
        ";
        $resProduto = pg_query($con, $sqlProduto);
        $produto = pg_fetch_assoc($resProduto);
        
        if (!$produto) {
            echo "Produto não encontrado.";
            exit;
        }

        $sql = "
            SELECT p.codigo, p.descricao, 
                   CASE WHEN p.ativo THEN 'Ativo' ELSE 'Inativo' END AS status,
                   to_char(p.data_input, 'DD/MM/YYYY') AS data_cadastro
            FROM tbl_lista_basica lb
            INNER JOIN tbl_peca p ON lb.peca = p.peca
            WHERE lb.produto = $produtoId AND lb.posto = $posto
            ORDER BY p.descricao ASC
        ";
        $res = pg_query($con, $sql);

        header('Content-Type: text/csv; charset=UTF-8');
        header("Content-Disposition: attachment; filename=relatorio_lista_basica_{$produto['codigo']}.csv");

        $output = fopen('php://output', 'w');

        $cabecalho = ['Código Peça', 'Descrição Peça', 'Status', 'Data Cadastro'];
        fputcsv($output, $cabecalho, ';');

        if (pg_num_rows($res) === 0) {

            fputcsv($output, ['Nenhuma peça amarrada para este produto', '', '', ''], ';');

        } else {

            while ($row = pg_fetch_assoc($res)) {

                fputcsv($output, [
                    $row['codigo'],
                    $row['descricao'],
                    $row['status'],
                    $row['data_cadastro']
                ], ';');
            }
        }

        fclose($output);
        exit;
    }
}
