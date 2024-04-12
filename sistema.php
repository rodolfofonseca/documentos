<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Sistema.php';
require_once 'Modelos/TipoArquivo.php';

router_add('index', function () {
    require_once 'includes/head.php';
?>
    <script>
        /**
         * Função responsável por realizar o salvamento dos dados básico do sistema
         */
        function salvar_dados() {
            let codigo_sistema = document.querySelector('#id_sistema').value;
            let chave_api = document.querySelector('#chave_api').value;
            let cidade = document.querySelector('#cidade').value;
            let versao_sistema = document.querySelector('#versao_sistema').value;

            sistema.request.post('/sistema.php', {
                'rota': 'enviar_dados',
                'codigo_sistema': codigo_sistema,
                'chave_api': chave_api,
                'cidade': cidade,
                'versao_sistema': versao_sistema
            }, function(retorno) {
                if (retorno.status == true) {
                    Swal.fire('Sucesso!', 'Operação realizada com sucesso!', 'success');
                } else {
                    Swal.fire('Erro', 'Erro durante a operação!', 'error');
                }
            });
        }

        /**
         * Função responsável por retornar a dashboard do sistema
         */
        function voltar_dashboard() {
            window.location.href = sistema.url('/dashboard.php', {
                'rota': 'index'
            });
        }

        /**
         * Método responsável por montar o select de tipo de arquivo
         */
        function montar_select_tipo_arquivo() {
            sistema.request.post('/sistema.php', {
                'rota': 'montar_array_tipo_arquivo'
            }, function(retorno) {
                let tamanho = retorno.length;
                let tipo_documento = document.querySelector('#tipo_arquivo');

                tipo_documento = sistema.remover_option(tipo_documento);

                for (let cont = 0; cont < tamanho; cont++) {
                    tipo_documento.appendChild(sistema.gerar_option(retorno[cont].tipo, "(" + retorno[cont].tipo + ") " + retorno[cont].descricao));
                }
            }, false);
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-title text-center">
                        <div class="row">
                            <div class="col-12">
                                <h1>Configuração do Sistema</h1>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="id_sistema" id="id_sistema" value="" />
                        <div class="row">
                            <div class="col-2 text-center">
                                <label>VERSÃO SISTEMA ATUAL</label>
                                <input type="text" class="form-control custom-radius text-center" name="versao_sistema" id="versao_sistema" value="1.0" readonly />
                            </div>
                            <div class="col-5 text-center">
                                <label>CHAVE DE API PREVISÃO TEMPO</label>
                                <input type="text" class="form-control custom-radius" name="chave_api" id="chave_api" placeholder="CHAVE DE API" />
                            </div>
                            <div class="col-5 text-center">
                                <label>CIDADE PREVISÃO DO TEMPO</label>
                                <input type="text" class="form-control custom-radius" name="cidade" id="cidade" placeholder="CIDADE" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <button class="btn btn-info botao_vertical_linha" onclick="salvar_dados();">Salvar Dados</button>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-secondary botao_vertical_linha" onclick="voltar_dashboard();">Voltar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-title text-center">
                        <div class="row">
                            <div class="col-12">
                                <h1>Configuração de Documentos</h1>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4 text-center">
                                <label>Tipo de Arquivo</label>
                                <select class="form-control custom-radius" name="tipo-arquivo" id="tipo_arquivo">
                                    <option value="">Selecione uma opção</option>
                                </select>
                            </div>
                            <div class="col-4 text-center">
                                <label>Endereço Documento</label>
                                <input type="text" class="form-control custom-radius" name="endereco_documento" id="endereco_documento" placeholder="C:/documento/word" />
                            </div>
                            <div class="col-2">
                                <button class="btn btn-info botao_vertical_linha" onclick="">Salvar Dados</button>
                            </div>
                            <div class="col-2">
                                <button class="btn btn-secondary botao_vertical_linha" onclick="voltar_dashboard();">Voltar</button>
                            </div>
                        </div>

                        <BR/>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="tabela_endereco_documentos">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">#</th>
                                                <th scope="col">EXTENSÃO</th>
                                                <th scope="col">DESCRICAO</th>
                                                <th scope="col">ENDEREÇO</th>
                                                <th scope="col">EXCLUIR</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="5" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA PESQUISA</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            sistema.request.post('/sistema.php', {
                'rota': 'pesquisar_dados_sistema'
            }, function(retorno) {
                document.querySelector('#id_sistema').value = retorno.dados.id_sistema;
                document.querySelector('#versao_sistema').value = retorno.dados.versao_sistema;
                document.querySelector('#chave_api').value = retorno.dados.chave_api;
                document.querySelector('#cidade').value = retorno.dados.cidade;

                montar_select_tipo_arquivo();
            });
        }
    </script>
<?php
    require_once 'includes/footer.php';
    exit;
});


/**
 * Rota responsável por realizar o envio dos dados para a modelos.
 */
router_add('enviar_dados', function () {
    $objeto_sistema = new Sistema();

    echo json_encode((array) ['status' => (bool) $objeto_sistema->salvar_dados($_REQUEST)]);
    exit;
});

/**
 * Rota responsável por realizar a pesquisa das informações do sistema para o usuário visualizar
 */
router_add('pesquisar_dados_sistema', function () {
    $filtro = (array) ['filtro' => (array) ['id_sistema', '===', (int) 1]];

    $objeto_sistema = new Sistema();

    echo json_encode((array) ['dados' => (array) $objeto_sistema->pesquisar($filtro)]);
    exit;
});

router_add('montar_array_tipo_arquivo', function () {
    $objeto_tipo_arquivo = new TipoArquivo();
    echo json_encode((array) $objeto_tipo_arquivo->montar_array_tipo_arquivo(), JSON_UNESCAPED_UNICODE);
});
?>