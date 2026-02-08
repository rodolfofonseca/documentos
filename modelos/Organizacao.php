<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Armario.php';
require_once 'LogSistema.php';
require_once 'Notificacoes.php';

class Organizacao implements InterfaceModelo
{
    private $id_organizacao;
    private $id_empresa;
    private $id_usuario;
    private $nome_organizacao;
    private $descricao;
    private $codigo_barras;
    private $forma_visualizacao;
    private $status_organizacao;
    private $data_cadastro;

    public function tabela()
    {
        return (string) 'organizacoes';
    }

    public function modelo()
    {
        return (array) ['_id' => convert_id(''), 'empresa' => convert_id(''), 'usuario' => convert_id(''), 'nome_organizacao' => (string) '', 'descricao' => (string) '', 'codigo_barras' => (string) '', 'forma_visualizacao' => (string) 'PUBLICO', 'status_organizacao' => (string) '', 'data_cadastro' => model_date()];
    }

    public function colocar_dados($dados)
    {
        if (array_key_exists('codigo_organizacao', $dados) == true) {
            if ($dados['codigo_organizacao'] != '') {
                $this->id_organizacao = convert_id($dados['codigo_organizacao']);
            }
        } else {
            $this->id_organizacao = null;
        }

        if (array_key_exists('codigo_empresa', $dados) == true) {
            if ($dados['codigo_empresa'] != '') {
                $this->id_empresa = convert_id($dados['codigo_empresa']);
            }
        } else {
            $this->id_empresa = null;
        }

        if (array_key_exists('codigo_usuario', $dados) == true) {
            if ($dados['codigo_usuario'] != '') {
                $this->id_usuario = convert_id($dados['codigo_usuario']);
            }
        } else {
            $this->id_usuario = null;
        }

        if (array_key_exists('nome_organizacao', $dados) == true) {
            $this->nome_organizacao = (string) strtoupper($dados['nome_organizacao']);
        } else {
            $this->nome_organizacao = (string) '';
        }

        if (array_key_exists('descricao', $dados) == true) {
            $this->descricao = (string) $dados['descricao'];
        } else {
            $this->descricao = (string) '';
        }

        if (array_key_exists('codigo_barras', $dados) == true) {
            $this->codigo_barras = (string) $dados['codigo_barras'];
        } else {
            $this->codigo_barras = (string) codigo_barras();
        }

        if (array_key_exists('forma_visualizacao', $dados) == true) {
            $this->forma_visualizacao = (string) $dados['forma_visualizacao'];
        } else {
            $this->forma_visualizacao = (string) 'PUBLICO';
        }

        if (array_key_exists('status_organizacao', $dados) == true) {
            $this->status_organizacao = (string) $dados['status_organizacao'];
        } else {
            $this->status_organizacao = (string) 'ATIVO';
        }

        if (array_key_exists('data_cadastro', $dados) == true) {
            $this->data_cadastro = model_date($dados['data_cadastro']);
        } else {
            $this->data_cadastro = model_date();
        }
    }

    public function salvar_dados($dados)
    {
        $objeto_log_sitema = new LogSistema();

        $this->colocar_dados($dados);
        $retorno = (bool) false;
        $retorno_log_sistema = (bool) false;

        if ($this->id_empresa == null && $this->id_organizacao == null || $this->id_usuario == null && $this->id_organizacao == null) {
            return false;
        } else {
            if ($this->id_organizacao != null) {
                $checar_existencia = (bool) model_check($this->tabela(), ['_id', '===', $this->id_organizacao]);

                if ($checar_existencia == true) {
                    $retorno = (bool) model_update((string) $this->tabela(), (array) ['_id', '===', $this->id_organizacao], (array) ['nome_organizacao' => (string) $this->nome_organizacao, 'descricao' => (string) $this->descricao, 'forma_visualizacao' => (string) $this->forma_visualizacao]);

                    $retorno_log_sistema = (bool) $objeto_log_sitema->salvar_dados(['id_empresa' => $this->id_empresa, 'usuario' => (string) $this->id_usuario, 'modulo' => 'ORGANIZACAO', 'descricao' => 'Usuario realizou a alteração da organizacao: ' . $this->nome_organizacao, 'tabela_acao' => (string) 'ALTERACAO']);
                } else {
                    return false;
                }
            } else {
                $retorno = (bool) model_insert((string) $this->tabela(), (array) ['empresa' => $this->id_empresa, 'usuario' => $this->id_usuario, 'nome_organizacao' => (string) $this->nome_organizacao, 'descricao' => (string) $this->descricao, 'codigo_barras' => (string) $this->codigo_barras, 'forma_visualizacao' => (string) $this->forma_visualizacao, 'status_organizacao' => (string) $this->status_organizacao, 'data_cadastro' => $this->data_cadastro]);

                $retorno_log_sistema = (bool) $objeto_log_sitema->salvar_dados(['id_empresa' => $this->id_empresa, 'usuario' => (string) $this->id_usuario, 'modulo' => 'ORGANIZACAO', 'descricao' => 'Usuario realizou o atualizacao da organizacao: ' . $this->nome_organizacao, 'tabela_acao' => (string) 'CADASTRO']);
            }
        }
        return $retorno;
    }
    public function pesquisar($filtro)
    {
        return (array) model_one($this->tabela(), $filtro['filtro']);
    }

    public function pesquisar_todos($filtro)
    {
        return (array) model_all($this->tabela(), $filtro['filtro'], $filtro['ordenacao'], $filtro['limite']);
    }

    /**
     * Função responsável por alteraar o status da organização (ATIVO/INATIVO)
     * @param array $dados ['codigo_organizacao' => 'xxxx', 'status' => 'xxxx' ];
     * @return bool
     */
    public function alterar_status_organizacao($dados){
        $this->colocar_dados($dados);

        if($this->status_organizacao == 'ATIVO'){
            $this->status_organizacao = 'INATIVO';
        }else{
            $this->status_organizacao = 'ATIVO';
        }

        return (bool) model_update((string) $this->tabela(), (array) ['_id', '===', $this->id_organizacao], (array) ['status_organizacao' => (string) $this->status_organizacao]);
    }

    /**
     * Função responsável por alteraar o tipo de organização (PÚBLICO/PRIVADO)
     * @param array $dados ['codigo_organizacao' => 'xxxx', 'forma_visualizacao' => 'xxxx' ];
     * @return bool
     */
    public function alterar_tipo_organizacao($dados){
        $this->colocar_dados($dados);

        if($this->forma_visualizacao == 'PUBLICO'){
            $this->forma_visualizacao = 'PRIVADO';
        }else{
            $this->forma_visualizacao = 'PUBLICO';
        }

        return (bool) model_update((string) $this->tabela(), (array) ['_id', '===', $this->id_organizacao], (array) ['forma_visualizacao' => (string) $this->forma_visualizacao]);
    }
}
