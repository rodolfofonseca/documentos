<?php
include_once __DIR__ . '/../trata-banco.php';
@session_start();
@session_cache_limiter("none");
@session_cache_expire(0);

ignore_user_abort(1);
set_time_limit(0);
header('P3P: CP="IDC DSP COR CURa ADM ADMa DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT PHY ONL COM STA"');

$nota_promi['numero']               = (isset($_GET['numero']))              ? strval(($_GET['numero']))                : '';
$nota_promi['vencimento']           = (isset($_GET['vencimento']))          ? strval(($_GET['vencimento']))            : '';
$nota_promi['vencimento_extenso']   = (isset($_GET['vencimento_extenso']))  ? strval(($_GET['vencimento_extenso']))    : '';
$nota_promi['nome_dest']            = (isset($_GET['nome_dest']))           ? strval(($_GET['nome_dest']))             : '';
$nota_promi['cpf_cnpj_dest']        = (isset($_GET['cpf_cnpj_dest']))       ? strval(($_GET['cpf_cnpj_dest']))         : '';
$nota_promi['valor']                = (isset($_GET['valor']))               ? floatval(($_GET['valor']))               : 0;
$nota_promi['valor_extenso']        = (isset($_GET['valor_extenso']))       ? strval(($_GET['valor_extenso']))         : '';
$nota_promi['cidade_credor']        = (isset($_GET['cidade_credor']))       ? strval(($_GET['cidade_credor']))         : '';
$nota_promi['estado_credor']        = (isset($_GET['estado_credor']))       ? strval(($_GET['estado_credor']))         : '';
$nota_promi['endereco_emitente']    = (isset($_GET['endereco_emitente']))   ? strval(($_GET['endereco_emitente']))     : '';
$nota_promi['nome_emitente']        = (isset($_GET['nome_emitente']))       ? strval(($_GET['nome_emitente']))         : '';
$nota_promi['cpf_cnpj_emitente']    = (isset($_GET['cpf_cnpj_emitente']))   ? strval(($_GET['cpf_cnpj_emitente']))     : '';
$nota_promi['data_emissao']         = (isset($_GET['data_emissao']))        ? strval(($_GET['data_emissao']))          : '';
$nota_promi['sigla_moeda']          = (isset($_GET['sigla_moeda']))         ? strval(($_GET['sigla_moeda']))           : '';
$nota_promi['nome_avalista']        = (isset($_GET['nome_avalista']))       ? strval(($_GET['nome_avalista']))         : '';
$nota_promi['cpf_cnpj_avalista']    = (isset($_GET['cpf_cnpj_avalista']))   ? strval(($_GET['cpf_cnpj_avalista']))     : '';

?>
<html>
    <head>
        <title></title>
        <meta http-equiv=Content-Type content=text/html charset=ISO-8859-1>
        <link rel="stylesheet" type="text/css" href="../Publico/ddo.css" />
        <style type="text/css">
            body {
                position: relative;
                color: #000;
            }
            #principal {
                margin: 50px 0 0 0;
            }
            #nota_promissoria {
                margin: 20px;
                border: 2px solid #000000;;
                font: 13px monospace;
                background-color: #FAFAFA;
                width: 800px;
                -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px;
            }
            #nota_promissoria tr, #nota_promissoria td {
                text-align: justify;
                padding: 5px;
                margin: 10px;
            }
            #nota_promissoria div, #nota_promissoria p{
                text-align: justify;
                font: 13px monospace;
                margin: 5px;
            }
            .texto1 {
                font-family: monospace;
                height: 20px;
                width: 200px;
                text-align: center;
                border: 1px solid #000000;
            }
            .texto2 {
                font-family: monospace;
                height: 20px;
                width: 350px;
                text-align: center;
            }
            .texto3 {
                font-family: monospace;
                height: 20px;
                width: 720px;
            }
            #acao_popup {position: relative; display: none; float: right; margin: 5px;}
        </style>
    </head>
    <body>
        <div id="acao_popup">
            <input type="button" class="btn" onclick="imprimir()" value="Imprimir (Ctrl+P)">
        </div>
        <div id="principal">
            <table id="nota_promissoria">
                <tr>
                    <td><b>Nº:</b> <u><?php echo str_pad($nota_promi['numero'], 15, '0', STR_PAD_LEFT) ?></u></td>
                    <td><b>Vencimento:</b> <u><?php echo $nota_promi['vencimento'] ?></u></td>
                </tr>
                <tr>
                    <td colspan="2"><b style="font-size: 15px;">Valor <?php echo $nota_promi['sigla_moeda'] ?>:</b> <input class="texto1" name="texto1" value="<?php echo number_format($nota_promi['valor'], 2, ',', '.') ?>" READONLY /></td>
                </tr>
                <tr>
                    <td colspan="2">
                        Ao(s) <u><?php echo str_pad($nota_promi['vencimento_extenso'] . ' ', (85 - strlen($nota_promi['vencimento_extenso'])), '--#', STR_PAD_RIGHT); ?></u><br />
                        pagar(ei/emos) por esta única via de <b>NOTA PROMISSÓRIA</b><br />
                        a <u><?php echo $nota_promi['nome_dest']; ?></u> CPF/CNPJ <u><?php echo $nota_promi['cpf_cnpj_dest']; ?></u><br />
                        ou à sua ordem, a quantia de <br />
                        <input class="texto3" value="<?php echo str_pad($nota_promi['valor_extenso'] . ' ', (98 - strlen($nota_promi['valor_extenso'])), '--#', STR_PAD_RIGHT); ?>" READONLY /><br />
                        em moeda corrente deste país, pagável em <u><?= strtoupper($nota_promi['cidade_credor']) . '-' . $nota_promi['estado_credor'] ?></u><br /><br />
                        <b>Emitente:</b> <?php echo $nota_promi['nome_emitente']; ?><br />
                        <b>CPF/CNPJ:</b> <?php echo $nota_promi['cpf_cnpj_emitente']; ?><br />
                        <b>Endereço:</b> <?php echo $nota_promi['endereco_emitente']; ?>
                    </td>
                </tr>
                <?php if ($nota_promi['nome_avalista'] != '') { ?>
                    <tr>
                        <td colspan="2">
                            <b>Avalista</b><br/>
                            <b>Nome:</b> <?php echo $nota_promi['nome_avalista']; ?><br />
                            <b>CPF/CNPJ:</b> <?php echo $nota_promi['cpf_cnpj_avalista']; ?><br />
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td>
                        <br>
                        ___________________________________________<br>
                    </td>
                    <td>
                        <br>
                        <u><?php echo str_pad($nota_promi['data_emissao'], 18, '-', STR_PAD_BOTH); ?></u><br>
                    </td>
                </tr>
                <tr>
                    <td>
                        Assinatura do Emitente
                    </td>
                    <td>
                        Data de Emissão
                    </td>
                </tr>
            </table>
        </div>
        <script type="text/javascript">
            function imprimir() {
                document.querySelector('#acao_popup').style.display = 'none';
                print();
                window.setTimeout(function () {
                    document.querySelector('#acao_popup').style.display = 'block';
                }, 500);
            }

            window.onload = function () {
                imprimir();
            }
        </script>
    </body>
</html>
