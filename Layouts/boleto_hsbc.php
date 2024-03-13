<?php
include_once __DIR__ . '/../trata-banco.php';
@session_start();
@session_cache_limiter("none");
@session_cache_expire(0);

ignore_user_abort(1);
set_time_limit(0);
header('P3P: CP="IDC DSP COR CURa ADM ADMa DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT PHY ONL COM STA"');

/**
 * @var float - Percentual a ser acrecentado no boleto.
 */
$bol["taxa_boleto"] = (isset($_GET['Taxa_Boleto'])) ? floatval(urldecode($_GET['Taxa_Boleto'])) : 0;
/**
 * @var string - Data de vencimento do boleto no formato DD/MM/AAAA.
 */
$bol["data_venc"] = (isset($_GET['Data_Venc'])) ? strval(urldecode($_GET['Data_Venc'])) : '';
/**
 * @var float - Valor cobrado pelo Serviço ou Mercadoria (REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal).
 */
$bol["valor_cobrado"] = (isset($_GET['Vlr'])) ? floatval(urldecode($_GET['Vlr'])) : 0;
/**
 * @var string - Número do documento - REGRA: Máximo de 13 digitos.
 */
$bol["num_doc"] = (isset($_GET['Num_Doc'])) ? strval(urldecode($_GET['Num_Doc'])) : '';
/**
 * @var string - Data do Documento.
 */
$bol["data_doc"] = (isset($_GET['Data_Emis'])) ? strval(urldecode($_GET['Data_Emis'])) : '';
/**
 * @var string - Data do Processamento.
 */
$bol["data_process"] = date("d/m/Y");
/**
 * @var string - Nome do Cliente (Sacado).
 */
$bol['nome_cliente'] = (isset($_GET['Nome_Cli'])) ? strval(urldecode($_GET['Nome_Cli'])) : '';
/**
 * @var string - Endereço do Cliente (Sacado).
 */
$bol['endereco_cliente'] = (isset($_GET['End_Cli'])) ? strval(urldecode($_GET['End_Cli'])) : '';
/**
 * @var string - Cidade/Estado do Cliente (Sacado).
 */
$bol['cid_uf_cli'] = (isset($_GET['cid_uf_cli'])) ? strval(urldecode($_GET['cid_uf_cli'])) : '';
/**
 * @var string - CEP do Cliente (Sacado).
 */
$bol['cep_cliente'] = (isset($_GET['cep_cli'])) ? strval(urldecode($_GET['cep_cli'])) : '';
/**
 * @var string - Demonstrativo 1 (Parte de Cima - Opcional).
 */
$bol['demo1'] = (isset($_GET['demo1'])) ? strval(urldecode($_GET['demo1'])) : '';
/**
 * @var string - Demonstrativo 2 (Parte de Cima - Opcional).
 */
$bol['demo2'] = (isset($_GET['demo2'])) ? strval(urldecode($_GET['demo2'])) : '';
/**
 * @var string - Demonstrativo 3 (Parte de Cima - Opcional).
 */
$bol['demo3'] = (isset($_GET['demo3'])) ? strval(urldecode($_GET['demo3'])) : '';
/**
 * @var string - Instrução 1 (Parte de Baixo - Opcional).
 */
$bol['inst1'] = (isset($_GET['inst1'])) ? strval(urldecode($_GET['inst1'])) : '';
/**
 * @var string - Instrução 2 (Parte de Baixo - Opcional).
 */
$bol['inst2'] = (isset($_GET['inst2'])) ? strval(urldecode($_GET['inst2'])) : '';
/**
 * @var string - Instrução 3 (Parte de Baixo - Opcional).
 */
$bol['inst3'] = (isset($_GET['inst3'])) ? strval(urldecode($_GET['inst3'])) : '';
/**
 * @var string - Instrução 4 (Parte de Baixo - Opcional).
 */
$bol['inst4'] = (isset($_GET['inst4'])) ? strval(urldecode($_GET['inst4'])) : '';
/**
 * @var string - Quantidade (Opcional).
 */
$bol['qtd'] = (isset($_GET['qtd'])) ? strval(urldecode($_GET['qtd'])) : '';
/**
 * @var string - Valor Unitário (Opcional - Usar só quando colocar Quantidade).
 */
$bol['val_unit'] = (isset($_GET['val_unit'])) ? strval(urldecode($_GET['val_unit'])) : '';
/**
 * @var string - Aceite.
 */
$bol['aceite'] = (isset($_GET['aceite'])) ? strval(urldecode($_GET['aceite'])) : 'DS';
/**
 * @var string - Espécie (Ex: R$, US$).
 */
$bol["esp"] = (isset($_GET['Moeda'])) ? strval(urldecode($_GET['Moeda'])) : 'R$';
/**
 * @var string - Espécie do documento (Ex: DS, DM).
 */
$bol['esp_doc'] = (isset($_GET['Tipo_Doc'])) ? strval(urldecode($_GET['Tipo_Doc'])) : '';
/**
 * @var string - Código do Cedente (Somente 7 digitos).
 */
$bol['cod_ced'] = (isset($_GET['Cod_Ced'])) ? strval(urldecode($_GET['Cod_Ced'])) : '';
/**
 * @var string - Código da Carteira.
 */
$bol['cart_ced'] = (isset($_GET['Cart'])) ? strval(urldecode($_GET['Cart'])) : '';
/**
 * @var string - Identificação.
 */
$bol['ident_ced'] = (isset($_GET['Nome_Ced'])) ? strval(urldecode($_GET['Nome_Ced'])) : '';
/**
 * @var string - CPF/CNPJ do Cedente.
 */
$bol['cpf_cnpj_ced'] = (isset($_GET['CNPJ_Ced'])) ? strval(urldecode($_GET['CNPJ_Ced'])) : '';
/**
 * @var string - Endereço do Cedente.
 */
$bol['end_ced'] = (isset($_GET['endereco'])) ? strval(urldecode($_GET['endereco'])) : '';
/**
 * @var string - Cidade/Estado do Cedente.
 */
$bol['cid_uf_ced'] = (isset($_GET['cid_uf_ced'])) ? strval(urldecode($_GET['cid_uf_ced'])) : '';
/**
 * @var string - Razão Social do Cedente (48 caracteres para não estourar o campo).
 */
$bol['razao_soc_ced'] = (isset($_GET['Nome_Ced'])) ? substr(urldecode($_GET['Nome_Ced']), 0, 48) : '';


// ------------------------- DADOS DINÂMICOS DO SEU CLIENTE PARA A GERAÇÃO DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formulário c/ POST, GET ou de BD (MySql,Postgre,etc)	//

// DADOS DO BOLETO PARA O SEU CLIENTE
$taxa_boleto    = $bol["taxa_boleto"];
$valor_cobrado  = str_replace(",", ".",$bol["valor_cobrado"]);
$valor_boleto   = number_format($valor_cobrado+(($taxa_boleto * $valor_cobrado) / 100), 2, ',', '');

$dadosboleto["numero_documento"]    = $bol["num_doc"];
$dadosboleto["data_vencimento"]     = $bol["data_venc"];
$dadosboleto["data_documento"]      = $bol["data_doc"];
$dadosboleto["data_processamento"]  = $bol["data_process"];
$dadosboleto["valor_boleto"]        = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"]      = $bol['nome_cliente'];
$dadosboleto["endereco1"]   = $bol['endereco_cliente'];
$dadosboleto["endereco2"]   = $bol['cid_uf_cli'] . ' - ' . $bol['cep_cliente'];

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"]  = $bol['demo1'];
$dadosboleto["demonstrativo2"]  = $bol['demo2'];
$dadosboleto["demonstrativo3"]  = $bol['demo3'];
$dadosboleto["instrucoes1"]     = $bol['inst1'];
$dadosboleto["instrucoes2"]     = $bol['inst2'];
$dadosboleto["instrucoes3"]     = $bol['inst3'];
$dadosboleto["instrucoes4"]     = $bol['inst4'];

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"]      = $bol['qtd'];
$dadosboleto["valor_unitario"]  = $bol['val_unit'];
$dadosboleto["aceite"]          = $bol['aceite'];
$dadosboleto["especie"]         = $bol["esp"];
$dadosboleto["especie_doc"]     = $bol['esp_doc'];


// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //

// DADOS PERSONALIZADOS - HSBC
$dadosboleto["codigo_cedente"]  = $bol['cod_ced'];
$dadosboleto["carteira"]        = $bol['cart_ced'];

// SEUS DADOS
$dadosboleto["identificacao"]   = $bol['ident_ced'];
$dadosboleto["cpf_cnpj"]        = $bol['cpf_cnpj_ced'];
$dadosboleto["endereco"]        = $bol['end_ced'];
$dadosboleto["cidade_uf"]       = $bol['cid_uf_ced'];
$dadosboleto["cedente"]         = $bol['razao_soc_ced'];

// NÃO ALTERAR!
include_once("BoletoHSBC/include/funcoes_hsbc.php");
include_once("BoletoHSBC/include/layout_hsbc.php");
?>
