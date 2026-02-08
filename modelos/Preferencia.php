<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Classes/Sistema/db.php';
require_once 'Interface.php';

class Preferencia implements InterfaceModelo{
    private $usuario;
    private $sistema;
    private $nome_preferencia;
    private $preferencia;

    public function tabela(){
        return (string) 'preferencia_usuario';
    }

    public function modelo(){
        return (array) ['id_usuario' => convert_id(''), 'id_sistema' => convert_id(''), 'nome_preferencia' => (string) '', 'preferencia' => (string) ''];
    }

    public function colocar_dados($dados){
        if(array_key_exists('codigo_usuario', $dados) == true){
            if($dados['codigo_usuario'] != ''){
                $this->usuario = convert_id($dados['codigo_usuario']);
            }
        }

        if(array_key_exists('codigo_sistema', $dados) == true){
            if($dados['codigo_sistema'] != ''){
                $this->sistema = convert_id($dados['codigo_sistema']);
            }
        }

        if(array_key_exists('usuario', $dados) == true){
            if($dados['usuario'] != ''){
                $this->usuario = convert_id($dados['usuario']);
            }
        }

        if(array_key_exists('sistema', $dados) == true){
            if($dados['sistema'] != ''){
                $this->sistema = convert_id($dados['sistema']);
            }
        }

        if(array_key_exists('nome_preferencia', $dados) == true){
            $this->nome_preferencia = (string) strtoupper($dados['nome_preferencia']);
        }else{
            $this->nome_preferencia = (string) '';
        }

        if(array_key_exists('preferencia', $dados) == true){
            $this->preferencia = (string) strtoupper($dados['preferencia']);
        }else{
            $this->preferencia = (string) '';
        }
    }

    /**
     * Função responsável por verificar e salvar os dados das preferência no banco de dados
     * @param array $dados
     * @return bool
     */
    public function salvar_dados($dados){
        $this->colocar_dados($dados);

        $filtro_pesquisa = (array) ['and' => (array) [['sistema', '===', $this->sistema], ['usuario', '===', $this->usuario], ['nome_preferencia', '===', (string) $this->nome_preferencia]]];
        $filtro = (array) ['filtro' => (array) $filtro_pesquisa];

        $retorno_pesquisa = (array) $this->pesquisar((array) $filtro);

        if(empty($retorno_pesquisa) == true){
            return (bool) model_insert((string) $this->tabela(), (array) ['sistema' => $this->sistema, 'usuario' => $this->usuario, 'nome_preferencia' => (string) $this->nome_preferencia, 'preferencia' => (string) $this->preferencia]);
        }else{
            return (bool) model_update((string) $this->tabela(), (array) $filtro_pesquisa, (array) ['sistema' => $this->sistema, 'usuario' => $this->usuario, 'nome_preferencia' => (string) $this->nome_preferencia, 'preferencia' => (string) $this->preferencia]);
        }

    }

    /**
     * Função responsável por excluir da base de dados uma preferência que bate com os filtro passado através do array $dados
     * @param array $dados
     * @return bool
     */
    public function excluir(array $dados){
        $this->colocar_dados($dados);
        
        $filtro_pesquisa = (array) ['and' => (array) [['sistema', '===', $this->sistema], ['usuario', '===', $this->usuario], ['nome_preferencia', '===', (string) $this->nome_preferencia]]];

        return (bool) model_delete((string) $this->tabela(), (array) $filtro_pesquisa);
    }

    /**
     * Função responsável por retornar uma organização apenas, que bate com o filtro passado
     * @param array $dados
     * @return array
     */
    public function pesquisar($dados){
        return (array) model_one((string) $this->tabela(), $dados['filtro']);
    }

    /**
     * Função responsável por retornar todas as organizações encontradas na base de dados, que bate com o fltro passado
     * @param array $dados
     * @return void
     */
    public function pesquisar_todos($dados){

    }

    /**
     * Função responsável por alterar no banco de dados a quantidade de retorno que o usuário prefere visualizar dentro do sistema.
     * Esta função é executada apenas quando as informações são divergente.
     * @param int $limite_preferencia_usuario
     * @param int $limite_retorno
     * @param string $nome_preferencia
     * @param int $codigo_usuario
     * @param int $codigo_sistema
     * @return void
     */
    public function alterar_quantidade_retorno(int $limite_preferencia_usuario, int $limite_retorno, string $nome_preferencia, string $codigo_usuario, string $codigo_sistema){
        if($limite_preferencia_usuario != $limite_retorno){
            $retorno_preferencia = (bool) $this->excluir((array) ['nome_preferencia' => (string) $nome_preferencia, 'usuario' => convert_id($codigo_usuario), 'sistema' => convert_id($codigo_sistema)]);
            $retorno_preferencia = (bool) $this->salvar_dados((array) ['usuario' => (string) $codigo_usuario, 'sistema' => (string) $codigo_sistema, 'nome_preferencia' => (string) $nome_preferencia, 'preferencia' => (string) $limite_preferencia_usuario]);
        }
    }

    /**
     * Função repsonsável por montar o array de pesquisa de preferência, pesquisar e retornar o valor da preferência do usuário
     * @param array $dados
     * @return string
     */
    public function pesquisar_preferencia_usuario($dados){
        $this->colocar_dados($dados);

        $filtro_pesquisa = (array) ['and' => (array) [['sistema', '===', convert_id($this->sistema)], ['usuario', '===', convert_id($this->usuario)], ['nome_preferencia', '===', (string) $this->nome_preferencia]]];
        $retorno_pesquisa_preferencia = (array) $this->pesquisar((array) ['filtro' => (array) $filtro_pesquisa]);

        if(empty($retorno_pesquisa_preferencia) == true){
            return (string) '';
        }else{
            if(array_key_exists('preferencia', $retorno_pesquisa_preferencia) == true){
                return (string) $retorno_pesquisa_preferencia['preferencia'];
            }else{
                return (string) '';
            }
        }
    }
    
    /**
     * Função responsável por pesquisa a preferência do usuário para a quantidade de retorno que o sistema possui, por padrão a quantidaade de retorno do sistema é de 25 itens, mas o usuário pode alterar esta quantidade.
     * @param array $dados
     * @return int
     */
    public function pesquisar_preferencia_quantidade_retorno($dados){
        $this->colocar_dados($dados);

        $filtro_pesquisa = (array) ['and' => (array) [['sistema', '===', $this->sistema], ['usuario', '===', $this->usuario], ['nome_preferencia', '===', (string) $this->nome_preferencia]]];
        $retorno_pesquisa = (array) $this->pesquisar((array) ['filtro' => (array) $filtro_pesquisa]);

        if(empty($retorno_pesquisa) == true){
            $dados['preferencia'] = (string) '25';
            $retorno_salvar_dados = (bool) $this->salvar_dados($dados);
            
            return (int) 25;
        }else{
            if(array_key_exists('preferencia', $retorno_pesquisa) == true){
                return (int) intval($retorno_pesquisa['preferencia'], 10);
            }else{
                return (int) 25;
            }
        }
    }
}
?>