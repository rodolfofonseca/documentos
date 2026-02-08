<?php
require_once 'Classes/bancoDedados.php';
require_once 'sistema.php';

class Empresa implements InterfaceModelo
{
    private $id_empresa;
    private $nome_empresa;
    private $cnpj;
    private $data_cadastro;
    private $status;

    public function tabela()
    {
        return (string) 'empresas';
    }

    public function modelo()
    {
        return (array) ['_id' => convert_id(''), 'nome_empresa' => (string) '', 'cnpj' => (string) '', 'data_cadastro' => 'date', 'status' => (string) ''];
    }

    public function colocar_dados($dados)
    {
        if (array_key_exists('_id', $dados) == true) {
            $this->id_empresa = (int) convert_id($dados['_id']);
        }

        if (array_key_exists('nome_empresa', $dados) == true) {
            $this->nome_empresa = (string) strtoupper($dados['nome_empresa']);
        } else {
            $this->nome_empresa = (string) '';
        }

        if (array_key_exists('cnpj', $dados) == true) {
            $this->cnpj = (string) strtoupper($dados['cnpj']);
        } else {
            $this->cnpj = (string) '';
        }

        if (array_key_exists('data_cadastro', $dados) == true) {
            $this->data_cadastro = model_date($dados['data_cadastro']);
        } else {
            $this->data_cadastro = model_date();
        }

        if (array_key_exists('status', $dados) == true) {
            $this->status = (string) strtoupper($dados['status']);
        } else {
            $this->status = (string) 'ATIVO';
        }
    }

    public function salvar_dados($dados)
    {
        $retorno_operacao = (bool) false;
        $cadastro_empresa = (bool) false;

        $this->colocar_dados($dados);

        $retorno_check = (bool) model_check((string) $this->tabela(), (array) ['cnpj', '===', (string) $this->cnpj]);

        if ($retorno_check == true) {
            $retorno_operacao = (bool) model_update((string) $this->tabela(), (array) ['cnpj', '===', (string) $this->cnpj], (array) ['nome_empresa' => (string) $this->nome_empresa, 'data_cadastro' => $this->data_cadastro, 'status' => (string) $this->status]);
        } else {
            $retorno_operacao = (bool) model_insert((string) $this->tabela(), (array) ['nome_empresa' => (string) $this->nome_empresa, 'cnpj' => (string) $this->cnpj, 'data_cadastro' => $this->data_cadastro, 'status' => (string) $this->status]);

            if($retorno_operacao == true){
                $cadastro_empresa = (bool) true;
            }
        }

        if($cadastro_empresa == true){
            $retorno_pesquisa = (array) model_one((string) $this->tabela(), (array) ['cnpj', '===', (string) $this->cnpj]);

            if(empty($retorno_pesquisa) == false){
                if(array_key_exists('_id', $retorno_pesquisa) == true){
                    $this->id_empresa = convert_id($retorno_pesquisa['_id']);
                }

                $objeto_sistema = new Sistema();
                $retorno_sistema = (bool) $objeto_sistema->salvar_dados(['codigo_empresa' => $this->id_empresa]);
            }
        }

        return (bool) $retorno_operacao;
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
     * Função responsável por excluir uma empresa cadastrada no banco de dados.
     * @param array $dados array contendo o "codigo_empresa"
     * @return boolean TRUE ou FALSE de acordo com o retorno da função.
     */
    public function excluir($dados)
    {
        $this->colocar_dados($dados);

        return (bool) false;
    }

    /**
     * Verifica se o sistema está dentro do prazo de validade da chave, lembranbdo que as funcionalidades do sistema é vitalícia, o que é pago apenas as funcionalidades de suporte.
     * @param array $dados
     * @return bool
     */
    public function verificar_ativacao($dados)
    {
        $this->colocar_dados($dados);

        return (bool) false;
    }
}
