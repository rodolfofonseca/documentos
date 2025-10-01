<?php
require_once 'Classes/bancoDeDados.php';
require_once 'modelos/Notificacoes.php';


//@audit index
router_add('index', function(){
    require_once 'includes/head.php';
    require_once 'includes/footer.php';
});

//@audit visualizar
/**
 * Rota responsável por pesquiar a notificação e alterar o status de leitura para "LIDO" e retornar as informações para que o usuário possa visualizar de forma cabal conforme for necessário.
 */
router_add('visualizar', function(){
    $id_notificacao = (int) (isset($_REQUEST['codigo_notificacao']) ? (int) intval($_REQUEST['codigo_notificacao'], 10):0);

    $objeto_notificacao = new Notificacoes();
    $retorno_alteracao = (bool) $objeto_notificacao->salvar_dados((array) ['id_notificacao' => (int) $id_notificacao, 'status_leitura' => (string) 'LIDO']);

    $retorno_pesquisa = (array) $objeto_notificacao->pesquisar((array) ['filtro' => (array) ['id_notificacao', '===', (int) $id_notificacao]]);

    if(empty($retorno_pesquisa) == false){
        echo json_encode((array) ['status' => (bool) false, 'titulo_notificacao' => (string) $retorno_pesquisa['titulo_notificacao'], 'mensagem_longa' => (string) $retorno_pesquisa['mensagem_longa'], 'data_notificacao' => (string) convert_date($retorno_pesquisa['data_notificacao'])], JSON_UNESCAPED_UNICODE);
    }else{
        echo json_encode((array) ['status' => (bool) false, 'titulo_notificacao' => (string) '', 'descricao_longa' => (string) '', 'data_notificacao' => (string) ''], JSON_UNESCAPED_UNICODE);
    }
});
?>