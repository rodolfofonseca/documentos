<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Template.php';

class TicketSuporte implements Template{
    private $id_ticket_suporte;
    private $id_empresa;
    private $id_usuario;
    private $login_usuario;
    private $programador_responsavel;
    private $data_abertura;
    private $data_fechamento;
    private $codigo_barras;
    private $status;

    public function tabela(){
        return (string) 'ticket_suporte';
    }

    public function modelo(){
        return (array) ['id_ticket_suporte' => (int) 0, 'id_empresa' => (int) 0, 'id_usuario' => (int) 0, 'login_usuario' => (string) '', 'programador_responsavel' => (string) '', 'data_abertura' => 'date', 'data_fechamento' => 'date', 'codigo_barras' => (string) codigo_barras(), 'status' => (string) 'AGUARDANDO'];
    } 

    public function colocar_dados($dados){
        if(array_key_exists('codigo_ticket_suporte', $dados) == true){
            $this->id_ticket_suporte = (int) intval($dados['codigo_ticket_suporte'], 10);
        }else{
            $this->id_ticket_suporte = (int) 0;
        }

        if(array_key_exists('codigo_empresa', $dados) == true){
            $this->id_empresa = (int) intval($dados['codigo_empresa'], 10);
        }else{
            $this->id_empresa = (int) 0;
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

        if(array_key_exists('programador_responsavel', $dados) == true){
            $this->programador_responsavel = (string) strtoupper($dados['programador_responsavel']);
        }else{
            $this->programador_responsavel = (string) '';
        }

        if(array_key_exists('data_abertura', $dados) == true){
            // file_put_contents('teste.json', $dados['data_abertura']);
            $this->data_abertura = model_date($dados['data_abertura'], null, true);
        }else{
            $this->data_abertura = model_date();
        }

        if(array_key_exists('data_fechamento', $dados) == true){

            if($dados['data_fechamento'] == '' || $dados['data_fechamento'] == null){

            }else{
                $this->data_fechamento = model_date($dados['data_fechamento'], null, true);
            }
        }else{
            $this->data_fechamento = model_date();
        }

        if(array_key_exists('codigo_barras', $dados) == true){
            if($dados['codigo_barras'] == ''){
                $this->codigo_barras = (string) codigo_barras();
            }else{
                $this->codigo_barras = (string) $dados['codigo_barras'];
            }
        }else{
            $this->codigo_barras = (string) codigo_barras();
        }

        if(array_key_exists('status', $dados) == true){
            $this->status = (string) $dados['status'];
        }else{
            $this->status = (string) 'AGUARDANDO';
        }
    }

    public function salvar_dados($dados){
        $this->colocar_dados($dados);

        $filtro_pesquisa = (array) [];
        $retorno_pesquisa_ticket = (array) [];

        if($this->id_ticket_suporte != 0){
            $filtro_pesquisa = (array) ['id_ticket_suporte', '===', (int) $this->id_ticket_suporte];
        }

        if($filtro_pesquisa != []){
            $retorno_pesquisa_ticket = (array) ravf_corp_model_one((string) $this->tabela(), (array) $filtro_pesquisa);
        }

        file_put_contents('json_.json', json_encode((array) $retorno_pesquisa_ticket), JSON_UNESCAPED_UNICODE);

        if(empty($retorno_pesquisa_ticket) == true){
            // file_put_contents('json_.json', json_encode((array) model_parse((array) $this->modelo(), (array) ['id_ticket_suporte' => (int) intval(ravf_corp_model_next((string) $this->tabela(), 'id_ticket_suporte'), 10), 'id_empresa' => (int) $this->id_empresa, 'id_usuario' => (int) $this->id_usuario, 'login_usuario' => (string) $this->login_usuario, 'programador_responsavel' => (string) $this->programador_responsavel,'data_abertura' => model_date(), 'data_fechamento' => model_date(), 'codigo_barras' => (string) $this->codigo_barras, 'status' => (string) 'AGUARDANDO', 'tabela' => (string) $this->tabela()]), JSON_UNESCAPED_UNICODE));
            
            $retorno_operacao = (bool) ravf_corp_model_insert((string) $this->tabela(), (array) model_parse((array) $this->modelo(), (array) ['id_ticket_suporte' => (int) intval(ravf_corp_model_next((string) $this->tabela(), 'id_ticket_suporte'), 10), 'id_empresa' => (int) $this->id_empresa, 'id_usuario' => (int) $this->id_usuario, 'login_usuario' => (string) $this->login_usuario, 'programador_responsavel' => (string) $this->programador_responsavel,'data_abertura' => model_date(), 'data_fechamento' => model_date(), 'codigo_barras' => (string) $this->codigo_barras, 'status' => (string) 'AGUARDANDO']));

            $retorno_pesquisa = (array) ravf_corp_model_one((string) $this->tabela(), (array) ['codigo_barras', '===', (string) $this->codigo_barras]);
            
            if(empty($retorno_pesquisa) == false){
                return (array) ['status' => (bool) $retorno_operacao, 'dados' => (array) $retorno_pesquisa];
            }else{
                return (array) ['status' => (bool) $retorno_operacao];
            }
        }else{
            // file_put_contents('json_alteracao_.json', json_encode((array) model_parse((array) $this->modelo(), (array) ['id_ticket_suporte' => (int) intval(ravf_corp_model_next((string) $this->tabela(), 'id_ticket_suporte'), 10), 'id_empresa' => (int) $this->id_empresa, 'id_usuario' => (int) $this->id_usuario, 'login_usuario' => (string) $this->login_usuario, 'programador_responsavel' => (string) $this->programador_responsavel,'data_abertura' => model_date(), 'data_fechamento' => model_date(), 'codigo_barras' => (string) $this->codigo_barras, 'status' => (string) 'AGUARDANDO', 'tabela' => (string) $this->tabela()]), JSON_UNESCAPED_UNICODE));
            // $retorno_operacao = (bool) ravf_corp_model_update((string) $this->tabela(), (array) ['id_ticket_suporte', '===', (int) $this->id_ticket_suporte], (array) model_parse((array) $this->modelo(), (array) ['status' => (string) $this->status, 'data_fechamento' => model_date()]));
            $retorno_operacao = (bool) true;

            return (array) ['status' => (bool) $retorno_operacao];
        }
    }

    public function pesquisar($dados){
        return (array) ravf_corp_model_one((string) $this->tabela(), (array) $dados['filtro']);
    }

    public function pesquisar_todos($dados){
        return (array) ravf_corp_model_all((string) $this->tabela(), (array) $dados['filtro'], (array) $dados['ordenacao'], (int) $dados['limite']);
    }
    
    public function deletar($dados){
        $this->colocar_dados($dados);

        $retorno_exclusao = (array) ravf_corp_model_delete((string) $this->tabela(), ['id_ticket_suporte', '===', (int) $this->id_ticket_suporte]);

        if($retorno_exclusao == true){
            return (array) ['titulo' => (string) 'Sucesso', 'mensagem' => (string) 'Sucesso durante o processo de exclusão do tícket de suporte', 'icone' => (string) 'success'];
        }else{
            return (array) ['titulo' => (string) 'Erro na exclusão', 'mensagem' => (string) 'Erro durante o processo de exclusão do tícket de suporte', 'icone' => (string) 'error'];
        }
    }
}
?>