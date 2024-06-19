<?php

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Configuration\Configuration;

require_once 'Classes/bancoDeDados.php';
require_once 'Classes/Sistema/db.php';

 Configuration::instance('cloudinary://553346733577561:KZcRLgJyqU7UtPv_h5aMpNcFKi8@dw5jerbyf?secure=true');
$upload = new UploadApi();


// //1718494508050.pdf
 $admin = new AdminApi();

$retorno = $admin->usage();

var_dump($retorno);
