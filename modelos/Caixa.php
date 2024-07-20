<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Prateleira.php';

class Caixa{
    private $id_caixa;
    private $id_empresa;
    private $id_usuario;
    private $id_prateleira;
    private $nome_caixa;
    private $descricao;
    private $codigo_barras;
    private $forma_visualizacao;

    private function tabela(){
        return (string) 'caixa';
    }

    private function modelo(){
        return (array) ['id_caixa' => (int) 0, 'id_empresa' => (int) 0, 'id_usuario' => (int) 0, 'id_prateleira' => (int) 0, 'nome_caixa' => (string) '', 'descricao' => (string) '', 'codigo_barras' => (string) '', 'forma_visualizacao' => (string) 'PUBLICO'];
    }

    /**
     * Função responsável por retornar o modelo que o front espera para os filtros de pesquisa
     * @return array $modelo
     */
    private function modelo_validacao(){
        return (array) ['id_caixa' => (int) 0, 'id_empresa' => (int) 0, 'id_usuario' => (int) 0, 'id_prateleira' => (int) 0, 'nome_caixa' => (string) '', 'nome_prateleira' => (string) '', 'descricao' => (string) '', 'codigo_barras' => (string) '', 'forma_visualizacao' => (string) 'PUBLICO'];
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_caixa', $dados) == true){
            $this->id_caixa = (int) intval($dados['codigo_caixa'], 10);
        }

        if(array_key_exists('codigo_empresa', $dados) == true){
            $this->id_empresa = (int) intval($dados['codigo_empresa'], 10);
        }

        if(array_key_exists('codigo_usuario', $dados) == true){
            $this->id_usuario = (int) $dados['codigo_usuario'];
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

        if(array_key_exists('forma_visualizacao', $dados) == true){
            $this->forma_visualizacao = (string) $dados['forma_visualizacao'];
        }
    }

    public function salvar($dados){
        $this->colocar_dados($dados);

        $filtro = (array) ['and' => (array) [(array) ['id_caixa', '===', (int) $this->id_caixa], (array) ['id_empresa', '===', (int) $this->id_empresa]]];
        $checar_existencia = (bool) model_check($this->tabela(), (array) $filtro);

        if($checar_existencia == true){
            $retorno_pesquisa_caixa = (array) model_one((string)$this->tabela(), (array) $filtro);


            return (bool) model_update((string) $this->tabela(), (array) $filtro, (array) model_parse((array) $retorno_pesquisa_caixa, (array) ['id_empresa' => (int) $this->id_empresa, 'id_prateleira' => (int) $this->id_prateleira, 'nome_caixa' => (string) $this->nome_caixa, 'descricao' => (string) $this->descricao, 'forma_visualizacao' => (string) $this->forma_visualizacao]));
        }else{
            return (bool) model_insert((string) $this->tabela(), (array) model_parse((array) $this->modelo(), (array) ['id_caixa' => (int) intval(model_next((string) $this->tabela(), 'id_caixa', (array) ['id_empresa', '===', (int) $this->id_empresa]), 10), 'id_empresa' => (int) $this->id_empresa, 'id_usuario' => (int) $this->id_usuario, 'id_prateleira' => (int) $this->id_prateleira, 'nome_caixa' => (string) $this->nome_caixa, 'descricao' => (string) $this->descricao, 'codigo_barras' => (string) $this->codigo_barras, 'forma_visualizacao' => (string) $this->forma_visualizacao]));
        }        
    }

    public function pesquisar($dados){
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    public function pesquisar_todos($dados){
        return (array) model_all($this->tabela(), $dados['filtro'], $dados['ordenacao'], $dados['limite']);
    }

    /**
     * Função responsável por montar o array de retorno da forma como o front do sistema espera.
     * @param array $dados filtro montado de pesquisa
     * @param int $id_empresa identificador da empresa.
     * @param array $retorno variável utilizada para armazenar as informações para que possa ser retornado.
     * @return arrat $retorno com as informações formatadas.
     */
    public function validar_dados_pesquisa_caixa($dados, $id_empresa, $retorno){
        $retorno_pesquisa = (array) $this->pesquisar_todos($dados);

        if(empty($retorno_pesquisa) == false){
            foreach($retorno_pesquisa as $caixa){
                $retorno_temporario = (array) model_parse($this->modelo_validacao(), $caixa);

                $objeto_prateleira = new Prateleira();
                $dados_prateleira = (array) $objeto_prateleira->pesquisar((array) ['filtro' => (array) ['and' => (array) [(array) ['id_empresa', '===', (int) $id_empresa], (array) ['id_prateleira', '===', (int) $caixa['id_prateleira']]]]]);

                if(empty($dados_prateleira) == false){
                    if(array_key_exists('nome_prateleira', $dados_prateleira) == true){
                        $retorno_temporario['nome_prateleira'] = (string) $dados_prateleira['nome_prateleira'];
                    }
                }
                array_push($retorno, $retorno_temporario);
            }
        }

        return (array) $retorno;
    }

    /**
     * Função responsável por verificar qual a forma de visualização atual da caixa e realizar a alteração de acordo com esta forma
     * @param array $dados contendo todas as informações necessária para a relização da alteração
     * @return boo contando true ou false de acordo com o resultado da função.
     */
    public function alterar_forma_visualizacao($dados){
        $this->colocar_dados($dados);

        $filtro = (array) ['and' => (array) [(array) ['id_empresa', '===', (int) $this->id_empresa], (array) ['id_caixa', '===', (int) $this->id_caixa]]];

        if($this->forma_visualizacao == 'PUBLICO'){
            return (bool) model_update((string) $this->tabela(), (array) $filtro, (array) ['forma_visualizacao' => (string) 'PRIVADO']);
        }else{
            return (bool) model_update((string) $this->tabela(), (array) $filtro, (array) ['forma_visualizacao' => (string) 'PUBLICO']);
        }
    }
}
?>