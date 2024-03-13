<?php
include_once "TrataBanco.php";
include_once "include/funcoes_santander_banespa.php";

conectarMovimento2($data);
conectarMovimento3($data);


// Campos necessários.
$codigo_boleto  = (int)     0;
$banco          = (string)  '';
$codigo_cedente = (int)     0;
$taxa_boleto    = (float)   0;
$agencia        = (int)     0;
$instrucao_01   = (string)  '';
$instrucao_02   = (string)  '';
$instrucao_03   = (string)  '';
$instrucao_04   = (string)  '';

# Dados boleto.
while ((!$Consulta1->BOF) and (!$Consulta1->EOF)) {
    
    # Código.
    if ($Consulta1->fields('Nome') == 'Codigo') {
        $codigo_boleto  = (int) $Consulta1->fields('Valor');
    } else
    # Banco.
    if ($Consulta1->fields('Nome') == 'Nome') {
        $banco = (string)  $Consulta1->fields('Valor');
    } else
    # Código Cedente.
    if ($Consulta1->fields('Nome') == 'Codigo_Cedente') {
        $codigo_cedente = (int)  $Consulta1->fields('Valor');
    } else
    # Taxa Boleto.
    if ($Consulta1->fields('Nome') == 'Taxa') {
        $taxa_boleto = (float)  $Consulta1->fields('Valor');
    }
    # Agência Boleto.
    if ($Consulta1->fields('Nome') == 'Agencia') {
        $agencia = (int)  $Consulta1->fields('Valor');
    }
    # Instrucao 01.
    if ($Consulta1->fields('Nome') == 'Instrucao_01') {
        $instrucao_01 = (string)  $Consulta1->fields('Valor');
    }
    # Instrucao 02.
    if ($Consulta1->fields('Nome') == 'Instrucao_02') {
        $instrucao_02 = (string)  $Consulta1->fields('Valor');
    }
    # Instrucao 03.
    if ($Consulta1->fields('Nome') == 'Instrucao_03') {
        $instrucao_03 = (string)  $Consulta1->fields('Valor');
    }
    # Instrucao 04.
    if ($Consulta1->fields('Nome') == 'Instrucao_04') {
        $instrucao_04 = (string)  $Consulta1->fields('Valor');
    }
    
    $Consulta1->movenext();
}

# Dados Empresa.
Conecta3('Loja');
Conecta4('Dados');
$Consulta3->open('SELECT * FROM DadosEmpresa;', $Conex3, 3, 4);
while ((!$Consulta3->BOF) and (!$Consulta3->EOF)) {
    $nome_emitente                  = (string)  $Consulta3->fields('Nome');
    $cpf_cnpj_emitente              = (string)  $Consulta3->fields('CPF_CNPJ');
    
    $Consulta4->open('SELECT * FROM Tipo_Logradouro WHERE Codigo = ' . intval($Consulta3->fields('Tipo_Logradouro'), 10) . ' ORDER BY Nome;', $Conex4, 3, 4);
    if ((!$Consulta4->BOF) or (!$Consulta4->EOF)) {
        $tipo_log = (string) $Consulta4->fields('Nome');
    } else {
        $tipo_log = (string) '';
    }
    $Consulta4->close();
    
    $Consulta4->open('SELECT * FROM Tipo_Complemento WHERE Codigo = ' . intval($Consulta3->fields('Tipo_Complemento'), 10) . ';', $Conex4, 3, 4);
    if ((!$Consulta4->BOF) or (!$Consulta4->EOF)) {
        $tipo_comp = (string) $Consulta4->fields('Nome');
    } else {
        $tipo_comp = (string) '';
    }
    $Consulta4->close();
    
    $Consulta4->open('SELECT * FROM Estados_Brasileiros WHERE Registro = ' . intval($Consulta3->fields('Estado'), 10) . ';', $Conex4, 3, 4);
    if ((!$Consulta4->BOF) or (!$Consulta4->EOF)) {
        $estado = (string) $Consulta4->fields('Sigla_UF');
    } else {
        $estado = (string) '';
    }
    $Consulta4->close();
    
    $Consulta4->open('SELECT * FROM Municipios_Brasileiros WHERE Codigo = ' . intval($Consulta3->fields('Cidade'), 10) . ';', $Conex4, 3, 4);
    if ((!$Consulta4->BOF) or (!$Consulta4->EOF)) {
        $cidade = (string) $Consulta4->fields('Nome');
    } else {
        $cidade = (string) '';
    }
    $Consulta4->close();
    
    $endereco_emitente = (string) $tipo_log . ' ' . $Consulta3->fields('Nome_Logradouro') . ', ' . $tipo_comp . ' ' . $Consulta3->fields('Desc_Complemento') . ', ' . $Consulta3->fields('Bairro');
    
    $Consulta3->movenext();
}
$Consulta3->close();

conectarMovimento2($data);
conectarMovimento3($data);

// Tipo_logradouroo.
$Consulta4->open("SELECT * FROM Tipo_Logradouro WHERE Codigo = {$Cliente['tipo_logradouro']} ORDER BY Nome;", $Conex4, 3, 4);
if ((!$Consulta4->BOF) and (!$Consulta4->EOF)) {
    $tipo_logradouro = (string) $Consulta4->fields('Nome');
} else {
    $tipo_logradouro = '';
}
$Consulta4->close();

// Tipo_complemento.
$Consulta4->open("SELECT * FROM Tipo_Complemento WHERE Codigo = {$Cliente['tipo_complemento']};", $Conex4, 3, 4);
if ((!$Consulta4->BOF) and (!$Consulta4->EOF)) {
    $tipo_complemento = (string) $Consulta4->fields('Nome');
} else {
    $tipo_complemento = '';
}
$Consulta4->close();

// Cidade.
$Consulta4->open("SELECT * FROM Municipios_Brasileiros WHERE Codigo = {$Cliente['cidade']};", $Conex4, 3, 4);
if ((!$Consulta4->BOF) and (!$Consulta4->EOF)) {
    $cidade = (string) $Consulta4->fields('Nome');
} else {
    $cidade = '';
}
$Consulta4->close();

// Estado.
$Consulta4->open("SELECT * FROM Estados_Brasileiros WHERE Registro = {$Cliente['estado']};", $Conex4, 3, 4);
if ((!$Consulta4->BOF) and (!$Consulta4->EOF)) {
    $estado = (string) $Consulta4->fields('Sigla_UF');
} else {
    $estado = '';
}
$Consulta4->close();

$valor_cobrado      = (float)   number_format(floatval($valor_total), 2, ',', '');
$valor_boleto       = (string)  number_format(floatval($valor_total), 2, ',', '');

$dadosboleto["numero_documento"]    = (string) str_pad($Cliente['codigo'], 6, '0', STR_PAD_LEFT) . $mes_fatura . $ano_fatura;
$dadosboleto["data_vencimento"]     = (string) $Cliente['data_vencimento_contrato']; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"]      = (string) date("t/m/Y", strtotime($ano_fatura . '-' . $mes_fatura.'-' . '01'));
$dadosboleto["data_processamento"]  = (string) date("t/m/Y", strtotime($ano_fatura . '-' . $mes_fatura.'-' . '01'));
$dadosboleto["valor_boleto"]        = number_format(floatval($valor_total), 2, ',', '');

// DADOS DO CLIENTE
$dadosboleto["sacado"]      = (string) $Cliente['nome'];
$dadosboleto["endereco1"]   = (string) $tipo_logradouro . ' ' . $Cliente['nome_logradouro'] . ', ' . strval($Cliente['numero']) . ", " . $Cliente['bairro'] . ", " . $tipo_complemento . ' ' . $Cliente['desc_complemento'];
$dadosboleto["endereco2"]   = $cidade . "/" . $estado . " - " . $Cliente['cep'];

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = "Pagamento do pedido n.º " . $maior_cod_saida . " - " . $nome_emitente;
$dadosboleto["demonstrativo2"] = "Mercadorias: " . SIGLA_MOEDA . ' ' . number_format($Cliente['valor_mensal'], 2, ',', '') . "    " . "Frete: " . SIGLA_MOEDA . ' ' . number_format(floatval($Cliente['valor_mensal']), 2, ',', '') . "<br>Taxa bancária: " . SIGLA_MOEDA . ' ' . number_format(floatval($taxa_boleto), 2, ',', '.');
$dadosboleto["demonstrativo3"] = "<b>NÃO PAGUE APÓS O VENCIMENTO</b> - Antecipar o pagamento quando a data de vencimento coincidir com feriados";
$dadosboleto["instrucoes1"] = (string) $instrucao_01;
$dadosboleto["instrucoes2"] = (string) $instrucao_02;
$dadosboleto["instrucoes3"] = (string) $instrucao_03;
$dadosboleto["instrucoes4"] = (string) $instrucao_04;


// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"]      = "";
$dadosboleto["valor_unitario"]  = "";
$dadosboleto["aceite"]          = "";
$dadosboleto["especie"]         = SIGLA_MOEDA;
$dadosboleto["especie_doc"]     = "";


// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //


// DADOS PERSONALIZADOS - SANTANDER BANESPA
$dadosboleto["codigo_cliente"]      = trim($codigo_cedente);// Código do Cedente (Somente 7 digitos)
$dadosboleto["ponto_venda"]         = (string) $agencia;   // Ponto de Venda = Agencia
$dadosboleto["carteira"]            = "102";                                    // Código da Carteira
$dadosboleto["carteira_descricao"]  = "COBRANÇA SIMPLES - CSR";                 // Descrição da Carteira

// SEUS DADOS
$dadosboleto["identificacao"]   = (string) $nome_emitente;
$dadosboleto["cpf_cnpj"]        = (string) $cpf_cnpj_emitente;
$dadosboleto["endereco"]        = (string) $endereco_emitente;
$dadosboleto["cidade_uf"]       = (string) $cidade . '/' . $estado;
$dadosboleto["cedente"]         = (string) $nome_emitente;

// Código que estava nas funcoes_santander_banespa.php
$codigobanco            = "033"; //Antigamente era 353
$codigo_banco_com_dv    = geraCodigoBanco_santander_banespa($codigobanco);
$nummoeda               = "9";
$fixo                   = "9";   // Numero fixo para a posição 05-05
$ios                    = "0";   // IOS - somente para Seguradoras (Se 7% informar 7, limitado 9%)
//
// Demais clientes usar 0 (zero)
$fator_vencimento = fator_vencimento_santander_banespa($dadosboleto["data_vencimento"]);

//valor tem 10 digitos, sem virgula
$valor = formata_numero_santander_banespa($dadosboleto["valor_boleto"],10,0,"valor");
//Modalidade Carteira
$carteira = $dadosboleto["carteira"];
//codigocedente deve possuir 7 caracteres
$codigocliente = formata_numero_santander_banespa($dadosboleto["codigo_cliente"], 7, 0);

//nosso número (sem dv) é 11 digitos
$nnum = formata_numero_santander_banespa($dadosboleto["numero_documento"],7,0);
//dv do nosso número
$dv_nosso_numero = modulo_11_santander_banespa($nnum,9,0);
// nosso número (com dvs) são 13 digitos
$nossonumero = "00000".$nnum.$dv_nosso_numero;

$vencimento = $dadosboleto["data_vencimento"];

$vencjuliano = dataJuliano_santander_banespa($vencimento);

// 43 numeros para o calculo do digito verificador do codigo de barras
$barra = "$codigobanco$nummoeda$fator_vencimento$valor$fixo$codigocliente$nossonumero$ios$carteira";

$dv = digitoVerificador_barra_santander_banespa($barra);

// Numero para o codigo de barras com 44 digitos
$linha = substr($barra,0,4) . $dv . substr($barra,4);

$dadosboleto["codigo_barras"] = $linha;
$dadosboleto["linha_digitavel"] = monta_linha_digitavel_santander_banespa($linha);
$dadosboleto["nosso_numero"] = $nossonumero;
$dadosboleto["codigo_banco_com_dv"] = $codigo_banco_com_dv;

include "include/layout_santander_banespa.php";

?>
