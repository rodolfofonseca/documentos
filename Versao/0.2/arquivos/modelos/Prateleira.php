<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Caixa.php';
require_once 'Modelos/Usuario.php';
require_once 'Modelos/LogSistema.php';
require_once 'Modelos/Notificacoes.php';

class Prateleira{
    private $id_prateleira;
    private $id_empresa;
    private $id_usuario;
    private $id_armario;
    private $nome_prateleira;
    private $descricao;
    private $codigo_barras;
    private $forma_visualizacao;

    private function tabela(){
        return (string) 'prateleira';
    }

    private function modelo(){
        return (array) ['id_prateleira' => (int) 0, 'id_empresa' => (int) 0, 'id_usuario' => (int) 0, 'id_armario' => (int) 0, 'nome_prateleira' => (string) '', 'descricao' => (string) '','codigo_barras' => (string) '', 'forma_visualizacao' => (string) 'PUBLICO'];
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_prateleira', $dados) == true){
            $this->id_prateleira = (int) intval($dados['codigo_prateleira'], 10);
        }

        if(array_key_exists('codigo_empresa', $dados) == true){
            $this->id_empresa = (int) intval($dados['codigo_empresa'], 10);
        }

        if(array_key_exists('codigo_usuario', $dados) == true){
            $this->id_usuario = (int) $dados['codigo_usuario'];
        }

        if(array_key_exists('codigo_armario', $dados) == true){
            $this->id_armario = (int) intval($dados['codigo_armario'], 10);
        }

        if(array_key_exists('nome_prateleira', $dados) == true){
            $this->nome_prateleira = (string) strtoupper($dados['nome_prateleira']);
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

    /**
     * Função responsável por verificar e executar a função de alterar ou cadastrar
     * @param array dados
     * @return bool opção
     */
    public function salvar_dados($dados){
        $this->colocar_dados($dados);

        $objeto_usuario = new Usuario();
        $objeto_log = new LogSistema();
        $objeto_notificacao = new Notificacoes();

        $filtro = (array) ['and' => (array) [(array) ['id_empresa', '===', (int) $this->id_empresa], (array) ['id_prateleira', '===', (int) $this->id_prateleira]]];
        
        $checar_existencia = (bool) model_check((string) $this->tabela(), (array) $filtro);

        if($checar_existencia == true){
            $retorno_pesquisa = (array) model_one((string) $this->tabela(), (array) $filtro);

            if(empty($retorno_pesquisa) == false){
                return (bool) model_update((string) $this->tabela(), (array) $filtro, (array) model_parse((array) $retorno_pesquisa, (array) ['id_armario' => (int) $this->id_armario, 'nome_prateleira' => (string) $this->nome_prateleira, 'descricao' => (string) $this->descricao, 'forma_visualizacao' => (string) $this->forma_visualizacao]));
            }else{
                return (bool) false;
            }

        }else{
            $retorno_cadastro = (bool) model_insert((string) $this->tabela(), (array) model_parse((array) $this->modelo(), (array) ['id_prateleira' => (int) model_next((string) $this->tabela(), (string)'id_prateleira', (array) ['id_empresa', '===', (int) $this->id_empresa]), 'id_empresa' => (int) $this->id_empresa, 'id_usuario' => (int) $this->id_usuario, 'id_armario' => (int) $this->id_armario, 'nome_prateleira' => (string) $this->nome_prateleira, 'descricao' => (string) $this->descricao, 'codigo_barras' => (string) $this->codigo_barras, 'forma_visualizacao' => (string) $this->forma_visualizacao]));

            if($retorno_cadastro == true){
                $retorno_usuario = (array) $objeto_usuario->pesquisar((array) ['filtro' => (array) ['id_usuario', '===', (int) intval($this->id_usuario, 10)]]);

                if(empty($retorno_usuario) == false){
                    $retorno_cadastro_log = (bool) $objeto_log->salvar_dados((array) ['id_empresa' => (int) $this->id_empresa, 'usuario' => (string) $retorno_usuario['login'], 'codigo_barras' => (string) $this->codigo_barras, 'modulo' => (string) 'PRATELEIRA', 'descricao' => (string) 'A prateleira '.$this->nome_prateleira.' foi cadastrado no sistema']);
                }

                $objeto_notificacao->salvar_dados((array) ['id_usuario' => (int) $this->id_usuario, 'titulo_notificacao' => (string) 'Nova prateleira cadastrada', 'mensagem_curta' => (string) 'Uma prateleira foi cadastrada no sistema!', 'mensagem_longa' => (string) 'A prateleira '.$this->nome_prateleira.' foi cadastrado no sistema, sendo possível agora realizar o cadastro de novas caixas!']);
            }

            return (bool) $retorno_cadastro;
        }
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

    /**
     * Função responsável por excluir a prateleira do sistema
     * @param mixed $dados contendo as informações da prateleira para montar o filtro de pesquisa
     * @return array contendo a mensagem para apresentação ao usuário
     */
    public function excluir($dados){
        $objeto_usuario = new Usuario();
        $objeto_log = new LogSistema();
        $objeto_caixa = new Caixa();

        $this->colocar_dados((array) $dados);
        
        $filtro_pesquisa_caixa = (array) ['filtro' => (array) ['id_prateleira', '===', (int) $this->id_prateleira]];
        $retorno_pesquisa_caixa = (array) $objeto_caixa->pesquisar((array) $filtro_pesquisa_caixa);

        $retorno_prateleira = (array) $this->pesquisar($filtro_pesquisa_caixa);

        if(empty($retorno_pesquisa_caixa) == false){
            return (array) ['titulo' => (string) 'PRATELEIRA CONTÉM CAIXAS', 'mensagem' => (string) 'Não é possível excluir uma prateleira que contém caixas!', 'icone' => (string) 'error'];
        }else{
            $retorno_exclusao = (bool) model_delete((string) $this->tabela(), (array)  ['id_prateleira', '===', (int) $this->id_prateleira]);

            if(empty($retorno_prateleira) == false){
                $retorno_usuario = (array) $objeto_usuario->pesquisar((array) ['filtro' => (array) ['id_usuario', '===', (int) intval($retorno_prateleira['id_usuario'], 10)]]);

                if(empty($retorno_usuario) == false){
                    $retorno_cadastro_log = (bool) $objeto_log->salvar_dados((array) ['id_empresa' => (int) $retorno_prateleira['id_empresa'], 'usuario' => (string) $retorno_usuario['login'], 'codigo_barras' => (string) $retorno_prateleira['codigo_barras'], 'modulo' => (string) 'PRATELEIRA', 'descricao' => (string) 'Excluiu a prateleira '.$retorno_prateleira['nome_prateleira']]);
                }
            }

            if($retorno_exclusao == true){
                return (array) ['titulo' => (string) 'EXCLUSÃO CONCLUÍDA', 'mensagem' => (string) 'Operação realizada com sucesso!', 'icone' => (string) 'success'];
            }else{
                return (array) ['titulo' => (string) 'PROBLEMAS NA EXCLUSÃO', 'mensagem' => (string) 'Não foi possível excluir a prateleira, aconteceu algum erro desconhecido por favor tente mais tarde!', 'icone' => (string) 'error'];
            }
        }
    }
}
?>