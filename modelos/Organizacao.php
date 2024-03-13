<?php
require_once 'Classes/bancoDeDados.php';

class Organizacao{
    private $id_organizacao;
    private $nome_organizacao;

    public function pesquisar($dados){
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    public function pesquisar_todos($dados){
        return (array) model_all($this->tabela(), $dados['filtro'], $dados['ordenacao'], $dados['limite']);
    }

    public function salvar($dados){
        $this->colocar_dados($dados);
        $checar_existencia = (bool) model_check($this->tabela(), ['id_organizacao', '===', (int) $this->id_organizacao]);
        $retorno = (bool) false;

        if($checar_existencia == true){
            $retorno = (bool) model_update($this->tabela(), ['id_organizacao', '===', (int) $this->id_organizacao], model_parse($this->modelo(), ['id_organizacao' => (int) $this->id_organizacao, 'nome_organizacao' => (string) $this->nome_organizacao]));
        }else{
            $retorno = (bool) model_insert($this->tabela(), model_parse($this->modelo(), ['id_organizacao' => model_next($this->tabela(), 'id_organizacao'), 'nome_organizacao' => (string) $this->nome_organizacao]));
        }

        return (bool) $retorno;
    }

    private function tabela(){
        return (string) 'organizacao';
    }

    private function modelo(){
        return (array) ['id_organizacao' => (int) 0, 'nome_organizacao' => (string) ''];
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_organizacao', $dados) == true){
            $this->id_organizacao = (int) intval($dados['codigo_organizacao'], 10);
        }

        if(array_key_exists('nome_organizacao', $dados) == true){
            $this->nome_organizacao = (string) strtoupper($dados['nome_organizacao']);
        }
    }
}
?>