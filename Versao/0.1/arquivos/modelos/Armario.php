<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Prateleira.php';
require_once 'Modelos/LogSistema.php';
require_once 'Modelos/Usuario.php';

class Armario{
    private $id_armario;
    private $id_empresa;
    private $id_organizacao;
    private $id_usuario;
    private $nome_armario;
    private $descricao;
    private $codigo_barras;
    private $forma_visualizacao;

    private function tabela(){
        return (string) 'armario';
    }

    private function modelo(){
        return (array) ['id_armario' => (int) 0, 'id_empresa' => (int) 0, 'id_usuario' => (int) 0, 'id_organizacao' => (int) 0,'nome_armario' => (string) '', 'descricao' => (string) '', 'codigo_barras' => (string) '', 'forma_visualizacao' => (string) 'PUBLICO'];
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_armario', $dados) == true){
            $this->id_armario = (int) intval($dados['codigo_armario'], 10);
        }else{
            $this->id_armario = (int) 0;
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

        if(array_key_exists('codigo_organizacao', $dados) == true){
            $this->id_organizacao = (int) intval($dados['codigo_organizacao'], 10);
        }else{
            $this->id_organizacao = (int) 0;
        }

        if(array_key_exists('nome_armario', $dados) == true){
            $this->nome_armario = (string) strtoupper($dados['nome_armario']);
        }

        if(array_key_exists('descricao', $dados) == true){
            $this->descricao = (string) $dados['descricao'];
        }

        if(array_key_exists('codigo_barras', $dados) == true){
            $this->codigo_barras = (string) $dados['codigo_barras'];
        }

        if(array_key_exists('forma_visualizacao', $dados) == true){
            $this->forma_visualizacao = (string) $dados['forma_visualizacao'];
        }else{
            $this->forma_visualizacao = (string) 'PUBLICO';
        }
    }
    
    public function salvar_dados($dados){
        $objeto_log = new LogSistema();
        $objeto_usuario = new Usuario();

        $retorno_operacao = (bool) false;

        $this->colocar_dados($dados);
        
        $filtro = (array) ['and' => (array) [(array) ['id_armario', '===', (int) $this->id_armario], (array) ['id_empresa', '===', (int) $this->id_empresa]]];
        $checar_existencia = (bool) model_check((string) $this->tabela(), (array) $filtro);

        $retorno_usuario = (array) $objeto_usuario->pesquisar((array) ['filtro' => (array) ['id_usuario', '===', (int) $this->id_usuario]]);

        if($checar_existencia == true){
            $retorno_pesquisa = (array) model_one((string) $this->tabela(), (array) $filtro);

            if(empty($retorno_pesquisa) == false){
                $retorno_operacao =  (bool) model_update((string) $this->tabela(), (array) $filtro, (array) model_parse($retorno_pesquisa, ['id_organizacao' => (int) $this->id_organizacao, 'nome_armario' => (string) $this->nome_armario, 'descricao' => (string) $this->descricao, 'forma_visualizacao' => (string) $this->forma_visualizacao]));

                if($retorno_operacao == true){
                    if(empty($retorno_usuario) == false){
                        $retorno_log = (bool) $objeto_log->salvar_dados((array) ['id_empresa' => (int) intval($retorno_pesquisa['id_empresa'], 10), 'usuario' => (string) $retorno_usuario['login'], 'codigo_barras' => (string) $retorno_pesquisa['codigo_barras'], 'modulo' => (string) 'ARMARIO', 'descricao' => (string) 'Alterou o armário '.$this->nome_armario ]);
                    }
                }
            }else{
                $retorno_operacao =  (bool) false;
            }

        }else{
            $retorno_operacao =  (bool) model_insert((string) $this->tabela(), (array) model_parse($this->modelo(), (array) ['id_armario' => (int) intval(model_next((string) $this->tabela(), (string) 'id_armario', (array) ['id_empresa', '===', (int) $this->id_empresa]), 10), 'id_empresa' => (int) $this->id_empresa, 'id_organizacao' => (int) $this->id_organizacao, 'id_usuario' => (int) $this->id_usuario, 'nome_armario' => (string) $this->nome_armario, 'descricao' => (string) $this->descricao, 'codigo_barras' => (string) $this->codigo_barras, 'forma_visualizacao' => (string) $this->forma_visualizacao]));

            if($retorno_operacao == true){
                if(empty($retorno_usuario) == false){
                    $retorno_log = (bool) $objeto_log->salvar_dados((array) ['id_empresa' => (int) $this->id_empresa, 'usuario' => (string) $retorno_usuario['login'], 'codigo_barras' => (string) $this->codigo_barras, 'modulo' => (string) 'ARMARIO', 'descricao' => (string) 'Cadastrou o armário '.$this->nome_armario]);
                }
            }
        }

        if($retorno_operacao == true){
            return (array) ['titulo' => (string) 'SUCESSO AO SALVAR DADOS', 'descricao' => (string) 'Operação realizada com sucesso!', 'icone' => (string) 'success'];
        }else{
            return (array) ['titulo' => (string) 'ERRO DURANTE A OPERAÇÃO', 'descricao' => (string) 'Erro durante o processo!', 'icone' => (string) 'error'];
        }
    }

    public function excluir($dados){
        $objeto_log = new LogSistema();

        $this->colocar_dados((array) $dados);

        $filtro_pesquisa_prateleira = (array) ['filtro' => (array) ['id_armario', '===', (int) $this->id_armario]];
        $objeto_prateleira = new Prateleira();
        $retorno_pesquisa_prateleira = (array) $objeto_prateleira->pesquisar($filtro_pesquisa_prateleira);

        if(empty($retorno_pesquisa_prateleira) == false){
            return (array) ['titulo' => (string) 'ARMARIO CONTÉM PRATELEIRAS', 'mensagem' => (string) 'Não é possível excluir um armário que contém prateleiras', 'icone' => (string) 'error'];
        }else{
            $retorno_armario = (array) $this->pesquisar((array) ['filtro' => (array) ['id_armario', '===', (int) $this->id_armario]]);
            $retorno_exclusao = (bool) model_delete((string) $this->tabela(), (array) ['id_armario', '===', (int) $this->id_armario]);

            if($retorno_exclusao == true){

                if(empty($retorno_armario) == false){
                    $objeto_usuario = new Usuario();
                    $retorno_usuario = $objeto_usuario->pesquisar((array) ['filtro' => (array) ['id_usuario', '===', (int) intval($retorno_armario['id_usuario'], 10)]]);

                    if(empty($retorno_exclusao) == false){
                        $retorno_log = $objeto_log->salvar_dados((array) ['id_empresa' => (int) intval($retorno_armario['id_empresa'], 10), 'usuario' => (string) $retorno_usuario['login'], 'codigo_barras' => (string) $retorno_armario['codigo_barras'], 'modulo' => (string) 'ARMARIO', 'descricao' => (string) 'Excluiu o armário '.$retorno_armario['nome_armario']]);
                    }
                }

                return (array) ['titulo' => (string) 'EXCLUSÃO CONCLUÍDA', 'mensagem' => (string) 'Operação realizada com sucesso!', 'icone' => (string) 'success'];
            }else{
                return (array) ['titulo' => (string) 'PROBLEMAS NA EXCLUSÃO', 'mensagem' => (string) 'Não foi possível excluir o armário, aconteceu algum erro desconhecido, por favor tente mais tarde!', 'icone' => (string) 'error'];
            }
        }
    }

    public function pesquisar($dados){
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    public function pesquisar_todos($dados){
        return (array) model_all($this->tabela(), $dados['filtro'], $dados['ordenacao'], $dados['limite']);
    }
}
?>