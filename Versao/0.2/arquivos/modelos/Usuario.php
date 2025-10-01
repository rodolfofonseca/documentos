<?php
require_once 'Classes/bancoDeDados.php';

class Usuario{
    private $id_usuario;
    private $id_empresa;
    private $nome_usuario;
    private $login;
    private $senha_usuario;
    private $tipo;
    private $status;
    private $email;
    private $opcao = ['const' => 8];

    private function tabela(){
        return (string) 'usuario';
    }

    private function modelo(){
        return (array) ['id_usuario' => (int) 0, 'id_empresa' => (int) 0, 'nome_usuario' => (string) '', 'login' => (string) '', 'senha_usuario' => (string) '', 'tipo' => (string)'COMUM', 'status' => (string) 'ATIVO', 'email' => (string) ''];
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_usuario', $dados) == true){
            $this->id_usuario = (int) intval($dados['codigo_usuario'], 10);
        }else{
            $this->id_usuario = (int) 0;
        }

        if(array_key_exists('codigo_empresa', $dados) == true){
            $this->id_empresa = (int) intval($dados['codigo_empresa'], 10);
        }else{
            $this->id_empresa = (int) 0;
        }

        if(array_key_exists('nome_usuario', $dados) == true){
            $this->nome_usuario = (string) strtoupper($dados['nome_usuario']);
        }

        if(array_key_exists('login', $dados) == true){
            $this->login = (string) $dados['login'];
        }

        if(array_key_exists('senha_usuario', $dados) == true){
            $this->senha_usuario = (string) password_hash($dados['senha_usuario'], PASSWORD_DEFAULT, $this->opcao);
        }

        if(array_key_exists('tipo', $dados) == true){
            $this->tipo = (string) $dados['tipo'];
        }else{
            $this->tipo = (string) 'COMUM';
        }

        if(array_key_exists('status', $dados) == true){
            $this->status = (string) $dados['status'];
        }else{
            $this->status = (string) 'ATIVO';
        }

        if(array_key_exists('email', $dados) == true){
            $this->email = (string) $dados['email'];
        }else{
            $this->email = (string) '';
        }
    }

    public function salvar_dados($dados){
        $this->colocar_dados($dados);
        $senha_vazia = (string) password_hash('', PASSWORD_DEFAULT, $this->opcao);

        $checar_existencia = (bool) ravf_corp_model_check($this->tabela(), ['id_usuario', '===', (int) $this->id_usuario]);

        $retorno_vazio = (bool) password_verify($this->senha_usuario, $senha_vazia);

        if($retorno_vazio == true){
            return (bool) false;
        }
        
        if($checar_existencia == true){
            $retorno_usuario = (array) model_one($this->tabela(), ['id_usuario', '===', (int) $this->id_usuario]);

            $retorno_usuario['id_empresa'] = (int) $this->id_empresa;
            $retorno_usuario['senha_usuario'] = (string) $this->senha_usuario;
            $retorno_usuario['nome_usuario'] = (string) $this->nome_usuario;
            $retorno_usuario['login'] = (string) $this->login;
            $retorno_usuario['email'] = (string) $this->email;

            $array_dados = (array) model_parse($this->modelo(), $retorno_usuario);

            $retorno_array_corp = (bool) ravf_corp_model_update($this->tabela(), ['id_usuario', '===', (int) $this->id_usuario], (array) $array_dados);

            return (bool) model_update($this->tabela(), ['id_usuario', '===', (int) $this->id_usuario], (array) $array_dados);
        }else{
            $retorno_login = (bool) ravf_corp_model_check($this->tabela(), ['login', '===', (string) $this->login]);

            if($retorno_login == true){
                return (bool) false;
            }

            $array_dados = (array) model_parse($this->modelo(), ['id_usuario' => (int) ravf_corp_model_next($this->tabela(), 'id_usuario'), 'id_empresa' => (int) $this->id_empresa,'nome_usuario' => (string) $this->nome_usuario, 'login' => (string) $this->login, 'senha_usuario' => (string) $this->senha_usuario, 'tipo' => (string) $this->tipo, 'status' => (string) $this->status, 'email' => (string) $this->email]);

            $retorno_ravf_corp = (bool) ravf_corp_model_insert($this->tabela(), (array) $array_dados);

            return (bool) model_insert($this->tabela(), (array) $array_dados);
        }
    }

    /**
     * Função responsável por realizar o login do usuário no sistema. Esta função recebe como parâmetro atrávés de array o login e senha e retorna o código se existir login e senha compatíveis senão retorna 0.
     * @param array $dados ['login' => 'xxxx', 'senha_usuario' => (string) 'xxxxxx' ];
     * @return array 
     */
    public function login_sistema($dados){
        $this->colocar_dados($dados);

        $retorno_usuario = (array) model_one($this->tabela(), ['login', '===', (string) $this->login]);

        if(empty($retorno_usuario) == false){
            $retorno_senha =  (bool) password_verify($dados['senha_usuario'], $retorno_usuario['senha_usuario']);

            if($retorno_senha == true){
                return (array) $retorno_usuario;
            }else{
                return (array) [];
            }
        }else{
            return (array) [];
        }
    }

    public function pesquisar_todos($dados){
        return (array) model_all($this->tabela(), $dados['filtro'], $dados['ordenacao'], $dados['limite']);
    }

    public function pesquisar($dados){
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    /**
     * Função responsável por verificar qual o status atual do usuário e realizar a alteração de acordo com o status.
     * @param array contendo os dados do usuário
     * @return bool com o status da operação
     */
    public function alterar_status($dados){
        $this->colocar_dados($dados);

        if($this->status == 'ATIVO'){
            $retorno_ravf_corp = (bool) ravf_corp_model_update((string) $this->tabela(), (array) ['and' => (array) [(array) ['id_empresa', '===', (int) $this->id_empresa], (array) ['id_usuario', '===', (int) $this->id_usuario]]], (array) ['status' => (string) 'INATIVO']);

            return (bool) model_update((string) $this->tabela(), (array) ['and' => (array) [(array) ['id_empresa', '===', (int) $this->id_empresa], (array) ['id_usuario', '===', (int) $this->id_usuario]]], (array) ['status' => (string) 'INATIVO']);
        }else if($this->status == 'INATIVO'){
            $retorno_ravf_corp = (bool) ravf_corp_model_update((string) $this->tabela(), (array) ['and' => (array) [(array) ['id_empresa', '===', (int) $this->id_empresa], (array) ['id_usuario', '===', (int) $this->id_usuario]]], (array) ['status' => (string) 'ATIVO']);
            return (bool) model_update((string) $this->tabela(), (array) ['and' => (array) [(array) ['id_empresa', '===', (int) $this->id_empresa], (array) ['id_usuario', '===', (int) $this->id_usuario]]], (array) ['status' => (string) 'ATIVO']);
        }else{
            return (bool) false;
        }
    }

    /**
     * Função responsável por realizar apenas a troca da senha do usuário
     * @param array contendo as informações do usuário que será realizado a troca da senha
     * @return bool contendo o resultado da troca da senha.
     */
    public function alterar_senha($dados){
        $this->colocar_dados($dados);

        $retorno_ravf_corp = (bool) ravf_corp_model_update((string) $this->tabela(), (array) ['and' => (array) [(array) ['id_empresa', '===', (int) $this->id_empresa], (array) ['id_usuario', '===', (int) $this->id_usuario]]], (array) ['senha_usuario' => (string) $this->senha_usuario]);
        return (bool) model_update((string) $this->tabela(), (array) ['and' => (array) [(array) ['id_empresa', '===', (int) $this->id_empresa], (array) ['id_usuario', '===', (int) $this->id_usuario]]], (array) ['senha_usuario' => (string) $this->senha_usuario]);
    }
}
?>