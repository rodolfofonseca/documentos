<?php
include_once __DIR__ . '/../trata-banco.php';
@session_start();
@session_cache_limiter("none");
@session_cache_expire(0);

ignore_user_abort(1);
set_time_limit(0);
header('P3P: CP="IDC DSP COR CURa ADM ADMa DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT PHY ONL COM STA"');

$recibo['nome_emitente']        = (isset($_GET['nome_emitente']))           ? strval(($_GET['nome_emitente']))                                 : '';
$recibo['endereco_emitente']    = (isset($_GET['endereco_emitente']))       ? strval(($_GET['endereco_emitente']))                             : '';
$recibo['cidade_emitente']      = (isset($_GET['cidade_emitente']))         ? strval(($_GET['cidade_emitente']))                             : '';
$recibo['estado_emitente']      = (isset($_GET['estado_emitente']))         ?strval(($_GET['estado_emitente']))                             : '';
$recibo['cep_emitente']         = (isset($_GET['cep_emitente']))            ? strval(($_GET['cep_emitente']))                                  : '';
$recibo['cnpj_emitente']        = (isset($_GET['cnpj_emitente']))           ? strval(($_GET['cnpj_emitente']))                                 : '';
$recibo['cpf_resp_legal']       = (isset($_GET['cpf_resp_legal']))          ? strval(($_GET['cpf_resp_legal']))                                : '';
$recibo['email_emitente']       = (isset($_GET['email_emitente']))          ? strval(($_GET['email_emitente']))                                : '';
$recibo['telefone_emitente']    = (isset($_GET['telefone_emitente']))       ? strval(($_GET['telefone_emitente']))                             : '';
$recibo['fax_emitente']         = (isset($_GET['fax_emitente']))            ? strval(($_GET['fax_emitente']))                                  : '';
$recibo['nome_rl_emitente']     = (isset($_GET['nome_rl_emitente']))        ? strval(($_GET['nome_rl_emitente']))                              : '';
$recibo['cpf_rl_emitente']      = (isset($_GET['cpf_rl_emitente']))         ? strval(($_GET['cpf_rl_emitente']))                               : '';

$recibo['valor']                = (isset($_GET['valor']))                   ? strval(($_GET['valor']))                                         : '';
$recibo['data_vencimento']      = (isset($_GET['data_vencimento']))         ? strval(($_GET['data_vencimento']))                               : '';
$recibo['codigo_destinatario']  = (isset($_GET['codigo_destinatario']))     ? str_pad(($_GET['codigo_destinatario']), 4, '0', STR_PAD_LEFT)    : '0000';
$recibo['nome_destinatario']    = (isset($_GET['nome_destinatario']))       ? strval(($_GET['nome_destinatario']))                             : '';
$recibo['endereco_destinatario']= (isset($_GET['endereco_destinatario']))   ? strval(($_GET['endereco_destinatario']))                         : '';
$recibo['cidade_destinatario']  = (isset($_GET['cidade_destinatario']))     ? strval(($_GET['cidade_destinatario']))                           : '';
$recibo['estado_destinatario']  = (isset($_GET['estado_destinatario']))     ? strval(($_GET['estado_destinatario']))                           : '';
$recibo['cep_destinatario']     = (isset($_GET['cep_destinatario']))        ? strval(($_GET['cep_destinatario']))                              : '';
$recibo['cnpj_destinatario']    = (isset($_GET['cnpj_destinatario']))       ? strval(($_GET['cnpj_destinatario']))                             : '';
$recibo['sigla_moeda']          = (isset($_GET['sigla_moeda']))             ? strval(($_GET['sigla_moeda']))                                   : '';
$recibo['pg']                   = (isset($_GET['qtd_pg']))                  ? str_pad(($_GET['qtd_pg']), 3, '0', STR_PAD_LEFT)                 : '';
$recibo['qtd_reg']              = (isset($_GET['qtd_reg']))                 ? intval(($_GET['qtd_reg']), 10)                                   : 0;
$recibo['numeroParcela']        = (isset($_GET['numero_parcela']))          ? intval(($_GET['numero_parcela']), 10)                            : 0;
$recibo['total_parcela']        = (isset($_GET['total_parcela']))          ? intval(($_GET['total_parcela']), 10)                            : 1;
// $recibo['codigo_destinatario']             = (isset($_GET['codigo_destinatario']))                ? intval(urldecode($_GET['codigo_destinatario']), 10)                                  : 0;

?>
<html>
    <head>
        <title>Recibo</title>
        <style type="text/css">
            #recibo {
                background-color: #FAFAFA;
                border-collapse: collapse;
                border: 2px solid #000000;
                font: 11px Verdana;
                width: 850px;
                margin-top: 20px;
                margin-right: 10px;
                margin-bottom: 10px;
                margin-left: 10px;
            }
            #recibo tr {
                border: 1px solid #000000;
                padding: 5px;
                margin: 10px;
            }
            #recibo td {
                border: 1px solid;
                padding: 5px;
                margin: 10px;
            }
            #recibo div {
                font: 11px Verdana;
                margin-top: 20px;
                margin-right: 40px;
                margin-bottom: 20px;
                margin-left: 40px;
            }
            #recibo p{
                font: 11px Verdana;
                margin-top: 20px;
                margin-right: 40px;
                margin-bottom: 20px;
                margin-left: 40px;
            }
        </style>
    </head>
    <body>
        <table id="recibo">
            <tr>
                <td colspan="1" style="border: 0px none;">
                    <p>
                        <?php if (file_exists('../Dados/Img_Loja/logotipo.jpg')) { ?>
                            <img src="../Dados/Img_Loja/logotipo.jpg" alt="Logotipo" border="0" ></img>
                        <?php } ?>
                    </p>
                </td>
                <td colspan="6" style="border: 0px none;">
                    <p style="margin-left: 0px !important">
                    <?php
                        echo '<b>' . $recibo['nome_emitente'] . '</b><br>' .
                        'CNPJ: ' . $recibo['cnpj_emitente'] . '<br>' .
                        'Endereço: ' . $recibo['endereco_emitente'] . '<br>' .
                        'Cidade: ' . $recibo['cidade_emitente'] . '/' . $recibo['estado_emitente'] . '<br/>'
                        . 'E-mail: ' . $recibo['email_emitente'] . '<br>' .
                        'Telefone: ' . $recibo['telefone_emitente'] . '<br>' .
                        'Fax: ' . $recibo['fax_emitente'] . '<br>';
                    ?>
                    </p>
                </td>
            </tr>
            <tr style="padding: 0; margin: 0;">
                <td colspan="7"><p style="font: 900 15px verdana;">Recibo de Pagamentos</p></td>
            </tr>
            <tr>
                <td style="width: 310px !important"><b>Descrição</b></td>
                <td style="width: 40px !important; text-align: center;"><b>Parc.</b></td>
                <td style="width: 40px !important; text-align: center;"><b>Qtd.</b></td>
                <td style="width: 80px !important"><b>Valor (R$)</b></td>
                <td style="width: 100px !important"><b>Desconto (R$)</b></td>
                <td style="width: 100px !important"><b>Vencimento</b></td>
                <td style="width: 100px !important"><b>Total (R$)</b></td>
            </tr>
            <?php
            $valor_total = (float) 0;
            for($index = 1; $index <= $recibo['qtd_reg']; $index = $index + 1) {
                $valor_total = $valor_total + $_SESSION['CLIE' . str_pad($recibo['codigo_destinatario'], 4, '0', STR_PAD_LEFT) . '_RECIBO_PG' . str_pad($recibo['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($recibo['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_valor_total'];
                ?>
                <tr>
                    <td>
                        <?php echo ($_SESSION['CLIE' . str_pad($recibo['codigo_destinatario'], 4, '0', STR_PAD_LEFT) . '_RECIBO_PG' . str_pad($recibo['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($recibo['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_nome']); ?>
                    </td>
                    <td style="text-align: right;">
                        <?php echo $recibo['numeroParcela'] . '/' . $recibo['total_parcela'] ?>
                    </td>
                    <td style="text-align: center;">
                        <?php echo $_SESSION['CLIE' . str_pad($recibo['codigo_destinatario'], 4, '0', STR_PAD_LEFT) . '_RECIBO_PG' . str_pad($recibo['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT)  . '_NumeroParcela' . str_pad($recibo['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_quantidade']; ?>
                    </td>
                    <td style="text-align: right;">
                        <?php echo $recibo['sigla_moeda'] . ' ' . number_format($_SESSION['CLIE' . str_pad($recibo['codigo_destinatario'], 4, '0', STR_PAD_LEFT) . '_RECIBO_PG' . str_pad($recibo['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT)  . '_NumeroParcela' . str_pad($recibo['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_valor_unitario'], 2, ',', ''); ?>
                    </td>
                    <td style="text-align: right;">
                        <?php echo $recibo['sigla_moeda'] . ' ' . number_format($_SESSION['CLIE' . str_pad($recibo['codigo_destinatario'], 4, '0', STR_PAD_LEFT) . '_RECIBO_PG' . str_pad($recibo['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT)  . '_NumeroParcela' . str_pad($recibo['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_valor_desconto'], 2, ',', ''); ?>
                    </td>
                    <td style="text-align: center;">
                        <?php echo $_SESSION['CLIE' . str_pad($recibo['codigo_destinatario'], 4, '0', STR_PAD_LEFT) . '_RECIBO_PG' . str_pad($recibo['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT)  . '_NumeroParcela' . str_pad($recibo['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_data_vencimento']; ?>
                    </td>
                    <td style="text-align: right;">
                        <?php echo $recibo['sigla_moeda'] . ' ' . number_format($_SESSION['CLIE' . str_pad($recibo['codigo_destinatario'], 4, '0', STR_PAD_LEFT) . '_RECIBO_PG' . str_pad($recibo['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT)  . '_NumeroParcela' . str_pad($recibo['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_valor_total'], 2, ',', ''); ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            <tr>
                <td colspan="5"><b>Total</b></td>
                <td></td>
                <td style="text-align: right;"><?php echo $recibo['sigla_moeda'] . ' ' . number_format($valor_total, 2, ',', '') ?></td>
            </tr>
            <tr>
                <td colspan="7"><b>Observações/Comentários:</b></td>
            </tr>
            <tr>
                <td colspan="7">
                    <p>
                    <?php
                        echo 'Código: <b>' . $recibo['codigo_destinatario'] . '</b><br/>';
                        echo 'Nome: <b>' . $recibo['nome_destinatario'] . '</b><br>';
                        echo 'CNPJ: ' . $recibo['cnpj_destinatario'] . '<br>';
                        echo 'Endereço: ' . $recibo['endereco_destinatario'] . '<br>';
                        echo 'Cidade: ' . $recibo['cidade_destinatario'] . '/' . $recibo['estado_destinatario'] . '<br/>';
                        echo 'CEP: ' . $recibo['cep_destinatario'] . '<br/>';
                    ?>
                    </p>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    <p>
                        Declaramos quitado o valor acima, descrito como "Total", nesta data:<br><br>
                        <img src="../Imagens/caixa_selecao.png" ></img>&nbsp;Dinheiro&nbsp;&nbsp;&nbsp;&nbsp;<img src="../Imagens/caixa_selecao.png" ></img>&nbsp;Duplicata&nbsp;&nbsp;&nbsp;&nbsp;<img src="../Imagens/caixa_selecao.png" ></img>&nbsp;Outro&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <img src="../Imagens/caixa_selecao.png" ></img>&nbsp;Cheque&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="../Imagens/caixa_selecao.png" ></img>&nbsp;Nota Promissória
                        <br><br><br>

                        Recebido em : ___/___/______

                        <br><br><br><br>
                        _________________________________________________________

                        <br><br>
                        <?php
                        echo '<b>' . $recibo['nome_emitente'] . '</b><br>';
                        echo 'CNPJ: ' . $recibo['cnpj_emitente'] . '<br><br>';
                        echo $recibo['nome_rl_emitente'] . '<br>';
                        echo 'CPF: ' . $recibo['cpf_rl_emitente'] . '<br><br>';
                        echo 'Autorizado a assinar quitação:<br><br>';
                        echo '___________________________________________________<br><br>';
                        echo 'RG ou CPF__________________________________________<br><br><br><br><br><br><br><br>';
                        ?>
                    </p>
                </td>
            </tr>
        </table>
    </body>
</html>
<?php

// Removo todos os serviços da SESSION.
$pg = array('qtd' => 1, 'qtd_serv' => 0);
for($index = 1; $index <= $recibo['qtd_reg']; $index = $index + 1) {
    $pg['qtd_serv'] = $pg['qtd_serv'] + 1;
    if ($pg['qtd_serv'] == 20) {
        $pg['qtd'] = $pg['qtd'] + 1;
    }
    if (isset($_SESSION['CLIE' . str_pad($recibo['codigo_destinatario'], 4, '0', STR_PAD_LEFT) . '_RECIBO_PG' . str_pad($recibo['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($recibo['numeroParcela'], 3, '0', STR_PAD_LEFT)  . '_nome'])) {
        unset($_SESSION['CLIE' . str_pad($recibo['codigo_destinatario'], 4, '0', STR_PAD_LEFT) . '_RECIBO_PG' . str_pad($recibo['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($recibo['numeroParcela'], 3, '0', STR_PAD_LEFT)  . '_nome']);
    }
    if (isset($_SESSION['CLIE' . str_pad($recibo['codigo_destinatario'], 4, '0', STR_PAD_LEFT) . '_RECIBO_PG' . str_pad($recibo['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($recibo['numeroParcela'], 3, '0', STR_PAD_LEFT)  . '_nome'])) {
        unset($_SESSION['CLIE' . str_pad($recibo['codigo_destinatario'], 4, '0', STR_PAD_LEFT) . '_RECIBO_PG' . str_pad($recibo['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($recibo['numeroParcela'], 3, '0', STR_PAD_LEFT)  . '_quantidade']);
    }
    if (isset($_SESSION['CLIE' . str_pad($recibo['codigo_destinatario'], 4, '0', STR_PAD_LEFT) . '_RECIBO_PG' . str_pad($recibo['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($recibo['numeroParcela'], 3, '0', STR_PAD_LEFT)  . '_valor_total'])) {
        unset($_SESSION['CLIE' . str_pad($recibo['codigo_destinatario'], 4, '0', STR_PAD_LEFT) . '_RECIBO_PG' . str_pad($recibo['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($recibo['numeroParcela'], 3, '0', STR_PAD_LEFT)  . '_valor_total']);
    }
    if (isset($_SESSION['CLIE' . str_pad($recibo['codigo_destinatario'], 4, '0', STR_PAD_LEFT) . '_RECIBO_PG' . str_pad($recibo['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($recibo['numeroParcela'], 3, '0', STR_PAD_LEFT)  . '_data_vencimento'])) {
        unset($_SESSION['CLIE' . str_pad($recibo['codigo_destinatario'], 4, '0', STR_PAD_LEFT) . '_RECIBO_PG' . str_pad($recibo['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($recibo['numeroParcela'], 3, '0', STR_PAD_LEFT)  . '_data_vencimento']);
    }
}
