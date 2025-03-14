<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Armario.php';
require_once 'LogSistema.php';

class Organizacao{
    private $id_organizacao;
    private $id_empresa;
    private $id_usuario;
    private $nome_organizacao;
    private $descricao;
    private $codigo_barras;
    private $forma_visualizacao;
    
    private function tabela(){
        return (string) 'organizacao';
    }

    private function modelo(){
        return (array) ['id_organizacao' => (int) 0, 'id_empresa' => (int) 0, 'id_usuario' => (int) 0, 'nome_organizacao' => (string) '', 'descricao' => (string) '', 'codigo_barras' => (string) '', 'forma_visualizacao' => (string) 'PUBLICO'];
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_organizacao', $dados) == true){
            $this->id_organizacao = (int) intval($dados['codigo_organizacao'], 10);
        }else{
            $this->id_organizacao = (int) 0;
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

        if(array_key_exists('nome_organizacao', $dados) == true){
            $this->nome_organizacao = (string) strtoupper($dados['nome_organizacao']);
        }

        if(array_key_exists('descricao', $dados) == true){
            $this->descricao = (string) $dados['descricao'];
        }
        
        if(array_key_exists('codigo_barras', $dados) == true){
            $this->codigo_barras = (string) $dados['codigo_barras'];
        }else{
            $this->codigo_barras = (string) '';
        }

        if(array_key_exists('forma_visualizacao', $dados) == true){
            $this->forma_visualizacao = (string) $dados['forma_visualizacao'];
        }else{
            $this->forma_visualizacao = (string) 'PUBLICO';
        }
    }
    
    public function salvar($dados){
        $objeto_log = new LogSistema();
        $retorno_operacao = (bool) false;
        
        $this->colocar_dados($dados);
        
        $filtro = (array) ['and' => (array) [(array) ['id_organizacao', '===', (int) $this->id_organizacao], ['id_empresa', '===', (int) $this->id_empresa]]];

        $checar_existencia = (bool) model_check((string) $this->tabela(), (array) $filtro);

        if($checar_existencia == true){
            $retorno_pesquisa = (array) model_one((string) $this->tabela(), (array) $filtro);

            $retorno_log = (bool) $objeto_log->salvar_dados((array) ['id_empresa' => (int) $this->id_empresa, 'id_usuario' => (int) $this->id_usuario, 'codigo_barras' => (string) $this->codigo_barras, 'modulo' => (string) 'ORGANIZACAO', 'descricao' => (string) 'Alterou a organização '.$this->nome_organizacao]);
            
            $retorno_operacao = (bool) model_update((string) $this->tabela(), (array) $filtro, (array) model_parse((array) $retorno_pesquisa, (array) ['nome_organizacao' => (string) $this->nome_organizacao, 'descricao' => (string) $this->descricao, 'codigo_barras' => (string) $this->codigo_barras, 'forma_visualizacao' => (string) $this->forma_visualizacao]));
        }else{
            $filtro = (array) ['and' => (array) [(array) ['nome_organizacao', '===', (string) $this->nome_organizacao], ['id_empresa', '===', (int) $this->id_empresa]]];
            
            $checar_existencia = (bool) model_check((string) $this->tabela(), (array) $filtro);
            
            if($checar_existencia == true){
                return (array) ['titulo' => (string) 'ERRO NA OPERAÇÃO', 'mensagem' => (string) 'Erro ao salvar os dados da organização!', 'icon' => (string) 'error'];
            }else{
                $retorno_log = (bool) $objeto_log->salvar_dados((array) ['id_empresa' => (int) $this->id_empresa, 'id_usuario' => (int) $this->id_usuario, 'codigo_barras' => (string) $this->codigo_barras, 'modulo' => (string) 'ORGANIZACAO', 'descricao' => (string) 'Cadastrou a organização '.$this->nome_organizacao]);

                $retorno_operacao = (bool) model_insert((string) $this->tabela(), (array) model_parse((array) $this->modelo(), (array) ['id_organizacao' => (int) intval(model_next((string) $this->tabela(), 'id_organizacao', (array) ['id_empresa', '===', (int) $this->id_empresa]), 10), 'id_empresa' => (int) $this->id_empresa, 'id_usuario' => (int) $this->id_usuario,'nome_organizacao' => (string) $this->nome_organizacao, 'descricao' => (string) $this->descricao, 'codigo_barras' => (string) $this->codigo_barras, 'forma_visualizacao' => (string) $this->forma_visualizacao]));
            }
        }

        if($retorno_operacao == true){
            return (array) ['titulo' => (string)'SUCESSO NA OPERAÇÃO', 'mensagem' => (string) 'Dados da organização salvos co sucesso!', 'icone' => (string) 'success'];
        }else{
            return (array) ['titulo' => (string)'ERRO NA OPERAÇÃO', 'mensagem' => (string) 'Erro ao salvar dados da organização', 'icone' => (string) 'error'];
        }
    }
    public function pesquisar($dados){
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    public function pesquisar_todos($dados){
        return (array) model_all($this->tabela(), $dados['filtro'], $dados['ordenacao'], $dados['limite']);
    }

    /**
     * Função responsável por receber o codigo_organizacao e realizar as validações necessárias para saber se pode realizar a exclusão das informações do banco de dados ou não.
     * @param array $dados
     * @return array 
     */
    public function excluir_organizacao($dados){
        $objeto_log = new LogSistema();

        $this->colocar_dados($dados);
        
        $filtro_pesquisa_armario = (array) ['filtro' => (array) ['id_organizacao', '===', (int) $this->id_organizacao]];
        $objeto_armario = new Armario();
        $retorno_pesquisa_armario = (array) $objeto_armario->pesquisar($filtro_pesquisa_armario);

        if(empty($retorno_pesquisa_armario) == false){
            return (array) ['titulo' => (string) 'ORGANIZAÇÃO CONTÉM ARMÁRIOS', 'mensagem' => (string) 'Não é possível excluir organização que contenha armários cadastrados', 'icone' => (string) 'error'];
        }else{
            $retorno_exclusao =  (bool) model_delete((string) $this->tabela(), (array) ['id_organizacao', '===', (int) $this->id_organizacao]);
            
            if($retorno_exclusao == true){
                $retorno_log = (bool) $objeto_log->salvar_dados((array) ['id_empresa' => (int) $this->id_empresa, 'id_usuario' => (int) $this->id_usuario, 'codigo_barras' => (string) $this->codigo_barras, 'modulo' => (string) 'ORGANIZACAO', 'descricao' => (string) 'excluiu a organização '.$this->nome_organizacao]);

                return (array) ['titulo' => (string) 'EXCLUSÃO CONCLUÍDA', 'mensagem' => (string) 'Operação de exclusão foi realizada com sucesso!', 'icone' => (string) 'success'];
            }else{
                return (array) ['titulo' => (string) 'PROBLEMAS NA EXCLUSÃO', 'mensagem' => (string) 'Aconteceu um erro desconhecido durante o processo de exclusão da organização', 'icone' => (string) 'error'];
            }
        }
    }
}
?>