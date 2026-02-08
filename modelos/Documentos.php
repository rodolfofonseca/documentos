<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Classes/Sistema/db.php';
require_once 'modelos/LogSistema.php';
require_once 'modelos/TipoArquivo.php';
require_once 'modelos/Cloudinary.php';
require_once 'modelos/Usuario.php';
require_once 'modelos/Sistema.php';
require_once 'Modelos/Notificacoes.php';

class Documentos implements InterfaceModelo
{
    //IDENTIFICADORES DO SISTEMA
    private $id_documento;
    private $empresa;
    private $caixa;
    private $tipo_arquivo;
    private $organizacao;
    private $usuario;
    private $armario;
    private $prateleira;
    private $cloudinary;

    //INFORMAÇÕES DO SISTEMA
    private $nome_documento;
    private $descricao;
    private $endereco;
    private $codigo_barras;
    private $quantidade_downloads;

    //DATA DE CADASTRO E UPDATE DO SISTEMA
    private $data_cadastro;
    private $data_alteracao;

    //INFORMAÇÕES CONSTANTE DO SISTEMA
    private $forma_visualizacao;
    private $escolha_usuario;
    //TAMANHO DO ARQUIVO EM MEGABYTES
    private $tamanho_arquivo;
    private $tipo_documento;

    public function tabela()
    {
        return (string) 'documentos';
    }

    public function colocar_dados($dados)
    {
        if (array_key_exists('codigo_documento', $dados) == true) {
            if ($dados['codigo_documento'] != '') {
                $this->id_documento = convert_id($dados['codigo_documento']);
            } else {
                $this->id_documento = null;
            }
        } else {
            $this->id_documento = null;
        }

        $this->empresa = (isset($dados['codigo_empresa']) ? convert_id($dados['codigo_empresa']) : null);
        $this->caixa = (isset($dados['codigo_caixa']) ? convert_id($dados['codigo_caixa']) : null);
        $this->tipo_arquivo = (isset($dados['codigo_tipo_arquivo']) ? convert_id($dados['codigo_tipo_arquivo']) : null);
        $this->usuario = (isset($dados['codigo_usuario']) ? convert_id($dados['codigo_usuario']) : null);
        $this->organizacao = (isset($dados['codigo_organizacao']) ? convert_id($dados['codigo_organizacao']) : null);
        $this->armario = (isset($dados['codigo_armario']) ? convert_id($dados['codigo_armario']) : null);
        $this->prateleira = (isset($dados['codigo_prateleira']) ? convert_id($dados['codigo_prateleira']) : null);

        $this->nome_documento = (string) (isset($dados['nome_documento']) ? (string) $dados['nome_documento'] : '');
        $this->descricao = (string) (isset($dados['descricao']) ? (string) $dados['descricao'] : '');
        $this->endereco = (string) (isset($dados['endereco']) ? (string) $dados['endereco'] : '');
        $this->codigo_barras = (string) (isset($dados['codigo_barras']) ? (string) $dados['codigo_barras'] : codigo_barras());
        $this->quantidade_downloads = (int) (isset($dados['quantidade_downlaods']) ? (int) intval($dados['quantidade_downloads'], 10) : 0);
        $this->cloudinary = (string) (isset($dados['cloudinary']) ? (string) $dados['cloudinary'] : '');
        $this->escolha_usuario = (string) (isset($dados['tipo_alteracao']) ? (string) $dados['tipo_alteracao'] : '');
        $this->forma_visualizacao = (string) (isset($dados['forma_visualizacao']) ? (string) $dados['forma_visualizacao'] : '');
        $this->tipo_documento = (string) (isset($dados['tipo_arquivo']) ? (string) $dados['tipo_arquivo'] : 'DOCUMENTO');
    }

    /**
     * Função responsável por salvar os arquivos maiores de 10 megas no local
     * @param mixed $arquivo
     * @return bool $retorno_salvamento
     */
    private function salvar_local($arquivo, $extensao)
    {
        $nome_documento = (string) $this->endereco . $this->codigo_barras . $extensao;
        $checar_existencia = (bool) model_check($this->tabela(), ['codigo_barras', '===', (string) $this->codigo_barras]);

        if ($checar_existencia == true) {
            chmod($this->endereco, 0777);
            if (file_exists($nome_documento) == true) {
                chmod($nome_documento, 0777);
                $retorno_exclusao = (bool) @unlink($nome_documento);
            }
        }

        $this->endereco = (string) $nome_documento;

        if (@move_uploaded_file((string) $arquivo['arquivo']['tmp_name'], $nome_documento)) {
            chmod($nome_documento, 0777);
            return (bool) true;
        } else {
            return (bool) false;
        }
    }

    public function salvar_dados($dados, $arquivo = null)
    {
        $objeto_log = new LogSistema();
        $objeto_usuario = new Usuario();
        $objeto_sistema = new Sistema();

        $retorno_operacao = (bool) false;
        $checar_existencia = (bool) false;
        $descricao_log = (string) '';
        $tamanho_arquivo_aceito_sistema = (float) 0;

        $this->colocar_dados($dados);

        $retorno_sistema = (array) $objeto_sistema->pesquisar(['filtro' => (array) ['empresa', '===', $this->empresa]]);

        if (empty($retorno_sistema) == false) {
            if (array_key_exists('tamanho_arquivo', $retorno_sistema) == true) {
                $tamanho_arquivo_aceito_sistema = (float) $retorno_sistema['tamanho_arquivo'];
            }
        }

        $retorno_usuario = (array) $objeto_usuario->pesquisar((array) ['filtro' => (array) ['_id', '===', $this->usuario]]);

        if ($this->escolha_usuario == 'INFORMACOES') {
            if ($this->id_documento != null) {
                $checar_existencia = (bool) model_check($this->tabela(), ['_id', '===', $this->id_documento]);
            }

            if ($checar_existencia == true) {
                $pesquisa_documento = (array) model_one($this->tabela(), ['_id', '===', $this->id_documento]);

                if (empty($pesquisa_documento) == false) {
                    $retorno_pesquisa_documento = (array) model_one($this->tabela(), ['codigo_barras', '===', (string) $this->codigo_barras]);

                    $retorno_operacao =  (bool) model_update($this->tabela(), (array) ['codigo_barras', '===', (string) $this->codigo_barras], (array) ['caixa' => $this->caixa, 'tipo_arquivo' => $this->tipo_arquivo, 'organizacao' => $this->organizacao, 'nome_documento' => (string) $this->nome_documento, 'descricao' => (string) $this->descricao, 'data_alteracao' => model_date(), 'cloudinary' => (string) $this->cloudinary, 'forma_visualizacao' => (string) $this->forma_visualizacao, 'armario' => $this->armario, 'prateleira' => $this->prateleira, 'tipo_documento' => (string) $this->tipo_documento]);

                    $descricao_log = 'Alterou apenas as informações do documento ' . $this->nome_documento;
                } else {
                    $retorno_operacao = (bool) model_insert($this->tabela(), (array) ['empresa' => $this->empresa, 'caixa' => $this->caixa, 'tipo_arquivo' => $this->tipo_arquivo, 'organizacao' => $this->organizacao, 'usuario' => $this->usuario, 'nome_documento' => (string) $this->nome_documento, 'descricao' => (string) $this->descricao, 'endereco' => (string) $this->endereco, 'codigo_barras' => (string) $this->codigo_barras, 'quantidade_downloads' => (int) 0, 'data_cadastro' => model_date(), 'data_alteracao' => model_date(), 'cloudinary' => (string) $this->cloudinary, 'armario' => $this->armario, 'prateleira' => $this->prateleira, 'tipo_documento' => (string) $this->tipo_documento, 'tamanho_arquivo' => (float) 0]);

                    $descricao_log = (string) 'Cadastrou apenas as informações do documento ' . $this->nome_documento;
                }
            } else {
                $retorno_operacao = (bool) model_insert($this->tabela(), (array) ['empresa' => $this->empresa, 'caixa' => $this->caixa, 'tipo_arquivo' => $this->tipo_arquivo, 'organizacao' => $this->organizacao, 'usuario' => $this->usuario, 'nome_documento' => (string) $this->nome_documento, 'descricao' => (string) $this->descricao, 'endereco' => (string) $this->endereco, 'codigo_barras' => (string) $this->codigo_barras, 'quantidade_downloads' => (int) 0, 'data_cadastro' => model_date(), 'data_alteracao' => model_date(), 'cloudinary' => (string) $this->cloudinary, 'armario' => $this->armario, 'prateleira' => $this->prateleira, 'tipo_documento' => (string) $this->tipo_documento, 'tamanho_arquivo' => (float) 0]);

                $descricao_log = (string) 'Cadastrou apenas as informações do documento ' . $this->nome_documento;
            }
        } else {
            if (isset($arquivo['arquivo']['name']) && $arquivo['arquivo']) {
                $tamanho_arquivo = (int) $arquivo['arquivo']['size'];
                $retorno_salvamento_arquivo = (bool) false;

                $nome_atual = (string) $arquivo['arquivo']['name'];

                $extensao = (string) strrchr($nome_atual, '.');
                $extensao = (string) strtolower($extensao);

                if (converter_tamanho_arquivo($tamanho_arquivo, false) > $tamanho_arquivo_aceito_sistema) {
                    return (bool) false;
                }

                $objeto_tipo_arquivo = new TipoArquivo();

                $retorno_extensao = (array) $objeto_tipo_arquivo->pesquisar((array) ['filtro' => (array) ['and' => (array) [(array) ['tipo_arquivo', '===', (string) strtoupper($extensao)], (array) ['empresa', '===', $this->empresa], (array) ['usar', '===', (string) 'S']]]]);

                if (empty($retorno_extensao) == true) {
                    return false;
                }

                if (array_key_exists('tipo_arquivo', $retorno_extensao) == true) {
                    $this->tipo_arquivo = convert_id($retorno_extensao['_id']);
                }

                if (array_key_exists('endereco_documento', $retorno_extensao) == true) {
                    $this->endereco = (string) $retorno_extensao['endereco_documento'];
                }

                if ($tamanho_arquivo < 9961472 && $extensao == '.pdf' || $tamanho_arquivo < 9961472 && $extensao == '.psd') {
                    $retorno_salvamento_arquivo = (bool) $this->salvar_api($arquivo, $extensao);

                    if ($retorno_salvamento_arquivo == false) {
                        if (array_key_exists('tipo_arquivo', $retorno_extensao) == true) {
                            $this->tipo_arquivo = convert_id($retorno_extensao['_id']);
                        }

                        if (array_key_exists('endereco_documento', $retorno_extensao) == true) {
                            $this->endereco = (string) $retorno_extensao['endereco_documento'];
                        }

                        $retorno_salvamento_arquivo = (bool) $this->salvar_local($arquivo, $extensao);
                    }
                } else {
                    $retorno_salvamento_arquivo = (bool) $this->salvar_local($arquivo, $extensao);
                }

                if ($this->escolha_usuario != 'ARQUIVOS') {
                    if ($retorno_salvamento_arquivo == true) {
                        $checar_existencia_arquivo = (bool) model_check($this->tabela(), (array) ['codigo_barras', '===', (string) $this->codigo_barras]);
                        if ($checar_existencia_arquivo == true) {
                            $retorno_pesquisa_documento = (array) model_one($this->tabela(), ['codigo_barras', '===', (string) $this->codigo_barras]);

                            if (empty($retorno_pesquisa_documento) == false) {
                                $retorno_operacao = (bool) model_update((string) $this->tabela(), (array) ['codigo_barras', '===', (string) $this->codigo_barras], ['empresa' => $this->empresa, 'caixa' => $this->caixa, 'tipo_arquivo' => $this->tipo_arquivo, 'organizacao' => $this->organizacao, 'usuario' => $this->usuario, 'nome_documento' => (string) $this->nome_documento, 'descricao' => (string) $this->descricao, 'endereco' => (string) $this->endereco, 'codigo_barras' => (string) $this->codigo_barras, 'data_alteracao' => model_date(), 'forma_visualizacao' => (string) $this->forma_visualizacao, 'armario' => $this->armario, 'prateleira' => $this->prateleira, 'tipo_documento' => (string) $this->tipo_documento, 'tamanho_arquivo' => (double) converter_tamanho_arquivo((double) $tamanho_arquivo, false), 'tipo_documento' => (string) $this->tipo_documento]);

                                $descricao_log = (string) 'Alterou o documento ' . $this->nome_documento;
                            } else {
                                $retorno_operacao = (bool) false;
                            }
                        } else {
                            $retorno_operacao = (bool) model_insert((string) $this->tabela(), (array) ['empresa' => $this->empresa, 'caixa' => $this->caixa, 'tipo_arquivo' => $this->tipo_arquivo, 'organizacao' => $this->organizacao, 'usuario' => $this->usuario, 'armario' => $this->armario, 'prateleira' => $this->prateleira, 'nome_documento' => (string) $this->nome_documento, 'descricao' => (string) $this->descricao, 'endereco' => (string) $this->endereco, 'codigo_barras' => (string) $this->codigo_barras, 'quantidade_downloads' => (int) 0, 'cloudinary' => (string) $this->cloudinary, 'data_cadastro' => model_date(), 'data_alteracao' => model_date(), 'forma_visualizacao' => (string) $this->forma_visualizacao,  'tipo_documento' => (string) $this->tipo_documento, 'tamanho_arquivo' => (double) converter_tamanho_arquivo((double) $tamanho_arquivo, false), 'tipo_documento' => (string) $this->tipo_documento]);

                            $descricao_log = (string) 'Cadastrou um novo documento ' . $this->nome_documento;
                        }
                    } else {
                        $retorno_operacao = (bool) false;
                    }
                } else {
                    $retorno_operacao = (bool) true;
                }
            }
        }

        if ($retorno_operacao == true) {
            $retorno_log = (bool) $objeto_log->salvar_dados((array) ['empresa' => $this->empresa, 'usuario' => $retorno_usuario['_id'], 'codigo_barras' => (string) $this->codigo_barras, 'modulo' => (string) 'DOCUMENTO', 'descricao' => (string) $descricao_log]);

            return (bool) true;
        } else {
            return (bool) false;
        }
    }

    /**
     * Método responsável por adicionar ao documento a quantidade de downloads que o documento teve. esta função retorna as informações do documento para que se possa utilizar em outros locais caso deseje.
     */
    public function update_download($dados)
    {
        $this->colocar_dados($dados);

        $retorno_documento = (array) model_one($this->tabela(), ['_id', '===', $this->id_documento]);

        if (empty($retorno_documento) == false) {
            $this->quantidade_downloads = (int) 0;

            if (array_key_exists('quantidade_downloads', $retorno_documento) == true) {
                $this->quantidade_downloads = (int) intval($retorno_documento['quantidade_downloads'], 10);
            }

            $this->quantidade_downloads++;

            $retorno_documento['quantidade_downloads'] = (int) $this->quantidade_downloads;
            $retorno_update = (bool) model_update($this->tabela(), ['_id', '===', $this->id_documento], (array) ['quantidade_downloads' => (int) $this->quantidade_downloads]);
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

    private function salvar_api($arquivo, $extensao)
    {
        $dados = (array) ['empresa' => $this->empresa, 'usar' => (string) 'S'];
        $retorno_cloudinary = (array) [];
        $status = (bool) false;

        $cloudinary = new Cloudinary();
        $retorno_cloudinary = (array) $cloudinary->upload_arquivo($dados, $this->codigo_barras, $extensao, $arquivo);

        if (array_key_exists('url', $retorno_cloudinary) == true) {
            $this->endereco = (string) $retorno_cloudinary['url'];
        }

        if (array_key_exists('cloudinary', $retorno_cloudinary) == true) {
            $this->cloudinary = (string) $retorno_cloudinary['cloudinary'];
        }

        if (array_key_exists('status', $retorno_cloudinary) == true) {
            $status = (bool) $retorno_cloudinary['status'];
        }

        return (bool) $status;
    }

    /**
     * Função responsável por deletar o arquivo fisico e dado banco de dados, para deletar o arquivo o mesmo deve existir virtualmente ou no cloudinary ou no sistema local.
     * @param array $dados variável request contento o identificador do documento.
     * @return array contendo as informações para apresentação da mensagem
     * */
    public function excluir_documento($dados)
    {
        $objeto_log = new LogSistema();
        $objeto_usuario = new Usuario();

        $retorno_usuario = (array) [];
        $retorno_operacao = (bool) false;

        $this->colocar_dados($dados);

        $retorno_pesquisa_documento = (array) model_one($this->tabela(), ['_id', '===', $this->id_documento]);

        if (empty($retorno_pesquisa_documento) == false) {
            if (array_key_exists('endereco', $retorno_pesquisa_documento) == true) {
                $this->endereco = (string) $retorno_pesquisa_documento['endereco'];
            }

            if (array_key_exists('cloudinary', $retorno_pesquisa_documento) == true) {
                $this->cloudinary = (string) $retorno_pesquisa_documento['cloudinary'];
            }

            $retorno_usuario = $objeto_usuario->pesquisar((array) ['filtro' => (array) ['_id', '===', $retorno_pesquisa_documento['usuario']]]);

            if ($this->cloudinary == '') {
                chmod($this->endereco, 0777);
                $retorno_exclusao_arquivo_fisico = (bool) unlink($this->endereco);

                if ($retorno_exclusao_arquivo_fisico == true) {
                    $retorno_exclusao_banco_dados = (bool) model_delete($this->tabela(), ['_id', '===', $this->id_documento]);

                    if ($retorno_exclusao_banco_dados == true) {
                        $retorno_operacao = (bool) true;
                    } else {
                        $retorno_operacao = (bool) false;
                    }
                } else {
                    $retorno_operacao = (bool) false;
                }
            } else {
                if (array_key_exists('nome_documento', $retorno_pesquisa_documento) == true) {
                    $this->nome_documento = (string) $retorno_pesquisa_documento['nome_documento'];
                }

                if (array_key_exists('cloudinary', $retorno_pesquisa_documento) == true) {
                    $this->cloudinary = (string) $retorno_pesquisa_documento['cloudinary'];
                }

                if (array_key_exists('tipo_arquivo', $retorno_pesquisa_documento) == true) {
                    $this->tipo_arquivo = convert_id($retorno_pesquisa_documento['tipo_arquivo']);
                }

                if (array_key_exists('codigo_barras', $retorno_pesquisa_documento) == true) {
                    $this->codigo_barras = (string) $retorno_pesquisa_documento['codigo_barras'];
                }


                $tipo_arquivo_objeto = new TipoArquivo();
                $filtro = (array) ['filtro' => (array) ['tipo_arquivo', '===', $this->tipo_arquivo]];
                $retorno_tipo_arquivo = (array) $tipo_arquivo_objeto->pesquisar($filtro);

                if (empty($retorno_tipo_arquivo) == false) {
                    $extensao = (string) '';

                    if (array_key_exists('tipo_arquivo', $retorno_tipo_arquivo) == true) {
                        $extensao = (string) strtolower($retorno_tipo_arquivo['tipo_arquivo']);
                    }

                    $objeto_cloudinary = new Cloudinary();
                    $exclusao_cloudinary = (bool) $objeto_cloudinary->deletar_documento((string) $this->codigo_barras . $extensao, $this->cloudinary);

                    if ($exclusao_cloudinary == true) {
                        $retorno_exclusao_banco_dados = (bool) model_delete($this->tabela(), ['_id', '===', $this->id_documento]);

                        if ($retorno_exclusao_banco_dados == true) {
                            $retorno_operacao = (bool) true;
                        } else {
                            $retorno_operacao = (bool) false;
                        }
                    }
                } else {
                    $retorno_operacao = (bool) false;
                }
            }
        } else {
            $retorno_operacao = (bool) false;
        }

        if ($retorno_operacao == true) {
            if (empty($retorno_usuario) == false) {
                $retorno_log = (bool) $objeto_log->salvar_dados((array) ['empresa' => $retorno_pesquisa_documento['empresa'], 'usuario' => (string) $retorno_usuario['login'], 'codigo_barras' => (string) $retorno_pesquisa_documento['codigo_barras'], 'modulo' => (string) 'DOCUMENTO', 'descricao' => (string) 'Excluiu o documento ' . $retorno_pesquisa_documento['nome_documento']]);
            }

            return (array) ['titulo' => (string) 'SUCESSO NA OPERAÇÃO', 'mensagem' => (string) 'Sucesso no processo de exclusão de documento', 'icone' => (string) 'success'];
        } else {
            return (array) ['titulo' => (string) 'ERRO DURANTE A OPERAÇÃO', 'mensagem' => (string) 'Erro durante o processo de exclusão de documento', 'icone' => (string) 'error'];
        }
    }

    /**
     * Função responsável por pesquisar no banco de dados os relatórios de sistema com mais de 30 dias e realizar a exclusão de forma automática
     * @return void
     */
    public function excluir_relatorio_antigo()
    {
        date_default_timezone_set('America/Sao_Paulo');

        $mes = (int) intval(date('m'), 10);
        $dia = (int) intval(date('d'), 10);
        $ano = (int) intval(date('Y'));

        if ($mes == 2 && $dia > 28) {
            $dia = (int) 28;
        }

        $mes = $mes - 1;

        $filtro = (array) [];

        array_push($filtro, ['tipo_arquivo', '===', (string) 'RELATORIO_SISTEMA']);
        array_push($filtro, ['data_cadastro', '<', model_date($ano . '-' . $mes . '-' . $dia)]);

        $retorno_documentos = (array) $this->pesquisar_todos(['filtro' => (array) $filtro, 'ordenacao' => (array) [], 'limite' => (int) 1000]);

        if (empty($retorno_documentos) == false) {
            foreach ($retorno_documentos as $documentos) {
                $array_documento = (array) [];
                $array_documento['codigo_documento'] = $documentos['_id'];

                $retorno_exclusao = (array) $this->excluir_documento($array_documento);
            }
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
