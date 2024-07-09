<?php
require_once 'Classes/bancoDeDados.php';

class Organizacao{
    private $id_organizacao;
    private $id_empresa;
    private $id_usuario;
    private $nome_organizacao;
    private $descricao;
    private $codigo_barras;
    private $forma_visualizacao;
    
    private function tabela(){
        return (string) 'organizacao';
    }

    private function modelo(){
        return (array) ['id_organizacao' => (int) 0, 'id_empresa' => (int) 0, 'id_usuario' => (int) 0, 'nome_organizacao' => (string) '', 'descricao' => (string) '', 'codigo_barras' => (string) '', 'forma_visualizacao' => (string) 'PUBLICO'];
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_organizacao', $dados) == true){
            $this->id_organizacao = (int) intval($dados['codigo_organizacao'], 10);
        }else{
            $this->id_organizacao = (int) 0;
        }

        if(array_key_exists('codigo_empresa', $dados) == true){
            $this->id_empresa = (int) intval($dados['codigo_empresa'], 10);
        }else{
            $this->id_empresa = (int) 0;
        }

        if(array_key_exists('codigo_usuario', $dados) == true){
            $this->id_usuario = (int) intval($dados['codigo_usuario'], 10);
        }else{
            $this->id_usuario = (int) 0;
        }

        if(array_key_exists('nome_organizacao', $dados) == true){
            $this->nome_organizacao = (string) strtoupper($dados['nome_organizacao']);
        }

        if(array_key_exists('descricao', $dados) == true){
            $this->descricao = (string) $dados['descricao'];
        }
        
        if(array_key_exists('codigo_barras', $dados) == true){
            $this->codigo_barras = (string) $dados['codigo_barras'];
        }else{
            $this->codigo_barras = (string) '';
        }

        if(array_key_exists('forma_visualizacao', $dados) == true){
            $this->forma_visualizacao = (string) $dados['forma_visualizacao'];
        }else{
            $this->forma_visualizacao = (string) 'PUBLICO';
        }
    }
    
    public function salvar($dados){
        $this->colocar_dados($dados);
        $filtro = (array) ['and' => (array) [(array) ['id_organizacao', '===', (int) $this->id_organizacao], ['id_empresa', '===', (int) $this->id_empresa]]];

        $checar_existencia = (bool) model_check((string) $this->tabela(), (array) $filtro);

        if($checar_existencia == true){
            $retorno_pesquisa = (array) model_one((string) $this->tabela(), (array) $filtro);

            return (bool) model_update((string) $this->tabela(), (array) $filtro, (array) model_parse((array) $retorno_pesquisa, (array) ['nome_organizacao' => (string) $this->nome_organizacao, 'descricao' => (string) $this->descricao, 'codigo_barras' => (string) $this->codigo_barras, 'forma_visualizacao' => (string) $this->forma_visualizacao]));
        }else{
            return (bool) model_insert((string) $this->tabela(), (array) model_parse((array) $this->modelo(), (array) ['id_organizacao' => (int) intval(model_next((string) $this->tabela(), 'id_organizacao', (array) ['id_empresa', '===', (int) $this->id_empresa]), 10), 'id_empresa' => (int) $this->id_empresa, 'id_usuario' => (int) $this->id_usuario,'nome_organizacao' => (string) $this->nome_organizacao, 'descricao' => (string) $this->descricao, 'codigo_barras' => (string) $this->codigo_barras, 'forma_visualizacao' => (string) $this->forma_visualizacao]));
        }
    }
    public function pesquisar($dados){
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    public function pesquisar_todos($dados){
        return (array) model_all($this->tabela(), $dados['filtro'], $dados['ordenacao'], $dados['limite']);
    }
}
?>