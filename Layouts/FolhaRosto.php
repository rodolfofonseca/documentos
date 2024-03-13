<?php
include_once __DIR__ . '/../trata-banco.php';
@session_start();
@session_cache_limiter("none");
@session_cache_expire(0);

ignore_user_abort(1);
set_time_limit(0);
header('P3P: CP="IDC DSP COR CURa ADM ADMa DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT PHY ONL COM STA"');


$fr['nome_cliente'] = (isset($_GET['nome_cliente']))    ? strval(($_GET['nome_cliente']))  : '';
$fr['cnpj']         = (isset($_GET['cnpj']))            ? strval(($_GET['cnpj']))          : '';
$fr['endereco']     = (isset($_GET['endereco']))        ? strval(($_GET['endereco']))      : '';
$fr['cidade']       = (isset($_GET['cidade']))          ? strval(($_GET['cidade']))        : '';
$fr['estado']       = (isset($_GET['estado']))          ? strval(($_GET['estado']))        : '';
$fr['cep']          = (isset($_GET['cep']))             ? strval(($_GET['cep']))           : '';
$fr['telefone']     = (isset($_GET['telefone']))        ? strval(($_GET['telefone']))      : '';
$fr['email']        = (isset($_GET['email']))           ? strval(($_GET['email']))         : '';

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <style type="text/css">
            body {
                position: relative;
            }
            #fr {
                border: 1px solid;
                height: 1000px;
                width: 850px;
                font: 13px Verdana;
                margin: 50px 0 0 0;
            }
            .tags {
                height: 50px;
                width: 800px;
                text-align: center;
            }
        </style>
        <script type="text/javascript" src="<?php echo PADRAO_JS; ?>"></script>
    </head>
    <body>
        <div id="fr">
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <h1 class="tags"><?php echo $fr['nome_cliente']; ?></h1>
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <?php if ($fr['cnpj'] !== '') { ?>
                <h4 class="tags">CNPJ: <?php echo $fr['cnpj'] ?></h4>
            <?php } ?>

            <?php if ($fr['endereco'] !== '' || $fr['cidade'] !== '' || $fr['estado'] !== '') { ?>
                <h4 class="tags">Endereço: <?php echo $fr['endereco'] . ', ' . $fr['cidade'] . '/' . $fr['estado']; ?></h4>
            <?php } ?>

            <?php if ($fr['cep'] !== '') { ?>
                <h4 class="tags">CEP: <?php echo $fr['cep']; ?></h4>
            <?php } ?>

            <?php if ($fr['telefone'] !== '') { ?>
                <h4 class="tags">Telefone: <?php echo $fr['telefone']; ?></h4>
            <?php } ?>

            <?php if ($fr['email'] !== '') { ?>
                <h4 class="tags">Email: <?php echo $fr['email']; ?></h4>
            <?php } ?>
        </div>
    </body>
</html>
