<?php
class Clima{
    private $chave = 'cd082846';
    private $cid = '457398';
    private $endpoint = 'weather';

    /**
     * Função responsável por pesquisar as informações a respeito da cidade que foi passada com chave
     * @return array com as informações de previsão do tempo informadas.
     */
    public function pesquisar_previsao(){
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
            
        }


        if(empty($resposta) == false){
            return (array) json_decode($resposta, true);
        }else{
            return (array) [];
        }
    }


}
?>