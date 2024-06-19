<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Classes/Sistema/db.php';

require_once 'modelos/TipoArquivo.php';
require_once 'modelos/Cloudinary.php';

class Documentos
{
    private $id_documento;
    private $id_empresa;
    private $id_caixa;
    private $id_tipo_arquivo;
    private $id_organizacao;
    private $id_usuario;
    private $nome_documento;
    private $descricao;
    private $endereco;
    private $codigo_barras;
    private $quantidade_downloads;
    private $cloudinary;
    private $data_cadastro;
    private $data_alteracao;
    private $escolha_usuario;

    private function tabela()
    {
        return (string) 'documentos';
    }

    private function modelo()
    {
        return (array) ['id_documento' => (int) 0, 'id_empresa' => (int) 0,'id_caixa' => (int) 0, 'id_tipo_arquivo' => (int) 0, 'id_organizacao' => (int) 0, 'id_usuario' => (int) 0, 'nome_documento' => (string) '', 'descricao' => (string) '', 'endereco' => (string) '', 'codigo_barras' => (string) '', 'quantidade_downloads' => (int) 0, 'cloudinary' => (int) 0, 'data_cadastro' => 'date', 'data_alteracao' => 'date'];
    }

    private function colocar_dados($dados)
    {
        if (array_key_exists('codigo_documento', $dados) == true) {
            $this->id_documento = (int) intval($dados['codigo_documento'], 10);
        }

        if(array_key_exists('codigo_empresa', $dados) == true){
            $this->id_empresa = (int) intval($dados['codigo_empresa'], 10);
        }

        if (array_key_exists('codigo_caixa', $dados) == true) {
            $this->id_caixa = (int) intval($dados['codigo_caixa'], 10);
        }

        if (array_key_exists('codigo_tipo_arquivo', $dados) == true) {
            $this->id_tipo_arquivo = (int) intval($dados['codigo_tipo_arquivo'], 10);
        }
        
        if (array_key_exists('codigo_usuario', $dados) == true) {
            $this->id_usuario = (int) $dados['codigo_usuario'];
        }
        
        if (array_key_exists('codigo_organizacao', $dados) == true) {
            $this->id_organizacao = (int) intval($dados['codigo_organizacao'], 10);
        }

        if (array_key_exists('nome_documento', $dados) == true) {
            $this->nome_documento = (string) strtoupper($dados['nome_documento']);
        }

        if (array_key_exists('descricao', $dados) == true) {
            $this->descricao = (string) $dados['descricao'];
        }

        if (array_key_exists('endereco', $dados) == true) {
            $this->endereco = (string) $dados['endereco'];
        }

        if (array_key_exists('codigo_barras', $dados) == true) {
            $this->codigo_barras = (string) $dados['codigo_barras'];
        }

        if (array_key_exists('quantidade_downloads', $dados) == true) {
            $this->quantidade_downloads = (int) intval($dados['quantidade_downloads'], 10);
        }

        if(array_key_exists('cloudinary', $dados) == true){
            $this->cloudinary = (int) intval($dados['cloudinary'], 10);
        }else{
            $this->cloudinary = (int) intval(0, 10);
        }

        if (array_key_exists('tipo_alteracao', $dados) == true) {
            $this->escolha_usuario = (string) $dados['tipo_alteracao'];
        }

    }

    /**
     * Função responsável por salvar os arquivos maiores de 10 megas no local
     * @param mixed $arquivo
     * @return bool $retorno_salvamento
     */
    private function salvar_local($arquivo, $extensao)
    {
        $nome_documento = (string) $this->endereco.$this->codigo_barras.$extensao;
        $checar_existencia = (bool) model_check($this->tabela(), ['codigo_barras', '===', (string) $this->codigo_barras]);

        if($checar_existencia == true){
            chmod($this->endereco, 0777);
            if(file_exists($nome_documento) == true){
                chmod($nome_documento, 0777);
                $retorno_exclusao = (bool) @unlink($nome_documento);
            }
        }

        $this->endereco = (string) $nome_documento;

        if(@move_uploaded_file((string) $arquivo['arquivo']['tmp_name'], $nome_documento)){
            chmod($nome_documento, 0777);
            return (bool) true;
        }else{
            return (bool) false;
        }
    }

    public function pesquisar_documentos_todos($dados)
    {
        return (array) model_all($this->tabela(), $dados['filtro'], $dados['ordenacao'], $dados['limite']);
    }

    public function pesquisar_documento($dados)
    {
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    public function salvar_dados($dados, $arquivo){
         $this->colocar_dados($dados);
        
        if($this->escolha_usuario == 'INFORMACOES'){
            $checar_existencia = (bool) model_check($this->tabela(), ['id_documento', '===', (int) $this->id_documento]);
            
            if($checar_existencia == true){
                $pesquisa_documento = (array) model_one($this->tabela(), ['id_documento', '===', (int) $this->id_documento]);
                
                if(empty($pesquisa_documento) == false){
                    $retorno_pesquisa_documento = (array) model_one($this->tabela(), ['codigo_barras', '===', (string) $this->codigo_barras]);

                    return (bool) model_update($this->tabela(), (array) ['codigo_barras', '===', (string) $this->codigo_barras], (array) ['id_documento' => (int) $this->id_documento, 'id_empresa' => (int) $this->id_empresa, 'id_caixa' => (int) $this->id_caixa, 'id_tipo_arquivo' => (int) $this->id_tipo_arquivo, 'id_organizacao' => (int) $this->id_organizacao, 'id_usuario' => (int) $this->id_usuario, 'nome_documento' => (string) $this->nome_documento, 'descricao' => (string) $this->descricao, 'codigo_barras' => (string) $this->codigo_barras, 'quantidade_downloads' => (int) $retorno_pesquisa_documento['quantidade_downloads'], 'data_cadastro' => model_date($retorno_pesquisa_documento['data_cadastro']), 'data_alteracao' => model_date()]);
                }else{
                    return (bool) model_insert($this->tabela(), (array) model_parse($this->modelo(), (array) ['id_documento' => (int) intval(model_next($this->tabela(), 'id_documento')), 'id_empresa' => (int) $this->id_empresa, 'id_caixa' => (int) $this->id_caixa, 'id_tipo_arquivo' => (int) $this->id_tipo_arquivo, 'id_organizacao' => (int) $this->id_organizacao, 'id_usuario' => (int) $this->id_usuario, 'nome_documento' => (string) $this->nome_documento, 'descricao' => (string) $this->descricao, 'endereco' => (string) $this->endereco, 'codigo_barras' => (string) $this->codigo_barras, 'quantidade_downloads' => (int) 0, 'data_cadastro' => model_date(), 'data_alteracao' => model_date()]));
                }
            }
        }else{
            if(isset($arquivo['arquivo']['name']) && $arquivo['arquivo']){
                $tamanho_arquivo = (int) $arquivo['arquivo']['size'];
                $retorno_salvamento_arquivo = (bool) false;
                
                $nome_atual = (string) $arquivo['arquivo']['name'];
                
                $extensao = (string) strrchr($nome_atual, '.');
                $extensao = (string) strtolower($extensao);
                
                $objeto_tipo_arquivo = new TipoArquivo();

                
                $retorno_extensao = (array) $objeto_tipo_arquivo->pesquisar((array) ['filtro' => (array) ['and' => (array) [(array) ['tipo_arquivo', '===', (string) strtoupper($extensao)], (array) ['id_empresa', '===', (int) intval($this->id_empresa, 10)], (array) ['usar', '===', (string) 'S']]]]);

                if(empty($retorno_extensao) == true){
                    return false;
                }
                
                if (array_key_exists('id_tipo_arquivo', $retorno_extensao) == true) {
                    $this->id_tipo_arquivo = (int) intval($retorno_extensao['id_tipo_arquivo'], 10);
                }
                
                if(array_key_exists('endereco_documento', $retorno_extensao) == true){
                    $this->endereco = (string) $retorno_extensao['endereco_documento'];
                }

                if($tamanho_arquivo < 9961472 && $extensao == '.pdf' || $tamanho_arquivo < 9961472 && $extensao == '.psd'){
                    $retorno_salvamento_arquivo = (bool) $this->salvar_api($arquivo, $extensao);

                    if($retorno_salvamento_arquivo == false){
                        if (array_key_exists('id_tipo_arquivo', $retorno_extensao) == true) {
                            $this->id_tipo_arquivo = (int) intval($retorno_extensao['id_tipo_arquivo'], 10);
                        }
                        
                        if(array_key_exists('endereco_documento', $retorno_extensao) == true){
                            $this->endereco = (string) $retorno_extensao['endereco_documento'];
                        }

                        $retorno_salvamento_arquivo = (bool) $this->salvar_local($arquivo, $extensao);
                    }
                }else{
                    $retorno_salvamento_arquivo = (bool) $this->salvar_local($arquivo, $extensao);
                }

                if($this->escolha_usuario != 'ARQUIVOS'){
                    if($retorno_salvamento_arquivo == true){
                        $checar_existencia_arquivo = (bool) model_check($this->tabela(), (array) ['id_documento', '===', (int) intval($this->id_documento, 10)]);
        
                        if($checar_existencia_arquivo == true){
                            $retorno_pesquisa_documento = (array) model_one($this->tabela(), ['codigo_barras', '===', (string) $this->codigo_barras]);
    
                            if(empty($retorno_pesquisa_documento) == false){
                                return (bool) model_update($this->tabela(), (array) ['codigo_barras', '===', (string) $this->codigo_barras], model_parse($this->modelo(), ['id_documento' => (int) $this->id_documento, 'id_empresa' => (int) $this->id_empresa, 'id_caixa' => (int) $this->id_caixa, 'id_tipo_arquivo' => (int) $this->id_tipo_arquivo, 'id_organizacao' => (int) $this->id_organizacao, 'id_usuario' => (int) $this->id_usuario, 'nome_documento' => (string) $this->nome_documento, 'descricao' => (string) $this->descricao, 'endereco' => (string) $this->endereco, 'codigo_barras' => (string) $this->codigo_barras, 'quantidade_downloads' => (int) $retorno_pesquisa_documento['quantidade_downloads'], 'cloudinary' => (int) $retorno_pesquisa_documento['cloudinary'], 'data_cadastro' => model_date($retorno_pesquisa_documento['data_cadastro']), 'data_alteracao' => model_date()]));
                            }else{
                                return (bool) false;
                            }
                        }else{
                            return (bool) model_insert($this->tabela(), (array) model_parse($this->modelo(), (array) ['id_documento' => (int) intval(model_next($this->tabela(), 'id_documento')), 'id_empresa' => (int) $this->id_empresa, 'id_caixa' => (int) $this->id_caixa, 'id_tipo_arquivo' => (int) $this->id_tipo_arquivo, 'id_organizacao' => (int) $this->id_organizacao, 'id_usuario' => (int) $this->id_usuario, 'nome_documento' => (string) $this->nome_documento, 'descricao' => (string) $this->descricao, 'endereco' => (string) $this->endereco, 'codigo_barras' => (string) $this->codigo_barras, 'quantidade_downloads' => (int) 0, 'cloudinary' => (int) $this->cloudinary, 'data_cadastro' => model_date(), 'data_alteracao' => model_date()]));
                        }
                    }else{
                        return (bool) false;
                    }
                }else{
                    return (bool) true;
                }
            }
        }
    }

    /**
     * Método responsável por adicionar ao documento a quantidade de downloads que o documento teve. esta função retorna as informações do documento para que se possa utilizar em outros locais caso deseje.
     */
    public function update_download($dados)
    {
        $this->colocar_dados($dados);

        $retorno_documento = (array) model_one($this->tabela(), ['id_documento', '===', (int) $this->id_documento]);

        if (empty($retorno_documento) == false) {
            $this->quantidade_downloads = (int) 0;

            if (array_key_exists('quantidade_downloads', $retorno_documento) == true) {
                $this->quantidade_downloads = (int) intval($retorno_documento['quantidade_downloads'], 10);
            }

            $this->quantidade_downloads++;

            $retorno_documento['quantidade_downloads'] = (int) $this->quantidade_downloads;
            model_update($this->tabela(), ['id_documento', '===', (int) $this->id_documento], model_parse($this->modelo(), $retorno_documento));
        }

        return (array) $retorno_documento;
    }

    /**
     * Função responsável por contar quantos documentos tem cadastrados na base de dados, Esta função se faz importante pois os documentos podem ser apagados.
     * Não tendo mais como saber através do maior id
     * 
     * @return int quantidade_documentos
     */
    public function contar_quantidade_documentos()
    {
        $classe = new DB();
        $retorno = $classe->connect($this->tabela());
        $connection = $classe->connection;
        $options = ['allowDiskUse' => TRUE];
        $pipeline = [['$group' => ['_id' => [], 'COUNT(*)' => ['$sum' => 1]]], ['$project' => ['COUNT(*)' => '$COUNT(*)', '_id' => 0]]];

        $quantidade_documentos = (int) 0;

        $cursor = $connection->aggregate($pipeline, $options);

        foreach ($cursor as $objeto) {
            foreach ($objeto as $quantidade) {
                $quantidade_documentos = (int) intval($quantidade, 10);
            }
        }

        return (int) $quantidade_documentos;
    }

    private function salvar_api($arquivo, $extensao){
        $dados = (array) ['id_empresa' => (int) $this->id_empresa, 'usar' => (string) 'S'];
        $retorno_cloudinary = (array) [];
        $status = (bool) false;
        
        $cloudinary = new Cloudinary();
        $retorno_cloudinary = (array) $cloudinary->upload_arquivo($dados, $this->codigo_barras, $extensao,$arquivo);

        if(array_key_exists('url', $retorno_cloudinary) == true){
            $this->endereco = (string) $retorno_cloudinary['url'];
        }

        if(array_key_exists('cloudinary', $retorno_cloudinary) == true){
            $this->cloudinary = (int) intval($retorno_cloudinary['cloudinary']);
        }

        if(array_key_exists('status', $retorno_cloudinary) == true){
            $status = (bool) $retorno_cloudinary['status'];
        }

        return (bool) $status;
    }

    /**
     * Função responsável por deletar o arquivo fisico e dado banco de dados, para deletar o arquivo o mesmo deve existir virtualmente ou no cloudinary ou no sistema local.
     * @param array $dados variável request contento o identificador do documento.
     * @param return true ou false de acordo com as informações provenientes das funcionalidades do sistema.
     */
    public function excluir_documento($dados){
        $this->colocar_dados($dados);

        $retorno_pesquisa_documento = (array) model_one($this->tabela(), ['id_documento', '===', (int) intval($this->id_documento, 10)]);

        if(empty($retorno_pesquisa_documento) == false){
            if(array_key_exists('endereco', $retorno_pesquisa_documento) == true){
                $this->endereco = (string) $retorno_pesquisa_documento['endereco'];
            }

            if(array_key_exists('cloudinary', $retorno_pesquisa_documento) == true){
                $this->cloudinary = (int) intval($retorno_pesquisa_documento['cloudinary'], 10);
            }

            if($this->cloudinary == 0){
                chmod($this->endereco, 0777);
                $retorno_exclusao_arquivo_fisico = (bool) unlink($this->endereco);
    
                if($retorno_exclusao_arquivo_fisico == true){
                    $retorno_exclusao_banco_dados = (bool) model_delete($this->tabela(), ['id_documento', '===', (int) intval($this->id_documento, 10)]);
    
                    if($retorno_exclusao_banco_dados == true){
                        return (bool) true;
                    }else{
                        return (bool) false;
                    }
                }else{
                    return (bool) false;
                }
            }else{
                if(array_key_exists('nome_documento', $retorno_pesquisa_documento) == true){
                    $this->nome_documento = (string) $retorno_pesquisa_documento['nome_documento'];
                }

                if(array_key_exists('cloudinary', $retorno_pesquisa_documento) == true){
                    $this->cloudinary = (int) intval($retorno_pesquisa_documento['cloudinary'], 10);
                }

                if(array_key_exists('id_tipo_arquivo', $retorno_pesquisa_documento) == true){
                    $this->id_tipo_arquivo = (int) intval($retorno_pesquisa_documento['id_tipo_arquivo'], 10);
                }

                if(array_key_exists('codigo_barras', $retorno_pesquisa_documento) == true){
                    $this->codigo_barras = (string) $retorno_pesquisa_documento['codigo_barras'];
                }

                
                $tipo_arquivo_objeto = new TipoArquivo();
                $filtro = (array) ['filtro' => (array) ['id_tipo_arquivo', '===', (int) intval($this->id_tipo_arquivo, 10)]];
                $retorno_tipo_arquivo = (array) $tipo_arquivo_objeto->pesquisar($filtro);
                
                if(empty($retorno_tipo_arquivo) == false){
                    $extensao = (string) '';
                    
                    if(array_key_exists('tipo_arquivo',$retorno_tipo_arquivo) == true){
                        $extensao = (string) strtolower($retorno_tipo_arquivo['tipo_arquivo']);
                    }
                    
                    $objeto_cloudinary = new Cloudinary();
                    $exclusao_cloudinary = (bool) $objeto_cloudinary->deletar_documento((string) $this->codigo_barras.$extensao, $this->cloudinary);

                    if($exclusao_cloudinary == true){
                        $retorno_exclusao_banco_dados = (bool) model_delete($this->tabela(), ['id_documento', '===', (int) intval($this->id_documento, 10)]);

                        if($retorno_exclusao_banco_dados == true){
                            return (bool) true;
                        }else{
                            return (bool) false;
                        }
                    }
                }else{
                    return (bool) false;
                }
            }

        }else{
            return (bool) false;
        }
    }

}
