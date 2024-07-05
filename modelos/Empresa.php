<?php
require_once 'Classes/bancoDedados.php';

class Empresa{
    private $id_empresa;
    private $nome_empresa;
    private $representante;
    private $informcao_contato;

    private function tabela(){
        return (string) 'empresa';
    }

    private function modelo(){
        return (array) ['id_empresa' => (int) 0, 'nome_empresa' => (string) '', 'representante' => (string) '', 'informacao_contato' => (string) ''];
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_empresa', $dados) == true){
            $this->id_empresa = (int) intval($dados['codigo_empresa'], 10);
        }

        if(array_key_exists('nome_empresa', $dados) == true){
            $this->nome_empresa = (string) strtoupper($dados['nome_empresa']);
        }

        if(array_key_exists('representante', $dados) == true){
            $this->representante = (string) strtoupper($dados['representante']);
        }

        if(array_key_exists('informacao_contato', $dados) == true){
            $this->informcao_contato = (string) $dados['informacao_contato'];
        }
    }

    public function salvar_dados($dados){
        $this->colocar_dados($dados);

        $retorno_pesquisa = (bool) model_check($this->tabela(), ['id_empresa', '===', (int) $this->id_empresa]);
        $retorno_operacao = (bool) false;

        if($retorno_pesquisa == true){
            $retorno_operacao = (bool) model_update($this->tabela(), ['id_empresa', '===', (int) $this->id_empresa], (array) model_parse((array) $this->modelo(), (array) ['id_empresa' => (int) $this->id_empresa, 'nome_empresa' => (string) $this->nome_empresa, 'representante' => (string) $this->representante, 'informacao_contato' => (string) $this->informcao_contato]));
        }else{
            $retorno_operacao = (bool) model_check($this->tabela(), ['nome_empresa', '===', (string) $this->nome_empresa]);

            if($retorno_operacao == false){
                $retorno_operacao = (bool) model_insert($this->tabela(), (array) model_parse((array) $this->modelo(), (array) ['id_empresa' => (int) intval(model_next($this->tabela(), 'id_empresa'), 10), 'nome_empresa' => (string) $this->nome_empresa, 'representante' => (string) $this->representante, 'informacao_contato' => (string) $this->informcao_contato]));
            }else{
                return (bool) false;
            }
        }

        return (bool) $retorno_operacao;
    }

    public function pesquisar($filtro){
        return (array) model_one($this->tabela(), $filtro['filtro']);
    }

    public function pesquisar_todos($filtro){
        return (array) model_all($this->tabela(), $filtro['filtro'], $filtro['ordenacao'], $filtro['limite']);
    }

    /**
     * Função responsável por excluir uma empresa cadastrada no banco de dados.
     * @param array $dados array contendo o "codigo_empresa"
     * @return boolean TRUE ou FALSE de acordo com o retorno da função.
     */
    public function excluir($dados){
        $this->colocar_dados($dados);

        return (bool) model_delete($this->tabela(), ['id_empresa', '===', (int) $this->id_empresa]);
    }
}
?>