<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Interface.php';
require_once 'Modelos/LogSistema.php';
require_once 'Modelos/Prateleira.php';

class Caixa implements InterfaceModelo{
    private $id_caixa;
    private $usuario;
    private $prateleira;
    private $empresa;
    private $nome_caixa;
    private $descricao;
    private $codigo_barras;
    private $tipo;
    private $status;
    private $data_cadastro;

    public function tabela(){
        return (string) 'caixas';
    }

    public function modelo(){
        return (array) ['_id' => convert_id(''), 'usuario' => convert_id(''), 'prateleira' => convert_id(''), 'empresa' => convert_id(''), 'nome_caixa' => (string) '', 'descricao' => (string) '', 'codigo_barras' => (string) '', 'tipo' => (string) 'PUBLICO', 'status' => (string) 'ATIVO', 'data_cadastro' => model_date()];
    }

    public function colocar_dados($dados){
        if(array_key_exists('codigo_caixa', $dados) == true){
            if($dados['codigo_caixa'] != ''){
                $this->id_caixa = convert_id($dados['codigo_caixa']);
            }else{
                $this->id_caixa = null;
            }
        }

        if(array_key_exists('codigo_usuario', $dados) == true){
            if($dados['codigo_usuario'] != ''){
                $this->usuario = convert_id($dados['codigo_usuario']);
            }
        }

        if(array_key_exists('codigo_prateleira', $dados) == true){
            if($dados['codigo_prateleira'] != ''){
                $this->prateleira = convert_id($dados['codigo_prateleira']);
            }
        }

        if(array_key_exists('codigo_empresa', $dados) == true){
            if($dados['codigo_empresa'] != ''){
                $this->empresa = convert_id($dados['codigo_empresa']);
            }
        }

        if(array_key_exists('nome_caixa', $dados) == true){
            $this->nome_caixa = (string) strtoupper($dados['nome_caixa']);
        }

        if(array_key_exists('descricao', $dados) == true){
            $this->descricao = (string) $dados['descricao'];
        }

        if(array_key_exists('codigo_barras', $dados) == true){
            $this->codigo_barras = (string) $dados['codigo_barras'];
        }else{
            $this->codigo_barras = (string) codigo_barras();
        }

        if(array_key_exists('tipo', $dados) == true){
            $this->tipo = (string) $dados['tipo'];
        }else{
            $this->tipo = (string) 'PUBLICO';
        }

        if(array_key_exists('status', $dados) == true){
            $this->status = (string) $dados['status'];
        }else{
            $this->status = (string) 'ATIVO';
        }

        if(array_key_exists('data_cadastro', $dados) == true){
            $this->data_cadastro = model_date($dados['data_cadastro']);
        }else{
            $this->data_cadastro = model_date();
        }
    }

    public function salvar_dados($dados){
        $retorno_operacao = (bool) false;
        $objeto_log_sistema = new LogSistema();

        $this->colocar_dados($dados);

        if($this->id_caixa != null){
            $retorno_checagem = (bool) model_check((string) $this->tabela(), (array) ['_id', '===', $this->id_caixa]);

            if($retorno_checagem == true){
                $retorno_operacao = (bool) model_update((string) $this->tabela(), ['_id', '===', $this->id_caixa], (array) ['nome_caixa' => (string) $this->nome_caixa, 'descricao' => (string) $this->descricao, 'tipo' => (string) $this->tipo, 'status' => (string) $this->status]);

                if($retorno_operacao == true){
                    $retorno_log = (array) $objeto_log_sistema->salvar_dados(['empresa' => $this->empresa, 'usuario' => $this->usuario, 'tabela_acao' => (string) 'ALTERACAO', 'modulo' => (string) 'CAIXA', 'descricao' => 'Usuário alterou a caixa '.$this->nome_caixa]);
                }
            }else{
                $retorno_operacao = (bool) false;
            }
        }else{
            $retorno_operacao = (bool) model_insert((string) $this->tabela(), ['usuario' => $this->usuario, 'prateleira' => $this->prateleira, 'empresa' => $this->empresa, 'nome_caixa' => (string) $this->nome_caixa, 'descricao' => (string) $this->descricao, 'codigo_barras' => (string) $this->codigo_barras, 'tipo' => (string) $this->tipo, 'status' => (string) $this->status, 'data_cadastro' => $this->data_cadastro]);

            if($retorno_operacao == true){
                $retorno_log = (bool) $objeto_log_sistema->salvar_dados((array) ['empresa' => $this->empresa, 'usuario' => $this->usuario, 'tabela_acao' => (string) 'CADASTRO', 'modulo' => (string) 'CAIXA', 'descricao' => (string) 'Usuário cadastrou a caixa '.$this->nome_caixa]);
            }
        }

        return (bool) $retorno_operacao;
    }

    public function pesquisar($dados){
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    public function pesquisar_todos($dados){
        return (array) model_all($this->tabela(), $dados['filtro'], $dados['ordenacao'], $dados['limite']);
    }

    /**
     * Função responsável por montar o array de retorno da forma como o front do sistema espera.
     * @param array $filtro, informação daa forma que vem do banco de dados
     * @param array $retorno, variável que armazena as informações corretas
     * @return array retorno de dados.
     */
    public function validar_dados_pesquisa_caixa($filtro, $retorno){
        $objeto_prateleira = new Prateleira();
        $retorno_caixa_pesquisa = (array) $this->pesquisar_todos($filtro);

        if(empty($retorno_caixa_pesquisa) == false){
            foreach($retorno_caixa_pesquisa as $retorno_pesquisa){
                if(array_key_exists('prateleira', $retorno_pesquisa) == true){
                    $filtro_pesquisa_prateleira = (array) ['filtro' => (array) ['_id', '===', $retorno_pesquisa['prateleira']]];
                    $retorno_prateleira = (array) $objeto_prateleira->pesquisar($filtro_pesquisa_prateleira);

                    if(empty($retorno_prateleira) == false){
                        if(array_key_exists('nome_prateleira', $retorno_prateleira) == true){
                            $retorno_pesquisa['nome_prateleira'] = (string) $retorno_prateleira['nome_prateleira'];
                        }else{
                            $retorno_pesquisa['nome_prateleira'] = (string) '';
                        }
                    }else{
                        $retorno_pesquisa['nome_prateleira'] = (string) '';
                    }
                }

                array_push($retorno, $retorno_pesquisa);
            }
        }

        return (array) $retorno;
    }
}
?>