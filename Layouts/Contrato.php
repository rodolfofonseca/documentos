<?php
include_once __DIR__ . '/../trata-banco.php';
@session_start();
@session_cache_limiter("none");
@session_cache_expire(0);

ignore_user_abort(1);
set_time_limit(0);
header('P3P: CP="IDC DSP COR CURa ADM ADMa DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT PHY ONL COM STA"');

/**
 * Parâmetros necessários.
 *
 * @param {int}     $_GET['codigo_cliente'] - Código do cliente.
 * @param {string}  $_GET['codigo_cliente'] - [Opcional]Código do contrato.
 *
 */
// include_once("../TrataBanco.php");

// Verificar Sessão.
if ((isset($_SESSION["usuario"]) == false) or (isset($_SESSION["senha"]) == false)) {
    ?>
    <html>
        <head>
            <title>Sessão Inválida</title>
            <style type="text/css">
                #principal{position:relative; width:840px; height:5870px; background-color:transparent; border:0px; font: 16px Verdana; color: #166B16; margin-left:0px; margin-right:0px; margin-bottom:0px; margin-top:0px; margin-left:auto; margin-right:auto; margin-top:0px; margin-bottom:0px}
                    .Rotulo{height: 25px; font: 14px Verdana; color: #166B16; text-align: left; border: 0px none; padding-top: 5px; padding-left: 0px; padding-bottom: 0px; padding-right: 0px; margin-top: 0px; margin-left: 0px; margin-bottom: 0px; margin-right: 0px; font-weight:normal}
                    a{color: #0000FF;}
                    a:hover{color: #000899;}
                    #dvDesconectado{height: 300px; width: 500px; border: 1px solid #166b16;margin: auto;}
                        #imgDesconectado{position: absolute; top:20px; left: 190px; height: 125px; width: 125px; border: 0px none;}
                        #lbPermissao{position: absolute; top: 150px; left: 0px; width: 500px; text-align: center;}
                        #lbLogin{position: absolute; top: 180px; left: 0px; width: 500px; text-align: center;}
            </style>
            <script type="text/javascript" src="<?php echo PADRAO_JS; ?>"></script>
            <script type="text/javascript">
                onload = function on_load() {
                    topo().EscondeDivTempo();
                }
                function chamar_principal() {
                    topo().location.href = exibirUrl();
                }
            </script>
        </head>
        <body>
            <div id="principal">
                <?php if (file_exists("Imagens/desconectado.png")){ ?>
                    <img id="imgDesconectado" src="Imagens/desconectado.png" width="128px" height="128px" title="Desconectado" alt="Desconectado" >
                <?php } else { ?>
                    <img id="imgDesconectado" src="../Imagens/desconectado.png" width="128px" height="128px" title="Desconectado" alt="Desconectado" >
                <?php } ?>

                <label class="Rotulo" id="lbPermissao">
                    Você não tem permissão para acessar diretamente esta página...
                </label>
                <label class="Rotulo" id="lbLogin">
                    Faça o seu login <a href="javascript: chamar_principal();">aqui</a>
                </label>
            </div>
        </body>
    </html>
    <?php
    exit;
}

// Parâmetros
$codigo_cliente     = (integer) 0;
$codigo_contrato    = (string)  '';

if (isset($_GET['codigo_cliente']) == true) {
    $codigo_cliente = (integer) ($_GET['codigo_cliente']);
}

if (isset($_GET['codigo_contrato']) == true) {
    $codigo_contrato = (string) ($_GET['codigo_contrato']);
}

$logotipo       = (string) "Dados/Contratos/Logotipos/{$codigo_contrato}.jpg";
// $nome_arquivo   = (string) DIRETORIO_SISTEMA . '/Dados/Contratos/Clausulas/' . $codigo_contrato . '.txt';
$nome_arquivo   = (string) DIRETORIO_SISTEMA . '/Dados/Contratos/Clausulas/' . $codigo_contrato . '.html';

// logotipo padrão
if (file_exists($logotipo) == false) {
    $logotipo = (string) "Dados/Contratos/Logotipos/logo_padrao.jpg";
}


// Contrato
$desc_rap               = (string)  '_______________________________________';
$nome_razao_contratada  = (string)  '_______________________________________';
$cpf_cnpj_contratada    = (string)  '_______________________________________';
$telefone_contratada    = (string)  '_______________________________________';
$tipo_log_contratada    = (string)  '________________________';
$nome_log_contratada    = (string)  '_______________________________________';
$numero_log_contratada  = (string)  '________________________';
$bairro_log_contratada  = (string)  '________________________';
$tipo_comp_contratada   = (string)  '';
$nome_comp_contratada   = (string)  '_______________________________________';
$total_comp_contratada  = (string)  '_______________________________________';
$uf_contratada          = (string)  '____';
$cidade_contratada      = (string)  '_______________________________________';
$cep_contratada         = (string)  '________________________';
$nome_resp_legal        = (string)  '_______________________________________';
$cpf_resp_legal         = (string)  '_______________________________________';
$telefone_resp_legal    = (string)  '_______________________________________';
$tipo_log_resp_legal    = (string)  '________________________';
$nome_log_resp_legal    = (string)  '_______________________________________';
$numero_log_resp_legal  = (string)  '__________';
$bairro_log_resp_legal  = (string)  '________________________';
$tipo_comp_resp_legal   = (int)     '';
$nome_comp_resp_legal   = (string)  '_______________________________________';
$total_comp_resp_legal  = (string)  '_______________________________________';
$uf_resp_legal          = (string)  '____';
$cidade_resp_legal      = (string)  '_______________________________________';
$cep_resp_legal         = (string)  '________________________';
$cargo_resp_legal       = (string)  '_______________________________________';
$data_assinatura        = (string)  '_______________________________________';
$cidade_assinatura      = (string)  '_______________________________________';
$uf_assinatura          = (string)  '____';
$data_ini_prest_serv    = (string)  '_______________________________________';
$data_vig_contrato      = (string)  '_______________________________________';
$data_dia_venc_mensal   = (string)  '_______________________________________';
$data_mes_venc_mensal   = (string)  '_______________________________________';
$valor_mensal           = (string)  '________________________';
$Clausulas              = array();
$clausulas              = '';
$texto_esp_clie_cont    = '';

// Contratante
$razao_social_contratante               = (string)  '_______________________________________';
$cpf_cnpj_contratante                   = (string)  '_______________________________________';
$cidade_contratante                     = (string)  '_______________________________________';
$uf_contratante                         = (string)  '____';
$cep_contratante                        = (string)  '________________________';
$telefone_contratante                   = (string)  '________________________';
$tipo_log_contratante                   = (string)  '________________________';
$nome_log_contratante                   = (string)  '________________________';
$numero_log_contratante                 = (string)  '________________________';
$bairro_log_contratante                 = (string)  '________________________';
$tipo_comp_contratante                  = (int)     0;
$tipo_comp_resp_legal_contratante       = (int)     0;
$total_comp_contratante                 = (string)  '______________,';
$cargo_resp_legal_contratante           = (string)  '_______________________________________';
$nome_resp_legal_contratante            = (string)  '_______________________________________';
$cpf_resp_legal_contratante             = (string)  '_______________________________________';
$cidade_resp_legal_contratante          = (string)  '_______________________________________';
$uf_resp_legal_contratante              = (string)  '____';
$tipo_log_resp_legal_contratante        = (string)  '________________________';
$nome_log_resp_legal_contratante        = (string)  '________________________';
$numero_log_resp_legal_contratante      = (string)  '__________';
$bairro_log_resp_legal_contratante      = (string)  '________________________';
$total_comp_resp_legal_contratante      = (string)  '________________________';
$cep_resp_legal_contratante             = (string)  '________________________';
$telefone_resp_legal_contratante        = (string)  '________________________';
$cod_uf_assinatura                      = (string)  0;
$cod_cidade_assinatura                  = (string)  0;
$usar_valor_mensal_contratante          = (string)  'S';
$valor_mensal_contratante               = (string)  '________________________';
$usar_data_ini_prest_serv_contratante   = (string)  'S';
$data_ini_prest_serv_contratante        = (string)  '_______________________________________';
$usar_data_vig_contrato_contratante     = (string)  'S';
$data_vig_contrato_contratante          = (string)  '_______________________________________';
$usar_data_vencimento_contrato          = (string)  'S';

// Contratos
$contrato_servico = DB::use('contrato_servico')->one(['codigo','===', $codigo_contrato]);
if (empty($contrato_servico) == false) {
    $desc_rap               = (string)  $contrato_servico['descricao_rapida'];
    $nome_razao_contratada  = (string)  $contrato_servico['nome_razao_contratada'];
    $cpf_cnpj_contratada    = (string)  $contrato_servico['cpf_cnpj_contratada'];
    $telefone_contratada    = (string)  $contrato_servico['telefone_contratada'];
    $tipo_log_contratada    = (int)     $contrato_servico['tipo_logradouro_contratada'];
    $nome_log_contratada    = (string)  $contrato_servico['nome_logradouro_contratada'];
    $numero_log_contratada  = (int)     $contrato_servico['numero_logradouro_contratada'];
    $bairro_log_contratada  = (string)  $contrato_servico['bairro_logradouro_contratada'];
    $tipo_comp_contratada   = (int)     $contrato_servico['tipo_complemento_contratada'];
    $nome_comp_contratada   = (string)  $contrato_servico['desc_complemento_contratada'];
    $cod_uf_contratada      = (int)     $contrato_servico['uf_contratada'];
    $cod_cidade_contratada  = (int)     $contrato_servico['cidade_contratada'];
    $cep_contratada         = (string)  $contrato_servico['cep_contratada'];
    $nome_resp_legal        = (string)  $contrato_servico['nome_resp_legal'];
    $cpf_resp_legal         = (string)  $contrato_servico['cpf_resp_legal'];
    $telefone_resp_legal    = (string)  $contrato_servico['telefone_resp_legal'];
    $tipo_log_resp_legal    = (int)     $contrato_servico['tipo_logradouro_resp_legal'];
    $nome_log_resp_legal    = (string)  $contrato_servico['nome_logradouro_resp_legal'];
    $numero_log_resp_legal  = (int)     $contrato_servico['numero_logradouro_resp_legal'];
    if ($numero_log_resp_legal == 0) $numero_log_resp_legal = '__________';
    $bairro_log_resp_legal  = (string)  $contrato_servico['bairro_logradouro_resp_legal'];
    $tipo_comp_resp_legal   = (int)     $contrato_servico['tipo_complemento_resp_legal'];
    $nome_comp_resp_legal   = (string)  $contrato_servico['desc_complemento_resp_legal'];
    $cod_uf_resp_legal      = (int)     $contrato_servico['uf_resp_legal'];
    $cod_cidade_resp_legal  = (int)     $contrato_servico['cidade_resp_legal'];
    $cep_resp_legal         = (string)  $contrato_servico['cep_resp_legal'];
    $cargo_resp_legal       = (string)  $contrato_servico['cargo_resp_legal'];
    $data_assinatura        = (string)  $contrato_servico['data_ass'];
    $cod_cidade_assinatura  = (int)     $contrato_servico['cidade_ass'];
    $cod_uf_assinatura      = (int)     $contrato_servico['uf_ass'];
    $data_ini_prest_serv    = (string)  $contrato_servico['data_ini_prest_serv'];
    $data_vig_contrato      = (string)  $contrato_servico['data_vig_contrato'];
    $data_dia_venc_mensal   = (string)  $contrato_servico['data_dia_venc_mensal'];
    $data_mes_venc_mensal   = (string)  $contrato_servico['data_mes_venc_mensal'];
    $valor_mensal           = (float)   $contrato_servico['valor_mensal'];

    if ($tipo_log_contratada != 0) {
        # Tipo Logradouro Contratada.
        $lista_tipo_logradouro = DB::use('tipo_logradouro')->all(['codigo', '===', $tipo_log_contratada],['nome'=>true]);
        foreach ($lista_tipo_logradouro as $tipo_logradouro) {
            $tipo_log_contratada = (string) $tipo_logradouro['nome'];
        }
    } else {
        $tipo_log_contratada = (string)  '________________________';
    }

    if ($tipo_comp_contratada != 0) {
        # Tipo Complemento Contratada.
        $lista_complemento = DB::use('tipo_complemento')->all(['codigo', '===', $tipo_comp_contratada]);
        foreach ($lista_complemento as $complemento) {
            $nome_tipo_comp_contratada = (string) $complemento['nome'];
        }
    }

    if ($tipo_log_resp_legal != 0) {
        # Tipo Logradouro resp_legal.
        $lista_tipo_logradouro = DB::use('tipo_logradouro')->all(['codigo', '===', $tipo_log_resp_legal],['nome' => true]);
        foreach ($lista_tipo_logradouro as $tipo_logradouro) {
            $tipo_log_resp_legal = (string) $tipo_logradouro['nome'];
        }
    } else {
        $tipo_log_resp_legal = (string)  '________________________';
    }

    if ($tipo_comp_resp_legal != 0) {
        # Tipo Complemento resp_legal.
        $lista_tipo_complemento = DB::use('tipo_complemento')->all(['codigo', '===', $tipo_comp_resp_legal]);
        foreach ($lista_tipo_complemento as $tipo_complemento) {
            $nome_tipo_comp_resp_legal = (string) $tipo_complemento['nome'];
        }
    }

    if ($cod_uf_contratada != 0) {
        # Estado Contratada.
        $lista_estado_brasileiro = DB::use('estado_brasileiro')->all(['registro', '===', $cod_uf_contratada]);
        foreach ($lista_estado_brasileiro as $estado_brasileiro) {
            $uf_contratada = (string) $estado_brasileiro['sigla_uf'];
        }
    } else {
        $cod_uf_contratada = (string)  '________________________';
    }

    if ($cod_cidade_contratada != 0) {
        # Cidade Contratada.
        $lista_municipio_brasileiro = DB::use('municipio_brasileiro')->all(['codigo', '===', $cod_cidade_contratada]);
        foreach ($lista_municipio_brasileiro as $municipio_brasileiro) {
            $cidade_contratada = (string) $municipio_brasileiro['nome'];
        }
    } else {
        $cod_cidade_contratada = (string)  '________________________';
    }

    if ($cod_uf_resp_legal != 0) {
        $lista_estado_brasileiro = DB::use('estado_brasileiro')->all(['registro', '===', $cod_uf_resp_legal]);
        # Estado Contratada.
        foreach ($lista_estado_brasileiro as $estado_brasileiro) {
            $uf_resp_legal = (string) $estado_brasileiro['sigla_uf'];
        }
    } else {
        $cod_uf_resp_legal = (string)  '________________________';
    }
    if ($cod_cidade_resp_legal != 0) {
        # Cidade Contratada.
        $lista_municipio_brasileiro = DB::use('municipio_brasileiro')->all(['codigo', '===', $cod_cidade_resp_legal]);
        foreach ($lista_municipio_brasileiro as $municipio_brasileiro) {
            $cidade_resp_legal = (string) $municipio_brasileiro['nome'];
        }
    } else {
        $cod_cidade_resp_legal = (string)  '________________________';
    }

    $usar_data_ass_contrato = (isset($_GET['usar_data_ass'])) ? strval(($_GET['usar_data_ass'])) : 'N';
    if ($usar_data_ass_contrato == 'S') {
        $data_ass_contrato = (isset($_GET['data_ass'])) ? strval(($_GET['data_ass'])) : $data_ass_contrato;
    }
    $usar_data_final_contrato = (isset($_GET['usar_data_final'])) ? strval(($_GET['usar_data_final'])) : 'N';
    if ($usar_data_final_contrato == 'S') {
        $data_final_contrato = (isset($_GET['data_final'])) ? strval(($_GET['data_final'])) : $data_final_contrato;
    }
    $usar_end_ass_contrato = (isset($_GET['usar_end_ass_contrato'])) ? strval(($_GET['usar_end_ass_contrato'])) : 'N';
    if ($usar_end_ass_contrato == 'S') {
        $cod_uf_assinatura = (isset($_GET['estado'])) ? intval(($_GET['estado']), 10) : $cod_uf_assinatura;
        $cod_cidade_assinatura = (isset($_GET['cidade'])) ? intval(($_GET['cidade']), 10) : $cod_cidade_assinatura;
    }

    if ($cod_uf_assinatura != 0) {
        # Estado Assinatura.
        $lista_estado_brasileiro = DB::use('estado_brasileiro')->all(['registro', '===', $cod_uf_assinatura]);
        foreach ($lista_estado_brasileiro as $estado_brasileiro) {
            $uf_assinatura = (string) $estado_brasileiro['sigla_uf'];
        }
    } else {
        $cod_uf_assinatura = (string)  '________________________';
    }

    if ($cod_cidade_assinatura != 0) {
        # Cidade Assinatura.
        $lista_municipio_brasileiro = DB::use('municipio_brasileiro')->all(['codigo', '===', $cod_cidade_assinatura]);
        foreach ($lista_municipio_brasileiro as $municipio_brasileiro) {
            $cidade_assinatura = (string) $municipio_brasileiro['nome'];
        }
    } else {
        $cod_cidade_assinatura = (string)  '________________________';
    }

    # Pego os dados do arquivo TXT.
    if (file_exists($nome_arquivo) == true) {
        $clausulas = file_get_contents($nome_arquivo, true);
        $clausulas = explode("||", $clausulas);

        # Tags.
        $clausulas = str_replace('[DATA_ASSINATURA]', $data_assinatura, $clausulas);
        $clausulas = str_replace('[ESTADO_ASSINATURA]', $uf_assinatura, $clausulas);
        $clausulas = str_replace('[CIDADE_ASSINATURA]', $cidade_assinatura, $clausulas);
    } else {
        exit("Problemas ao carregar contrato!<br> Consulte um administrador.");
    }
}


// Cliente

$cliente = DB::use('cliente')->one(['codigo', '===', $codigo_cliente]);
if (empty($cliente) == false) {
    $razao_social_contratante               = (string)  $cliente['nome'];
    $cpf_cnpj_contratante                   = (string)  $cliente['cpf'];
    $telefone_contratante                   = (string)  $cliente['telefone'];
    $tipo_log_contratante                   = (int)     $cliente['tipo_logradouro'];
    $nome_log_contratante                   = (string)  $cliente['nome_logradouro'];
    $numero_log_contratante                 = (int)     $cliente['numero'];
    $bairro_log_contratante                 = (string)  $cliente['bairro'];
    $tipo_comp_contratante                  = (int)     $cliente['tipo_complemento'];
    $nome_comp_contratante                  = (string)  $cliente['desc_complemento'];
    $uf_contratante                         = (string)  $cliente['estado'];
    $cidade_contratante                     = (string)  $cliente['cidade'];
    $cep_contratante                        = (string)  $cliente['cep'];
    $nome_resp_legal_contratante            = (string)  $cliente['nome_reprelegal'];
    $cpf_resp_legal_contratante             = (string)  $cliente['cpf_reprelegal'];
    $telefone_resp_legal_contratante        = (string)  $cliente['telefone_reprelegal'];
    $tipo_log_resp_legal_contratante        = (int)     $cliente['tipo_logradouro_reprelegal'];
    $nome_log_resp_legal_contratante        = (string)  $cliente['nome_logradouro_reprelegal'];

    $numero_log_resp_legal_contratante      = (int)     $cliente['numero_reprelegal'];
    if ($numero_log_resp_legal_contratante == 0) $numero_log_resp_legal_contratante = '__________';

    $bairro_log_resp_legal_contratante      = (string)  $cliente['bairro_reprelegal'];
    if ($bairro_log_resp_legal == '') $bairro_log_resp_legal = '__________';

    $tipo_comp_resp_legal_contratante       = (int)     $cliente['tipo_complemento_reprelegal'];
    $nome_comp_resp_legal_contratante       = (string)  $cliente['desc_complemento_reprelegal'];
    $cod_uf_resp_legal_contratante          = (int)     $cliente['uf_reprelegal'];
    $cod_cidade_resp_legal_contratante      = (int)     $cliente['cidade_reprelegal'];
    $cep_resp_legal_contratante             = (string)  $cliente['cep_reprelegal'];
    $cargo_resp_legal_contratante           = (string)  $cliente['cargo_reprelegal'];
    $texto_esp_clie_cont                    = (string)  $cliente['texto_esp_clie_contrato_1'] . $cliente['texto_esp_clie_contrato_2']. $cliente['texto_esp_clie_contrato_3']. $cliente['texto_esp_clie_contrato_4']. $cliente['texto_esp_clie_contrato_5'] . $cliente['texto_esp_clie_contrato_6'];
    $usar_valor_mensal_contratante          = (string)  $cliente['usar_valor'];
    $valor_mensal_contratante               = (float)   $cliente['valor_mensal'];
    $usar_data_ini_prest_serv_contratante   = (string)  $cliente['usar_data_inicio_contrato'];
    $data_ini_prest_serv_contratante        = (string)  $cliente['data_inicio_contrato'];
    $usar_data_vig_contrato_contratante     = (string)  $cliente['usar_data_final_contrato'];
    $data_vig_contrato_contratante          = (string)  $cliente['data_final_contrato'];
    $usar_data_vencimento_contrato          = (string)  $cliente['usar_data_vencimento_contrato'];
    $dia_vencimento_contrato                = (string)  $cliente['dia_vencimento_contrato'];
    $mes_vencimento_contrato                = (string)  $cliente['mes_vencimento_contrato'];
    $usar_data_assinatura_contrato                   = (string)  $cliente['usar_data_data_ass_contrato'];
    $data_assinatura_cliente                = (string)  $cliente['data_ass_contrato'];

    if ($usar_data_assinatura_contrato == 'N') {
        $data_assinatura = (string) $data_assinatura_cliente;
    }


    if ($tipo_log_contratante != 0) {
        # Tipo Logradouro contratante.
        $lista_tipo_logradouro = DB::use('tipo_logradouro')->all(['codigo', '===', $tipo_log_contratante]);
        // $Consulta1->open("SELECT * FROM Tipo_Logradouro WHERE Codigo = {$tipo_log_contratante} ORDER BY Nome", $Conex1, 3, 4);
        foreach ($lista_tipo_logradouro as $tipo_logradouro) {
            $tipo_log_contratante = (string) $tipo_logradouro['nome'];
        }
    } else {
        $tipo_log_contratante = (string) '________________________';
    }

    if ($tipo_comp_contratante != 0) {
        # Tipo Complemento contratante.
        $lista_tipo_complemento = DB::use('tipo_complemento')->all(['codigo', '===', $tipo_comp_contratante]);
        foreach ($lista_tipo_complemento as $tipo_complemento) {
            $nome_tipo_comp_contratante = (string) $tipo_complemento['nome'];
        }
    }

    if ($tipo_log_resp_legal_contratante != 0) {
        # Tipo Logradouro resp_legal.
        $lista_tipo_logradouro = DB::use('tipo_logradouro')->all(['codigo', '===', $tipo_log_resp_legal_contratante]);
        foreach ($lista_tipo_logradouro as $tipo_logradouro) {
            $tipo_log_resp_legal_contratante = (string) $tipo_logradouro['nome'];
        }
    } else {
        $tipo_log_resp_legal_contratante = (string)  '________________________';
    }

    if ($tipo_comp_resp_legal_contratante != 0) {
        # Tipo Complemento resp_legal.
        $lista_tipo_complemento = DB::use('tipo_complemento')->all(['codigo', '===', $tipo_comp_resp_legal_contratante]);
        foreach ($lista_tipo_complemento as $tipo_complemento) {
            $nome_tipo_comp_resp_legal_contratante = (string) $tipo_complemento['nome'];
        }
    }

    if ($uf_contratante != 0) {
        # Estado contratante.
        $lista_estado_brasileiro = DB::use('estado_brasileiro')->all(['registro', '===', $uf_contratante]);
        foreach ($lista_estado_brasileiro as $estado_brasileiro) {
            $uf_contratante = (string) $estado_brasileiro['sigla_uf'];
        }
    }

    if ($cidade_contratante != 0) {
        # Cidade contratante.
        $lista_municipio_brasileiro = DB::use('municipio_brasileiro')->all(['codigo', '===', $cidade_contratante]);
        foreach ($lista_municipio_brasileiro as $municipio_brasileiro) {
            $cidade_contratante = (string) $municipio_brasileiro['nome'];
        }
    }

    if ($cod_uf_resp_legal_contratante != 0) {
        # Estado Contratada.
        $lista_estado_brasileiro = DB::use('estado_brasileiro')->all(['registro', '===', $cod_uf_resp_legal_contratante]);
        foreach ($lista_estado_brasileiro as $estado_brasileiro) {
            $uf_resp_legal_contratante = (string) $estado_brasileiro['sigla_uf'];
        }
    }

    if ($cod_cidade_resp_legal_contratante != 0) {
        # Cidade Contratada.
        $lista_municipio_brasileiro = DB::use('municipio_brasileiro')->all(['codigo', '===', $cod_cidade_resp_legal_contratante]);
        foreach ($lista_municipio_brasileiro as $municipio_brasileiro) {
            $cidade_resp_legal_contratante = (string) $municipio_brasileiro['nome'];
        }
    }

    if ($cod_uf_assinatura != 0) {
        # Estado Assinatura.
        $lista_estado_brasileiro = DB::use('estado_brasileiro')->all(['registro', '===', $cod_uf_assinatura]);
        foreach ($lista_estado_brasileiro as $estado_brasileiro) {
            $uf_assinatura = (string) $estado_brasileiro['sigla_uf'];
        }
    }

    if ($cod_cidade_assinatura != 0) {
        # Cidade Assinatura.
        $lista_municipio_brasileiro = DB::use('municipio_brasileiro')->all(['codigo', '===', $cod_cidade_assinatura]);
        foreach ($lista_municipio_brasileiro as $municipio_brasileiro) {
            $cidade_assinatura = (string) $municipio_brasileiro['nome'];
        }
    }

    if ($cod_uf_resp_legal_contratante != 0) {
        # Estado Responsável Legal Contratante.
        $lista_estado_brasileiro = DB::use('estado_brasileiro')->all(['registro', '===', $cod_uf_resp_legal_contratante]);
        foreach ($lista_estado_brasileiro as $estado_brasileiro) {
            $uf_resp_legal_contratante = (string) $estado_brasileiro['sigla_uf'];
        }
    }

    if ($cod_cidade_resp_legal_contratante != 0) {
        # Cidade Responsável Legal Contratante.
        $lista_municipio_brasileiro = DB::use('municipio_brasileiro')->all(['codigo', '===', $cod_cidade_resp_legal_contratante]);
        foreach ($lista_municipio_brasileiro as $municipio_brasileiro) {
            $cidade_resp_legal_contratante = (string) $municipio_brasileiro['nome'];
        }
    }

    // Para não aparecer uma vígula no complemento quando estiver vazio.
    $total_comp_contratante = '______________,';
    if ($tipo_comp_contratante != 0) {
        $total_comp_contratante = $nome_tipo_comp_contratante;

        if ($nome_comp_contratante != '') {
            $total_comp_contratante .= ' ' . $nome_comp_contratante . ', ';
        } else {
            $total_comp_contratante .= ' ______________, ';
        }
    } else {
        if ($nome_comp_contratante != '') {
            $total_comp_contratante = $nome_comp_contratante . ', ';
        }
    }

    $total_comp_resp_legal_contratante = '______________,';
    if ($tipo_comp_resp_legal_contratante != 0) {
        $total_comp_resp_legal_contratante = $nome_tipo_comp_resp_legal_contratante;

        if ($nome_comp_resp_legal_contratante != '') {
            $total_comp_resp_legal_contratante .= ' ' . $nome_comp_resp_legal_contratante . ', ';
        } else {
            $total_comp_resp_legal_contratante .= ' ______________, ';
        }
    } else {
        if ($nome_comp_resp_legal_contratante != '') {
            $total_comp_resp_legal_contratante = ' ' . $nome_comp_resp_legal_contratante . ', ';
        }
    }

    $total_comp_contratada = '______________,';
    if ($tipo_comp_contratada != 0) {
        $total_comp_contratada = $nome_tipo_comp_contratada;

        if ($nome_comp_contratada != '') {
            $total_comp_contratada .= ' ' . $nome_comp_contratada . ', ';
        } else {
            $total_comp_contratada .= ' ______________, ';
        }
    } else {
        if ($nome_comp_contratada != '') {
            $total_comp_contratada = ' ' . $nome_comp_contratada . ', ';
        }
    }


    $total_comp_resp_legal = '______________,';
    if ($tipo_comp_resp_legal != 0) {
        $total_comp_resp_legal = ' ' . $nome_tipo_comp_resp_legal;

        if ($nome_comp_resp_legal != '') {
            $total_comp_resp_legal .= ' ' . $nome_comp_resp_legal . ', ';
        } else {
            $total_comp_resp_legal .= ' ______________, ';
        }
    } else {
        if ($nome_comp_resp_legal != '') {
            $total_comp_resp_legal = ' ' . $nome_comp_resp_legal . ', ';
        }
    }
}

// Fornecedor
$fornecedor = DB::use('fornecedor')->one(['codigo', '===', $codigo_cliente]);
if (empty($fornecedor) == false) {
    $razao_social_contratante               = (string)  $fornecedor['nome'];
    $cpf_cnpj_contratante                   = (string)  $fornecedor['cnpj'];
    $telefone_contratante                   = (string)  $fornecedor['telefone'];
    $tipo_log_contratante                   = (int)     $fornecedor['tipo_logradouro'];
    $nome_log_contratante                   = (string)  $fornecedor['nome_logradouro'];
    $numero_log_contratante                 = (int)     $fornecedor['numero'];
    $bairro_log_contratante                 = (string)  $fornecedor['bairro'];
    $tipo_comp_contratante                  = (int)     $fornecedor['tipo_complemento'];
    $nome_comp_contratante                  = (string)  $fornecedor['desc_complemento'];
    $uf_contratante                         = (string)  $fornecedor['estado'];
    $cidade_contratante                     = (string)  $fornecedor['cidade'];
    $cep_contratante                        = (string)  $fornecedor['cep'];
    $nome_resp_legal_contratante            = (string)  $fornecedor['nome_reprelegal'];
    $cpf_resp_legal_contratante             = (string)  $fornecedor['cpf_reprelegal'];
    $telefone_resp_legal_contratante        = (string)  $fornecedor['telefone_reprelegal'];
    $tipo_log_resp_legal_contratante        = (int)     $fornecedor['tipo_logradouro_reprelegal'];
    $nome_log_resp_legal_contratante        = (string)  $fornecedor['nome_logradouro_reprelegal'];

    $numero_log_resp_legal_contratante      = (int)     $fornecedor['numero_reprelegal'];
    if ($numero_log_resp_legal_contratante == 0) $numero_log_resp_legal_contratante = '__________';

    $bairro_log_resp_legal_contratante      = (string)  $fornecedor['bairro_reprelegal'];
    if ($bairro_log_resp_legal == '') $bairro_log_resp_legal = '__________';

    $tipo_comp_resp_legal_contratante       = (int)     $fornecedor['tipo_complemento_reprelegal'];
    $nome_comp_resp_legal_contratante       = (string)  $fornecedor['desc_complemento_reprelegal'];
    $cod_uf_resp_legal_contratante          = (int)     $fornecedor['uf_reprelegal'];
    $cod_cidade_resp_legal_contratante      = (int)     $fornecedor['cidade_reprelegal'];
    $cep_resp_legal_contratante             = (string)  $fornecedor['cep_reprelegal'];
    $cargo_resp_legal_contratante           = (string)  $fornecedor['cargo_reprelegal'];
    $texto_esp_clie_cont                    = (string)  $fornecedor['texto_esp_clie_contrato_1'] . $fornecedor['texto_esp_clie_contrato_2']. $fornecedor['texto_esp_clie_contrato_3']. $fornecedor['texto_esp_clie_contrato_4']. $fornecedor['texto_esp_clie_contrato_5'] . $fornecedor['texto_esp_clie_contrato_6'];
    $usar_valor_mensal_contratante          = (string)  $fornecedor['usar_valor'];
    $valor_mensal_contratante               = (float)   $fornecedor['valor_mensal'];
    $usar_data_ini_prest_serv_contratante   = (string)  $fornecedor['usar_data_inicio_contrato'];
    $data_ini_prest_serv_contratante        = (string)  $fornecedor['data_inicio_contrato'];
    $usar_data_vig_contrato_contratante     = (string)  $fornecedor['usar_data_final_contrato'];
    $data_vig_contrato_contratante          = (string)  $fornecedor['data_final_contrato'];
    $usar_data_vencimento_contrato          = (string)  $fornecedor['usar_data_vencimento_contrato'];
    $dia_vencimento_contrato                = (string)  $fornecedor['dia_vencimento_contrato'];
    $mes_vencimento_contrato                = (string)  $fornecedor['mes_vencimento_contrato'];
    $usar_data_assinatura_contrato                   = (string)  $fornecedor['usar_data_data_ass_contrato'];
    $data_assinatura_cliente                = (string)  $fornecedor['data_ass_contrato'];

    if ($usar_data_assinatura_contrato == 'N') {
        $data_assinatura = (string) $data_assinatura_cliente;
    }


    if ($tipo_log_contratante != 0) {
        # Tipo Logradouro contratante.
        $lista_tipo_logradouro = DB::use('tipo_logradouro')->all(['codigo', '===', $tipo_log_contratante]);
        foreach ($lista_tipo_logradouro as $tipo_logradouro) {
            $tipo_log_contratante = (string) $tipo_logradouro['nome'];
        }
    } else {
        $tipo_log_contratante = (string) '________________________';
    }

    if ($tipo_comp_contratante != 0) {
        # Tipo Complemento contratante.
        $lista_tipo_complemento = DB::use('tipo_complemento')->all(['codigo', '===', $tipo_comp_contratante]);
        foreach ($lista_tipo_complemento as $tipo_complemento) {
            $nome_tipo_comp_contratante = (string) $tipo_complemento['nome'];
        }
    }

    if ($tipo_log_resp_legal_contratante != 0) {
        # Tipo Logradouro resp_legal.
        $lista_tipo_logradouro = DB::use('tipo_logradouro')->all(['codigo', '===', $tipo_log_resp_legal_contratante]);
        foreach ($lista_tipo_logradouro as $tipo_logradouro) {
            $tipo_log_resp_legal_contratante = (string) $tipo_logradouro['nome'];
        }
    } else {
        $tipo_log_resp_legal_contratante = (string)  '________________________';
    }

    if ($tipo_comp_resp_legal_contratante != 0) {
        # Tipo Complemento resp_legal.
        $lista_tipo_complemento = DB::use('tipo_complemento')->all(['codigo', '===', $tipo_comp_resp_legal_contratante]);
        foreach ($lista_tipo_complemento as $tipo_complemento) {
            $nome_tipo_comp_resp_legal_contratante = (string) $tipo_complemento['nome'];
        }
    }

    if ($uf_contratante != 0) {
        # Estado contratante.
        $lista_estado_brasileiro = DB::use('estado_brasileiro')->all(['registro', '===', $uf_contratante]);
        foreach ($lista_estado_brasileiro as $estado_brasileiro) {
            $uf_contratante = (string) $estado_brasileiro['sigla_uf'];
        }
    }

    if ($cidade_contratante != 0) {
        # Cidade contratante.
        $lista_municipio_brasileiro = DB::use('municipio_brasileiro')->all(['codigo', '===', $cidade_contratante]);
        foreach ($lista_municipio_brasileiro as $municipio_brasileiro) {
            $cidade_contratante = (string) $municipio_brasileiro['nome'];
        }
    }

    if ($cod_uf_resp_legal_contratante != 0) {
        # Estado Contratada.
        $lista_estado_brasileiro = DB::use('estado_brasileiro')->all(['registro', '===', $cod_uf_resp_legal_contratante]);
        foreach ($lista_estado_brasileiro as $estado_brasileiro) {
            $uf_resp_legal_contratante = (string) $estado_brasileiro['sigla_uf'];
        }
    }

    if ($cod_cidade_resp_legal_contratante != 0) {
        # Cidade Contratada.
        $lista_municipio_brasileiro = DB::use('municipio_brasileiro')->all(['codigo', '===', $cod_cidade_resp_legal_contratante]);
        foreach ($lista_municipio_brasileiro as $municipio_brasileiro) {
            $cidade_resp_legal_contratante = (string) $municipio_brasileiro['nome'];
        }
    }

    if ($cod_uf_assinatura != 0) {
        # Estado Assinatura.
        $lista_estado_brasileiro = DB::use('estado_brasileiro')->all(['registro', '===', $cod_uf_assinatura]);
        foreach ($lista_estado_brasileiro as $estado_brasileiro) {
            $uf_assinatura = (string) $estado_brasileiro['sigla_uf'];
        }
    }

    if ($cod_cidade_assinatura != 0) {
        # Cidade Assinatura.
        $lista_municipio_brasileiro = DB::use('municipio_brasileiro')->all(['codigo', '===', $cod_cidade_assinatura]);
        foreach ($lista_municipio_brasileiro as $municipio_brasileiro) {
            $cidade_assinatura = (string) $municipio_brasileiro['nome'];
        }
    }

    if ($cod_uf_resp_legal_contratante != 0) {
        # Estado Responsável Legal Contratante.
        $lista_estado_brasileiro = DB::use('estado_brasileiro')->all(['registro', '===', $cod_uf_resp_legal_contratante]);
        foreach ($lista_estado_brasileiro as $estado_brasileiro) {
            $uf_resp_legal_contratante = (string) $estado_brasileiro['sigla_uf'];
        }
    }

    if ($cod_cidade_resp_legal_contratante != 0) {
        # Cidade Responsável Legal Contratante.
        $Consulta1->open("SELECT * FROM Municipios_Brasileiros WHERE Codigo = {$cod_cidade_resp_legal_contratante}", $Conex1, 3, 4);
        $lista_municipio_brasileiro = DB::use('municipio_brasileiro')->all(['codigo', '===', $cod_cidade_resp_legal_contratante]);
        foreach ($lista_municipio_brasileiro as $municipio_brasileiro) {
            $cidade_resp_legal_contratante = (string) $municipio_brasileiro['nome'];
        }
    }

    // Para não aparecer uma vígula no complemento quando estiver vazio.
    $total_comp_contratante = '______________,';
    if ($tipo_comp_contratante != 0) {
        $total_comp_contratante = $nome_tipo_comp_contratante;

        if ($nome_comp_contratante != '') {
            $total_comp_contratante .= ' ' . $nome_comp_contratante . ', ';
        } else {
            $total_comp_contratante .= ' ______________, ';
        }
    } else {
        if ($nome_comp_contratante != '') {
            $total_comp_contratante = $nome_comp_contratante . ', ';
        }
    }

    $total_comp_resp_legal_contratante = '______________,';
    if ($tipo_comp_resp_legal_contratante != 0) {
        $total_comp_resp_legal_contratante = $nome_tipo_comp_resp_legal_contratante;

        if ($nome_comp_resp_legal_contratante != '') {
            $total_comp_resp_legal_contratante .= ' ' . $nome_comp_resp_legal_contratante . ', ';
        } else {
            $total_comp_resp_legal_contratante .= ' ______________, ';
        }
    } else {
        if ($nome_comp_resp_legal_contratante != '') {
            $total_comp_resp_legal_contratante = ' ' . $nome_comp_resp_legal_contratante . ', ';
        }
    }

    $total_comp_contratada = '______________,';
    if ($tipo_comp_contratada != 0) {
        $total_comp_contratada = $nome_tipo_comp_contratada;

        if ($nome_comp_contratada != '') {
            $total_comp_contratada .= ' ' . $nome_comp_contratada . ', ';
        } else {
            $total_comp_contratada .= ' ______________, ';
        }
    } else {
        if ($nome_comp_contratada != '') {
            $total_comp_contratada = ' ' . $nome_comp_contratada . ', ';
        }
    }


    $total_comp_resp_legal = '______________,';
    if ($tipo_comp_resp_legal != 0) {
        $total_comp_resp_legal = ' ' . $nome_tipo_comp_resp_legal;

        if ($nome_comp_resp_legal != '') {
            $total_comp_resp_legal .= ' ' . $nome_comp_resp_legal . ', ';
        } else {
            $total_comp_resp_legal .= ' ______________, ';
        }
    } else {
        if ($nome_comp_resp_legal != '') {
            $total_comp_resp_legal = ' ' . $nome_comp_resp_legal . ', ';
        }
    }
}

// $clausulas = (string) @file_get_contents($nome_arquivo);
$clausulas = str_replace('[TEXTO_ESP_CLIENTE]', $texto_esp_clie_cont, $clausulas);
// $clausulas = str_replace("\r\n", "\n", $clausulas);
// $clausulas = str_replace("\n", "<br>", $clausulas);
// $clausulas = str_replace("[b]", "<b>", $clausulas);
// $clausulas = str_replace("[/b]", "</b>", $clausulas);
// $clausulas = str_replace("[i]", "<i>", $clausulas);
// $clausulas = str_replace("[/i]", "</i>", $clausulas);
// $clausulas = str_replace("[u]", "<u>", $clausulas);
// $clausulas = str_replace("[/u]", "</u>", $clausulas);
// $clausulas = str_replace("[/u]", "</u>", $clausulas);
// $clausulas = str_replace("&#43;", "+", $clausulas);
// $clausulas = str_replace("&lt;", "<", $clausulas);
// $clausulas = str_replace("&gt;", ">", $clausulas);
// $clausulas = str_replace("&amp;", "&", $clausulas);
// $clausulas = str_replace("-&amp;", "&", $clausulas);
// $clausulas = str_replace("&nbsp;", " ", $clausulas);
// $clausulas = str_replace(",,", "\n", $clausulas);
// $clausulas = str_replace("nbsp;,", "\n", $clausulas);
// $clausulas = str_replace(" ,", "\n", $clausulas);

if ($usar_valor_mensal_contratante == 'N') {
  $clausulas = str_replace('[VALOR_MENSAL]', SIGLA_MOEDA . ' ' . number_format($valor_mensal_contratante, 2, ',', '') . ' (' . strval(converter_extenso($valor_mensal_contratante, 'MOEDA')). ')', $clausulas);
} else {
  $clausulas = @str_replace('[VALOR_MENSAL]', SIGLA_MOEDA . ' ' . number_format($valor_mensal, 2, ',', '') . ' (' . strval(converter_extenso($valor_mensal, 'MOEDA')). ')', $clausulas);
}
if ($usar_data_ini_prest_serv_contratante == 'N') {
  $clausulas = str_replace('[DATA_PRESTACAO]', $data_ini_prest_serv_contratante, $clausulas);
} else {
  $clausulas = str_replace('[DATA_PRESTACAO]', $data_ini_prest_serv, $clausulas);
}
if ($usar_data_vig_contrato_contratante == 'N') {
  $clausulas = str_replace('[DATA_VIGENCIA]', $data_vig_contrato_contratante, $clausulas);
} else {
  $clausulas = str_replace('[DATA_VIGENCIA]', $data_vig_contrato, $clausulas);
}
if ($usar_data_vencimento_contrato == 'N') {
  $clausulas = str_replace('[DATA_MENSALIDADE]', $dia_vencimento_contrato . ' do mês ' . $mes_vencimento_contrato , $clausulas);
} else {
  $clausulas = str_replace('[DATA_MENSALIDADE]', $data_dia_venc_mensal . ' do mês ' . $data_mes_venc_mensal , $clausulas);
}

$linha      = '';
$max_linha  = 94;
$caractere  = '';
$ult_carac  = "";

?>
<html>
  <style type="text/css">
    .Pagina {
      position: relative;
      text-align: justify;
      font: normal 13px "Courier New";
      width: 950px !important;
      box-sizing: border-box !important;
    }
    p{
      margin: 0px;
    }
    #imgLogotipo {
      position: relative;
      top: 80px;
    }
    #lbTitulo1 {
      position: relative; top: -70px; left: 220px; height: 40px; width: 300px;
      font: 900 15px Verdana;
      text-align:left;
      background-color:transparent;
    }
    #Div_IdContrato {
      position: relative; top: -105px; left:590px; width:320px; height:130px;
      border-width:1px;
      border-color:black;
      border-style:solid;
      padding-right:5px;
      padding-left:5px;
      padding-top:5px;
      padding-bottom:5px;
      margin-top:0px;
      margin-bottom:0px;
      margin-left:0px;
      margin-right:0px;
      word-wrap: break-word;
      font-family:Verdana;
      font-size: 12px;
    }
    #dv_imprimir {
      display: none;
      position: absolute;
      top: 0px;
      left: 870px;
      width: 110px;
      border: 2px solid #D8D8D8;
      background-color: #F0F0F0;
      padding: 3px 1px 3px 5px;
    }
    #btn_imprimir {
      top: 0px;
      left: 10px;
      height: 50px;
      width: 50px;
      background: transparent url('Imagens/btnImpressora.png') no-repeat;
      padding: 0px;
      margin: 0px;
      border: 0px none;
    }
    #btn_fechar {
      position: absolute;
      top: 10px;
      left: 65px;
      height: 33px;
      width: 33px;
      background: transparent url('Imagens/x.png') no-repeat;
      border: 0px none;
    }
    .pointer {
      cursor: pointer;
    }

    #assinaturas {
      height: 450px;
      page-break-before: always;
    }
  </style>
  <script type="text/javascript">
    function on_load () {
      mostrarImprimir();
    }

    function mostrarImprimir() {
      document.getElementById('dv_imprimir').style.display = 'block';
      ligarTimer = 'S';
      timer_Centralizar = setTimeout('centralizarImprimir()', 10);
    }

    function centralizarImprimir() {
      try {clearTimeout(timer_Centralizar);} catch (e) {}

      document.getElementById('dv_imprimir').style.top = (10 + document.body.scrollTop) + 'px';

      if (ligarTimer == 'S') {
        timer_Centralizar = window.setTimeout('centralizarImprimir()', 10000);
        return;
      } else {
        window.document.getElementById('dv_imprimir').style.display = 'none';
        return;
      }
    }

    function onImprimir() {
      document.getElementById('dv_imprimir').style.display = 'none';
      if (!print()) {
        document.getElementById('dv_imprimir').style.display = 'block';
      }
    }

    function onClose() {
      close();
    }
  </script>
  <body onLoad="on_load();">
    <?php
    echo '<div class="Pagina">';
      if (file_exists($logotipo)) {
        echo '<img id="imgLogotipo" src="' . $logotipo . '" >';
        echo '<div id="lbTitulo1">CONTRATO TIPO ' . $codigo_contrato . '<br>' . $desc_rap . '</div>';
        echo '<div id="Div_IdContrato">';
        echo "Código interno: " . '<b>' . str_pad(strval($codigo_cliente), 6, "0", STR_PAD_LEFT) . '</b><br><br>';
        echo $razao_social_contratante . '<br>';
        echo "CNPJ/CPF: " . $cpf_cnpj_contratante;
        echo '</div>';
      } else {
        $logotipo = "Dados/contratos/logotipos/logo_padrao.png";
        echo '<img id="imgLogotipo" src="' . $logotipo . '" >';
        echo '<div id="lbTitulo1">CONTRATO TIPO ' . $codigo_contrato . '<br>' . $desc_rap . '</div>';
        echo '<div id="Div_IdContrato">';
        echo "Código interno: " . '<b>' . str_pad(strval($codigo_cliente), 6, "0", STR_PAD_LEFT) . '</b><br><br>';
        echo $razao_social_contratante . '<br>';
        echo "CNPJ/CPF: " . $cpf_cnpj_contratante;
        echo '</div>';
      }


        # Contratante.
        echo '<br/><br/>1) <b>Contratante:</b>' . $razao_social_contratante . ', CPF/CNPJ ' . $cpf_cnpj_contratante . ' com sede social estabelecida na cidade de ' .
        $cidade_contratante . '/' . $uf_contratante . ' à ' . $tipo_log_contratante . ' ' . $nome_log_contratante . ', n.º ' . $numero_log_contratante . ', ' . $bairro_log_contratante . ', ' . $total_comp_contratante . ' ' . $cep_contratante . ', com telefone comercial ' . $telefone_contratante . ', neste ato representado(a) por seu(sua) ' .
        $cargo_resp_legal_contratante . ', Sr(a). ' . $nome_resp_legal_contratante . ', portador(a) do CPF ' . $cpf_resp_legal_contratante . ', residente e domiciliado na cidade de ' .
        $cidade_resp_legal_contratante . '/' . $uf_contratante . ', à ' .
        $tipo_log_resp_legal_contratante . ' ' . $nome_log_resp_legal_contratante . ', n.º ' . $numero_log_resp_legal_contratante . ', ' . $bairro_log_resp_legal_contratante . ', ' . $total_comp_resp_legal_contratante . ' ' . $cep_resp_legal_contratante . ', com telefone de contato sob n.º ' .
        $telefone_resp_legal_contratante . ', que, ao final, subscreve, doravante designada apenas como Contratante, para todos os efeitos que se façam nas cláusulas contratuais;';

        # Contratada.
        echo '<br/><br/>2) <b>Contratada: </b>' . $nome_razao_contratada . ', CPF/CNPJ ' . $cpf_cnpj_contratada . ' com sede social estabelecida na cidade de ' . $cidade_contratada . '/' . $uf_contratada .
                ', à ' . $tipo_log_contratada . ' ' . $nome_log_contratada . ', n.º ' . $numero_log_contratada . ', ' . $bairro_log_contratada . ', ' . $total_comp_contratada . ' ' . $cep_contratada . ', com telefone comercial ' . $telefone_contratada . ', neste ato representado(a) por seu(sua) ' .
          $cargo_resp_legal . ', portador(a) do CPF ' . $cpf_resp_legal . ', residente e domiciliado na cidade de ' . $cidade_resp_legal . '/' . $uf_resp_legal . ', à ' .
          $tipo_log_resp_legal . ' ' . $nome_log_resp_legal . ', n.º ' . $numero_log_resp_legal . ', ' . $bairro_log_resp_legal . ', ' . $total_comp_resp_legal . ' ' . $cep_resp_legal . ', ' . ' com telefone de contato sob n.º ' . $telefone_resp_legal . ', que, ao final, subscreve, doravante designada apenas como Contratada, para todos os efeitos que se façam nas cláusulas contratuais;';
        echo '<br /><br />Resolvem, de comum acordo, estabelecerem o seguinte contrato particular de prestação de serviços conforme as cláusulas contratuais:<br /><br />';

        echo '<div class="Pagina">';
        echo "<pre>";
        foreach ($clausulas as $chave => $linha) {
          if ($linha == "" || $linha == "&nbsp;") {
            echo "<br>";
          }else if(strpos($linha, "&nbsp;&nbsp;&nbsp;") != false ){
            $cont = substr_count($linha, "&nbsp;&nbsp;&nbsp;");
            if ($cont > 0 ) {
              echo '<p>' . $linha . '</p>';
            }
          }else{
            echo '<p>' . str_replace("&nbsp;", " ", $linha) . '</p>';
          }
          echo "</pre>";
        }
        echo '</div><div class="Pagina">';
        # Cidade e data de assintatura.
        echo '<div id="assinaturas">';
          echo '<br /><br />' . $cidade_assinatura . '/' . $uf_assinatura .  ', ' .$data_assinatura;
          echo '<br /><br><br>_______________________________________';
          echo '<br />Contratante:';
          echo '<br />' . $razao_social_contratante . ' - ' . $cpf_cnpj_contratante;
          echo '<br />' . $nome_resp_legal_contratante . ' - ' . $cpf_resp_legal_contratante;
          echo '<br />' . $cargo_resp_legal_contratante;
          echo '<br /><br /><br>_______________________________________';
          echo '<br />Contratada:';
          echo '<br />' . $nome_razao_contratada . ' - ' . $cpf_cnpj_contratada;
          echo '<br />' . $nome_resp_legal . ' - ' . $cpf_resp_legal;
          echo '<br />' . $cargo_resp_legal;
          echo '<br><br><br>';
          echo '<b>Testemunhas:</b>';
          echo '<br><br><br>';
          echo '1)&nbsp;Nome:___________________________________&nbsp;&nbsp;&nbsp;2)&nbsp;Nome:___________________________________<br>';
          echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RG:__________________&nbsp;Emissor:________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RG:__________________&nbsp;Emissor:________<br>";
        echo '</div>';
      echo '</div>';
      ?>
      <div id="dv_imprimir">
        <input class="pointer" type="button" id="btn_imprimir" onClick="onImprimir();" value="" title="Imprimir" />
        <input class="pointer" type="button" id="btn_fechar" onClick="onClose();" value="" title="Fechar" />
      </div>
  </body>
</html>
<?php
exit;
