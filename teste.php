<?php

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Configuration\Configuration;

require_once 'Classes/bancoDeDados.php';
require_once 'Classes/Sistema/db.php';

$arquivo_configuracao = (string) 'versao_sistema.ini';


if (file_exists($arquivo_configuracao) == true) {
    $configuracao = (array) parse_ini_file($arquivo_configuracao, true);
    
    if (isset($configuracao['sistema']['versao_sistema']) == true) {
        echo $configuracao['sistema']['versao_sistema'];
    }else{
        echo 'não encontrado array';
    }
}else{
    echo 'não encontrado arquivo';
}

