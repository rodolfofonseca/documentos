<?php
require_once 'Classes/bancoDeDados.php';


//@note salvar_preferencia_usuario 
/**
 * Rota responsável por salvar no banco de dados as preferências do usuário
 */
router_add('salvar_preferencia_usuario', function(){
    $codigo_sistema = (string) (isset($_REQUEST['codigo_sistema']) ? (string) $_REQUEST['codigo_sistema']: '');
    $codigo_usuario = (string) (isset($_REQUEST['codigo_usuario']) ? (string) $_REQUEST['codigo_usuario']: '');
    $nome_preferencia = (string) (isset($_REQUEST['nome_preferencia']) ? (string) $_REQUEST['nome_preferencia']: '');
    $preferencia = (string) (isset($_REQUEST['preferencia']) ? (string) $_REQUEST['preferencia']:'');
    $retorno = (bool) false;
    $objeto_preferencia = new Preferencia();
    
   $dados = (array) ['codigo_sistema' => (string) $codigo_sistema, 'codigo_usuario' => (string) $codigo_usuario, 'nome_preferencia' => (string) $nome_preferencia, 'preferencia' => (string) $preferencia];


    $retorno_pesquisa_preferencia = (array) $objeto_preferencia->pesquisar((array) ['filtro' => (array) ['and' => (array) [['sistema', '===', convert_id($codigo_sistema)], ['usuario', '===', convert_id($codigo_usuario)], ['nome_preferencia', '===', (string) $nome_preferencia]]]]);

    if(empty($retorno_pesquisa_preferencia) == true){
        $retorno = (bool) $objeto_preferencia->salvar_dados((array) $dados);
    }else{
        $retorno = (bool) $objeto_preferencia->excluir((array) $dados);
    }

    echo json_encode((array) ['retorno' => (bool) $retorno], JSON_UNESCAPED_UNICODE);
    
    exit;
});

?>