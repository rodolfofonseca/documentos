<?php
include_once __DIR__ . '/../trata-banco.php';
@session_start();
@session_cache_limiter("none");
@session_cache_expire(0);

ignore_user_abort(1);
set_time_limit(0);
header('P3P: CP="IDC DSP COR CURa ADM ADMa DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT PHY ONL COM STA"');

$pedido['nome_emitente']            = (string)  ($_GET['nome_emitente']);
$pedido['nome_resp_legal']          = (string)  ($_GET['nome_resp_legal']);
$pedido['endereco_emitente']        = (string)  ($_GET['endereco_emitente']);
$pedido['cep_emitente']             = (string)  ($_GET['cep_emitente']);
$pedido['cnpj_emitente']            = (string)  ($_GET['cpf_cnpj_emitente']);
$pedido['cpf_resp_legal']           = (string)  ($_GET['cpf_resp_legal']);
$pedido['email_emitente']           = (string)  ($_GET['email_emitente']);
$pedido['fone_fax_emitente']        = (string)  ($_GET['fone_fax_emitente']);
$pedido['data_vencimento']          = (string)  ($_GET['data_vencimento']);
$pedido['codigo_destinatario']      = (string)  ($_GET['codigo_destinatario']);
$pedido['nome_destinatario']        = (string)  ($_GET['nome_destinatario']);
$pedido['endereco_destinatario']    = (string)  ($_GET['endereco_destinatario']);
$pedido['cidade_destinatario']      = (string)  ($_GET['cidade_destinatario']);
$pedido['estado_destinatario']      = (string)  ($_GET['estado_destinatario']);
$pedido['cep_destinatario']         = (string)  ($_GET['cep_destinatario']);
$pedido['cnpj_destinatario']        = (string)  ($_GET['cnpj_destinatario']);
$pedido['insc_est_destinatario']    = (string)  ($_GET['insc_est_destinatario']);
$pedido['valor_total']              = (float)   ($_GET['valor_total']);
$pedido['sigla_moeda']              = (string)  ($_GET['sigla_moeda']);
$pedido['num_nf']                   	= (string)  ($_GET['num_nf']);
$pedido['pg']                       = (isset($_GET['qtd_pg']))                              ? str_pad(($_GET['qtd_pg']), 3, '0', STR_PAD_LEFT)                : '';
$pedido['qtd_reg']                  = (isset($_GET['qtd_reg']))                             ? intval(($_GET['qtd_reg']), 10)                                  : 0;
$pedido['cod_clie']                 = (isset($_GET['cod_clie']))                            ? intval(($_GET['cod_clie']), 10)                                 : 0;
$pedido['data_emissao']             = (isset($_GET['data_emissao']))                        ? strval(($_GET['data_emissao']))                                 : '';
$pedido['numeroParcela']            = (isset($_GET['numero_parcela']))                      ? intval(($_GET['numero_parcela']), 10)                           : 0;

// Remover Pedidos da SESSION
$pg = array('qtd' => 1, 'qtd_pedido' => 0);
for($index = 1; $index <= $pedido['qtd_reg']; $index = $index + 1) {
    $pg['qtd_pedido'] = $pg['qtd_pedido'] + 1;
    if ($pg['qtd_pedido'] == 20) {
        $pg['qtd'] = $pg['qtd'] + 1;
    }
    if (isset($_SESSION['CLIE' . str_pad($pedido['cod_clie'], 4, '0', STR_PAD_LEFT) . '_PEDIDO_PG' . str_pad($pedido['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($pedido['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_nome'])) {
        unset($_SESSION['CLIE' . str_pad($pedido['cod_clie'], 4, '0', STR_PAD_LEFT) . '_PEDIDO_PG' . str_pad($pedido['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($pedido['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_nome']);
    }
    if (isset($_SESSION['CLIE' . str_pad($pedido['cod_clie'], 4, '0', STR_PAD_LEFT) . '_PEDIDO_PG' . str_pad($pedido['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($pedido['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_nome'])) {
        unset($_SESSION['CLIE' . str_pad($pedido['cod_clie'], 4, '0', STR_PAD_LEFT) . '_PEDIDO_PG' . str_pad($pedido['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($pedido['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_quantidade']);
    }
    if (isset($_SESSION['CLIE' . str_pad($pedido['cod_clie'], 4, '0', STR_PAD_LEFT) . '_PEDIDO_PG' . str_pad($pedido['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($pedido['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_valor_total'])) {
        unset($_SESSION['CLIE' . str_pad($pedido['cod_clie'], 4, '0', STR_PAD_LEFT) . '_PEDIDO_PG' . str_pad($pedido['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($pedido['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_valor_total']);
    }
    if (isset($_SESSION['CLIE' . str_pad($pedido['cod_clie'], 4, '0', STR_PAD_LEFT) . '_PEDIDO_PG' . str_pad($pedido['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($pedido['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_data_vencimento'])) {
        unset($_SESSION['CLIE' . str_pad($pedido['cod_clie'], 4, '0', STR_PAD_LEFT) . '_PEDIDO_PG' . str_pad($pedido['pg'], 3, '0', STR_PAD_LEFT) . '_REG' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_NumeroParcela' . str_pad($pedido['numeroParcela'], 3, '0', STR_PAD_LEFT) . '_data_vencimento']);
    }
}

?>
