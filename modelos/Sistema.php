<?php
require_once 'Classes/bancoDeDados.php';

Class Sistema{
    private $id_sistema;
    private $versao_sistema;
    private $chave_api;
    private $cidade;

    private function tabela(){
        return (string) 'sistema';
    }

    private function modelo(){
        return (array) ['id_sistema' => (int) 0, 'versao_sistema' => (string) '', 'chave_api' => (string) '', 'cidade' => (string) ''];
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_sistema', $dados) == true){
            $this->id_sistema = (int) intval($dados['codigo_sistema'], 10);
        }

        if(array_key_exists('versao_sistema', $dados) == true){
            $this->versao_sistema = (string) $dados['versao_sistema'];
        }

        if(array_key_exists('chave_api', $dados) == true){
            $this->chave_api = (string) $dados['chave_api'];
        }

        if(array_key_exists('cidade', $dados) == true){
            $this->cidade = (string) $dados['cidade'];
        }
    }

    public function salvar_dados($dados){
        $this->colocar_dados($dados);
        $retorno = (bool) false;
        $checar_existencia = (bool) model_check($this->tabela(), ['id_sistema', '===', (int) 1]);

        if($checar_existencia == true){
            $retorno = (bool) model_update($this->tabela(), ['id_sistema', '===', (int) $this->id_sistema], model_parse($this->modelo(), ['id_sistema' => (int) $this->id_sistema, 'versao_sistema' => (string) $this->versao_sistema, 'chave_api' => (string) $this->chave_api, 'cidade' => (string) $this->cidade]));
        }else{
            $retorno = (bool) model_insert($this->tabela(), model_parse($this->modelo(), ['id_sistema' => (int) 1, 'versao_sistema' => (string) '1.0']));
        }

        return (bool) $retorno;
    }

    public function pesquisar($dados){
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    public function pesquisar_todos($dados){
        return (array) model_all($this->tabela(), $dados['filtro'], $dados['ordenacao'], $dados['limite']);
    }
}
?>