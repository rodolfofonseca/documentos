<?php
// +----------------------------------------------------------------------+
// | BoletoPhp - Versão Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo está disponível sob a Licença GPL disponível pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Você deve ter recebido uma cópia da GNU Public License junto com     |
// | esse pacote; se não, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colaborações de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de João Prado Maia e Pablo Martins F. Costa			      |
// | 																	  |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordenação Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto Bradesco: Ramon Soares						  |
// +----------------------------------------------------------------------+


// ------------------------- DADOS DINÂMICOS DO SEU CLIENTE PARA A GERAÇÃO DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formulário c/ POST, GET ou de BD (MySql,Postgre,etc)	//

// DADOS DO BOLETO PARA O SEU CLIENTE
$dias_de_prazo_para_pagamento = (integer) 0;
$taxa_boleto = (float) 0.0;
$data_venc = (string) date("d/m/Y", time() + (30 * 86400));
$valor_cobrado = (float) 0.0;
$valor_boleto = (float) 0.0;
$nosso_numero = (string) '';
$numero_documento = (string) '';
$data_documento = (string) date('d/m/Y');
$nome_cliente = (string) '';
$endereco_cliente_1 = (string) '';
$endereco_cliente_2 = (string) '';
$instrucoes1 = (string) '';
$instrucoes2 = (string) '';
$instrucoes3 = (string) '';
$instrucoes4 = (string) '';
$especie_documento = (string) '';
$aceite = (string) '';
$agencia = (string) '';
$agencia_dv = (string) '';
$conta = (string) '';
$conta_dv = (string) '';
$conta_cedente = (string) '';
$conta_cedente_dv = (string) '';
$carteira = (string) '';
$razao_social_empresa = (string) '';
$nome_fantasia_empresa = (string) '';
$cpf_cnpj_empresa = (string) '';
$endereco_empresa = (string) '';
$cidade_uf_empresa = (string) '';

if (isset($_REQUEST['dias_de_prazo_para_pagamento']) == true) {
    $dias_de_prazo_para_pagamento = (integer) $_REQUEST['dias_de_prazo_para_pagamento'];
}

if (isset($_REQUEST['taxa_boleto']) == true) {
    $taxa_boleto = (float) $_REQUEST['taxa_boleto'];
}

if (isset($_REQUEST['data_vencimento']) == true) {
    $data_venc = (string) $_REQUEST['data_vencimento'];
}
if (isset($_REQUEST['valor_cobrado']) == true) {
    $valor_cobrado = (float) $_REQUEST['valor_cobrado'];
}

if ($dias_de_prazo_para_pagamento > 0) {
    $data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));
}

if (isset($_REQUEST['nosso_numero']) == true) {
    $nosso_numero =  (string) $_REQUEST['nosso_numero'];
}

if (isset($_REQUEST['numero_documento']) == true) {
    $numero_documento =  (string) $_REQUEST['numero_documento'];
}

if (isset($_REQUEST['data_documento']) == true) {
    $data_documento =  (string) $_REQUEST['data_documento'];
}

if (isset($_REQUEST['nome_cliente']) == true) {
    $nome_cliente =  (string) $_REQUEST['nome_cliente'];
}

if (isset($_REQUEST['cpf_cnpj_pagador']) == true) {
    $cpf_cnpj_pagador =  (string) $_REQUEST['cpf_cnpj_pagador'];
}

if (isset($_REQUEST['endereco_cliente_1']) == true) {
    $endereco_cliente_1 =  (string) $_REQUEST['endereco_cliente_1'];
}

if (isset($_REQUEST['endereco_cliente_2']) == true) {
    $endereco_cliente_2 =  (string) $_REQUEST['endereco_cliente_2'];
}

if (isset($_REQUEST['instrucoes1']) == true) {
    $instrucoes1 =  (string) $_REQUEST['instrucoes1'];
}

if (isset($_REQUEST['instrucoes2']) == true) {
    $instrucoes2 =  (string) $_REQUEST['instrucoes2'];
}

if (isset($_REQUEST['instrucoes3']) == true) {
    $instrucoes3 =  (string) $_REQUEST['instrucoes3'];
}

if (isset($_REQUEST['instrucoes4']) == true) {
    $instrucoes4 =  (string) $_REQUEST['instrucoes4'];
}

if (isset($_REQUEST['especie_documento']) == true) {
    $especie_documento =  (string) $_REQUEST['especie_documento'];
}

if (isset($_REQUEST['aceite']) == true) {
    $aceite =  (string) $_REQUEST['aceite'];
}

if (isset($_REQUEST['agencia']) == true) {
    $agencia =  (string) $_REQUEST['agencia'];
}

if (isset($_REQUEST['agencia_dv']) == true) {
    $agencia_dv =  (string) $_REQUEST['agencia_dv'];
}

if (isset($_REQUEST['conta']) == true) {
    $conta =  (string) $_REQUEST['conta'];
}

if (isset($_REQUEST['conta_dv']) == true) {
    $conta_dv =  (string) $_REQUEST['conta_dv'];
}

if (isset($_REQUEST['conta_cedente']) == true) {
    $conta_cedente =  (string) $_REQUEST['conta_cedente'];
}

if (isset($_REQUEST['conta_cedente_dv']) == true) {
    $conta_cedente_dv =  (string) $_REQUEST['conta_cedente_dv'];
}

if (isset($_REQUEST['carteira']) == true) {
    $carteira =  (string) $_REQUEST['carteira'];
}

if (isset($_REQUEST['razao_social_empresa']) == true) {
    $razao_social_empresa =  (string) $_REQUEST['razao_social_empresa'];
}

if (isset($_REQUEST['nome_fantasia_empresa']) == true) {
    $nome_fantasia_empresa =  (string) $_REQUEST['nome_fantasia_empresa'];
}

if (isset($_REQUEST['cpf_cnpj_empresa']) == true) {
    $cpf_cnpj_empresa =  (string) $_REQUEST['cpf_cnpj_empresa'];
}

if (isset($_REQUEST['endereco_empresa']) == true) {
    $endereco_empresa =  (string) $_REQUEST['endereco_empresa'];
}

if (isset($_REQUEST['cidade_uf_empresa']) == true) {
    $cidade_uf_empresa =  (string) $_REQUEST['cidade_uf_empresa'];
}

$valor_boleto = (float) $valor_cobrado + $taxa_boleto;

$dadosboleto["nosso_numero"] = $nosso_numero;
$dadosboleto["numero_documento"] = $numero_documento;
$dadosboleto["data_vencimento"] = $data_venc;
$dadosboleto["data_documento"] = $data_documento;
$dadosboleto["data_processamento"] = $data_documento;
$dadosboleto["valor_boleto"] = number_format($valor_boleto, 2, ',', '');

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = (string) $nome_cliente;
$dadosboleto["cpf_cnpj_pagador"] = (string) $cpf_cnpj_pagador;
$dadosboleto["endereco1"] = (string) $endereco_cliente_1;
$dadosboleto["endereco2"] = (string) $endereco_cliente_2;

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = '';
$dadosboleto["demonstrativo2"] = '';
$dadosboleto["demonstrativo3"] = '';
$dadosboleto["instrucoes1"] = $instrucoes1;
$dadosboleto["instrucoes2"] = $instrucoes2;
$dadosboleto["instrucoes3"] = $instrucoes3;
$dadosboleto["instrucoes4"] = $instrucoes4;

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = '';
$dadosboleto["aceite"] = $aceite;
$dadosboleto["especie"] = SIGLA_MOEDA;
$dadosboleto["especie_doc"] = $especie_documento;


// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //


// DADOS DA SUA CONTA - Bradesco
$dadosboleto["agencia"] = $agencia; // Num da agencia, sem digito
$dadosboleto["agencia_dv"] = $agencia_dv; // Digito do Num da agencia
$dadosboleto["conta"] = $conta; 	// Num da conta, sem digito
$dadosboleto["conta_dv"] = $conta_dv; 	// Digito do Num da conta

// DADOS PERSONALIZADOS - Bradesco
$dadosboleto["conta_cedente"] = $conta_cedente; // ContaCedente do Cliente, sem digito (Somente Números)
$dadosboleto["conta_cedente_dv"] = $conta_cedente_dv; // Digito da ContaCedente do Cliente
$dadosboleto["carteira"] = $carteira;  // Código da Carteira: pode ser 06 ou 03

// SEUS DADOS
$dadosboleto["cedente"] = $razao_social_empresa;
$dadosboleto["identificacao"] = $nome_fantasia_empresa;
$dadosboleto["cpf_cnpj"] = $cpf_cnpj_empresa;
$dadosboleto["endereco"] = $endereco_empresa;
$dadosboleto["cidade_uf"] = $cidade_uf_empresa;

// NÃO ALTERAR!
include("BoletoBradesco/include/funcoes_bradesco.php");
include("BoletoBradesco/include/layout_bradesco.php");
?>
