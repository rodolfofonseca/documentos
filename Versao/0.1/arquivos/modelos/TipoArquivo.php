<?php
require_once 'Classes/bancoDeDados.php';

class TipoArquivo
{

    private $id_tipo_arquivo;
    private $id_empresa;
    private $descricao;
    private $tipo_arquivo;
    private $usar;
    private $endereco_documento;

    private function tabela(){
        return (string) 'tipo_arquivo';
    }

    private function modelo(){
        return (array) ['id_tipo_arquivo' => (int) 0, 'id_empresa' => (int) 0, 'descricao' => (string) '', 'tipo_arquivo' => (string) '', 'usar' => (string) 'S', 'endereco_documento' => (string) ''];
    }

    private function colocar_dados($dados){
        if(array_key_exists('id_tipo_arquivo', $dados)){
            $this->id_tipo_arquivo = (int) intval($dados['id_tipo_arquivo'], 10);
        }

        if(array_key_exists('id_empresa', $dados)){
            $this->id_empresa = (int) intval($dados['id_empresa'], 10);
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
        $return_salvar_dados = (bool) false;
        $dados_tipo_arquivo = (array) [];
        
        $this->colocar_dados($dados);

        $check_tipo_arquivo = (bool) model_check($this->tabela(), (array) ['and' => [['tipo_arquivo', '===', (string) $this->tipo_arquivo], ['id_empresa', '===', (int) $this->id_empresa]]]);

        $dados_tipo_arquivo['id_empresa'] = (int) $this->id_empresa;
        $dados_tipo_arquivo['descricao'] = (string) $this->descricao;
        $dados_tipo_arquivo['tipo_arquivo'] = (string) $this->tipo_arquivo;
        $dados_tipo_arquivo['usar'] = (string) $this->usar;
        $dados_tipo_arquivo['endereco_documento'] = (string) $this->endereco_documento;

        if($check_tipo_arquivo == true){
            $dados_tipo_arquivo = (array) model_one($this->tabela(), (array) ['id_tipo_arquivo' => (string) $this->id_tipo_arquivo]);

            if(array_key_exists('id_tipo_arquivo', $dados_tipo_arquivo['id_tipo_arquivo'])){
                $this->tipo_arquivo = (int) $dados_tipo_arquivo['id_tipo_arquivo'];
            }

            $return_salvar_dados = (bool) model_update($this->tabela(), (array) ['id_tipo_arquivo' => (int) $this->tipo_arquivo], (array) model_parse($this->modelo(), $dados_tipo_arquivo));
        }else{         
            $dados_tipo_arquivo['id_tipo_arquivo'] = (int) intval(model_next($this->tabela(), 'id_tipo_arquivo'), 10);
            
            $return_salvar_dados = (bool) model_insert($this->tabela(), (array) model_parse($this->modelo(), $dados_tipo_arquivo));
        }

        return (bool) $return_salvar_dados;
    }

    public function pesquisar($dados){
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    public function pesquisar_todos($filtro){
        return (array) model_all($this->tabela(), $filtro['condicao'], $filtro['ordenacao'], $filtro['limite']);
    }

    /**
     * Função responsável por pesquisar e montar o componente de visualização, onde o usuário pode configurar os tipos de documentos aceitos pela empresa e onde o mesmo deseja que seja salvo.
     */
    public function montar_array_tipo_arquivo()
    {
        $array_padrao_tipo_arquivo = (array) [0 => (array) ['tipo' => (string) '.jpg', 'descricao' => (string) 'IMAGEM'], 1 => (array) ['tipo' => (string) '.doc', 'descricao' => (string) 'WORD'], 2 => (array) ['tipo' => (string) '.odt', 'descricao' => (string) 'WORD'], 3 => (array) ['tipo' => (string) '.rar', 'descricao' => (string) 'WINRAR'], 4 => (array) ['tipo' => (string) '.bin', 'descricao' => (string) 'BIN'], 5 => (array) ['tipo' => (string) '.asta', 'descricao' => (string) 'ASTAH'], 6 => (array) ['tipo' => (string) '.jpeg', 'descricao' => (string) 'IMAGEM'], 7 => (array) ['tipo' => (string) '.docx', 'descricao' => (string) 'WORD'], 8 => (array) ['tipo' => (string) '.pdf', 'descricao' => (string) 'PDF'], 9 => (array) ['tipo' => (string) '.zip', 'descricao' => (string) 'WINRAR'], 10 => (array) ['tipo' => (string) '.png', 'descricao' => (string) 'IMAGEM'], 11 => (array) ['tipo' => (string) '.psd', 'descricao' => (string) 'PHOTOSHOP', ], 12 => (array) ['tipo' => (string) '.xlsx', 'descricao' => (string) 'EXCELL']];

        return (array) $array_padrao_tipo_arquivo;
    }

    public function deletar($dados){
        $this->colocar_dados($dados);

        $return_deletar = (bool) model_delete($this->tabela(), (array) ['id_tipo_arquivo', '===' ,(int) $this->id_tipo_arquivo]);
        
        return (bool) $return_deletar;
    }
}
