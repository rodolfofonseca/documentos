<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Prateleira.php';
require_once 'Modelos/LogSistema.php';
require_once 'Modelos/Usuario.php';
require_once 'Modelos/Notificacoes.php';
require_once 'Modelos/Interface.php';
require_once 'Modelos/Organizacao.php';

class Armario implements InterfaceModelo
{
    private $id_armario;
    private $empresa;
    private $organizacao;
    private $usuario;
    private $nome_armario;
    private $descricao;
    private $codigo_barras;
    private $tipo;
    private $status;
    private $data_cadastro;

    public function tabela()
    {
        return (string) 'armarios';
    }

    public function modelo()
    {
        return (array) ['_id' => convert_id(''), 'empresa' => convert_id(''), 'organizacao' => convert_id(''), 'usuario' => convert_id(''), 'nome_armario' => (string) '', 'descricao' => (string) '', 'codigo_barras' => (string) '', 'tipo' => (string) 'PUBLICO', 'status' => (string) 'ATIVO', 'data_cadastro' => model_date()];
    }

    public function colocar_dados($dados)
    {
        if (array_key_exists('codigo_armario', $dados) == true) {
            if ($dados['codigo_armario'] != '') {
                $this->id_armario = convert_id($dados['codigo_armario']);
            }
        } else {
            $this->id_armario = null;
        }

        if (array_key_exists('empresa', $dados) == true) {
            if ($dados['empresa'] != '') {
                $this->empresa = convert_id($dados['empresa']);
            }
        }

        if (array_key_exists('usuario', $dados) == true) {
            if ($dados['usuario'] != '') {
                $this->usuario = convert_id($dados['usuario']);
            }
        }

        if (array_key_exists('organizacao', $dados) == true) {
            if ($dados['organizacao'] != '') {
                $this->organizacao = convert_id($dados['organizacao']);
            }
        }

        if (array_key_exists('nome_armario', $dados) == true) {
            $this->nome_armario = (string) strtoupper($dados['nome_armario']);
        } else {
            $this->nome_armario = (string) '';
        }

        if (array_key_exists('descricao', $dados) == true) {
            $this->descricao = (string) $dados['descricao'];
        } else {
            $this->descricao = (string) '';
        }

        if (array_key_exists('codigo_barras', $dados) == true) {
            $this->codigo_barras = (string) $dados['codigo_barras'];
        } else {
            $this->codigo_barras = codigo_barras();
        }

        if (array_key_exists('tipo', $dados) == true) {
            $this->tipo = (string) $dados['tipo'];
        } else {
            $this->tipo = (string) 'PUBLICO';
        }

        if (array_key_exists('status', $dados) == true) {
            $this->status = (string) $dados['status'];
        } else {
            $this->status = (string) 'ATIVO';
        }

        if (array_key_exists('data_cadastro', $dados) == true) {
            $this->data_cadastro = model_date($dados['data_cadastro']);
        } else {
            $this->data_cadastro = model_date();
        }
    }

    public function salvar_dados($dados)
    {
        $this->colocar_dados($dados);

        $objeto_log = new LogSistema();
        $objeto_usuario = new Usuario();
        $retorno_operacao_armario = (bool) false;
        $retorno_operacao_log = (bool) false;

        if ($this->id_armario != null) {
            $retorno_operacao_armario = (bool) model_update((string) $this->tabela(), (array) ['_id', '===', $this->id_armario], (array) ['organizacao' => $this->organizacao, 'nome_armario' => (string) $this->nome_armario, 'descricao' => (string) $this->descricao, 'tipo' => (string) $this->tipo, 'status' => (string) $this->status]);

            if ($retorno_operacao_armario == true) {
                $retorno_operacao_log = (bool) $objeto_log->salvar_dados((array) ['id_empresa' => $this->empresa, 'usuario' => $this->usuario, 'tabela_acao' => (string) 'ALTERACAO', 'modulo' => (string) 'ARMARIO', 'descricao' => (string) 'Usuário alterou os dados do armário']);
            }
        } else {
            $retorno_operacao_armario = (bool) model_insert((string) $this->tabela(), (array) ['empresa' => $this->empresa, 'organizacao' => $this->organizacao, 'usuario' => $this->usuario, 'nome_armario' => (string) $this->nome_armario, 'descricao' => (string) $this->descricao, 'codigo_barras' => (string) $this->codigo_barras, 'tipo' => (string) $this->tipo, 'status' => (string) $this->status, 'data_cadastro' => $this->data_cadastro]);

            if ($retorno_operacao_armario == true) {
                $retorno_operacao_log = (bool) $objeto_log->salvar_dados((array) ['id_empresa' => $this->empresa, 'usuario' => $this->usuario, 'tabela_acao' => (string) 'CADASTRAR', 'modulo' => (string) 'ARMARIO', 'descricao' => (string) 'Usuário cadastrou o armário de nome ' . $this->nome_armario]);
            }
        }

        return $retorno_operacao_armario;
    }

    public function pesquisar($dados)
    {
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    public function pesquisar_todos($dados)
    {
        return (array) model_all($this->tabela(), $dados['filtro'], $dados['ordenacao'], $dados['limite']);
    }

    /**
     * Função que valida os campos e retorna os dados validados
     * @param (array) com os dados a ser validados
     * @param (array) array que e para montar com os dados validados
     * @return (array) validado
     */
    public function validar_campos_filtro($retorno_pesquisa, $retorno)
    {
        $objeto_organizacao = new Organizacao();

        if (empty($retorno_pesquisa) == false) {
            foreach ($retorno_pesquisa as $retorno_corrigido) {
                if (array_key_exists('organizacao', $retorno_corrigido) == true) {
                    $filtro_organizacao = (array) ['filtro' => (array) ['_id', '===', convert_id($retorno_corrigido['organizacao'])]];

                    $retorno_organizacao = (array) $objeto_organizacao->pesquisar($filtro_organizacao);

                    if (empty($retorno_organizacao) == false) {
                        if (array_key_exists('nome_organizacao', $retorno_organizacao) == true) {
                            $retorno_corrigido['nome_organizacao'] = (string) $retorno_organizacao['nome_organizacao'];
                        } else {
                            $retorno_corrigido['nome_organizacao'] = (string) '';
                        }
                    } else {
                        $retorno_corrigido['nome_organizacao'] = (string) '';
                    }

                    array_push($retorno, $retorno_corrigido);
                }
            }
        }

        return (array) $retorno;
    }
}
