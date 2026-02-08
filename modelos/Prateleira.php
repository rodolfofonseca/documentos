<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/LogSistema.php';
require_once 'Modelos/Interface.php';
require_once 'Modelos/Armario.php';

class Prateleira implements InterfaceModelo{
    private $id_prateleira;
    private $empresa;
    private $usuario;
    private $armario;
    private $nome_prateleira;
    private $descricao;
    private $codigo_barras;
    private $tipo;
    private $status;
    private $data_cadastro;

    public function tabela(){
        return (string) 'prateleiras';
    }

    public function modelo(){
        return (array) ['_id' => convert_id(''), 'empresa' => convert_id(''), 'usuario' => convert_id(''), 'armario' => convert_id(''), 'nome_prateleira' => (string) '', 'descricao' => (string) '', 'codigo_barras' => (string) codigo_barras(), 'tipo' => (string) 'PUBLICO', 'status' => (string) 'ATIVO', 'data_cadastro' => model_date()];
    }

    public function colocar_dados($dados){
        if(array_key_exists('codigo_prateleira', $dados) == true){
            if($dados['codigo_prateleira'] != ''){
                $this->id_prateleira = convert_id($dados['codigo_prateleira']);
            }else{
                $this->id_prateleira = null;
            }
        }

        if(array_key_exists('codigo_empresa', $dados) == true){
            if($dados['codigo_empresa'] != ''){
                $this->empresa = convert_id($dados['codigo_empresa']);
            }
        }

        if(array_key_exists('codigo_usuario', $dados) == true){
            if($dados['codigo_usuario'] != ''){
                $this->usuario = convert_id($dados['codigo_usuario']);
            }
        }

        if(array_key_exists('codigo_armario', $dados) == true){
            if($dados['codigo_armario'] != ''){
                $this->armario = convert_id($dados['codigo_armario']);
            }
        }

        if(array_key_exists('nome_prateleira', $dados) == true){
            $this->nome_prateleira = (string) strtoupper($dados['nome_prateleira']);
        }else{
            $this->nome_prateleira = (string) '';
        }

        if(array_key_exists('descricao', $dados) == true){
            $this->descricao = (string) $dados['descricao'];
        }else{
            $this->descricao = (string) '';
        }

        if(array_key_exists('codigo_barras', $dados) == true){
            $this->codigo_barras = (string) $dados['codigo_barras'];
        }else{
            $this->codigo_barras = codigo_barras();
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

    /**
     * Função responsável por verificar e executar a função de alterar ou cadastrar
     * @param array dados
     * @return bool opção
     */
    public function salvar_dados($dados){
        $this->colocar_dados($dados);
        $retorno_operacao = (bool) false;
        $objeto_log_sistema = new LogSistema();

        if($this->id_prateleira != null){
            $retorno_checagem = (bool) model_check((string) $this->tabela(), (array) ['_id', '===', $this->id_prateleira]);

            if($retorno_checagem == true){
                $retorno_operacao = (bool) model_update((string) $this->tabela(), (array) ['_id', '===', $this->id_prateleira], (array) ['armario' => $this->armario, 'nome_prateleira' => (string) $this->nome_prateleira, 'descricao' => (string) $this->descricao, 'tipo' => (string) $this->tipo, 'status' => (string) $this->status]);

                if($retorno_operacao == true){
                    $retorno_log_sistema = (bool) $objeto_log_sistema->salvar_dados((array) ['id_empresa' => (string) $this->empresa, 'usuario' => (string) $this->usuario, 'tabla_acao' => (string) 'ALTERACAO', 'modulo' => (string) 'PRATELEIRA', 'descricao' => (string) 'Usuário alterou o cadastro da prateleira '.$this->nome_prateleira]);
                }
            }else{
                $retorno_operacao = (bool) false;
            }
        }else{
            $retorno_operacao = (bool) model_insert((string) $this->tabela(), (array) ['empresa' => $this->empresa, 'usuario' => $this->usuario, 'armario' => $this->armario, 'nome_prateleira' => (string) $this->nome_prateleira, 'descricao' => (string) $this->descricao, 'codigo_barras' => (string) $this->codigo_barras, 'tipo' => (string) $this->tipo, 'status' => (string) $this->status, 'data_cadastro' => $this->data_cadastro]);

            if($retorno_operacao == true){
                $retorno_log_sistema = (bool) $objeto_log_sistema->salvar_dados((array) ['id_empresa' => (string) $this->empresa, 'usuario' => (string) $this->usuario, 'tabla_acao' => (string) 'CADASTRAR', 'modulo' => (string) 'PRATELEIRA', 'descricao' => (string) 'Usuário cadastrou a prateleira '.$this->nome_prateleira]);
            }
        }

        return (bool) $retorno_operacao;
    }

    /**
     * Função responsável por retornar as informações de apenas uma prateleira, de acordo com os parâmetros de filtros passados
     * @param array informações para pesquisadas ['filtro'];
     * @return array com as informações pesquisadas
     */
    public function pesquisar($dados){
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    /**
     * Função responsável por pesquisar todas as informações das prateleiras cadastradas na base de dados de acordo com os parâmetros passados
     * @param array ['filtro', 'ordenacao', 'limite']
     * @param array com o retorno
     */
    public function pesquisar_todos($dados){
        return (array) model_all($this->tabela(), $dados['filtro'], $dados['ordenacao'], $dados['limite']);
    }

    /**
     * Função que valida o retorno do filtro e adiciona o norme do armário.
     * @param (array) $retono_pesquisa retorno que vem do banco de dados
     * @param (array) $retorno vazio, que será validado
     * @return (array) $retorno corrigido
     */
    public function validar_dados_pesquisa_prateleira($retorno_pesquisa, $retorno){
        $objeto_armario = new Armario();

        if(empty($retorno_pesquisa) == false){
            foreach($retorno_pesquisa as $retorno_corrigido){
                if(array_key_exists('armario', $retorno_corrigido) == true){
                    $filtro_armario = (array) ['filtro' => (array) ['_id', '===', convert_id($retorno_corrigido['armario'])]];

                    $retorno_armario = (array) $objeto_armario->pesquisar($filtro_armario);

                    if(empty($retorno_armario) == false){
                        if(array_key_exists('nome_armario', $retorno_armario) == true){
                            $retorno_corrigido['nome_armario'] = (string) $retorno_armario['nome_armario'];
                        }else{
                            $retorno_corrigido['nome_armario'] = (string) '';
                        }
                    }else{
                        $retorno_corrigido['nome_armario'] = (string) '';
                    }

                    array_push($retorno, $retorno_corrigido);
                }
            }
        }

        return (array) $retorno;
    }
}
?>