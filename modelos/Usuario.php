<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Interface.php';

class Usuario implements InterfaceModelo{
    private $id_usuario;
    private $id_empresa;
    private $nome_usuario;
    private $login;
    private $senha_usuario;
    private $tipo;
    private $status;
    private $email;
    private $opcao = ['const' => 8];

    public function tabela(){
        return (string) 'usuarios';
    }

    public function modelo(){
        return (array) ['_id' => convert_id(''), 'empresa' => convert_id(''), 'nome_usuario' => (string) '', 'login_usuario' => (string) '', 'senha_usuario' => (string) '', 'tipo_usuario' => (string)'COMUM', 'status' => (string) 'ATIVO', 'data_cadastro' => 'date', 'ultimo_login' => 'date'];
    }

    public function colocar_dados($dados){
        if(array_key_exists('codigo_usuario', $dados) == true){
            if($dados['codigo_usuario'] != ''){
                $this->id_usuario = convert_id($dados['codigo_usuario']);
            }
        }

        if(array_key_exists('codigo_empresa', $dados) == true){
            if($dados['codigo_empresa'] != ''){
                $this->id_empresa = convert_id($dados['codigo_empresa']);
            }
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
    }

    public function salvar_dados($dados){
        $this->colocar_dados($dados);
        $senha_vazia = (string) password_hash('', PASSWORD_DEFAULT, $this->opcao);
        $retorno_operacao = (bool) false;

        $retorno_checagem = (bool) model_check((string) $this->tabela(), (array) ['login_usuario', '===', (string) $this->login]);

        if($retorno_checagem == true){
            $retorno_pesquisa = (array) model_one((string) $this->tabela(), (array) ['login_usuario', '===', (string) $this->login]);

            if(empty($retorno_pesquisa) == true){
            }
            $retorno_operacao = (bool) model_update((string) $this->tabela(), (array) ['login_usuario', '===', (string) $this->login], (array) ['nome_usuario' => (string) $this->nome_usuario, 'tipo_usuario' => (string) $this->tipo, 'status' => (string) $this->status]);
        }else{
            $retorno_operacao = (bool) model_insert((string) $this->tabela(), (array) ['empresa' => $this->id_empresa, 'nome_usuario' => (string) $this->nome_usuario, 'login_usuario' => (string) $this->login, 'senha_usuario' => (string) $this->senha_usuario, 'tipo_usuario' => (string) $this->tipo, 'status' => (string) $this->status, 'data_cadastro' => model_date(), 'ultimo_login' => model_date()]);
        }

        return (bool) $retorno_operacao;
    }

    /**
     * Função responsável por realizar o login do usuário no sistema. Esta função recebe como parâmetro atrávés de array o login e senha e retorna o código se existir login e senha compatíveis senão retorna 0.
     * @param array $dados ['login_usuario' => 'xxxx', 'senha_usuario' => (string) 'xxxxxx' ];
     * @return array 
     */
    public function login_sistema($dados){
        $this->colocar_dados($dados);

        $retorno_usuario = (array) model_one($this->tabela(), ['login_usuario', '===', (string) $this->login]);

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
     * Função responsáel por pesquisar o login_usuario no banco de e retornar essa informação
     * @param (ObjectfId) id_usuario identificador do usuário do tipo _id
     * @param (string) login_usuario retorna o login do usuário
     */
    public function retornar_usuario($id_usuario){
        $retorno_usuario = (array) $this->pesquisar((array) ['filtro' => (array) ['_id', '===', $id_usuario]]);

        if(empty($retorno_usuario) == false){
            if(array_key_exists('login_usuario', $retorno_usuario) == true){
                return (string) $retorno_usuario['login_usuario'];
            }else{
                return (string) '';
            }
        }else{
            return (string) '';
        }
    }
}
?>