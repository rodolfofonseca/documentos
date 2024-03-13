<?php
require_once 'Classes/bancoDeDados.php';
require_once 'ModelosInterface.php';

class TiposContas implements ModelosInterface{
    private $id_tipo_conta;
    private $id_tipo_despesa;
    private $descricao_tipo_conta;

    public function tabela(){
        return (string) 'tipo_conta';
    }

    public function modelo(){
        return (array) ['id_tipo_conta' => (int) 0, 'id_tipo_despesa' => (int) 0, 'descricao_tipo_conta' => (string) ''];
    }

    public function colocar_dados($dados){
        if(array_key_exists('codigo_tipo_conta', $dados) == true){
            $this->id_tipo_conta = (int) intval($dados['codigo_tipo_conta'], 10);
        }

        if(array_key_exists('codigo_tipo_despesa', $dados) == true){
            $this->id_tipo_despesa = (int) intval($dados['codigo_tipo_despesa'], 10);
        }

        if(array_key_exists('descricao_tipo_conta', $dados) == true){
            $this->descricao_tipo_conta = (string) strtoupper($dados['descricao_tipo_conta']);
        }
    }

    public function salvar($dados){
        $this->colocar_dados($dados);

        $checar_existencia = (bool) model_check($this->tabela(), ['id_tipo_conta', '===', (int) $this->id_tipo_conta]);

        if($checar_existencia == true){
            return (bool) model_update($this->tabela(), ['id_tipo_conta', '===', (int) $this->id_tipo_conta], model_parse($this->modelo(), ['id_tipo_conta' => (int) $this->id_tipo_conta, 'id_tipo_despesa' => (int) $this->id_tipo_despesa, 'descricao_tipo_conta' => (string) $this->descricao_tipo_conta]));
        }else{
            return (bool) model_insert($this->tabela(), model_parse($this->modelo(), ['id_tipo_conta' => (int) model_next($this->tabela(), 'id_tipo_conta'), 'id_tipo_despesa' => (int) $this->id_tipo_despesa, 'descricao_tipo_conta' => (string) $this->descricao_tipo_conta]));
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