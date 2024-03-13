<?php
//require_once 'TrataBanco.php';
//echo "Cliente " . $Cliente['nome'] . '<br>';
Conecta4('Dados');
Conecta5('Loja');
$codigo_boleto      = (int)     0;
$banco              = (string)  '';
$codigo_cedente     = (int)     0;
$taxa_boleto        = (float)   0;
$agencia            = (int)     0;
$num_conta          = (string)  '';
$instrucao_01       = (string)  '';
$instrucao_02       = (string)  '';
$instrucao_03       = (string)  '';
$instrucao_04       = (string)  '';
$Demonstrativo_01   = (string)  '';
$Demonstrativo_02   = (string)  '';
$Demonstrativo_03   = (string)  '';
$esp_doc            = (string)  '';
$carteira           = (string)  '';
$ini_nosso_num      = (int)     0;

$Consulta5->open("SELECT * FROM DadosBoleto WHERE Codigo = {$codigo_banco_boleto}", $Conex5, 3, 4);
while ((!$Consulta5->BOF) and (!$Consulta5->EOF)) {
    # Código.
    if ($Consulta5->fields('Nome') == 'Codigo') {
        $codigo_boleto  = (int) $Consulta5->fields('Valor');
    } else
    # Banco.
    if ($Consulta5->fields('Nome') == 'Nome') {
        $banco = (string)  $Consulta5->fields('Valor');
    } else
    # Código Cedente.
    if ($Consulta5->fields('Nome') == 'Codigo_Cedente') {
        $codigo_cedente = (int)  $Consulta5->fields('Valor');
    } else
    # Taxa Boleto.
    if ($Consulta5->fields('Nome') == 'Taxa') {
        $taxa_boleto = (float)  $Consulta5->fields('Valor');
    }
    # Agência Boleto.
    if ($Consulta5->fields('Nome') == 'Agencia') {
        $agencia = (int)  $Consulta5->fields('Valor');
    }
    # Numero Conta Boleto.
    if ($Consulta5->fields('Nome') == 'NumConta') {
        $num_conta = (string)  $Consulta5->fields('Valor');
    }
    # Instrucao 01.
    if ($Consulta5->fields('Nome') == 'Instrucao_01') {
        $instrucao_01 = (string)  $Consulta5->fields('Valor');
    }
    # Instrucao 02.
    if ($Consulta5->fields('Nome') == 'Instrucao_02') {
        $instrucao_02 = (string)  $Consulta5->fields('Valor');
    }
    # Instrucao 03.
    if ($Consulta5->fields('Nome') == 'Instrucao_03') {
        $instrucao_03 = (string)  $Consulta5->fields('Valor');
    }
    # Instrucao 04.
    if ($Consulta5->fields('Nome') == 'Instrucao_04') {
        $instrucao_04 = (string)  $Consulta5->fields('Valor');
    }
    # Demonstrativo 01.
    if ($Consulta5->fields('Nome') == 'Demonstrativo_01') {
        $Demonstrativo_01 = (string)  $Consulta5->fields('Valor');
    }
    # Demonstrativo 02.
    if ($Consulta5->fields('Nome') == 'Demonstrativo_02') {
        $Demonstrativo_02 = (string)  $Consulta5->fields('Valor');
    }
    # Demonstrativo 03.
    if ($Consulta5->fields('Nome') == 'Demonstrativo_03') {
        $Demonstrativo_03 = (string)  $Consulta5->fields('Valor');
    }
    # Espécie do documento.
    if ($Consulta5->fields('Nome') == 'Especie_Doc') {
        $esp_doc = (string)  $Consulta5->fields('Valor');
    }
    # Carteira.
    if ($Consulta5->fields('Nome') == 'Carteira') {
        $carteira = (string)  $Consulta5->fields('Valor');
    }
    # Início Nosso Número.
    if ($Consulta5->fields('Nome') == 'Ini_Nosso_Numero') {
        $ini_nosso_num = (string)  $Consulta5->fields('Valor');
    }
    $Consulta5->movenext;
}
$Consulta5->close;

$nome = '';
$cpf_cnpj = '';
$endereco = '';
$Consulta5->open('SELECT * FROM DadosEmpresa', $Conex5, 3, 4);
if ((!$Consulta5->BOF) or (!$Consulta5->EOF)) {
    $nome       = (string) $Consulta5->fields('Nome');
    $cpf_cnpj   = (string) $Consulta5->fields('CPF_CNPJ');

    // Endereço.
    $tipo_log   = (int)     $Consulta5->fields('Tipo_Logradouro');
    $nome_log   = (string)  $Consulta5->fields('Nome_Logradouro');
    $numero     = (int)     $Consulta5->fields('Numero');
    $bairro     = (string)  $Consulta5->fields('Bairro');
    $tipo_comp  = (int)     $Consulta5->fields('Tipo_Complemento');
    $desc_comp  = (string)  $Consulta5->fields('Desc_Complemento');
    $cod_uf     = (int)     $Consulta5->fields('Estado');
    $cod_cidade = (int)     $Consulta5->fields('Cidade');

    $desc_tipo_log      = (string) '';
    $desc_tipo_comp     = (string) '';
    $desc_cod_uf        = (string) '';
    $desc_cod_cidade    = (string) '';

    // Tipo logradouro.
    $Consulta4->open("SELECT * FROM Tipo_Logradouro WHERE Codigo = {$tipo_log} ORDER BY Nome", $Conex4, 3, 4);
    if ((!$Consulta4->BOF) or (!$Consulta4->EOF)) {
        $desc_tipo_log = (string) $Consulta4->fields('Nome');
    }
    $Consulta4->close;

    // Tipo Complemento.
    $Consulta4->open("SELECT * FROM Tipo_Complemento WHERE Codigo = {$tipo_comp}", $Conex4, 3, 4);
    if ((!$Consulta4->BOF) or (!$Consulta4->EOF)) {
        $desc_tipo_comp = (string) $Consulta4->fields('Nome');
    }
    $Consulta4->close;

    // Estado.
    $Consulta4->open("SELECT * FROM Estados_Brasileiros WHERE Registro = {$cod_uf}", $Conex4, 3, 4);
    if ((!$Consulta4->BOF) or (!$Consulta4->EOF)) {
        $desc_cod_uf = (string) $Consulta4->fields('Sigla_UF');
    }
    $Consulta4->close;

    // Cidade.
    $Consulta4->open("SELECT * FROM Municipios_Brasileiros WHERE Codigo = {$cod_cidade}", $Conex4, 3, 4);
    if ((!$Consulta4->BOF) or (!$Consulta4->EOF)) {
        $desc_cod_cidade = (string) $Consulta4->fields('Nome');
    }
    $Consulta4->close;


    $endereco_clie = '';
    // Logradouro Cliente.
    $log_cliente = '';
    $Consulta4->open("SELECT * FROM Tipo_Logradouro WHERE Codigo = {$Cliente['tipo_logradouro']} ORDER BY Nome;", $Conex4, 3, 4);
    if ((!$Consulta4->BOF) or (!$Consulta4->EOF)) {
        $endereco_clie = (string) $Consulta4->fields('Nome') . ' ' . $Cliente['nome_logradouro'];
    }
    $Consulta4->close;

    // Complemento Cliente.
    $comp_cliente = '';
    $Consulta4->open("SELECT * FROM Tipo_Complemento WHERE Codigo = {$Cliente['tipo_complemento']};", $Conex4, 3, 4);
    if ((!$Consulta4->BOF) or (!$Consulta4->EOF)) {
        $comp_cliente = (string) $Consulta4->fields('Nome') . ' ' . $Cliente['desc_complemento'];
    }
    $Consulta4->close;

    // Cidade Cliente.
    $cidade_cliente = '';
    $Consulta4->open("SELECT * FROM Municipios_Brasileiros WHERE Codigo = {$Cliente['cidade']};", $Conex4, 3, 4);
    if ((!$Consulta4->BOF) or (!$Consulta4->EOF)) {
        $cidade_cliente = (string) $Consulta4->fields('Nome');
    }
    $Consulta4->close;

    // Estado Cliente.
    $estado_cliente = '';
    $Consulta4->open("SELECT * FROM Estados_Brasileiros WHERE Registro = {$Cliente['estado']};", $Conex4, 3, 4);
    if ((!$Consulta4->BOF) or (!$Consulta4->EOF)) {
        $estado_cliente = (string) $Consulta4->fields('Sigla_UF');
    }
    $Consulta4->close;



    // Endereço.
    $endereco = $desc_tipo_log . ' ' .  $nome_log;
    if ($desc_tipo_comp != '') {
        $endereco .= $desc_tipo_comp . ',' . $desc_comp;
    }
    // Endereço Cliente.
    $endereco_clie = $desc_tipo_log . ' ' .  $nome_log;
    if ($comp_cliente != '') {
        $endereco_clie .= $comp_cliente . ',' . $desc_comp;
    }
    $Consulta5->movenext;
}
$Consulta5->close;
Desconecta4();
Desconecta5();


// ------------------------- DADOS DINÂMICOS DO SEU CLIENTE PARA A GERAÇÃO DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formulário c/ POST, GET ou de BD (MySql,Postgre,etc)	//

// DADOS DO BOLETO PARA O SEU CLIENTE
$data_venc = (string) $Cliente['data_vencimento_contrato'];  // Prazo de X dias OU informe data: "13/04/2006";
$valor_cobrado = (string) number_format($valor_total,2,",",""); // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
$valor_cobrado = str_replace(",", ".",$valor_cobrado);
$valor_boleto = (string) number_format(FLOATVAL($valor_cobrado) + $taxa_boleto, 2, ',', '');

//$dias_de_prazo_para_pagamento = 5;
//$taxa_boleto = 0.0;
//$data_venc = "08/07/2013"; //date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006";
//$valor_cobrado = "454,00"; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
//$valor_cobrado = str_replace(",", ".",$valor_cobrado);
//$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

// Composição Nosso Numero - CEF SIGCB

$mes_fatura = (string) "06";
$ano_fatura = (string) "2013";
$Cod_Cliente_Data_15posic = (string) str_pad(strval($Cliente['codigo']) . $mes_fatura . $ano_fatura, 15, '0', STR_PAD_LEFT);
$dadosboleto["nosso_numero1"] = (string) substr($Cod_Cliente_Data_15posic, 0, 3); // tamanho 3
$dadosboleto["nosso_numero_const1"] = "2"; //constante 1 , 1=registrada , 2=sem registro
$dadosboleto["nosso_numero2"] = (string) substr($Cod_Cliente_Data_15posic, 3, 3); // tamanho 3
$dadosboleto["nosso_numero_const2"] = "4"; //constante 2 , 4=emitido pelo proprio cliente
$dadosboleto["nosso_numero3"] = (string) substr($Cod_Cliente_Data_15posic, 6, 9); // tamanho 9

$dadosboleto["numero_documento"] = (string) strval($Cliente['codigo']) . $mes_fatura . $ano_fatura;	// Num do pedido ou do documento
$dadosboleto["data_vencimento"] = (string) $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = (string) date("d/m/Y"); // Data de emissão do Boleto
$dadosboleto["data_processamento"] = (string) date("d/m/Y"); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = (string) $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = (string) strval($Cliente['codigo']) . " - " . $Cliente['nome'];
$dadosboleto["endereco1"] = (string) $Cliente['nome_tipo_logradouro'] . " " . $Cliente['nome_logradouro'] . ", " . $Cliente['numero'] . ", " . $Cliente['bairro'] . ", " . $Cliente['tipo_complemento'] . " " . $Cliente['desc_complemento'];                ;
$dadosboleto["endereco2"] = (string) $Cliente['nome_cidade'] . "/" . $Cliente['sigla_uf'] . " - CEP " . $Cliente['cep'];

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = $Demonstrativo_01;
$dadosboleto["demonstrativo2"] = $Demonstrativo_02;
$dadosboleto["demonstrativo3"] = $Demonstrativo_03;

// INSTRUÇÕES PARA O CAIXA
$dadosboleto["instrucoes1"] = $instrucao_01;
$dadosboleto["instrucoes2"] = $instrucao_02;
$dadosboleto["instrucoes3"] = $instrucao_03;
$dadosboleto["instrucoes4"] = $instrucao_04;


// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"] = "NAO";
$dadosboleto["especie"] = SIGLA_MOEDA;
$dadosboleto["especie_doc"] = (string) $esp_doc;


// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //

// DADOS DA SUA CONTA - CEF
$dadosboleto["agencia"] = (string) $agencia; // Num da agencia, sem digito
$dadosboleto["conta"] = (string) substr($codigo_cedente, 0, 6); 	// Num da conta, sem digito
$dadosboleto["conta_dv"] = (string) substr($codigo_cedente, (strlen($num_conta) - 1), 1); 	// Digito do Num da conta

// DADOS PERSONALIZADOS - CEF
$dadosboleto["conta_cedente"] = (string) substr($codigo_cedente, 0, 6); // Código Cedente do Cliente, com 6 digitos (Somente Números)
$dadosboleto["carteira"] = (string) $carteira;  // Código da Carteira: pode ser SR (Sem Registro) ou CR (Com Registro) - (Confirmar com gerente qual usar)

// SEUS DADOS
$dadosboleto["identificacao"] = (string) substr($nome, 0, 40);
$dadosboleto["cpf_cnpj"] = (string) $cpf_cnpj;
$dadosboleto["endereco"] = (string) $endereco;
$dadosboleto["cidade_uf"] = (string) $desc_cod_cidade . '/' . $desc_cod_uf;
$dadosboleto["cedente"] = (string) substr($nome, 0, 40);





// DADOS DA SUA CONTA - CEF
//$dadosboleto["agencia"] = "0597"; // Num da agencia, sem digito
//$dadosboleto["conta"] = "407562"; 	// Num da conta, sem digito
//$dadosboleto["conta_dv"] = "5"; 	// Digito do Num da conta

// DADOS PERSONALIZADOS - CEF
//$dadosboleto["conta_cedente"] = "407562"; // Código Cedente do Cliente, com 6 digitos (Somente Números)
//$dadosboleto["carteira"] = "SR";  // Código da Carteira: pode ser SR (Sem Registro) ou CR (Com Registro) - (Confirmar com gerente qual usar)

// SEUS DADOS
//$dadosboleto["identificacao"] = "BoletoPhp - Código Aberto de Sistema de Boletos";
//$dadosboleto["cpf_cnpj"] = "";
//$dadosboleto["endereco"] = "Coloque o endereço da sua empresa aqui";
//$dadosboleto["cidade_uf"] = "Cidade / Estado";
//$dadosboleto["cedente"] = "Coloque a Razão Social da sua empresa aqui";

// NÃO ALTERAR!
require_once ("include/funcoes_cef_sigcb.php");
require ("include/layout_cef.php");
?>
