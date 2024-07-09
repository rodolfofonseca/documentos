<?php
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Admin\AdminApi;
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
        $retorno_validacao_ja_existe_empresa = (bool) model_check((string) $this->tabela(), (array) ['and' => (array) [(array) ['id_empresa', '===', (int) $this->id_empresa], (array) ['dns', '===', (string) $this->dns]]]);

        if($retorno_validacao_ja_existe_empresa == true){
            return (bool) false;
        }

        if($this->id_cloudinary != 0){
            model_update($this->tabela(), ['id_cloudinary', '===', (int) $this->id_cloudinary], model_parse($this->modelo(), ['id_cloudinary' => (int) $this->id_cloudinary, 'id_empresa' => (int) $this->id_empresa, 'dns' => (string) $this->dns, 'usar' => (string) $this->usar]));

            return (bool) $this->alterar_tipo_cloudinary((array) []);
        }else{
            model_insert($this->tabela(), model_parse($this->modelo(), ['id_cloudinary' => (int) model_next((string) $this->tabela(), (string) 'id_cloudinary', (array) ['id_empresa', '===', (int) $this->id_empresa]), 'id_empresa' => (int) $this->id_empresa, 'dns' => (string) $this->dns, 'usar' => (string) $this->usar]));

            $retorno_pesquisa = (array) model_one((string) $this->tabela(), (array) ['and' => (array) [(array) ['id_empresa', '===', (int) $this->id_empresa], (array) ['dns', '===', (string) $this->dns]]]);

            $this->colocar_dados($retorno_pesquisa);
            
            return (bool) $this->alterar_tipo_cloudinary((array) []);
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
     * Função responsável por realizar o upload do arquivo, quando o mesmo for salvo no cloudinary
     * @param array $dados
     * @param string $codigo_barras
     * @param string $extensao
     * @param file $arquivo
     * @return array $dados
     */
    public function upload_arquivo($dados, $codigo_barras, $extensao, $arquivo){
        $this->colocar_dados($dados);

        $array_retorno = (array) ['url' => (string) '', 'cloudinary' => (int) 0, 'status' => (bool) false];

        $dados_cloudinary_ativo = (array) model_one($this->tabela(), (array) ['and' => (array) [(array) ['id_empresa', '===', (int) $this->id_empresa], (array) ['usar', '===', (string) 'S']]]);

        if(empty($dados_cloudinary_ativo) == false){
            if(array_key_exists('dns', $dados_cloudinary_ativo) == true){
                $this->dns = (string) $dados_cloudinary_ativo['dns'];
            }
            
            if(array_key_exists('id_cloudinary', $dados_cloudinary_ativo) == true){
                $this->id_cloudinary = (int) $dados_cloudinary_ativo['id_cloudinary'];
                $array_retorno['cloudinary'] = (int) intval($dados_cloudinary_ativo['id_cloudinary'], 10);
            }
    
            Configuration::instance($this->dns);
            $upload = new UploadApi();
            
            $retorno = (array) $upload->upload($arquivo['arquivo']['tmp_name'], ['public_id' => (string) $codigo_barras.$extensao, 'user_file_name' => (bool) true, 'overwrite' => (bool) true]);
    
            if(array_key_exists('url',  $retorno) == true){
                $array_retorno['url'] = (string) $retorno['url'];
            }

            $array_retorno['status'] = (bool) true;
        }

        return (array) $array_retorno;
    }

    /**
     * Função responsável por realizar a exclusão do arquivo no cloudinary
     * @param string $nome_arquivo Nome do documento, com  extesão
     * @param int $cloudinary identificador do cloudinary na base de dados
     * @return bool retornar true ou false de acordo com o resultado da funcionalidade.
     */
    public function deletar_documento($nome_arquivo, $cloudinary){
        $retorno_cloudinary = (array) model_one($this->tabela(), ['id_cloudinary', '===', (int) intval($cloudinary, 10)]);

        if(empty($retorno_cloudinary) == false){
            if(array_key_exists('dns', $retorno_cloudinary) == true){
                $this->dns = (string) $retorno_cloudinary['dns'];
            }

            Configuration::instance($this->dns);
            $admin = new AdminApi();
            return (bool) $admin->deleteAssets((string) $nome_arquivo);
        }else{
            return (bool) false;
        }
    }

    /**
     * Função responsável por realizar a checagem se possui algum cloudinary no sistema como S antees de colocar outro.
     * Por padrão apenas um cloudinary no sistema pode estar ativo como S.
     * @param array $dados contendo as informações que serão trabalhadas.
     * @return bool TRUE ou FALSE de acordo com o resultado da função.
     */
    public function alterar_tipo_cloudinary($dados){

        if(empty($dados) == false){
            $this->colocar_dados($dados);
        }

        if($this->usar == 'S'){
            $retorno_alteracao_massiva = (bool) model_update($this->tabela(), (array) ['id_empresa', '===', (int) $this->id_empresa], (array) ['usar' => (string) 'N']);

            if($retorno_alteracao_massiva == true){
                return (bool) model_update($this->tabela(), (array) ['and' => (array) [(array) ['id_empresa', '===', (int) $this->id_empresa], (array) ['id_cloudinary', '===', (int) $this->id_cloudinary]]], (array) ['usar' => (string) 'S']);
            }else{
                return (bool) false;
            }
        }else{
            return (bool) model_update($this->tabela(), (array) ['and' => (array) [(array) ['id_empresa', '===', (int) $this->id_empresa], (array) ['id_cloudinary', '===', (int) $this->id_cloudinary]]], (array) ['usar' => (string) 'N']);
        }
    }

    /**
     * função responsável por excluir o cloudinary da base de dados. Uma vez realizado a operação de exclusão, não tem como ativar novamente.
     * @param array $dados do cloudinary a ser excluido
     * @return bool TRUE ou FALSE de acordo com o resultado da função.
     */
    public function deletar_cloudinary($dados){
        $this->colocar_dados($dados);

        return (bool) model_delete((string) $this->tabela(), (array) ['and' => (array) [(array) ['id_empresa', '===', (int) $this->id_empresa], (array) ['id_cloudinary', '===', (int) $this->id_cloudinary]]]);
    }

    //cloudinary://266345532352813:HgYJP-tuYqUVBk-i9Eo9LBrPi2c@dptzzccb6?secure=true     N
    //cloudinary://553346733577561:KZcRLgJyqU7UtPv_h5aMpNcFKi8@dw5jerbyf?secure=true     S
}
?>