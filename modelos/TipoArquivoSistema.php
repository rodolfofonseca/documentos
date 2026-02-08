<?php
require_once 'Classes/bancoDeDados.php';
require_once 'modelos/Interface.php';

class TipoArquivoSistema implements InterfaceModelo
{
    private $id_tipo_arquivo;
    private $nome_tipo_arquivo;
    private $tipo;
    private $status;

    public function tabela()
    {
        return (string) 'tipo_arquivo_sistema';
    }

    public function colocar_dados($dados)
    {
        if(array_key_exists('codigo_tipo_arquivo', $dados) == true){
            if($dados['tipo_arquivo'] != ''){
                $this->id_tipo_arquivo = convert_id($dados['codigo_tipo_arquivo']);
            }else{
                $this->id_tipo_arquivo = null;
            }
        }else{
            $this->id_tipo_arquivo = null;
        }

        if(array_key_exists('nome_tipo_arquivo', $dados) == true){
            $this->nome_tipo_arquivo = (string) strtoupper($dados['nome_tipo_arquivo']);
        }else{
            $this->nome_tipo_arquivo = (string) '';
        }

        if(array_key_exists('tipo', $dados) == true){
            $this->tipo = (string) $dados['tipo'];
        }else{
            $this->tipo = (string) '';
        }

        if(array_key_exists('status', $dados) == true){
            $this->status = (string) strtoupper($dados['status']);
        }else{
            $this->status = (string) 'ATIVO';
        }
    }

    public function salvar_dados($dados)
    {
        $this->colocar_dados($dados);

        if($this->id_tipo_arquivo != null){
            return (bool) model_update((string) $this->tabela(), (array) ['_id', '===', convert_id($this->id_tipo_arquivo)], (array) ['nome_tipo_arquivo' => (string) $this->nome_tipo_arquivo, 'tipo' => (string) $this->tipo, 'status' => (string) $this->status]);
        }else{
            return (bool) model_insert((string) $this->tabela(), (array) ['nome_tipo_arquivo' => (string) $this->nome_tipo_arquivo, 'tipo' => (string) $this->tipo, 'status' => (string) $this->status]);
        }
    }
    public function pesquisar($filtro)
    {
        return (array) model_one((string) $this->tabela(), (array) $filtro['filtro']);
    }

    public function pesquisar_todos($filtro)
    {
        return (array) model_all((string) $this->tabela(), (array) $filtro['filtro'], (array) $filtro['ordenacao'], (int) $filtro['limite']);
    }
}
