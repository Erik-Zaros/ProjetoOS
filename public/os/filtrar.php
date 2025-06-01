<?php

require '../../vendor/autoload.php';
session_start();

use App\Controller\OsController;

$result = OsController::filtrar($_POST);
echo json_encode($result);
