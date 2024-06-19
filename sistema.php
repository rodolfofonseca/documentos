<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Sistema.php';
require_once 'Modelos/TipoArquivo.php';
require_once 'Modelos/Cloudinary.php';

router_add('index', function () {
    require_once 'includes/head.php';

    $id_sistema = (int) intval($_SESSION['id_sistema'], 10);
    $id_empresa = (int) intval($_SESSION['id_empresa'], 10);
?>
    <script>
        const ID_EMPRESA = <?php echo $id_empresa; ?>;
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

        /**
         * Método responsável por cadastrar no banco de dados as informações a respeito do endereço do documento.
         */
        function salvar_endereco_documento() {
            let tipo_arquivo = document.querySelector('#tipo_arquivo').value;
            let endereco_documento = document.querySelector('#endereco_documento').value;
            let descricao = document.querySelector('#descricao').value;

            if (ID_EMPRESA != 0) {
                sistema.request.post('/sistema.php', {
                    'rota': 'enviar_dados_tipo_arquivo',
                    'id_empresa': ID_EMPRESA,
                    'tipo_arquivo': tipo_arquivo,
                    'endereco_documento': endereco_documento,
                    'descricao': descricao
                }, function(retorno) {
                    if (retorno.status == true) {
                        Swal.fire('Sucesso!', 'Operação realizada com sucesso!', 'success');
                    } else {
                        Swal.fire('Erro', 'Erro durante a operação!', 'error');
                    }
                    pesquisar_tipo_arquivo();
                });
            }
        }

        function pesquisar_tipo_arquivo() {
            sistema.request.post('/sistema.php', {
                'rota': 'pesquisar_todos_tipos_arquivos',
                'id_empresa': ID_EMPRESA
            }, function(retorno) {
                let tipo_arquivo = retorno.dados;
                let tamanho = tipo_arquivo.length;
                let tabela = document.querySelector('#tabela_tipo_arquivo tbody');
                let tamanho_tabela = tabela.rows.length;

                if (tamanho_tabela > 0) {
                    tabela = sistema.remover_linha_tabela(tabela);
                }

                if (tamanho < 1) {
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM TIPO DE ARQUIVO ENCONTRADO!', 'inner', true, 5));
                    tabela.appendChild(linha);
                } else {
                    sistema.each(tipo_arquivo, function(contador, arquivo) {
                        let linha = document.createElement('tr');
                        linha.appendChild(sistema.gerar_td(['text-center'], arquivo.id_tipo_arquivo, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], arquivo.tipo_arquivo, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], arquivo.descricao, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], arquivo.endereco_documento, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('excluir_tipo_arquivo_' + arquivo.id_tipo_arquivo, 'EXCLUIR', ['btn', 'btn-danger'], function excluir_tipo_arquivo_function() {
                            excluir_tipo_arquivo(arquivo.id_tipo_arquivo);
                        }), 'append'));

                        tabela.appendChild(linha);
                    });
                }
            }, false);
        }

        function excluir_tipo_arquivo(id_tipo_arquivo) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: "Você tem certeza?",
                text: "A operação de exclusão é irreversível! \n Os arquivos na pasta serão mantidos, mas não poderão ser adicionados novos arquivos a pasta.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sim, deletar pasta!",
                cancelButtonText: "Não, cancelar!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    sistema.request.post('/sistema.php', {
                        'rota': 'excluir_tipo_arquivo',
                        'id_tipo_arquivo': id_tipo_arquivo
                    }, function(retorno) {}, false);

                    pesquisar_tipo_arquivo();

                    swalWithBootstrapButtons.fire({
                        title: "Deletado!",
                        text: "A pasta foi excluída com sucesso.",
                        icon: "success"
                    });
                } else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire({
                        title: "Cancelado",
                        text: "Não foi deletado nenhuma pasta do sistema :)",
                        icon: "error"
                    });
                }
            });
        }

        function salvar_dados_cloudinary(){
            let dns = document.querySelector('#dns').value;
            let usar = document.querySelector('#usar').value;

            if(ID_EMPRESA != 0){
                sistema.request.post('/sistema.php', {
                    'rota':'enviar_dados_cloudinary',
                    'id_empresa': ID_EMPRESA,
                    'dns': dns,
                    'usar': usar
                }, function(retorno){
                    if(retorno.status == true){
                        Swal.fire('Sucesso!', 'Operação realizada com sucesso!', 'success');
                    }else{
                        Swal.fire('Erro', 'Erro durante a operação!', 'error');
                    }
                });
            }
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
                        <input type="hidden" name="id_sistema" id="id_sistema" value="<?php echo $id_sistema; ?>" />
                        <div class="row">
                            <div class="col-2 text-center">
                                <label>VERSÃO SISTEMA ATUAL</label>
                                <input type="text" class="form-control custom-radius text-center" name="versao_sistema" id="versao_sistema" value="0.0" readonly />
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
                            <div class="col-2 text-center">
                                <label>Tipo de Arquivo</label>
                                <select class="form-control custom-radius" name="tipo-arquivo" id="tipo_arquivo">
                                    <option value="">Selecione uma opção</option>
                                </select>
                            </div>
                            <div class="col-3 text-center">
                                <label>Nome Tipo Arquivo</label>
                                <input type="text" class="form-control custom-radius" name="descricao" id="descricao" placeholder="DESCRIÇÃO" />
                            </div>
                            <div class="col-3 text-center">
                                <label>Endereço Documento</label>
                                <input type="text" class="form-control custom-radius" name="endereco_documento" id="endereco_documento" placeholder="C:/documento/word" />
                            </div>
                            <div class="col-2">
                                <button class="btn btn-info botao_vertical_linha" onclick="salvar_endereco_documento();">Salvar Dados</button>
                            </div>
                            <div class="col-2">
                                <button class="btn btn-secondary botao_vertical_linha" onclick="voltar_dashboard();">Voltar</button>
                            </div>
                        </div>

                        <BR />

                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="tabela_tipo_arquivo">
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-title text-center">
                    <div class="row">
                        <div class="col-12">
                            <h1>Configuração de cloudinary</h1>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8 text-center">
                            <label>DNS DO CLOUDINARY</label>
                            <input type="text" class="form-control custom-radius" name="dns" id="dns" placeholder="DNS DO CLOUDINARY" />
                        </div>
                        <div class="col-2 text-center">
                            <label>USAR</label>
                            <select name="usar" id="usar" class="form-control custom-radius">
                                <option value="N">SELECIONE UMA OPÇÃO</option>
                                <option value="S">SIM</option>
                                <option value="N">NÃO</option>
                            </select>
                        </div>
                        <div class="col-2">
                        <button class="btn btn-info botao_vertical_linha" onclick="salvar_dados_cloudinary();">Salvar Dados</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            let id_sistema = document.querySelector('#id_sistema').value;

            sistema.request.post('/sistema.php', {
                'rota': 'pesquisar_dados_sistema',
                'codigo_sistema': id_sistema
            }, function(retorno) {
                let dados_sistema = retorno.dados;
                document.querySelector('#id_sistema').value = retorno.dados.id_sistema;
                document.querySelector('#versao_sistema').value = retorno.dados.versao_sistema;
                document.querySelector('#chave_api').value = retorno.dados.chave_api;
                document.querySelector('#cidade').value = retorno.dados.cidade;

                montar_select_tipo_arquivo();
            });

            pesquisar_tipo_arquivo();
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
 * Rota responsável por enviar para a modelos as informações do tipo de arquivo para salvar no banco de dados.
 */
router_add('enviar_dados_tipo_arquivo', function () {
    $objeto_tipo_arquivo = new TipoArquivo();

    echo json_encode((array) ['status' => (bool) $objeto_tipo_arquivo->salvar_dados($_REQUEST)]);
    exit;
});

router_add('enviar_dados_cloudinary', function(){
    $objeto_cloudinary = new Cloudinary();
    echo json_encode((array) ['status' => (bool) $objeto_cloudinary->salvar_dados($_REQUEST)]);    
    exit;
});

/**
 * Rota responsável por realizar a pesquisa das informações do sistema para o usuário visualizar
 */
router_add('pesquisar_dados_sistema', function () {
    $id_sistema = (isset($_REQUEST['codigo_sistema']) ? (int) intval($_REQUEST['codigo_sistema'], 10) : 0);

    $filtro = (array) ['filtro' => (array) ['id_sistema', '===', (int) $id_sistema]];

    $objeto_sistema = new Sistema();

    echo json_encode((array) ['dados' => (array) $objeto_sistema->pesquisar($filtro)]);
    exit;
});

/**
 * Rota responsável por pesquisar todos os tipos de arquivo de acordo com os filtros passados.
 */
router_add('pesquisar_todos_tipos_arquivos', function () {
    $id_empresa = (isset($_REQUEST['id_empresa']) ? (int) intval($_REQUEST['id_empresa'], 10) : 0);
    $objeto_tipo_arquivo = new TipoArquivo();

    $filtro = (array) ['condicao' => (array) ['id_empresa', '===', (int) $id_empresa], 'ordenacao' => (array) [], 'limite' => (int) intval(10, 10)];

    echo json_encode((array) ['dados' => (array) $objeto_tipo_arquivo->pesquisar_todos($filtro)]);

    exit;
});

router_add('montar_array_tipo_arquivo', function () {
    $objeto_tipo_arquivo = new TipoArquivo();
    echo json_encode((array) $objeto_tipo_arquivo->montar_array_tipo_arquivo(), JSON_UNESCAPED_UNICODE);
});

/**
 * Rota responsável por deletar o tipo de arquivo do sistema.
 */
router_add('excluir_tipo_arquivo', function () {
    $id_tipo_arquivo = (isset($_REQUEST['id_tipo_arquivo']) ? (int) intval($_REQUEST['id_tipo_arquivo'], 10) : 0);
    $objeto_tipo_arquivo = new TipoArquivo();

    echo json_encode((array) ['status' => (bool) $objeto_tipo_arquivo->deletar((array) ['id_tipo_arquivo' => $id_tipo_arquivo])]);
    exit;
});

?>