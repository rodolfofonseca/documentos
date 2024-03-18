<?php
require_once 'Classes/bancoDeDados.php';

class Prateleira{
    private $id_prateleira;
    private $id_armario;
    private $nome_prateleira;
    private $codigo_barras;

    private function tabela(){
        return (string) 'prateleira';
    }

    private function modelo(){
        return (array) ['id_prateleira' => (int) 0, 'id_armario' => (int) 0, 'nome_prateleira' => (string) '', 'codigo_barras' => (string) ''];
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_prateleira', $dados) == true){
            $this->id_prateleira = (int) intval($dados['codigo_prateleira'], 10);
        }

        if(array_key_exists('codigo_armario', $dados) == true){
            $this->id_armario = (int) intval($dados['codigo_armario'], 10);
        }

        if(array_key_exists('nome_prateleira', $dados) == true){
            $this->nome_prateleira = (string) strtoupper($dados['nome_prateleira']);
        }

        if(array_key_exists('codigo_barras', $dados) == true){
            $this->codigo_barras = (string) $dados['codigo_barras'];
        }
    }

    /**
     * Função responsável por verificar e executar a função de alterar ou cadastrar
     * @param array dados
     * @return bool opção
     */
    public function salvar_dados($dados){
        $this->colocar_dados($dados);
        $retorno = (bool) false;
        $checar_existencia = (bool) model_check($this->tabela(), ['id_prateleira', '===', (int) $this->id_prateleira]);

        if($checar_existencia == true){
            $retorno = (bool) model_update($this->tabela(), ['id_prateleira', '===', (int) $this->id_prateleira], model_parse($this->modelo(), ['id_prateleira' => (int) $this->id_prateleira, 'id_armario' => (int) $this->id_armario, 'nome_prateleira' => (string) $this->nome_prateleira, 'codigo_barras' => (string) $this->codigo_barras]));
        }else{
            $retorno = (bool) model_insert($this->tabela(), model_parse($this->modelo(), ['id_prateleira' => (int) model_next($this->tabela(), 'id_prateleira'), 'id_armario' => (int) $this->id_armario, 'nome_prateleira' => (string) $this->nome_prateleira, 'codigo_barras' => (string) $this->codigo_barras]));
        }

        return (bool) $retorno;
    }

    /**
     * Função responsável por retornar as informações de apenas uma prateleira, de acordo com os parâmetros de filtros passados
     * @param array informações para pesquisadas ['filtro'];
     * @return array com as informações pesquisadas
     */
    public function pesquisar($dados){
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    /**
     * Função responsável por pesquisar todas as informações das prateleiras cadastradas na base de dados de acordo com os parâmetros passados
     * @param array ['filtro', 'ordenacao', 'limite']
     * @param array com o retorno
     */
    public function pesquisar_todos($dados){
        return (array) model_all($this->tabela(), $dados['filtro'], $dados['ordenacao'], $dados['limite']);
    }
}
?>