<?php
@include_once __DIR__ . '/../trata-banco.php';
@session_start();
@ignore_user_abort(1);
@set_time_limit(0);
@session_cache_limiter("none");
@session_cache_expire(0);
@header('P3P: CP="IDC DSP COR CURa ADM ADMa DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT PHY ONL COM STA"');

//Gera o boleto bancário da Cx Federal - Sistema SIGCB na página html chamada pela url, sendo os parâmetros sempre enviados por GET
$Codigo_Agencia = (string) urldecode($_GET["Cod_Ag"]);             //Código da agência bancária
$Cod_Cedente = (string) urldecode($_GET['Cod_Ced']);               //Código do cedente. São sempre 7 dígitos incluído o dígito verificador, que é o último
$Data_Vencimeno = (string) urldecode($_GET['Data_Venc']);          //Data de vencimento do boleto
$Data_Emissao = (string) urldecode($_GET['Data_Emis']);            //Data de emissão do título e do boleto
$Carteira = (string) urldecode($_GET['Cart']);                     //Código da carteira de cobrança - 2 caracteres
$Tipo_Doc = (string) urldecode($_GET['Tipo_Doc']);                 //DS = Duplicata de serviços, DM = Duplicata mercantil, NP = Nota Promissória
$Valor = (string) urldecode($_GET['Vlr']);                         //Valor com vírgula e duas casas decimais
$Inicio_nosso_numero = (string) urldecode($_GET['Inic_Nos_Num']);  //Dois primeiros dígitos fixos do Nosso Número - fornecido pela Caixa Federal
$Instrucao1 = (string) urldecode($_GET['inst1']);                  //Texto da primeira linha de instruções
$Instrucao2 = (string) urldecode($_GET['inst2']);                  //Texto da segunda linha de instruções
$Instrucao3 = (string) urldecode($_GET['inst3']);                  //Texto da terceira linha de instruções
$Instrucao4 = (string) urldecode($_GET['inst4']);                  //Texto da quarta linha de instruções
$CNPJ_Cedente = (string) urldecode($_GET['CNPJ_Ced']);             //CNPJ do cedente
$CNPJ_Cliente = (string) urldecode($_GET['CNPJ_Cli']);             //CNPJ do cliente
$Nome_Cedente = (string) urldecode($_GET['Nome_Ced']);             //Razão social do cedente
$Nome_Cliente = (string) urldecode($_GET['Nome_Cli']);             //Razão social do cliente
$Endereco_Cedente = (string) urldecode($_GET['End_Ced']);          //Tipo Lograd, Nome Lograd, Numero, Bairro, Complem e CEP do cedente
$Endereco_Cliente = (string) urldecode($_GET['End_Cli']);          //Tipo Lograd, Nome Lograd, Numero, Bairro, Complem e CEP do cliente
$CidadeUF_Cliente = (string) urldecode($_GET['cid_uf_cli']);       //Cidade / UF do cliente
$CidadeUF_Cedente = (string) urldecode($_GET['cid_uf_ced']);       //Cidade / UF do cedente
$Taxa_Boleto = (string) urldecode($_GET['Taxa_Boleto']);           //Valor da taxa do boleto - vírgula e duas casas decimais
$Cod_Cliente = (string) urldecode($_GET['Cod_Cli']);               //Código do cliente com, no máximo, 6 dígitos
$Numero_Documento = (string) urldecode($_GET['Num_Doc']);          //Código numérico do título de crédito com, no máximo, 6 dígitos
// $UsarClienteTitulo = (string) urldecode($_GET['Cli_Tit']);         //Se vier a string CLI, o código do boleto será formado pelo código do cliente concatenado com o mês de emissão e o ano de emissão
$Moeda = (string) urldecode($_GET['Moeda']);                       //Vem aqui a sigla da moeda, em duas casas                                                                   //Se vier a string TIT, o código do boleto será formado pelo código do título concatenado com o mês de emissão e o ano de emissão
//
// ------------------------- DADOS DINÂMICOS DO SEU CLIENTE PARA A GERAÇÃO DO BOLETO (FIXO OU VIA GET) -------------------- //
//

// DADOS DO BOLETO PARA O SEU CLIENTE
if (strlen($Data_Vencimeno) == 10){       //testo a consistencia da data de vencimento
  if ((substr($Data_Vencimeno, 2, 1) == "/") and (substr($Data_Vencimeno, 5, 1) == "/")){
    if ((intval(substr($Data_Vencimeno,0,2),10) >= 1) and (intval(substr($Data_Vencimeno,0,2),10) <= 31)){
      if ((intval(substr($Data_Vencimeno,3,2),10) >= 1) and (intval(substr($Data_Vencimeno,3,2),10) <= 12)){
        if ((intval(substr($Data_Vencimeno,6,4),10) >= 1900) and (intval(substr($Data_Vencimeno,6,4),10) <= 2200)){
          $data_venc = (string) $Data_Vencimeno;
        }else{
          $DescErro = (string) "Parâmetro Data de Vencimento (Data_Venc) inválido - ano inválido <br>";
          goto ErroBoleto;
        }
      }else{
        $DescErro = (string) "Parâmetro Data de Vencimento (Data_Venc) inválido - mês inválido <br>";
        goto ErroBoleto;
      }
    }else{
      $DescErro = (string) "Parâmetro Data de Vencimento (Data_Venc) inválido - dia inválido <br>";
      goto ErroBoleto;
    }
  }else{
    $DescErro = (string) "Parâmetro Data de Vencimento (Data_Venc) inválido - não está no formato DD/MM/AAAA <br>";
    goto ErroBoleto;
  }
}else{
  $DescErro = (string) "Parâmetro Data de Vencimento (Data_Venc) inválido - não contém 10 caracteres <br>";
  goto ErroBoleto;
}
//

// $valor_cobrado = str_replace(".","",$Valor);
$valor_cobrado = str_replace(",",".", $Valor);
$valor_cobrado = number_format(floatval($valor_cobrado),2,",","");
$valor_boleto = (string) number_format(floatval(str_replace(",",".",$valor_cobrado)) + floatval(str_replace(",",".",$Taxa_Boleto)), 2, ',', '');

// Composição Nosso Numero - CEF SIGCB

if (strlen($Data_Vencimeno) == 10){       //testo a consistencia da data de emissão
  if ((substr($Data_Vencimeno, 2, 1) == "/") and (substr($Data_Vencimeno, 5, 1) == "/")){
    if ((intval(substr($Data_Vencimeno,0,2),10) >= 1) and (intval(substr($Data_Vencimeno,0,2),10) <= 31)){
      if ((intval(substr($Data_Vencimeno,3,2),10) >= 1) and (intval(substr($Data_Vencimeno,3,2),10) <= 12)){
        if ((intval(substr($Data_Vencimeno,6,4),10) >= 1900) and (intval(substr($Data_Vencimeno,6,4),10) <= 2200)){
          $mes_fatura = (string) substr($Data_Emissao,3,2);
          $ano_fatura = (string) substr($Data_Emissao,6,4);

        }else{
          $DescErro = (string) "Parâmetro Data de Emissao (Data_Emis) inválido - ano inválido <br>";
          goto ErroBoleto;
        }
      }else{
        $DescErro = (string) "Parâmetro Data de Emissao (Data_Emis) inválido - mês inválido <br>";
        goto ErroBoleto;
      }
    }else{
      $DescErro = (string) "Parâmetro Data de Emissao (Data_Emis) inválido - dia inválido <br>";
      goto ErroBoleto;
    }
  }else{
    $DescErro = (string) "Parâmetro Data de Emissao (Data_Emis) inválido - não está no formato DD/MM/AAAA <br>";
    goto ErroBoleto;
  }
}else{
  $DescErro = (string) "Parâmetro Data de Emissao (Data_Emis) inválido - não contém 10 caracteres <br>";
  goto ErroBoleto;
}
//
// if ($UsarClienteTitulo == "CLI"){
//    $Cod_Cliente_Data_15posic = (string) str_pad(strval($Cod_Cliente) . $mes_fatura . $ano_fatura, 15, '0', STR_PAD_LEFT);
// }else if($UsarClienteTitulo == "TIT"){
//    $Cod_Cliente_Data_15posic = (string) str_pad(strval($Numero_Documento) . $mes_fatura . $ano_fatura, 15, '0', STR_PAD_LEFT);
// }else{
//    $Cod_Cliente_Data_15posic = (string) str_pad("000" . $mes_fatura . $ano_fatura, 15, '0', STR_PAD_LEFT);
// }
$Cod_Cliente_Data_15posic = (string) $Numero_Documento;// Novo padrão PEDIDO(12) . PARCELA(3)
$dadosboleto["nosso_numero1"] = (string) substr($Cod_Cliente_Data_15posic, 0, 3); // tamanho 3
$dadosboleto["nosso_numero_const1"] = "2"; //constante 1 , 1=registrada , 2=sem registro
$dadosboleto["nosso_numero2"] = (string) substr($Cod_Cliente_Data_15posic, 3, 3); // tamanho 3
$dadosboleto["nosso_numero_const2"] = "4"; //constante 2 , 4=emitido pelo proprio cliente
$dadosboleto["nosso_numero3"] = (string) substr($Cod_Cliente_Data_15posic, 6, 9); // tamanho 9
//
// if ($UsarClienteTitulo == "CLI"){
//    $dadosboleto["numero_documento"] = (string) strval($Cod_Cliente) . $mes_fatura . $ano_fatura;	// Num do pedido ou do documento
// }else if($UsarClienteTitulo == "TIT"){
//    $dadosboleto["numero_documento"] = (string) strval($Numero_Documento) . $mes_fatura . $ano_fatura;	// Num do pedido ou do documento
// }else{
//    $dadosboleto["numero_documento"] = (string) "000" . $mes_fatura . $ano_fatura;	// Num do pedido ou do documento
// }

$dadosboleto["numero_documento"] = (string) $Cod_Cliente_Data_15posic;

$dadosboleto["data_vencimento"] = (string) $Data_Vencimeno; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = (string) $Data_Emissao; // Data de emissão do Boleto
$dadosboleto["data_processamento"] = (string) date("d/m/Y"); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = (string) $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
$CNPJ_Cliente = str_replace(array('.', '-', '/'), '', $CNPJ_Cliente);
if ($CNPJ_Cliente != '') {
    $CNPJ_Cliente = substr($CNPJ_Cliente, 0, 2) . '.' .
        substr($CNPJ_Cliente, 2, 3) . '.' .
        substr($CNPJ_Cliente, 5, 3) . '/' .
        substr($CNPJ_Cliente, 8, 4) . '-' .
        substr($CNPJ_Cliente, 12);
}

$dadosboleto["sacado"] = (string) 'CPF/CNPJ: ' . $CNPJ_Cliente . ' - Nome: ' . $Nome_Cliente;
// $dadosboleto["sacado"] = (string) strval($Cod_Cliente) . " - " . $Nome_Cliente;
$dadosboleto["endereco1"] = (string) $Endereco_Cliente;
$dadosboleto["endereco2"] = (string) $CidadeUF_Cliente;

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = $Instrucao1;
$dadosboleto["demonstrativo2"] = $Instrucao2;
$dadosboleto["demonstrativo3"] = $Instrucao3;

// INSTRUÇÕES PARA O CAIXA
$dadosboleto["instrucoes1"] = $Instrucao1;
$dadosboleto["instrucoes2"] = $Instrucao2;
$dadosboleto["instrucoes3"] = $Instrucao3;
$dadosboleto["instrucoes4"] = $Instrucao4;


// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"] = "NAO";
$dadosboleto["especie"] = (string) $Moeda;
$dadosboleto["especie_doc"] = (string) $Tipo_Doc;

// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //

// DADOS DA SUA CONTA - CEF
$dadosboleto["agencia"] = (string) $Codigo_Agencia; // Num da agencia, sem digito
$dadosboleto["conta"] = (string) substr($Cod_Cedente, 0, 6); 	// Num da conta, sem digito
$dadosboleto["conta_dv"] = (string) substr($Cod_Cedente, (strlen($Cod_Cedente) - 1), 1); 	// Digito do Num da conta

// DADOS PERSONALIZADOS - CEF
$dadosboleto["conta_cedente"] = (string) substr($Cod_Cedente, 0, 6); // Código Cedente do Cliente, com 6 digitos (Somente Números)
$dadosboleto["carteira"] = (string) $Carteira;  // Código da Carteira: pode ser SR (Sem Registro) ou CR (Com Registro) - (Confirmar com gerente qual usar)

// SEUS DADOS
$dadosboleto["identificacao"] = (string) substr($Nome_Cedente, 0, 40);
$dadosboleto["cpf_cnpj"] = (string) $CNPJ_Cedente;
$dadosboleto["endereco"] = (string) $Endereco_Cedente;
$dadosboleto["cidade_uf"] = (string) $CidadeUF_Cedente;
$dadosboleto["cedente"] = (string) substr($Nome_Cedente, 0, 40);

// NÃO ALTERAR!
require_once ("BoletoCEF_SIGCB/include/funcoes_cef_sigcb.php");
require_once ("BoletoCEF_SIGCB/include/layout_cef.php");
exit;
//Se der erro vai sair por aqui:
ErroBoleto:
echo $DescErro;
?>
