<?php
require_once 'Classes/bancoDeDados.php';
require_once 'ModelosInterface.php';

class ContasBancarias implements ModelosInterface{
    private $id_conta_bancaria;
    private $nome_conta;

    public function tabela(){
        return (string) 'contas_bancarias';
    }

    public function modelo(){
        return (array) ['id_conta_bancaria' => (int) 0, 'nome_conta' => (string) ''];
    }

    public function colocar_dados($dados){
        if(array_key_exists('codigo_conta', $dados) == true){
            $this->id_conta_bancaria = (int) intval($dados['codigo_conta'], 10);
        }else{
            $this->id_conta_bancaria = 0;
        }

        if(array_key_exists('nome_conta', $dados) == true){
            $this->nome_conta = (string) strtoupper($dados['nome_conta']);
        }else{
            $this->nome_conta = (string) '';
        }
    }

    public function pesquisar($dados){
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    public function pesquisar_todos($dados){
        return (array) model_all($this->tabela(), $dados['filtro'], $dados['ordenacao'], $dados['limite']);
    }

    public function salvar($dados){
        $this->colocar_dados($dados);

        $checar_existencia = (bool) model_check($this->tabela(), ['id_conta_bancaria', '===', (int) $this->id_conta_bancaria]);

        if($checar_existencia == true){
            return (bool) model_update($this->tabela(), ['id_conta_bancaria', '===', (int) $this->id_conta_bancaria], model_parse($this->modelo(), ['id_conta_bancaria' => (int) $this->id_conta_bancaria, 'nome_conta' => (string) $this->nome_conta]));
        }else{
            return (bool) model_insert($this->tabela(), model_parse($this->modelo(), ['id_conta_bancaria' => (int) model_next($this->modelo(), 'id_conta_bancaria'), 'nome_conta' => (string) $this->nome_conta]));
        }
    }
}
?>