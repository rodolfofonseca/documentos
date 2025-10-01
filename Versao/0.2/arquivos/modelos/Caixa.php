<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Prateleira.php';
require_once 'Modelos/Documentos.php';
require_once 'Modelos/LogSistema.php';
require_once 'Modelos/Usuario.php';
require_once 'Modelos/Notificacoes.php';

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
        $objeto_log = new LogSistema();
        $objeto_usuario = new Usuario();
        $objeto_notificacao = new Notificacoes();

        $retorno_operacao = (bool) false;
        $descricao_log = (string) '';

        $this->colocar_dados($dados);

        $filtro = (array) ['and' => (array) [(array) ['id_caixa', '===', (int) $this->id_caixa], (array) ['id_empresa', '===', (int) $this->id_empresa]]];
        $checar_existencia = (bool) model_check($this->tabela(), (array) $filtro);

        if($checar_existencia == true){
            $retorno_pesquisa_caixa = (array) model_one((string)$this->tabela(), (array) $filtro);

            $retorno_operacao =  (bool) model_update((string) $this->tabela(), (array) $filtro, (array) model_parse((array) $retorno_pesquisa_caixa, (array) ['id_empresa' => (int) $this->id_empresa, 'id_prateleira' => (int) $this->id_prateleira, 'nome_caixa' => (string) $this->nome_caixa, 'descricao' => (string) $this->descricao, 'forma_visualizacao' => (string) $this->forma_visualizacao]));

            $descricao_log = (string) 'Alterou a caixa '.$this->nome_caixa;
        }else{
            $retorno_operacao = (bool) model_insert((string) $this->tabela(), (array) model_parse((array) $this->modelo(), (array) ['id_caixa' => (int) intval(model_next((string) $this->tabela(), 'id_caixa', (array) ['id_empresa', '===', (int) $this->id_empresa]), 10), 'id_empresa' => (int) $this->id_empresa, 'id_usuario' => (int) $this->id_usuario, 'id_prateleira' => (int) $this->id_prateleira, 'nome_caixa' => (string) $this->nome_caixa, 'descricao' => (string) $this->descricao, 'codigo_barras' => (string) $this->codigo_barras, 'forma_visualizacao' => (string) $this->forma_visualizacao]));

            $descricao_log = (string) 'Cadastrou a caixa '.$this->nome_caixa;
        }
        
        if($retorno_operacao == true){
            $retorno_usuario = (array) $objeto_usuario->pesquisar((array) ['filtro' => (array) ['id_usuario', '===', (int) intval($this->id_usuario, 10)]]);

            if(empty($retorno_usuario) == false){
                $retorno_log = (bool) $objeto_log->salvar_dados((array) ['id_empresa' => (int) intval($this->id_empresa, 10), 'usuario' => (string) $retorno_usuario['login'], 'codigo_barras' => (string) $this->codigo_barras, 'modulo' => (string) 'CAIXA', 'descricao' => (string) $descricao_log]);
            }

            if($this->forma_visualizacao == 'PUBLICO'){
                $retorno_objeto_notificacao = (array) $objeto_notificacao->salvar_dados((array) ['titulo_notificacao' => (string) 'Cadastro de Nova Caixa', 'mensagem_longa' => (string) 'A caixa '.$this->nome_caixa.' foi cadastrada no sistema', 'mensagem_curta' => (string) 'Cadastro de nova caixa!']);
            }

            return (array) ['titulo' => (string) 'SUCESSO NA OPERACAÇÃO!', 'mensagem' => (string) 'Operação realizada com sucesso!', 'icone' => (string) 'success'];
        }else{
            return (array) ['titulo' => (string) 'ERRO DURANTE O PROCESSO!', 'mensagem' => (string) 'Erro durante o processo!', 'icone' => (string) 'error'];
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
     * @return array $retorno com as informações formatadas.
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
     * @return bool contando true ou false de acordo com o resultado da função.
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

    /**
     * Função responsável por realizar a pesquisa se é possível realizar a exclusão da caixa e caso seja possível exclui a mesma do banco de dados.
     * @param mixed $dados
     * @return array
     */
    public function excluir($dados){
        $objeto_log = new LogSistema();
        $objeto_usuario = new Usuario();
        $objeto_documento = new Documentos();

        $this->colocar_dados((array) $dados);

        $array_filtro = (array) ['id_caixa', '===', (int) $this->id_caixa];

        $filtro_pesquisa_documento = (array) ['filtro' => (array) $array_filtro];
        $retorno_pesquisa = (array) $objeto_documento->pesquisar_documento($filtro_pesquisa_documento);

        if(empty($retorno_pesquisa) == false){
            return (array) mensagem_retorno('NÃO É POSSÍVEL EXCLUIR', 'Não é possível excluir uma caixa que contenha documentos cadastrados', 'error');
        }else{
            $retorno_caixa = (array) $this->pesquisar((array) ['filtro' => (array) ['id_caixa', '===', (int) intval($this->id_caixa, 10)]]);

            if(empty($retorno_caixa) == false){
                $retorno_usuario = (array) $objeto_usuario->pesquisar((array) ['filtro' => (array) ['id_usuario', '===', (int) intval($retorno_caixa['id_usuario'], 10)]]);

                if(empty($retorno_usuario) == false){
                    $retorno_log = (bool) $objeto_log->salvar_dados((array) ['id_empresa' => (int) intval($retorno_caixa['id_empresa'], 10), 'usuario' => (string) $retorno_usuario['login'], 'codigo_barras' => (string) $retorno_caixa['codigo_barras'], 'modulo' => (string) 'CAIXA', 'descricao' => (string) 'Excluiu a caixa '.$retorno_caixa['nome_caixa']]);
                }
            }

            $retorno_operacao = (bool) model_delete($this->tabela(), $array_filtro);

            if($retorno_operacao == true){
                return (array) mensagem_retorno('SUCESSO NA OPERAÇÃO', 'Operação realizada com sucesso!', 'success');
            }else{
                return (array) mensagem_retorno('ERRO DESCONHECIDO', 'Aconteceu algum erro desconhecido \n no processo de exclusão!\n por favor tente mais tarde', 'error');
            }
        }
    }
}
?>