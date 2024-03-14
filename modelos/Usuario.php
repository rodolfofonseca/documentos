<?php
require_once 'Classes/bancoDeDados.php';

class Usuario{
    private $id_usuario;
    private $nome_usuario;
    private $login;
    private $senha_usuario;
    private $tipo;
    private $opcao = ['const' => 8];

    private function tabela(){
        return (string) 'usuario';
    }

    private function modelo(){
        return (array) ['id_usuario' => (int) 0, 'nome_usuario' => (string) '', 'login' => (string) '', 'senha_usuario' => (string) '', 'tipo' => (string)'COMUM'];
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_usuario', $dados) == true){
            $this->id_usuario = (int) intval($dados['codigo_usuario'], 10);
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
        }
    }

    public function salvar_dados($dados){
        $this->colocar_dados($dados);
        $senha_vazia = (string) password_hash('', PASSWORD_DEFAULT, $this->opcao);

        $checar_existencia = (bool) model_check($this->tabela(), ['id_usuario', '===', (int) $this->id_usuario]);

        $retorno_vazio = (bool) password_verify($this->senha_usuario, $senha_vazia);

        if($retorno_vazio == true){
            return (bool) false;
        }
        
        if($checar_existencia == true){
            $retorno_usuario = (array) model_one($this->tabela(), ['id_usuario', '===', (int) $this->id_usuario]);

            $retorno_usuario['senha_usuario'] = (string) $this->senha_usuario;
            $retorno_usuario['nome_usuario'] = (string) $this->nome_usuario;
            $retorno_usuario['login'] = (string) $this->login;

            return (bool) model_update($this->tabela(), ['id_usuario', '===', (int) $this->id_usuario], model_parse($this->modelo(), $retorno_usuario));
        }else{
            $retorno_login = (bool) model_check($this->tabela(), ['login', '===', (string) $this->login]);

            if($retorno_login == true){
                return (bool) false;
            }

            return (bool) model_insert($this->tabela(), model_parse($this->modelo(), ['id_usuario' => (int) model_next($this->tabela(), 'id_usuario'), 'nome_usuario' => (string) $this->nome_usuario, 'login' => (string) $this->login, 'senha_usuario' => (string) $this->senha_usuario, 'tipo' => (string) $this->tipo]));
        }
    }

    /**
     * Função responsável por realizar o login do usuário no sistema. Esta função recebe como parâmetro atrávés de array o login e senha e retorna o código se existir login e senha compatíveis senão retorna 0.
     * @param array $dados ['login' => 'xxxx', 'senha_usuario' => (string) 'xxxxxx' ];
     * @return int $id_usuario
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

    public function pesquisar($dados){
        return (array) model_one($this->tabela(), $dados['filtro']);
    }
}
?>