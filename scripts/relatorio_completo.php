<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Db;

$con = Db::getConnection();
$arquivo = __DIR__ . '/relatorio_completo.csv';
$fp = fopen($arquivo, 'w');

if (!$fp) {
    die("Erro ao abrir arquivo para escrita.");
}

function escreverSecao($fp, $titulo, $cabecalho, $dados) {
    fputcsv($fp, []);
    fputcsv($fp, [$titulo]);
    fputcsv($fp, $cabecalho);
    foreach ($dados as $linha) {
        fputcsv($fp, $linha);
    }
}

$sql = "
    SELECT os.os, os.data_abertura, os.nome_consumidor, os.cpf_consumidor,
           p.codigo AS produto_codigo, p.descricao AS produto_descricao,
           c.nome AS cliente_nome, c.cpf AS cliente_cpf, os.finalizada
      FROM tbl_os os
 LEFT JOIN tbl_produto p ON p.produto = os.produto
 LEFT JOIN tbl_cliente c ON c.cliente = os.cliente
 ORDER BY os.os ASC
";
$res = pg_query($con, $sql);
$dados = pg_fetch_all($res) ?: [];
$cabecalho = ['OS', 'Data Abertura', 'Nome Consumidor', 'CPF Consumidor', 'Produto Código', 'Produto Descrição', 'Cliente Nome', 'Cliente CPF', 'Finalizada'];
escreverSecao($fp, 'RELATÓRIO DE ORDENS DE SERVIÇO', $cabecalho, $dados);

$sql = "SELECT produto, codigo, descricao, ativo, data_input FROM tbl_produto ORDER BY produto ASC";
$res = pg_query($con, $sql);
$dados = pg_fetch_all($res) ?: [];
$cabecalho = ['Produto ID', 'Código', 'Descrição', 'Ativo', 'Data de Cadastro'];
escreverSecao($fp, 'RELATÓRIO DE PRODUTOS', $cabecalho, $dados);

$sql = "SELECT peca, codigo, descricao, ativo, data_input FROM tbl_peca ORDER BY peca ASC";
$res = pg_query($con, $sql);
$dados = pg_fetch_all($res) ?: [];
$cabecalho = ['Peça ID', 'Código', 'Descrição', 'Ativo', 'Data de Cadastro'];
escreverSecao($fp, 'RELATÓRIO DE PEÇAS', $cabecalho, $dados);

$sql = "SELECT cliente, nome, cpf, cep, endereco, bairro, numero, cidade, estado FROM tbl_cliente ORDER BY cliente ASC";
$res = pg_query($con, $sql);
$dados = pg_fetch_all($res) ?: [];
$cabecalho = ['Cliente ID', 'Nome', 'CPF', 'CEP', 'Endereço', 'Bairro', 'Número', 'Cidade', 'Estado'];
escreverSecao($fp, 'RELATÓRIO DE CLIENTES', $cabecalho, $dados);

fclose($fp);
echo "✅ Relatório gerado com sucesso: $arquivo" . PHP_EOL;

$botToken = '';
$chatId = '';
$csvPath = $arquivo;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot{$botToken}/sendDocument");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'chat_id' => $chatId,
    'caption' => '📄 Relatório ProjetoOS gerado com sucesso!',
    'document' => new CURLFile($csvPath)
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "📬 Arquivo enviado com sucesso ao Telegram!" . PHP_EOL;
} else {
    echo "❌ Erro ao enviar o arquivo ao Telegram. Resposta: $response" . PHP_EOL;
}
