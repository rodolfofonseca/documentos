<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Sistema.php';

class Clima{
    private $chave;
    private $cid;
    private $endpoint = 'weather';

    /**
     * Função responsável por pesquisar as informações a respeito da cidade que foi passada com chave
     * @return array com as informações de previsão do tempo informadas.
     * 
     * cd082846
     */
    public function pesquisar_previsao(){
        $this->buscar_dados_cidade();

        if($this->chave == null || $this->chave == ''){
            return (array) [];
        }

        if($this->cid == null || $this->cid == ''){
            return (array) [];
        }

        $parametros = (array) ['cid' => $this->cid, $this->chave];
        $url = 'https://api.hgbrasil.com/weather?woeid='.$this->cid.'&format=json';
        $parametros = array_merge($parametros, (array) ['key' => $this->chave]);
        $resposta = (array) [];

        foreach($parametros as $chave => $valor){
            if(empty($valor)){
                continue;
            }
            $url = $url.$chave.'='.urldecode($valor).'&';
        }

        try{
            $resposta = file_get_contents(substr($url, 0, -1));
        }catch(Exception $ex){
            return (array) [];
        }


        if(empty($resposta) == false){
            return (array) json_decode($resposta, true);
        }else{
            return (array) [];
        }
    }

    /**
     * Função responsável por realizar a pesquisa no banco de dados pela chave de api do cliente e a cidade do cliente
     */
    private function buscar_dados_cidade(){
        $objeto_sistema = new Sistema();
        $filtro = (array) ['filtro' => (array) ['id_sistema', '===', (int) 1]];
        $retorno_pesquisa = $objeto_sistema->pesquisar($filtro);

        if(array_key_exists('chave_api', $retorno_pesquisa) == true){
            $this->chave = (string) $retorno_pesquisa['chave_api'];
        }

        if(array_key_exists('cidade', $retorno_pesquisa) == true){
            $this->cid = (string) $retorno_pesquisa['cidade'];
        }        
    }
}
?>