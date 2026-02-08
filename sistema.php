<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Sistema.php';
require_once 'Modelos/TipoArquivo.php';
require_once 'Modelos/Cloudinary.php';
require_once 'Modelos/TipoArquivoSistema.php';

router_add('index', function () {
    require_once 'includes/head.php';
?>
    <script>
        const ID_EMPRESA = "<?php echo CODIGO_EMPRESA; ?>";
        const CODIGO_SISTEMA = "<?php echo CODIGO_SISTEMA; ?>";
        var ID_CLOUDINARY = "";
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
            window.location.href = sistema.url('/dashboard.php', {'rota': 'index'});
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
                    tipo_documento.appendChild(sistema.gerar_option(retorno[cont].tipo, "(" + retorno[cont].tipo + ") " + retorno[cont].nome_tipo_arquivo));
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

            if (ID_EMPRESA != '') {
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
                    window.location.href = sistema.url('/sistema.php', {'rota': 'index'});
                });
            }
        }

        function pesquisar_tipo_arquivo() {
            sistema.request.post('/sistema.php', {
                'rota': 'pesquisar_todos_tipos_arquivos',
                'empresa': ID_EMPRESA
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
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA CONFIGURAÇÃO TIPO DE ARQUIVO ENCONTRADO!', 'inner', true, 4));
                    tabela.appendChild(linha);
                } else {
                    sistema.each(tipo_arquivo, function(contador, arquivo) {
                        let linha = document.createElement('tr');
                        linha.appendChild(sistema.gerar_td(['text-center'], arquivo.tipo_arquivo, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], arquivo.descricao, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], arquivo.endereco_documento, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('excluir_tipo_arquivo_' + arquivo._id.$oid, 'EXCLUIR', ['btn', 'btn-danger'], function excluir_tipo_arquivo_function() {
                            excluir_tipo_arquivo(arquivo._id.$oid);
                        }), 'append'));

                        tabela.appendChild(linha);
                    });
                }
            }, false);
        }

        function excluir_tipo_arquivo(id_tipo_arquivo) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success custom-radius",
                    cancelButton: "btn btn-danger custom-radius"
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
                    }, function(retorno) {pesquisar_tipo_arquivo();}, false);

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

        /** 
         * Função responsável por salvar os dados do cloudinary no banco de dados.
         */
        function salvar_dados_cloudinary(){
            let dns = document.querySelector('#dns').value;
            let usar = document.querySelector('#usar').value;

            if(ID_EMPRESA != ""){
                sistema.request.post('/sistema.php', {
                    'rota':'enviar_dados_cloudinary',
                    'id_cloudinary': ID_CLOUDINARY,
                    'id_empresa': ID_EMPRESA,
                    'dns': dns,
                    'usar': usar
                }, function(retorno){
                    validar_retorno(retorno);
                    pesquisar_cloudinary();
                });
            }
        }

        /** Função responsável por pesquisar os cloudinary cadastrados no banco de dados para empresa. */
        function pesquisar_cloudinary(){
            sistema.request.post('/sistema.php', {'rota': 'pesquisar_cloudinary_empresa', 'codigo_empresa':ID_EMPRESA}, function(retorno){
                let cloudinary = retorno.dados;
                let tamanho = cloudinary.length;
                let tabela = document.querySelector('#tabela_cloudinary tbody');
                let tamanho_tabela = tabela.rows.length;

                if(tamanho_tabela > 0){
                    tabela = sistema.remover_linha_tabela(tabela);
                }

                if(tamanho < 1){
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM CLOUDINARY ENCONTRADO!', 'inner', true, 5));
                    tabela.appendChild(linha);
                }else{
                    sistema.each(cloudinary, function(contador, dados){
                        linha = document.createElement('tr');

                        linha.appendChild(sistema.gerar_td(['text-center'], dados.dns, 'inner'));

                        if(dados.usar == 'S'){
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_usar_sim_'+dados._id.$oid, 'SIM', ['btn', 'btn-success', 'custom-radius'], function usar(){}), 'append'));
                        }else{
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_usar_sim_'+dados._id.$oid, 'NÃO', ['btn', 'btn-danger', 'custom-radius'], function usar(){}), 'append'));
                        }

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_alterar_cloudinary_'+dados._id.$oid, 'ALTERAR TIPO', ['btn', 'btn-info', 'custom-radius'], function alterar_usar_cloudinary(){alterar_cloudinary(dados._id.$oid, dados.usar);}), 'append'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_alterar_dns_cloudinary_'+dados._id.$oid, 'ALTERAR DNS', ['btn', 'btn-info', 'custom-radius'], function alterar_cloudinary_dns(){alterar_dns(dados._id.$oid, dados.dns, dados.usar);}), 'append'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_excluir_cloudinary_'+dados._id.$oid, 'EXCLUIR',['btn', 'btn-danger', 'custom-radius'], function excluir_cloudinary_sistema(){excluir_cloudinary(dados._id.$oid);}), 'append'));

                        tabela.appendChild(linha);
                    });
                }
            }, false);
        }

        /**
         * Função responsável por alterar a visualização do cloudinary para usar = S ou usar = N
         * @param {int} id_cloudinary identificador único do cloudinary na base de dados
         * @param string dns string contendo o endereço do cloudinary para fazer o upload do arquivo
         * @param string usar string contendo a informação de qual cloudinary o usuário deseja estar utilizando.
         */
        function alterar_dns(id_cloudinary, dns, usar){
            document.querySelector('#dns').value = dns;
            document.querySelector('#usar').value = usar;
            ID_CLOUDINARY = id_cloudinary;
        }

        /**
         * Função responsável por adicionar as informações do cloudinary nos campos para que o usuário possa estar realizando a alteração completa do cloudinary
         * @param {int} id_cloudinary 
         * @param {string} usar
         * 
         */
        function alterar_cloudinary(id_cloudinary, usar){
            if(usar == 'S'){
                usar = 'N';
            }else{
                usar = 'S';
            }

            sistema.request.post('/sistema.php', {'rota': 'alterar_tipo_cloudinary', 'id_cloudinary': id_cloudinary, 'id_empresa': ID_EMPRESA, 'usar': usar}, function(retorno){
                validar_retorno(retorno);
                pesquisar_cloudinary();
            });
        }

        /**
         * Função responsável por perguntar se o usuário deseja mesmo excluir o cloudinary que está cadastrado no sistema. 
         * Caso o usuário escolha excluir o cloudinary o sistema já faz na hora a exclusão do mesmo.
         * @param {int} id_cloudinary  */
        function excluir_cloudinary(id_cloudinary){
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: { confirmButton: "btn btn-success custom-radius", cancelButton: "btn btn-danger custom-radius"}, buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: "Você tem certeza?",
                text: "A operação de exclusão é irreversível! \n As informações pertinentes a este cloudinary serão mantidas. Mas os arquvios já cadastrados não serão excluídos.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sim, deletar!",
                cancelButtonText: "Não, cancelar!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    sistema.request.post('/sistema.php', {
                        'rota': 'deletar_cloudinary',
                        'id_cloudinary': id_cloudinary,
                        'id_empresa': ID_EMPRESA
                    }, function(retorno) {pesquisar_cloudinary();}, false);

                    

                    swalWithBootstrapButtons.fire({
                        title: "Deletado!",
                        text: "O cloudinary foi excluído com sucesso.",
                        icon: "success"
                    });
                } else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire({
                        title: "Cancelado",
                        text: "O cloudinary não foi deletado do sistema :)",
                        icon: "error"
                    });
                }
            });
        }

        /** 
         * Função responsável por salvar no banco de dados a configuração de tamanho de arquivo suportado pelo sistema.
        */
        function salvar_tamanho_arquivo(){
            let tamanho_arquivo = sistema.int(document.querySelector('#tamanho_arquivo_sistema').value);

            sistema.request.post('/sistema.php', {'rota': 'alterar_tamanho_arquivo', 'codigo_sistema': CODIGO_SISTEMA, 'codigo_empresa': ID_EMPRESA, 'tamanho_arquivo': tamanho_arquivo}, function(retorno){
                validar_retorno(retorno, '/sistema.php', 1);
            });
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
                        <input type="hidden" name="id_sistema" id="id_sistema" value="<?php echo CODIGO_SISTEMA; ?>" />
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
                                <button class="btn btn-info botao_vertical_linha custom-radius" onclick="salvar_dados();">Salvar Dados</button>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-secondary botao_vertical_linha custom-radius" onclick="voltar_dashboard();">Voltar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br/>

        <!-- Configuração de tamanho máximo de arquivo que o sistema aceita -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-title text-center">
                        <div class="row">
                            <div class="col-12">
                                <h1>Tamanho máximo arquivo</h1>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <p class="text-left">
                                    Selecionar o tamanho máximo em Megabytes que o sistema aceita para arquivos!
                                    <br/>
                                    Para o caso de o sistema não possuir configuração o mesmo irá pegar o valor padrão de 40 Megabytes
                                </p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3 text-center">
                                    <input type="number" id="tamanho_arquivo_sistema" name="tamanho_arquivo_sistema" class="form-control custom-radius" placeholder="TAMANHO ARQUIVO SUPORTADO SISTEMA EM MABAGYTES" sistema-mask="codigo"/>
                                </div>
                                <div class="col-3 text-center">
                                    <button class="btn btn-info custom-radius" id="salvar_informacoes_tamanho_arquivo" onclick="salvar_tamanho_arquivo();">Salvar Dados</button>    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br/>
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
                                <button class="btn btn-info botao_vertical_linha custom-radius" onclick="salvar_endereco_documento();">Salvar Dados</button>
                            </div>
                            <div class="col-2">
                                <button class="btn btn-secondary botao_vertical_linha custom-radius" onclick="voltar_dashboard();">Voltar</button>
                            </div>
                        </div>

                        <br />

                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="tabela_tipo_arquivo">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">EXTENSÃO</th>
                                                <th scope="col">DESCRICAO</th>
                                                <th scope="col">ENDEREÇO</th>
                                                <th scope="col">EXCLUIR</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="4" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA PESQUISA</td>
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
                        <button class="btn btn-info botao_vertical_linha custom-radius" onclick="salvar_dados_cloudinary();">Salvar Dados</button>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped" id="tabela_cloudinary">
                                    <thead class="bg-info text-white">
                                        <tr class="text-center">
                                            <th scope="col">DNS</th>
                                            <th scope="col">USAR</th>
                                            <th scope="col">ALTERAR TIPO</th>
                                            <th scope="col">ALTERAR ENDEREÇO</th>
                                            <th scope="col">EXCLUIR</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="text-center">NENHUM CLOUDINARY ENCONTRADO!</td>
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

    <script>
        window.onload = function() {
            validar_acesso_administrador('<?php echo TIPO_USUARIO; ?>');

            sistema.request.post('/sistema.php', {
                'rota': 'pesquisar_dados_sistema',
                'codigo_sistema': CODIGO_SISTEMA
            }, function(retorno) {
                let dados_sistema = retorno.dados;
                document.querySelector('#id_sistema').value = dados_sistema._id.$oid;
                document.querySelector('#versao_sistema').value = dados_sistema.versao_sistema;
                document.querySelector('#chave_api').value = dados_sistema.chave_api;
                document.querySelector('#cidade').value = dados_sistema.cidade;
                document.querySelector('#tamanho_arquivo_sistema').value = dados_sistema.tamanho_arquivo;

                montar_select_tipo_arquivo();
                pesquisar_tipo_arquivo();
                pesquisar_cloudinary();
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
    $id_sistema = (string) (isset($_REQUEST['codigo_sistema']) ? (string) $_REQUEST['codigo_sistema'] : '');

    $filtro = (array) ['filtro' => (array) ['_id', '===', convert_id($id_sistema)]];

    $objeto_sistema = new Sistema();

    echo json_encode((array) ['dados' => (array) $objeto_sistema->pesquisar($filtro)]);
    exit;
});

/**
 * Rota responsável por pesquisar todos os tipos de arquivo de acordo com os filtros passados.
 */
router_add('pesquisar_todos_tipos_arquivos', function () {
    $id_empresa =(string) (isset($_REQUEST['empresa']) ? (string) $_REQUEST['empresa'] : '');
    $objeto_tipo_arquivo = new TipoArquivo();

    $filtro = (array) ['condicao' => (array) ['empresa', '===', convert_id($id_empresa)], 'ordenacao' => (array) [], 'limite' => (int) intval(10, 10)];

    echo json_encode((array) ['dados' => (array) $objeto_tipo_arquivo->pesquisar_todos($filtro)]);

    exit;
});

/**
 * Rota responsável por pesquisar os cloudinary cadastrados no sistema para empresa.
 */
router_add('pesquisar_cloudinary_empresa', function(){
    $id_empresa = (isset($_REQUEST['codigo_empresa']) ? (string) $_REQUEST['codigo_empresa'] : '');
    $objeto_cloudinary = new Cloudinary();

    echo json_encode(['dados' => (array) $objeto_cloudinary->pesquisar_todos((array) ['filtro' => (array) ['empresa', '===', convert_id($id_empresa)], 'ordenacao' => (array) [], 'limite' => (int) 0])], JSON_UNESCAPED_UNICODE);
    exit;
});

router_add('montar_array_tipo_arquivo', function () {
    $objeto_tipo_arquivo = new TipoArquivoSistema();
    echo json_encode((array) $objeto_tipo_arquivo->pesquisar_todos((array) ['filtro' => (array) [], 'ordenacao' => (array) ['nome_tipo_arquivo' => (bool) true], 'limite' => (int) 0]), JSON_UNESCAPED_UNICODE);
});

/**
 * Rota responsável por deletar o tipo de arquivo do sistema.
 */
router_add('excluir_tipo_arquivo', function () {
    $id_tipo_arquivo = (string) (isset($_REQUEST['id_tipo_arquivo']) ? (string) $_REQUEST['id_tipo_arquivo'] : '');
    $objeto_tipo_arquivo = new TipoArquivo();

    echo json_encode((array) ['status' => (bool) $objeto_tipo_arquivo->deletar((array) ['id_tipo_arquivo' => $id_tipo_arquivo])]);
    exit;
});

/**
 * Rota responsável por alterar a variável usar do cloudinary
 * 
 * Por padrão apenas um cloudinary pode estar ativo como usar = S.
 * Então se o usuário vai alterar um cloudinary S para N o sistema permite, mas se o usuário for alterar um N para S ele altera o outro S para N.
 * 
 */
router_add('alterar_tipo_cloudinary', function(){
    $objeto_cloudinary = new Cloudinary();

    echo json_encode(['status' => (bool) $objeto_cloudinary->alterar_tipo_cloudinary($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});

/**
 * Rota responsável por excluir o cloudinary do banco de dados.
 * 
 * A opção de exclusão é irreversível, portanto antes de realizar essa opção o sistema pergunta para o usuário se o mesmo deseja realmente realizar a operação de exclusão.
 */
router_add('deletar_cloudinary', function(){
    $objeto_cloudinary = new Cloudinary();

    echo json_encode(['status' => (bool) $objeto_cloudinary->deletar_cloudinary($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit
/** 
 * Rota responsável por alterar ou cadastrar no banco de dados o tamanho máximo de arquivo suportado pelo sistema
*/
router_add('alterar_tamanho_arquivo', function(){
    $objeto_sistema = new Sistema();

    echo json_encode((array) $objeto_sistema->salvar_tamanho_arquivo($_REQUEST), JSON_UNESCAPED_UNICODE);
    exit;
});
?>