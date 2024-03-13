<?php
require_once 'Classes/bancoDeDados.php';

class Armario{
    private $id_armario;
    private $nome_armario;
    private $codigo_barras;

    private function tabela(){
        return (string) 'armario';
    }

    private function modelo(){
        return (array) ['id_armario' => (int) 0, 'nome_armario' => (string) '', 'codigo_barras' => (string) ''];
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_armario', $dados) == true){
            $this->id_armario = (int) intval($dados['codigo_armario'], 10);
        }

        if(array_key_exists('nome_armario', $dados) == true){
            $this->nome_armario = (string) strtoupper($dados['nome_armario']);
        }

        if(array_key_exists('codigo_barras', $dados) == true){
            $this->codigo_barras = (string) $dados['codigo_barras'];
        }
    }
    
    public function salvar_dados($dados){
        $this->colocar_dados($dados);
        $retorno = (bool) false;
        $checar_existencia = (bool) model_check($this->tabela(), ['id_armario', '===', (int) $this->id_armario]);

        if($checar_existencia == true){
            $retorno = (bool) model_update($this->tabela(), ['id_armario', '===', (int) $this->id_armario], model_parse($this->modelo(), ['id_armario' => (int) $this->id_armario, 'nome_armario' => (string) $this->nome_armario, 'codigo_barras' => (string) $this->codigo_barras]));
        }else{
            $retorno = (bool) model_insert($this->tabela(), ['id_armario' => (int) model_next($this->tabela(), 'id_armario'), 'nome_armario' => (string) $this->nome_armario, 'codigo_barras' => (string) $this->codigo_barras]);
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