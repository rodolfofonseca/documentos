<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Template.php';

class MensagemTicketSuporte implements Template{
    private $id_mensagem_ticket;
    private $id_ticket_suporte;
    private $id_usuario;
    private $login_usuario;
    private $mensagem;
    private $data_mensagem;
    private $codigo_barras;

    public function tabela(){
        return (string) 'mensagem_ticket_suporte';
    }

    public function modelo(){
        return (array) ['id_mensagem_ticket' => (int) 0, 'id_ticket_suporte' => (int) 0, 'id_usuario' => (int) 0, 'login_usuario' => (string) '', 'mensagem' => (string) '', 'data_mensagem' => 'date', 'codigo_barras' => (string) ''];
    }

    public function colocar_dados($dados){
        if(array_key_exists('codigo_mensagem_ticket', $dados) == true){
            $this->id_mensagem_ticket = (int) intval($dados['codigo_mensagem_ticket'], 10);
        }else{
            $this->id_mensagem_ticket = (int) 0;
        }

        if(array_key_exists('codigo_ticket_suporte', $dados) == true){
            $this->id_ticket_suporte = (int) intval($dados['codigo_ticket_suporte'], 10);
        }else{
            $this->id_ticket_suporte = (int) 0;
        }

        if(array_key_exists('codigo_usuario', $dados) == true){
            $this->id_usuario = (int) intval($dados['codigo_usuario'], 10);
        }else{
            $this->id_usuario = (int) 0;
        }

        if(array_key_exists('login_usuario', $dados) == true){
            $this->login_usuario = (string) strtoupper($dados['login_usuario']);
        }else{
            $this->login_usuario = (string) '';
        }

        if(array_key_exists('mensagem', $dados) == true){
            $this->mensagem = (string) $dados['mensagem'];
        }else{
            $this->mensagem = (string)'';
        }

        if(array_key_exists('data_log', $dados) == true){
            $this->data_mensagem = model_date($dados['data_log']);
        }else{
            $this->data_mensagem = model_date();
        }

        if(array_key_exists('codigo_barras', $dados) == true){
            $this->codigo_barras = (string) $dados['codigo_barras'];
        }else{
            $this->codigo_barras = (string) codigo_barras();
        }
    }

    public function salvar_dados($dados){
        $this->colocar_dados((array) $dados);

        $retorno_operacao = (bool) ravf_corp_model_insert((string) $this->tabela(), (array) model_parse($this->modelo(), (array) ['id_mensagem_ticket' => (int) intval(ravf_corp_model_next((string) $this->tabela(), 'id_mensagem_ticket'), 10), 'id_ticket_suporte' => (int) $this->id_ticket_suporte, 'id_usuario' => (int) $this->id_usuario, 'login_usuario' => (string) $this->login_usuario, 'mensagem' => (string) $this->mensagem, 'data_mensagem' => model_date(), 'codigo_barras' => (string) $this->codigo_barras]));

        return (array) ['status' => (bool) $retorno_operacao];
    }

    public function pesquisar($dados){
        return (array) ravf_corp_model_one((string) $this->tabela(), (array) $dados['filtro']);
    }

    public function pesquisar_todos($dados){
        return (array) ravf_corp_model_all((string) $this->tabela(), (array) $dados['filtro'], (array) $dados['ordenacao'], (int) $dados['limite']);
    }

    public function deletar($dados){
        $retorno = (bool) ravf_corp_model_delete((string) $this->tabela(), (array) $dados['filtro']);

        if($retorno == true){
            return (array) ['titulo' => (string) 'EXCLUSÃO COM SUCESSO', 'mensagem' => (string) 'Mensagem excluída com sucesso!', 'icone' => (string) 'success'];
        }else{
            return (array) ['titulo' => (string) 'ERRO NA EXCLUSÃO', 'mensagem' => (string) 'Erro durante o processo de exclusão da mensagem', 'icone' => (string) 'error'];
        }
    }
}
?>