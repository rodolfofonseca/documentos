<?php
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;

require_once 'Classes/bancoDeDados.php';
require_once 'Classes/Sistema/db.php';

class Cloudinary{
    private $id_cloudinary;
    private $id_empresa;
    private $dns;
    private $usar;

    private function tabela(){
        return (string) 'cloudinary';
    }

    private function modelo(){
        return (array) ['id_cloudinary' => (int) 0, 'id_empresa' => (int) 0, 'dns' => (string)'', 'usar' => (string) ''];
    }

    private function colocar_dados($dados){
        if(array_key_exists('id_cloudinary', $dados) == true){
            $this->id_cloudinary = (int) intval($dados['id_cloudinary'], 10);
        }else{
            $this->id_cloudinary = (int) 0;
        }

        if(array_key_exists('id_empresa', $dados) == true){
            $this->id_empresa = (int) intval($dados['id_empresa'], 10);
        }else{
            $this->id_empresa = (int) 0;
        }

        if(array_key_exists('dns', $dados) == true){
            $this->dns = (string) $dados['dns'];
        }else{
            $this->dns = (string) '';
        }

        if(array_key_exists('usar', $dados) == true){
            $this->usar = (string) strtoupper($dados['usar']);
        }else{
            $this->usar = (string) 'N';
        }
    }

    public function salvar_dados($dados){
        $this->colocar_dados($dados);

        if($this->id_cloudinary != 0){
            return (bool) model_update($this->tabela(), ['id_cloudinary', '===', (int) $this->id_cloudinary], model_parse($this->modelo(), ['id_cloudinary' => (int) $this->id_cloudinary, 'id_empresa' => (int) $this->id_empresa, 'dns' => (string) $this->dns, 'usar' => (string) $this->usar]));
        }else{
            return (bool) model_insert($this->tabela(), model_parse($this->modelo(), ['id_cloudinary' => (int) model_next($this->tabela(), 'id_cloudinary'), 'id_empresa' => (int) $this->id_empresa, 'dns' => (string) $this->dns, 'usar' => (string) $this->usar]));
        }
    }

    public function pesquisar($dados){
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    public function pesqusiar_todos($dados){
        return (array) model_all($this->tabela(), $dados['filtro'], $dados['ordenacao'], $dados['limite']);
    }

    public function deletar($dados){
        return (bool) model_delete($this->tabela(), $dados['filtro']);
    }

    /**
     * Função responsável por realizar a conexão com o cloudinary previamente configurado e realizar o upload do arquivo.
     * @param (array) $dados
     */
    private function upload_arquivo_cloudinary($dados){
        $return_url = (string) '';

        $filtro_pesquisa_cloudinary = (array) ['filtro' => (array) ['and' => (array) [(array) ['id_empresa', '===', (int) $this->id_empresa], (array) ['usar', '===', (string) 'S']]]];

        $retorno_cloudinary = (array) $this->pesquisar($filtro_pesquisa_cloudinary);

        if(array_key_exists('dns', $retorno_cloudinary) == true){
            $this->dns = $retorno_cloudinary['dns'];
        }

        if($this->dns != ''){
            Configuration::instance($this->dns);
            $upload = new UploadApi();

            $retorno_upload = (array) $upload->upload($dados['nome_temporario'], (array) ['public_id' => (string) $dados['nome_definitivo'], 'user_file_name' => (bool) true, 'overwrite' => (bool) true]);

            if(array_key_exists('url', $retorno_upload) == true){
                $return_url = (String) $retorno_upload['url'];
            }
        }
        
        return (string) $return_url;
    }

    public function upload_arquivo($dados, $arquivo){
        $this->colocar_dados($dados);

        $array_upload = (array) ['nome_temporario' => (string) '', 'nome_definitivo' => (string) ''];
        $nome_atual = (string) '';
        $id_documento = (int) 0;

        $array_upload['nome_temporario'] = (string) $arquivo['arquivo']['tmp_name'];
        $nome_atual = (string) $arquivo['arquivo']['name'];
        
        $extensao = (string) strtoupper(strrchr($nome_atual, '.'));

        $filtro_pesquisa_tipo_arquivo = (array) [(array) ['and' => (array) [(array) ['id_empresa', '===', (int) $this->id_empresa], (array) ['tipo_arquivo', '===', (string) $extensao], (array) ['usar', '===', (string) 'S']]]];

        if(empty($filtro_pesquisa_tipo_arquivo) == false){
            $objeto_documento = new Documentos();
            $filtro_pesquisa_existencia_documento = (array) ['and' => [(array) ['id_empresa', '===', (int) $this->id_empresa], (array) ['id_documento', '===', (int) $dados['id_documento']]]];

            $retorno_existencia_documento = (bool) $objeto_documento->checar_existencia_documento($filtro_pesquisa_existencia_documento);

            if($retorno_existencia_documento == false){
                $id_documento = (int) intval($objeto_documento->proximo_id_documento(['id_empresa' => $this->id_empresa]), 10);
            }

            $nome_documento = (string) $objeto_documento->formatar_nome_documento($id_documento);
            $retorno_upload = (array) $this->upload_arquivo_cloudinary((array) ['nome_temporario' => (string) $array_upload['nome_temporario'], 'nome_documento' => (string) $nome_documento]);

            //PAREI AQUI, NA LINHA 162 DO ARQUIVO DOCUMENTOS, ONDE EU PEGAR O IDENTIFICADOR DO USUÁRIO QUE IRA VIR ATRAVES DA VARIAÇÃO DE SESSAO POR MEIO DO REQUEST.
        }
    }
}
?>