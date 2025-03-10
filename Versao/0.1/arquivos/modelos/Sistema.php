<?php
require_once 'Classes/bancoDeDados.php';

Class Sistema{
    private $id_sistema;
    private $id_empresa;
    private $versao_sistema;
    private $chave_api;
    private $cidade;
    private $tamanho_arquivo;

    private function tabela(){
        return (string) 'sistema';
    }

    private function modelo(){
        return (array) ['id_sistema' => (int) 0, 'id_empresa' => (int) 0,'versao_sistema' => (string) '0.0', 'chave_api' => (string) '', 'cidade' => (string) '', 'tamanho_arquivo' => (int) 0];
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_sistema', $dados) == true){
            $this->id_sistema = (int) intval($dados['codigo_sistema'], 10);
        }

        if(array_key_exists('codigo_empresa', $dados) == true){
            $this->id_empresa = (int) intval($dados['codigo_empresa'], 10);
        }

        if(array_key_exists('versao_sistema', $dados) == true){
            $this->versao_sistema = (string) $dados['versao_sistema'];
        }

        if(array_key_exists('chave_api', $dados) == true){
            $this->chave_api = (string) $dados['chave_api'];
        }

        if(array_key_exists('cidade', $dados) == true){
            $this->cidade = (string) $dados['cidade'];
        }

        if(array_key_exists('tamanho_arquivo', $dados) == true){
            $this->tamanho_arquivo = (int) intval($dados['tamanho_arquivo'], 10);
        }
    }

    public function salvar_dados($dados){
        $this->colocar_dados($dados);
        $retorno = (bool) false;
        $checar_existencia = (bool) model_check($this->tabela(), ['id_sistema', '===', (int) $this->id_sistema]);

        if($checar_existencia == true){
            $retorno_pesquisa = (array) model_one($this->tabela(), ['id_sistema', '===', (int) $this->id_sistema]);
            
            $retorno_pesquisa['versao_sistema'] = (string) $this->versao_sistema;
            $retorno_pesquisa['chave_api'] = (string) $this->chave_api;
            $retorno_pesquisa['cidade'] = (string) $this->cidade;

            $retorno = (bool) model_update((string) $this->tabela(), (array) ['id_sistema', '===', (int) $this->id_sistema], (array) model_parse((array) $this->modelo(), (array) $retorno_pesquisa));
        }else{
            $retorno = (bool) model_insert($this->tabela(), model_parse($this->modelo(), ['id_sistema' => (int) model_next($this->tabela(), 'id_empresa'), 'id_empresa' => (int) $this->id_empresa, 'versao_sistema' => (string) '1.0']));
        }

        return (bool) $retorno;
    }

    public function pesquisar($dados){
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    public function pesquisar_todos($dados){
        return (array) model_all($this->tabela(), $dados['filtro'], $dados['ordenacao'], $dados['limite']);
    }

    /**
     * Método responsável por cadastrar e alterar o tamanho de arquivo que o sistema aceita.
     * @param array $dados
     * @return array
     */
    public function salvar_tamanho_arquivo($dados){
        $this->colocar_dados($dados);

        $retorno = (bool) model_update($this->tabela(), (array) ['and' => (array) [(array) ['id_sistema', '===', (int) $this->id_sistema], ['id_empresa', '===', (int) $this->id_empresa]]], (array) ['tamanho_arquivo' => (int) $this->tamanho_arquivo]);

        if($retorno == true){
            return (array) ['retorno' => (bool) true, 'titulo' => (string) 'SUCESSO NA OPERAÇÃO', 'mensagem' => (string) 'Operação realizada com sucesso!', 'icone' => (string) 'success'];
        }else{
            return (array) ['retorno' => (bool) false, 'titulo' => (string) 'FALHA NA OPERAÇÃO', 'mensagem' => (string) 'Erro durante a operação de cadastrar o tamanho do arquivo!', 'icone' => (string) 'error'];
        }
    }
}
?>