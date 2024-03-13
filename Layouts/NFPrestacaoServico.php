<?php
include_once __DIR__ . '/../trata-banco.php';
@session_start();
@session_cache_limiter("none");
@session_cache_expire(0);

ignore_user_abort(1);
set_time_limit(0);
header('P3P: CP="IDC DSP COR CURa ADM ADMa DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT PHY ONL COM STA"');

$nf['nome_emitente']            = (string)  ($_GET['nome_emitente']);
$nf['nome_resp_legal']          = (string)  ($_GET['nome_resp_legal']);
$nf['endereco_emitente']        = (string)  ($_GET['endereco_emitente']);
$nf['cep_emitente']             = (string)  ($_GET['cep_emitente']);
$nf['cnpj_emitente']            = (string)  ($_GET['cpf_cnpj_emitente']);
$nf['cpf_resp_legal']           = (string)  ($_GET['cpf_resp_legal']);
$nf['email_emitente']           = (string)  ($_GET['email_emitente']);
$nf['fone_fax_emitente']        = (string)  ($_GET['fone_fax_emitente']);
$nf['data_vencimento']          = (string)  ($_GET['data_vencimento']);
$nf['codigo_destinatario']      = (string)  ($_GET['codigo_destinatario']);
$nf['nome_destinatario']        = (string)  ($_GET['nome_destinatario']);
$nf['endereco_destinatario']    = (string)  ($_GET['endereco_destinatario']);
$nf['cidade_destinatario']      = (string)  ($_GET['cidade_destinatario']);
$nf['estado_destinatario']      = (string)  ($_GET['estado_destinatario']);
$nf['cep_destinatario']         = (string)  ($_GET['cep_destinatario']);
$nf['cnpj_destinatario']        = (string)  ($_GET['cnpj_destinatario']);
$nf['insc_est_destinatario']    = (string)  ($_GET['insc_est_destinatario']);
$nf['valor_total']              = (float)   ($_GET['valor_total']);
$nf['sigla_moeda']              = (string)  ($_GET['sigla_moeda']);
$nf['num_nf']                   = (string)  ($_GET['num_nf']);
$nf['pg']                       = (isset($_GET['qtd_pg']))                              ? str_pad(($_GET['qtd_pg']), 3, '0', STR_PAD_LEFT)                : '';
$nf['qtd_reg']                  = (isset($_GET['qtd_reg']))                             ? intval(($_GET['qtd_reg']), 10)                                  : 0;
$nf['cod_clie']                 = (isset($_GET['cod_clie']))                            ? intval(($_GET['cod_clie']), 10)                                 : 0;
$nf['data_emissao']             = (isset($_GET['data_emissao']))                        ? strval(($_GET['data_emissao']))                                 : '';
$nf['numeroParcela']            = (isset($_GET['numero_parcela']))                      ? intval(($_GET['numero_parcela']), 10)                           : 0;

?>
<html>
    <head>
        <title>Nota Fiscal de Prestação de Serviços</title>
        <style type="text/css">
            body {
                position: relative;
            }
            #principal {
                margin: 50px 0 0 0;
            }
            #tabelaNFPrestacaoServico {
                background-color: #FAFAFA;
                border-collapse: collapse;
                border: 2px solid #000000;
                font: 11px Verdana;
                width: 800px;
            }
            #tabelaNFPrestacaoServico tr {
                /*border: 0px none;*/
                padding: 5px;
                margin: 5px;
            }
            #tabelaNFPrestacaoServico td {
                /*border: 0px none;*/
                padding: 5px;
                margin: 5px;
            }
            #tabelaNFPrestacaoServico div, p{
                font: 11px Verdana;
                margin: 20px 40px 20px 40px;
            }
        </style>
    </head>
    <body>
        <div id="principal">
            <table id="tabelaNFPrestacaoServico">
                <tr style="border: 0px none;">
                    <td colspan="1" style="border: 0px none;">
                        <p>
                            <?php if (file_exists('../Dados/Img_Loja/logotipo.jpg')) { ?>
                                <img src="../Dados/Img_Loja/logotipo.jpg" alt="A CONTÁBIL" title="A CONTÁBIL" border="0" >
                            <?php } ?>
                        </p>
                    </td>
                    <td colspan="2" style="border: 0px none;">
                        <p>
                        <?php
                            echo '<b>' . $nf['nome_emitente'] . '</b><br />Endereço: ' . $nf['endereco_emitente'] . '<br />';
                            echo 'CNPJ: ' . $nf['cnpj_emitente'] . '<br />E-mail: ' . $nf['email_emitente'] . '<br />';
                            echo 'Fone/Fax: ' . $nf['fone_fax_emitente'] . '<br />';
                        ?>
                        </p>
                    </td>
                    <td colspan="1"></td>
                </tr>
                <tr>
                    <td colspan="4" style="font: 900 16px Verdana;"><u>Nota Fiscal de Prestação de Serviços</u></td>
                </tr>
                <tr>
                    <td colspan="2"><b>Data:</b> <?php echo $nf['data_emissao'] ?></td>
                    <td colspan="2"><b style="color: red">Nº: <?php echo str_pad($nf['num_nf'], 6, '0', STR_PAD_LEFT); ?></b></td>
                </tr>
                <tr>
                    <td colspan="4"><b>Nome:</b> <?php echo $nf['nome_destinatario'];?></td>
                </tr>
                <tr>
                    <td colspan="4"><b>Endereço:</b> <?php echo $nf['endereco_destinatario'];?></td>
                </tr>
                <tr>
                    <td colspan="2"><b>Município:</b> <?php echo $nf['cidade_destinatario'];?></td>
                    <td colspan="2"><b>Estado:</b> <?php echo $nf['estado_destinatario'];?></td>
                </tr>
                <tr>
                    <td colspan="2"><b>Inscr. CNPJ/CPF:</b> <?php echo $nf['cnpj_destinatario'];?></td>
                    <td colspan="2"><b>Inscr. Est.:</b> <?php echo $nf['insc_est_destinatario'];?></td>
                </tr>
                <tr>
                    <td colspan="4">
                        <b>Condições:</b>
                        _________________________________________________________________________________________________________________
                    </td>
                </tr>
                <tr style="border: 1px solid;">
                    <td colspan="1" style="border: 1px solid"><b>Quant.</b></td>
                    <td colspan="2" style="border: 1px solid"><b>DESCRIÇÃO DOS SERVIÇOS</b></td>
                    <td colspan="1" style="border: 1px solid"><b>VALOR</b></td>
                </tr>
                <?php
                $valor_total = (float) 0;
                for ($index = 1; $index <= $nf['qtd_reg']; $index = $index + 1) {
                    $valor_total = $valor_total + $_SESSION['CLIE' . str_pad($nf['cod_clie'], 4, '0', STR_PAD_LEFT) . '_NFPS_PG' . str_pad($nf['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($nf['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_valor_total'];
                    ?>
                    <tr style="border: 1px solid">
                        <td colspan="1" style="border: 1px solid;">
                            <?php echo $_SESSION['CLIE' . str_pad($nf['cod_clie'], 4, '0', STR_PAD_LEFT) . '_NFPS_PG' . str_pad($nf['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($nf['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_quantidade']; ?>
                        </td>
                        <td colspan="2" style="border: 1px solid">
                            <?php echo $_SESSION['CLIE' . str_pad($nf['cod_clie'], 4, '0', STR_PAD_LEFT) . '_NFPS_PG' . str_pad($nf['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($nf['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_nome']; ?>
                        </td>
                        <td colspan="1" style="border: 1px solid">
                            <?php echo $nf['sigla_moeda'] . ' ' . number_format($_SESSION['CLIE' . str_pad($nf['cod_clie'], 4, '0', STR_PAD_LEFT) . '_NFPS_PG' . str_pad($nf['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($nf['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_valor_total'], 2, ',', ''); ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr style="border: 1px solid;">
                    <td style="font: 900 8px Verdana">
                    </td>
                    <td colspan="2" style="border: 1px solid;"><b>VALOR TOTAL</b></td>
                    <td style="border: 1px solid"><?php echo $nf['sigla_moeda'] . ' ' . number_format($valor_total, 2, ',', '') ?></td>
                </tr>
            </table>
        </div>
    </body>
</html>
<?php

// Removo todos os serviços da SESSION.
// $pg = array('qtd' => 1, 'qtd_serv' => 0);
// for($index = 1; $index <= $nf['qtd_reg']; $index = $index + 1) {
//     $pg['qtd_serv'] = $pg['qtd_serv'] + 1;
//     if ($pg['qtd_serv'] == 20) {
//         $pg['qtd'] = $pg['qtd'] + 1;
//     }
//     if (isset($_SESSION['CLIE' . str_pad($nf['cod_clie'], 4, '0', STR_PAD_LEFT) . '_NFPS_PG' . str_pad($nf['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($nf['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_nome'])) {
//         unset($_SESSION['CLIE' . str_pad($nf['cod_clie'], 4, '0', STR_PAD_LEFT) . '_NFPS_PG' . str_pad($nf['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($nf['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_nome']);
//     }
//     if (isset($_SESSION['CLIE' . str_pad($nf['cod_clie'], 4, '0', STR_PAD_LEFT) . '_NFPS_PG' . str_pad($nf['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($nf['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_nome'])) {
//         unset($_SESSION['CLIE' . str_pad($nf['cod_clie'], 4, '0', STR_PAD_LEFT) . '_NFPS_PG' . str_pad($nf['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($nf['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_quantidade']);
//     }
//     if (isset($_SESSION['CLIE' . str_pad($nf['cod_clie'], 4, '0', STR_PAD_LEFT) . '_NFPS_PG' . str_pad($nf['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($nf['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_valor_total'])) {
//         unset($_SESSION['CLIE' . str_pad($nf['cod_clie'], 4, '0', STR_PAD_LEFT) . '_NFPS_PG' . str_pad($nf['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($nf['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_valor_total']);
//     }
//     if (isset($_SESSION['CLIE' . str_pad($nf['cod_clie'], 4, '0', STR_PAD_LEFT) . '_NFPS_PG' . str_pad($nf['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($nf['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_data_vencimento'])) {
//         unset($_SESSION['CLIE' . str_pad($nf['cod_clie'], 4, '0', STR_PAD_LEFT) . '_NFPS_PG' . str_pad($nf['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($nf['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_data_vencimento']);
//     }
// }
