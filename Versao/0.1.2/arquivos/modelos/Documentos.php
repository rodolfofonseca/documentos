<?php
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
require_once 'Classes/bancoDeDados.php';
require_once 'Classes/Sistema/db.php';

class Documentos{
    private $id_documento;
    private $id_caixa;
    private $id_tipo_arquivo;
    private $id_organizacao;
    private $nome_documento;
    private $descricao;
    private $endereco;
    private $codigo_barras;
    private $quantidade_downloads;
    private $escolha_usuario;

    private function tabela(){
        return (string) 'documentos';
    }

    /**
     * Função responsável por retornar o nome da tabela de tipos de documentos
     * @return string $tipo_documento
     */
    private function tabela_tipo_arquivo(){
        return (string) 'tipo_arquivo';
    }

    private function modelo(){
        return (array) ['id_documento' => (int) 0, 'id_caixa' => (int) 0, 'id_tipo_arquivo' => (int) 0, 'id_organizacao' => (int) 0, 'nome_documento' => (string) '', 'descricao' => (string) '', 'endereco' => (string) '', 'codigo_barras' => (string) '', 'quantidade_downloads' => (int) 0, 'cloudinary' => (int) 0];
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_documento', $dados) == true){
            $this->id_documento = (int) intval($dados['codigo_documento'], 10);
        }

        if(array_key_exists('codigo_caixa', $dados) == true){
            $this->id_caixa = (int) intval($dados['codigo_caixa'], 10);
        }

        if(array_key_exists('codigo_tipo_arquivo', $dados) == true){
            $this->id_tipo_arquivo = (int) intval($dados['codigo_tipo_arquivo'], 10);
        }

        if(array_key_exists('codigo_organizacao', $dados) == true){
            $this->id_organizacao = (int) intval($dados['codigo_organizacao'], 10);
        }

        if(array_key_exists('nome_documento', $dados) == true){
            $this->nome_documento = (string) strtoupper($dados['nome_documento']);
        }

        if(array_key_exists('descricao', $dados) == true){
            $this->descricao = (string) $dados['descricao'];
        }

        if(array_key_exists('endereco', $dados) == true){
            $this->endereco = (string) $dados['endereco'];
        }

        if(array_key_exists('codigo_barras', $dados) == true){
            $this->codigo_barras = (string) $dados['codigo_barras'];
        }

        if(array_key_exists('quantidade_downloads', $dados) == true){
            $this->quantidade_downloads = (int) intval($dados['quantidade_downloads'], 10);
        }

        if(array_key_exists('tipo_alteracao', $dados) == true){
            $this->escolha_usuario = (string) $dados['tipo_alteracao'];
        }
    }

    /**
     * Função responsável por validar o endereço do documento
     * 
     * Esta função também cria a pasta caso ela não exista.
     * @param array com as informações do banco sobre a extensão do arquivo
     * @return string endereço do documento de acordo com a extensão
     */
    private function return_diretorio($array_retorno){
        $arquivo_configuracao = (array) parse_ini_file('configuracao.ini', true);

        $endereco_documento = (string) '';
        $diretorio = (string) '';
        
        $diretorio = (string) str_replace('\\', '/', __DIR__);
        $diretorio = (string) str_replace('modelos', '', $diretorio);

        if(array_key_exists('descricao', $array_retorno) == true){
            $endereco_documento = (string) $arquivo_configuracao['ENDERECO_DOCUMENTO'][$array_retorno['descricao']];
        }

        if(array_key_exists('id_tipo_arquivo', $array_retorno) == true){
            $this->id_tipo_arquivo = (int) intval($array_retorno['id_tipo_arquivo'], 10);
        }

        if(is_dir($diretorio.$endereco_documento) == false){
            mkdir($diretorio.$endereco_documento, 0777);
        }

        return (string) $diretorio.$endereco_documento;
    }

    /**
     * Função que faz a conexão com o cloudinary e realiza o upload do arquivo
     * @param string nome_temporario do arquivo, na variável files
     * @param string nome_definitivo nome do arquivo para consultar posteriormente
     * @return array array_retorno contendo as chaves para utilizar mais a frente.
     */
    private function upload_arquivos($nome_temporario, $nome_definitivo){
        $arquivo_de_configuracao = (array) ler_arquivo_configuracao('COMPLETO');

        $usar = (string) '1';
        $dns = (string) '';
        $array_retorno = (array) ['url' => (string) '', 'cloudinary' => (int) 1];

        if(array_key_exists('usar', $arquivo_de_configuracao['ARQUIVOS']) == true){
            $usar = (string) $arquivo_de_configuracao['ARQUIVOS']['usar'];
        }

        $dns = (string) $arquivo_de_configuracao['ARQUIVOS']['dns_arquivos_'.$usar];
        Configuration::instance($dns);

        $upload = new UploadApi();
        $retorno = (array) $upload->upload($nome_temporario, ['public_id' => (string) $nome_definitivo, 'user_file_name' => (bool) true, 'overwrite' => (bool) true]);

        if(array_key_exists('url', $retorno) == true){
            $array_retorno['url'] = (string) $retorno['url'];
        }

        $array_retorno['cloudinary'] = (int) intval($usar, 10);

        return (array) $array_retorno;
    }

    /**
     * Função responsável por salvar as informações do arquivo menor de 10 megas na api
     * @param mixed $arquivo
     * @return bool
     */
    private function salvar_api($arquivo){
        $nome_temporario = (string) $arquivo['arquivo']['tmp_name'];
        $nome_atual = (string) $arquivo['arquivo']['name'];
    
        $extensao = (string) strrchr($nome_atual, '.');
        $extensao = (string) strtolower($extensao);
    
        $retorno_extensao = (array) model_one($this->tabela_tipo_arquivo(), ['tipo', '===', (string) $extensao]);
    
        if(array_key_exists('id_tipo_arquivo', $retorno_extensao) == true){
            $this->id_tipo_arquivo = (int) intval($retorno_extensao['id_tipo_arquivo'], 10);
        }
    
        $checar_existencia = (bool) model_check($this->tabela(), ['id_documento', '===', (int) $this->id_documento]);
    
        if ($checar_existencia == false) {
            $this->id_documento = (int) intval(model_next($this->tabela(), 'id_documento'), 10);
        }
    
        $nome_documento = (string) str_pad($this->id_documento, 12, '0', STR_PAD_LEFT);
        $retorno_upload = (array) $this->upload_arquivos($nome_temporario, $nome_documento);
    
        if ($this->escolha_usuario == 'TODOS') {
            if ($checar_existencia == true) {
                return (bool) model_update($this->tabela(), ['id_documento', '===', (int) $this->id_documento], model_parse($this->modelo(), ['id_documento' => (int) $this->id_documento, 'id_caixa' => (int) $this->id_caixa, 'id_tipo_arquivo' => (int) $this->id_tipo_arquivo, 'id_organizacao' => (int) $this->id_organizacao, 'nome_documento' => (string) $this->nome_documento, 'descricao' => (string) $this->descricao, 'endereco' => (string) $retorno_upload['url'], 'codigo_barras' => (string) $this->codigo_barras, 'quantidade_downloads' => (int) $this->quantidade_downloads, 'cloudinary' => (int) $retorno_upload['cloudinary']]));
            } else {
                return (bool) model_insert($this->tabela(), model_parse($this->modelo(), ['id_documento' => (int) $this->id_documento, 'id_caixa' => (int) $this->id_caixa, 'id_tipo_arquivo' => (int) $this->id_tipo_arquivo, 'id_organizacao' => (int) $this->id_organizacao, 'nome_documento' => (string) $this->nome_documento, 'descricao' => (string) $this->descricao, 'endereco' => (string) $retorno_upload['url'], 'codigo_barras' => (string) $this->codigo_barras, 'quantidade_downloads' => (int) $this->quantidade_downloads, 'cloudinary' => (int) $retorno_upload['cloudinary']]));
            }
        } else {
            return (bool) true;
        }

    }

    /**
     * Função responsável por salvar os arquivos maiores de 10 megas no local
     * @param mixed $arquivo
     * @return bool $retorno_salvamento
    */
    private function salvar_local($arquivo){
        $nome_atual = (string) $arquivo['arquivo']['name'];
        $nome_temporario = (string) $arquivo['arquivo']['tmp_name'];
        $extensao = (string) strrchr($nome_atual, '.');
        $extensao = (string) strtolower($extensao);

        $retorno_extensao = (array) model_one($this->tabela_tipo_arquivo(), ['tipo', '===', (string) $extensao]);

        if (empty($retorno_extensao) == false) {
            $endereco_documento = (string) $this->return_diretorio($retorno_extensao);
            $nome_documento = (string) $endereco_documento . str_pad($this->id_documento, 12, '0', STR_PAD_LEFT) . $extensao;

            $checar_existencia = (bool) model_check($this->tabela(), ['id_documento', '===', (int) $this->id_documento]);

            if ($checar_existencia == true) {

                chmod($endereco_documento, 0777);

                if (file_exists($nome_documento) == true) {
                    chmod($nome_documento, 0777);
                    $retorno_exclusao = (bool) @unlink($nome_documento);
                }
            } else {
                $this->id_documento = (int) intval(model_next($this->tabela(), 'id_documento'), 10);
            }

            $nome_documento = (string) $endereco_documento . str_pad($this->id_documento, 12, '0', STR_PAD_LEFT) . $extensao;
            if (@move_uploaded_file($nome_temporario, $nome_documento)) {
                chmod($nome_documento, 0777);

                if ($this->escolha_usuario == 'TODOS') {
                    if ($checar_existencia == true) {
                        return (bool) model_update($this->tabela(), ['id_documento', '===', (int) $this->id_documento], model_parse($this->modelo(), ['id_documento' => (int) $this->id_documento, 'id_caixa' => (int) $this->id_caixa, 'id_tipo_arquivo' => (int) $this->id_tipo_arquivo, 'id_organizacao' => (int) $this->id_organizacao, 'nome_documento' => (string) $this->nome_documento, 'descricao' => (string) $this->descricao, 'endereco' => (string) $nome_documento, 'codigo_barras' => (string) $this->codigo_barras, 'quantidade_downloads' => (int) $this->quantidade_downloads]));
                    } else {
                        return (bool) model_insert($this->tabela(), model_parse($this->modelo(), ['id_documento' => (int) $this->id_documento, 'id_caixa' => (int) $this->id_caixa, 'id_tipo_arquivo' => (int) $this->id_tipo_arquivo, 'id_organizacao' => (int) $this->id_organizacao, 'nome_documento' => (string) $this->nome_documento, 'descricao' => (string) $this->descricao, 'endereco' => (string) $nome_documento, 'codigo_barras' => (string) $this->codigo_barras, 'quantidade_downloads' => (int) $this->quantidade_downloads]));
                    }
                } else {
                    return (bool) true;
                }

            } else {
                return (bool) false;
            }
        }
        return (bool) false;
    }

    public function pesquisar_documentos_todos($dados){
        return (array) model_all($this->tabela(), $dados['filtro'], $dados['ordenacao'], $dados['limite']);
    }

    public function pesquisar_documento($dados){
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    public function salvar($dados, $arquivo){
        $this->colocar_dados($dados);
        
        if($this->escolha_usuario == 'TODOS' || $this->escolha_usuario == 'ARQUIVOS'){
            if (isset($arquivo['arquivo']['name']) && $arquivo['arquivo']) {
                $tamanho_arquivo = (int) $arquivo['arquivo']['size'];
                $retorno = (bool) false;

                $nome_atual = (string) $arquivo['arquivo']['name'];
    
                $extensao = (string) strrchr($nome_atual, '.');
                $extensao = (string) strtolower($extensao);
    
                $retorno_extensao = (array) model_one($this->tabela_tipo_arquivo(), ['tipo', '===', (string) $extensao]);
    
                if(array_key_exists('id_tipo_arquivo', $retorno_extensao) == true){
                    $this->id_tipo_arquivo = (int) intval($retorno_extensao['id_tipo_arquivo'], 10);
                }

                if($tamanho_arquivo < 9961472 && $this->id_tipo_arquivo == 1 || $tamanho_arquivo < 9961472 && $this->id_tipo_arquivo == 2 || $tamanho_arquivo < 9961472 && $this->id_tipo_arquivo == 3 || $tamanho_arquivo < 9961472 && $this->id_tipo_arquivo == 10 || $tamanho_arquivo < 9961472 && $this->id_tipo_arquivo == 11){
                    $retorno = (bool) $this->salvar_api($arquivo);
                }else{
                    $retorno = (bool) $this->salvar_local($arquivo);
                }

                return (bool) $retorno;
            }
        }else if($this->escolha_usuario == 'INFORMACOES'){
            $checar_existencia = (bool) model_check($this->tabela(), ['id_documento', '===', (int) $this->id_documento]);

            if($checar_existencia == true){
                return (bool) model_update($this->tabela(), ['id_documento', '===', (int) $this->id_documento], ['id_documento' => (int) $this->id_documento, 'id_caixa' => (int) $this->id_caixa, 'id_organizacao' => (int) $this->id_organizacao, 'nome_documento' => (string) $this->nome_documento, 'descricao' => (string) $this->descricao, 'codigo_barras' => (string) $this->codigo_barras, 'quantidade_downloads' => (int) $this->quantidade_downloads]);
            }else{
                return (bool) model_insert($this->tabela(), model_parse($this->modelo(), ['id_documento' => (int) $this->id_documento, 'id_caixa' => (int) $this->id_caixa, 'id_tipo_arquivo' => (int) $this->id_tipo_arquivo, 'id_organizacao' => (int) $this->id_organizacao, 'nome_documento' => (string) $this->nome_documento, 'descricao' => (string) $this->descricao, 'codigo_barras' => (string) $this->codigo_barras, 'quantidade_downloads' => (int) $this->quantidade_downloads]));
            }
            //não passar modelo na alteração buga endereço documento
        }else{
            return (bool) false;
        }
    }

    /**
     * Método responsável por adicionar ao documento a quantidade de downloads que o documento teve. esta função retorna as informações do documento para que se possa utilizar em outros locais caso deseje.
     */
    public function update_download($dados){
        $this->colocar_dados($dados);

        $retorno_documento = (array) model_one($this->tabela(), ['id_documento', '===', (int) $this->id_documento]);

        if(empty($retorno_documento) == false){
            $this->quantidade_downloads = (int) 0;

            if(array_key_exists('quantidade_downloads', $retorno_documento) == true){
                $this->quantidade_downloads = (int) intval($retorno_documento['quantidade_downloads'], 10);
            }

            $this->quantidade_downloads++;

            model_update($this->tabela(), ['id_documento', '===', (int) $this->id_documento], ['quantidade_downloads' => (int) $this->quantidade_downloads]);
        }

        return (array) $retorno_documento;
    }

    /**
     * Função responsável por contar quantos documentos tem cadastrados na base de dados, Esta função se faz importante pois os documentos podem ser apagados.
     * Não tendo mais como saber através do maior id
     * 
     * @return int quantidade_documentos
     */
    public function contar_quantidade_documentos(){
        $classe = new DB();
        $retorno = $classe->connect($this->tabela());
        $connection = $classe->connection;
        $options = ['allowDiskUse' => TRUE];
        $pipeline = [['$group' => ['_id' => [], 'COUNT(*)' => ['$sum' => 1]]], ['$project' => ['COUNT(*)' => '$COUNT(*)', '_id' => 0]]];

        $quantidade_documentos = (int) 0;

        $cursor = $connection->aggregate($pipeline, $options);

        foreach($cursor as $objeto){
            foreach($objeto as $quantidade){
                $quantidade_documentos = (int) intval($quantidade, 10);
            }
        }

        return (int) $quantidade_documentos;
    }
}
?>