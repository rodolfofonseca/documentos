<?php
require_once 'Classes/bancoDeDados.php';

class Caixa{
    private $id_caixa;
    private $id_prateleira;
    private $nome_caixa;
    private $descricao;
    private $codigo_barras;

    private function tabela(){
        return (string) 'caixa';
    }

    private function modelo(){
        return (array) ['id_caixa' => (int) 0, 'id_prateleira' => (int) 0, 'nome_caixa' => (string) '', 'descricao' => (string) '', 'codigo_barras' => (string) ''];
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_caixa', $dados) == true){
            $this->id_caixa = (int) intval($dados['codigo_caixa'], 10);
        }

        if(array_key_exists('codigo_prateleira', $dados) == true){
            $this->id_prateleira = (int) intval($dados['codigo_prateleira'], 10);
        }

        if(array_key_exists('nome_caixa', $dados) == true){
            $this->nome_caixa = (string) strtoupper($dados['nome_caixa']);
        }

        if(array_key_exists('descricao', $dados) == true){
            $this->descricao = (string) $dados['descricao'];
        }

        if(array_key_exists('codigo_barras', $dados) == true){
            $this->codigo_barras = (string) $dados['codigo_barras'];
        }
    }

    public function salvar($dados){
        $this->colocar_dados($dados);
        $checar_existencia = (bool) model_check($this->tabela(), ['id_caixa', '===', (int) $this->id_caixa]);

        if($checar_existencia == true){
            return (bool) model_update($this->tabela(), ['id_caixa', '===', (int) $this->id_caixa], model_parse($this->modelo(), ['id_caixa' => (int) $this->id_caixa, 'id_prateleira' => (int) $this->id_prateleira, 'nome_caixa' => (string) $this->nome_caixa, 'descricao' => (string) $this->descricao, 'codigo_barras' => (string) $this->codigo_barras]));
        }else{
            return (bool) model_insert($this->tabela(), model_parse($this->modelo(), ['id_caixa' => (int) model_next($this->tabela(), 'id_caixa'), 'id_prateleira' => (int) $this->id_prateleira, 'nome_caixa' => (string) $this->nome_caixa, 'descricao' => (string) $this->descricao, 'codigo_barras' => (string) $this->codigo_barras]));
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