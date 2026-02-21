<?php
require '../../vendor/autoload.php';
use App\Core\Db;
use App\Auth\Autenticador;
Autenticador::iniciar();
$contexto_anexo = intval($_GET['contexto_anexo'] ?? 0);
$con = Db::getConnection();
$sql = "SELECT tipo_anexo, descricao, ativo
     FROM tbl_tipo_anexo
     WHERE contexto_anexo = {$contexto_anexo}
     ORDER BY descricao";
$res  = pg_query($con, $sql);
$rows = [];
while ($row = pg_fetch_assoc($res)) $rows[] = $row;
echo json_encode($rows);