<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Interface.php';
require_once 'Usuario.php';

class Notificacoes implements InterfaceModelo{
    private $id_notificacao;
    private $id_usuario;
    private $data_feather;
    private $titulo_notificacao;
    private $mensagem_curta;
    private $mensagem_longa;
    private $data_notificacao;
    private $data_leitura;
    private $status_leitura;

    public function tabela(){
        return (string) 'notificacoes';
    }

    public function modelo(){
        return (array) ['id_notificacao' => (int) 0, 'id_usuario' => (int) 0, 'data_feather' => (string) '', 'titulo_notificacao' => (string) '', 'mensagem_curta' => (string) '', 'mensagem_longa' => (string) '', 'data_notificacao' => 'date', 'data_leitura' => 'date', 'status_leitura' => (string) 'NAO_LIDO'];
    }

    public function colocar_dados($dados){
        if(array_key_exists('id_notificacao', $dados) == true){
            $this->id_notificacao = (int) intval($dados['id_notificacao'], 10);
        }else{
            $this->id_notificacao = (int) intval(0, 10);
        }

        if(array_key_exists('id_usuario', $dados) == true){
            $this->id_usuario = (int) intval($dados['id_usuario'], 10);
        }else{
            $this->id_usuario = (int)intval(0, 10);
        }

        if(array_key_exists('data_feather', $dados) == true){
            $this->data_feather = (string) $dados['data_feather'];
        }else{
            $this->data_feather = (string) 'activity';
        }

        if(array_key_exists('titulo_notificacao', $dados) == true){
            $this->titulo_notificacao = (string) $dados['titulo_notificacao'];
        }else{
            $this->titulo_notificacao = (string) '';
        }

        if(array_key_exists('mensagem_curta', $dados) == true){
            $this->mensagem_curta = (string) $dados['mensagem_curta'];
        }else{
            $this->mensagem_curta = (string) '';
        }

        if(array_key_exists('mensagem_longa', $dados) == true){
            $this->mensagem_longa = (string) $dados['mensagem_longa'];
        }else{
            $this->mensagem_longa = (string) '';
        }

        if(array_key_exists('status_leitura', $dados) == true){
            $this->status_leitura = (string) $dados['status_leitura'];
        }else{
            $this->status_leitura = (string) 'NAO_LIDO';
        }
    }

    public function salvar_dados($dados){
        $this->colocar_dados((array) $dados);

        if($this->id_notificacao != 0){
            $retorno_pesquisa = (array) model_one((string) $this->tabela(), (array) ['id_notificacao', '===', (int) intval($this->id_notificacao, 10)]);

            if(empty($retorno_pesquisa) == true){
                return (array) ['titulo' => (string) 'ERRO NA OPERAÇÃO', 'mensagem' => (string) 'Erro durante o processo de alteração da notificação', 'icone' => (string) 'ERROR'];
            }else{
                $retorno_alteracao = (bool) model_update((string) $this->tabela(), (array) ['id_notificacao', '===', intval($this->id_notificacao, 10)], (array) model_parse((array) $this->modelo(), (array) ['id_notificacao' => (int) intval($this->id_notificacao, 10), 'id_usuario' => (int) intval($retorno_pesquisa['id_usuario'], 10), 'data_feather' => (string) $retorno_pesquisa['data_feather'], 'titulo_notificacao' => (string) $retorno_pesquisa['titulo_notificacao'], 'mensagem_curta' => (string) $retorno_pesquisa['mensagem_curta'], 'mensagem_longa' => (string) $retorno_pesquisa['mensagem_longa'], 'data_notificacao' => model_date($retorno_pesquisa['data_notificacao']), 'data_leitura' => model_date(), 'status_leitura' => (string) $this->status_leitura]));

                if($retorno_alteracao == true){
                    return (array) ['status' => (bool) true, 'titulo' => (string) 'SUCESSO NA ALTERAÇÃO', 'mensagem' => (string) 'Alteração da mensagem realizada com sucesso!', 'icone' => (string) 'success'];
                }else{
                    return (array) ['status' => (bool) false, 'titulo' => (string) 'ERRO DURANTE ALTERAÇÃO', 'mensagem' => (string) 'Erro durante o prcesso de alteração da mensagem', 'icone' => (string) 'error'];
                }
            }
        }else{
            $usuario = new Usuario();
            $retorno_insercao = (bool) false;

            $retorno_usuario = (array) $usuario->pesquisar_todos((array) ['filtro' => (array) [], 'ordenacao' => (array) ['id_usuario' => (bool) false], 'limite' => (int) 0]);

            if(empty($retorno_usuario) == false){
                foreach($retorno_usuario as $usuario){
                    $this->id_usuario = (int) intval($usuario['id_usuario'], 10);

                    $retorno_insercao = (bool) model_insert((string) $this->tabela(), (array) model_parse((array) $this->modelo(), (array) ['id_notificacao' => (int) model_next((string) $this->tabela(), 'id_notificacao'), 'id_usuario' => (int) intval($this->id_usuario, 10), 'data_feather' => (string) $this->data_feather, 'titulo_notificacao' => (string) $this->titulo_notificacao, 'mensagem_curta' => (string) $this->mensagem_curta, 'mensagem_longa' => (string) $this->mensagem_longa, 'data_notificacao' => model_date(), 'data_leitura' => model_date(), 'status_leitura' => (string) $this->status_leitura]));        
                }
            }
            
            if($retorno_insercao == true){
                return (array) ['status' => (bool) true, 'titulo' => (string) 'SUCESSO NA OPERAÇÃO', 'mensagem' => (string) 'Sucesso durante o processo de cadastro de notificação', 'icone' => (string) 'success'];
            }else{
                return (array) ['status' => (bool) false, 'titulo' => (string) 'ERRO NA OPERAÇÃO', 'mensagem' => (string) 'Erro durante o processo de cadastro de notificacao', 'icone' => (string) 'error'];
            }
        }
    }

    public function pesquisar($dados){
        return (array) model_one((string) $this->tabela(), (array) $dados['filtro']);
    }

    public function pesquisar_todos($dados){
        return (array) model_all((string) $this->tabela(), (array) $dados['filtro'], (array) $dados['ordenacao'], (int) intval($dados['limite'], 10));
    }

    public function deletar($dados){
        $this->colocar_dados((array) $dados);

        if($this->id_notificacao != 0){
            $retorno_operacao = (bool) model_delete((string) $this->tabela(), (array) ['id_notificacao', '===', (int) intval($this->id_notificacao, 10)]);

            if($retorno_operacao == true){
                return (array) ['titulo' => (string) 'SUCESSO NA OPERAÇÃO', 'mensagem' => (string) 'Sucesso durante o processo de excluir a notificação!', 'icone' => (string) 'success'];
            }else{
                return (array) ['titulo' => (string) 'ERRO DURANTE OPERAÇÃO', 'mensagem' => (string) 'Erro durante o processo de excluir a notificação!', 'icone' => (string) 'error'];
            }
        }else{
            return (array) ['titulo' => (string) 'ERRO DURANTE OPERAÇÃO', 'mensagem' => (string) 'É necessário selecionar uma notificação para excluir!', 'icone' => (string) 'error'];
        }
    }

    /**
     * Responsável por contar e retornar a quantidade de registros de notificacoes que o usuário do sistema possuir
     * @param array dados
     * @return int
     */
    public function contar_notificacoes($dados){
        $this->colocar_dados((array) $dados);
        $quantidade_notificacao = 0;

        $retorno = pesquisa_banco_aggregate((string) $this->tabela(), [['$match' => ['id_usuario' => $this->id_usuario, 'status_leitura' => 'NAO_LIDO']], ['$group' => ['_id' => [], 'COUNT(*)' => ['$sum' => 1]]], ['$project' => ['COUNT(*)' => '$COUNT(*)', '_id' => 0]]]);

        foreach($retorno as $document){
            $quantidade_notificacao = (int) intval($document['COUNT(*)'], 10);
        }

        return intval($quantidade_notificacao, 10);
    }
}
?>