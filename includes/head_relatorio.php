<?php
$versao_sistema = (string) "00.00";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistema de Controle de documentos físicos e digitais">
    <meta name="author" content="Rodolfo Fonseca">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <title>Admin Smart</title>
    <link href="assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="assets/extra-libs/jvector/jquery-jvectormap-2.0.2.css" rel="stylesheet" />
    <link href="dist/css/style.min.css" rel="stylesheet">
    <link href="css/alerta_css.css" rel="stylesheet">
    <script type="text/javascript" src="js/alerta.js"></script>
    <script type="text/javascript" src="dist/js/sistema.js?v=<?php echo filemtime('dist/js/sistema.js'); ?>"></script>
    <script type="text/javascript" src="dist/js/padrao.js"></script>
    <script type="text/javascript" src="js/basics.js?v=<?php echo filemtime('js/basics.js'); ?>"></script>
    <script type="text/javascript" src="js/mensagens.js?v=<?php echo filemtime('js/mensagens.js'); ?>"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <link href="css/estilo.css?v=<?php echo filemtime('css/estilo.css'); ?>" rel="stylesheet">
</head>

<body>
    <div class="preloader" id="loader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>