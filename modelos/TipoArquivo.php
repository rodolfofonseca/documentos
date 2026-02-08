<?php
require_once 'Classes/bancoDeDados.php';
require_once 'modelos/Interface.php';

class TipoArquivo
{

    private $id_tipo_arquivo;
    private $empresa;
    private $descricao;
    private $tipo_arquivo;
    private $usar;
    private $endereco_documento;

    public function tabela(){
        return (string) 'tipo_arquivo';
    }

    public function colocar_dados($dados){
        if(array_key_exists('id_tipo_arquivo', $dados)){
            if($dados['id_tip_arquivo'] != ''){
                $this->id_tipo_arquivo = (string) convert_id($dados['id_tipo_arquivo']);
            }else{
                $this->id_tipo_arquivo = null;
            }
        }else{
            $this->id_tipo_arquivo = null;
        }

        if(array_key_exists('id_empresa', $dados)){
            if($dados['id_empresa'] != ''){
                $this->empresa = (string) convert_id($dados['id_empresa']);
            }
        }

        if(array_key_exists('descricao', $dados)){
            $this->descricao = (string) strtoupper($dados['descricao']);
        }

        if(array_key_exists('tipo_arquivo', $dados)){
            $this->tipo_arquivo = (string) strtoupper($dados['tipo_arquivo']);
        }

        if(array_key_exists('usar', $dados)){
            $this->usar = (string) strtoupper($dados['usar']);
        }else{
            $this->usar = (string) 'S';
        }

        if(array_key_exists('endereco_documento', $dados)){
            $this->endereco_documento = (string) $dados['endereco_documento'];
        }
    }

    public function salvar_dados($dados){        
        $this->colocar_dados($dados);

        if($this->id_tipo_arquivo != null){
            return (bool) model_update((string) $this->tabela(), ['_id', '===', convert_id($this->id_tipo_arquivo)], (array) ['descricao' => (string) $this->descricao, 'tipo_arquivo' => (string) $this->tipo_arquivo, 'usar' => (string) $this->usar, 'endereco_documento' => (string) $this->endereco_documento]);
        }else{
            return (bool) model_insert((string) $this->tabela(), (array) ['empresa' => convert_id($this->empresa), 'descricao' => (string) $this->descricao, 'tipo_arquivo' => (string) $this->tipo_arquivo, 'usar' => (string) $this->usar, 'endereco_documento' => (string) $this->endereco_documento]);
        }
    }

    public function pesquisar($dados){
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    public function pesquisar_todos($filtro){
        return (array) model_all($this->tabela(), $filtro['condicao'], $filtro['ordenacao'], $filtro['limite']);
    }

    public function deletar($dados){
        $this->colocar_dados($dados);

        $return_deletar = (bool) model_delete($this->tabela(), (array) ['_id', '===' , convert_id($this->id_tipo_arquivo)]);
        
        return (bool) $return_deletar;
    }
}
