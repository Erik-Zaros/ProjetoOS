<?php

include '../../model/dbconfig.php';

function finalizaOS() {

    global $con;

    $os = $_POST['os'] ?? '';

    if (!empty($os)) {
        $sql = "UPDATE tbl_os SET finalizada = 't' WHERE os = '$os'";

        $update = pg_query($con, $sql);

        if ($update) {
            echo "Ordem de serviço $os finalizada com sucesso!";
        } else {
            echo "Erro ao finalizar ordem de serviço: " . pg_last_error($con);
        }
    }

}

finalizaOS();

pg_close($con);
?>
