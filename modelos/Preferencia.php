<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Classes/Sistema/db.php';
class Preferencia{
    private $id_usuario;
    private $id_sistema;
    private $nome_preferencia;
    private $preferencia;

    private function tabela(){
        return (string) 'preferencia_usuario';
    }

    private function modelo(){
        return (array) ['id_usuario' => (int) 0, 'id_sistema' => (int) 0, 'nome_preferencia' => (string) '', 'preferencia' => (string) ''];
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_usuario', $dados) == true){
            $this->id_usuario = (int) intval($dados['codigo_usuario'], 10);
        }else{
            $this->id_usuario = (int) 0;
        }

        if(array_key_exists('codigo_sistema', $dados) == true){
            $this->id_sistema = (int) intval($dados['codigo_sistema'], 10);
        }else{
            $this->id_sistema = (int) 0;
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

    public function salvar_dados($dados){
        $this->colocar_dados($dados);

        $filtro_pesquisa = (array) ['and' => (array) [['id_sistema', '===', (int) $this->id_sistema], ['id_usuario', '===', (int) $this->id_usuario], ['nome_preferencia', '===', (string) $this->nome_preferencia]]];
        $filtro = (array) ['filtro' => (array) $filtro_pesquisa];

        $retorno_pesquisa = (array) $this->pesquisar((array) $filtro);

        if(empty($retorno_pesquisa) == true){
            return (bool) model_insert((string) $this->tabela(), (array) model_parse((array) $this->modelo(), (array) ['id_sistema' => (int) $this->id_sistema, 'id_usuario' => (int) $this->id_usuario, 'nome_preferencia' => (string) $this->nome_preferencia, 'preferencia' => (string) $this->preferencia]));
        }else{
            return (bool) model_update((string) $this->tabela(), (array) $filtro_pesquisa, (array) model_parse((array) $this->modelo(), (array) ['id_sistema' => (int) $this->id_sistema, 'id_usuario' => (int) $this->id_usuario, 'nome_preferencia' => (string) $this->nome_preferencia, 'preferencia' => (string) $this->preferencia]));
        }

    }

    public function excluir($dados){
        $this->colocar_dados($dados);
        
        $filtro_pesquisa = (array) ['and' => (array) [['id_sistema', '===', (int) $this->id_sistema], ['id_usuario', '===', (int) $this->id_usuario], ['nome_preferencia', '===', (string) $this->nome_preferencia]]];
        $filtro = (array) ['filtro' => (array) $filtro_pesquisa];

        return (bool) model_delete((string) $this->tabela(), (array) $filtro);
    }

    public function pesquisar($dados){
        return (array) model_one((string) $this->tabela(), $dados['filtro']);
    }

    public function pesquisar_todos($dados){

    }
}
?>