<?php
require_once 'Classes/bancoDeDados.php';
require_once 'modelos/Preferencia.php';

//author: @rodolfofonseca
//version: 1.0
//since: 26/01/2025


//@note salvar_preferencia_usuario 
/**
 * Rota responsável por salvar no banco de dados as preferências do usuário
 */
router_add('salvar_preferencia_usuario', function(){
    $codigo_sistema = (int) (isset($_REQUEST['codigo_sistema']) ? (int) intval($_REQUEST['codigo_sistema'], 10): 0);
    $codigo_usuario = (int) (isset($_REQUEST['codigo_usuario']) ? (int) intval($_REQUEST['codigo_usuario'], 10):0);
    $nome_preferencia = (string) (isset($_REQUEST['nome_preferencia']) ? (string) $_REQUEST['nome_preferencia']: '');
    $preferencia = (string) (isset($_REQUEST['preferencia']) ? (string) $_REQUEST['preferencia']:'');
    $retorno = (bool) false;
    $objeto_preferencia = new Preferencia();

    $dados = (array) ['codigo_sistema' => (int) $codigo_sistema, 'codigo_usuario' => (int) $codigo_usuario, 'nome_preferencia' => (string) $nome_preferencia, 'preferencia' => (string) $preferencia];

    $retorno_pesquisa_preferencia = (array) $objeto_preferencia->pesquisar((array) ['filtro' => (array) ['and' => (array) [['id_sistema', '===', (int) $codigo_sistema], ['id_usuario', '===', (int) $codigo_usuario], ['nome_preferencia', '===', (string) $nome_preferencia]]]]);

    if(empty($retorno_pesquisa_preferencia) == true){
        $retorno = (bool) $objeto_preferencia->salvar_dados((array) $dados);
    }else{
        $retorno = (bool) $objeto_preferencia->excluir((array) $dados);
    }

    echo json_encode((array) ['retorno' => (bool) $retorno], JSON_UNESCAPED_UNICODE);
    
    exit;
});

?>