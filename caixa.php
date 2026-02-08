<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Caixa.php';
require_once 'Modelos/Preferencia.php';

/**
 * Rota responsável por apresentar o formulário de pesquisa onde o usuário pode visalizar todas as informações das caixas que ele cadastrou ou tem acesso para visualizar as informações.
 */
router_add('index', function () {
    require_once 'includes/head.php';

    $objeto_preferencia = new Preferencia();
    $usuario_preferencia_nome_caixa = (string) 'CHECKED';
    $usuario_preferencia_pesquisar_caixa_automaticamente = (string) 'CHECKED';
    $usuario_preferencia_quantidade_caixa = (int) intval(25, 10);

    $array_filtro_preferencia_usuario = (array) ['usuario' => convert_id(CODIGO_USUARIO), 'sistema' => convert_id(CODIGO_SISTEMA), 'nome_preferencia' => (string) 'NOME_COMPLETO_CAIXA'];

    $usuario_preferencia_nome_caixa = (string) $objeto_preferencia->pesquisar_preferencia_usuario($array_filtro_preferencia_usuario);
    
    $array_filtro_preferencia_usuario['nome_preferencia'] = (string) 'PESQUISAR_CAIXA_AUTOMATICAMENTE';
    $usuario_preferencia_pesquisar_caixa_automaticamente = (string) $objeto_preferencia->pesquisar_preferencia_usuario($array_filtro_preferencia_usuario);

    $array_filtro_preferencia_usuario['nome_preferencia'] = (string) 'QUANTIDADE_LIMITE_CAIXA';
    $usuario_preferencia_quantidade_caixa = (int) $objeto_preferencia->pesquisar_preferencia_quantidade_retorno($array_filtro_preferencia_usuario);

    ?>
    <script>
        
        const CODIGO_EMPRESA = "<?php echo CODIGO_EMPRESA; ?>";
        const CODIGO_USUARIO = "<?php echo CODIGO_USUARIO;?>";
        const CODIGO_SISTEMA = "<?php echo CODIGO_SISTEMA; ?>";
        const PREFERENCIA_QUANTIDADE_CAIXA = <?php echo $usuario_preferencia_quantidade_caixa; ?>;
        const PESQUISAR_CAIXA_AUTOMATICAMENTE = "<?php echo $usuario_preferencia_pesquisar_caixa_automaticamente; ?>";

        function pesquisar_caixa() {
            let visualizar_nome_completo = document.querySelector('#visualizar_nome_caixa_completo');
            
            sistema.request.post('/caixa.php', {
                'rota': 'pesquisar_caixa_todas',
                'empresa':CODIGO_EMPRESA,
                'usuario':CODIGO_USUARIO,
                'sistema': CODIGO_SISTEMA,
                'prateleira': document.querySelector('#codigo_prateleira').value,
                'nome_caixa': sistema.string(document.querySelector('#nome_caixa').value),
                'descricao': sistema.string(document.querySelector('#descricao').value),
                'codigo_barras': sistema.string(document.querySelector('#codigo_barras').value),
                'tipo':sistema.string(document.querySelector('#tipo').value),
                'status':document.querySelector('#status').value,
                'limite_retorno': PREFERENCIA_QUANTIDADE_CAIXA,
                'preferencia_usuario': sistema.int(document.querySelector('#limite_retorno').value)
            }, function(retorno) {
                let caixas = retorno.dados;
                let tamanho_retorno = caixas.length;
                let tabela = document.querySelector('#tabela_caixa tbody');

                tabela = sistema.remover_linha_tabela(tabela);

                if (tamanho_retorno == 0) {
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA CAIXA ENCONTRADA COM OS FILTROS INFORMADOS', 'inner', true, 7));
                    tabela.appendChild(linha);
                } else {
                    sistema.each(caixas, function(index, caixa) {
                        let linha = document.createElement('tr');

                        if(visualizar_nome_completo.checked == false){
                            linha.appendChild(sistema.gerar_td(['text-left'], caixa.nome_caixa.substring(0,15), 'inner'));
                            linha.appendChild(sistema.gerar_td(['text-left'], caixa.descricao.substring(0,40), 'inner'));
                        }else{
                            linha.appendChild(sistema.gerar_td(['text-left'], caixa.nome_caixa, 'inner'));
                            linha.appendChild(sistema.gerar_td(['text-left'], caixa.descricao, 'inner'));
                        }

                        linha.appendChild(sistema.gerar_td(['text-center'], caixa.nome_prateleira, 'inner'));

                        if(caixa.tipo == 'PUBLICO'){
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_forma_visualizacao_caixa_' + caixa._id.$oid, caixa.tipo, ['btn', 'btn-primary'], function visualizar_caixa_tipo() {}), 'append'));
                        }else{
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_forma_visualizacao_caixa_' + caixa._id.$oid, caixa.tipo, ['btn', 'btn-secondary'], function visualizar_caixa_tipo() {}), 'append'));
                        }
                        if(caixa.status == 'ATIVO'){
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_forma_visualizacao_caixa_' + caixa._id.$oid, caixa.status, ['btn', 'btn-success'], function visualizar_caixa_status() {}), 'append'));
                        }else{
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_forma_visualizacao_caixa_' + caixa._id.$oid, caixa.status, ['btn', 'btn-danger'], function visualizar_caixa_status() {}), 'append'));
                        }

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('imprimir_codigo_barra_' + caixa.id_caixa, caixa.codigo_barras, ['btn', 'btn-success'], function imprimir_caixa() {caixa_imprimir_codigo_barras(caixa._id.$oid);}), 'append'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_caixa_' + caixa._id.$oid, 'VISUALIZAR', ['btn', 'btn-info'], function visualizar_caixa() {cadastrar_caixa(caixa._id.$oid);}), 'append'));

                        tabela.appendChild(linha);
                    });
                }
            }, false);
        }

        /** 
         * Função responsável por chamar a rota de impressão de código de barras
        */
        function caixa_imprimir_codigo_barras(codigo_caixa) {
            window.open(sistema.url('/caixa.php', {
                'rota': 'impressao_codigo_barra_caixa',
                'codigo_caixa': codigo_caixa
            }), 'Impressão de código de barras', 'width=740px, height=500px, scrollbars=yes');
        }

        /**
         * Função responsável por chamar a rota de cadastro de uma nova caixa, abrindo o formulário para o usuário preenxer as informações
        */
        function cadastrar_caixa(codigo_caixa) {
            window.location.href = sistema.url('/caixa.php', {
                'rota': 'salvar_dados_caixa',
                'codigo_caixa': codigo_caixa
            });
        }

        /** 
         * Função responsável por colocar na base de dados a preferência que o usuário selecionou em sistema.
        */
        function alterar_preferencia_nome_completo_caixa(){
            let check_visualizar_nome_caixa = document.querySelector('#visualizar_nome_caixa_completo');
            let preferencia = '';

            if(check_visualizar_nome_caixa.checked == true){
                preferencia = 'CHECKED';
            }else{
                preferencia = '';
            }

            sistema.request.post('/preferencia.php', {'rota':'salvar_preferencia_usuario', 'codigo_sistema': CODIGO_SISTEMA, 'codigo_usuario': CODIGO_USUARIO, 'nome_preferencia': 'NOME_COMPLETO_CAIXA', 'preferencia': preferencia}, function(retorno){}, false);
        }

        /** 
         * Função responsável por colocar no componente no form a quantidade de preferência que o usuário escolheu.
        */
        function colocar_preferencia_quantidade_caixa(){
            let objeto_limite_caixa = document.querySelector('#limite_retorno');
            objeto_limite_caixa.value = PREFERENCIA_QUANTIDADE_CAIXA;
        }

        /** 
         * Função responsável por colocar na base de dados se o usuário prefere pesquisar as caixas automáticamente ou não.
        */
        function alterar_preferencia_pesquisar_caixa_automaticamente(){
            let check_pesquisar_caixa_automaticamente = document.querySelector('#pesquisar_caixa_automaticamente');
            let preferencia = '';

            if(check_pesquisar_caixa_automaticamente.checked == true){
                preferencia = 'CHECKED';
            }else{
                preferencia = '';
            }

            sistema.request.post('/preferencia.php', {'rota': 'salvar_preferencia_usuario', 'codigo_sistema': CODIGO_SISTEMA, 'codigo_usuario': CODIGO_USUARIO, 'nome_preferencia': 'PESQUISAR_CAIXA_AUTOMATICAMENTE', 'preferencia': preferencia}, function (retorno){}, false);
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Pesquisa de Caixas</h4>
                        <br />
                        <div class="row">
                            <div class="col-3">
                                <button class="btn btn-secondary custom-radius btn-lg" onclick="cadastrar_caixa('');">Cadastrar Caixa</button>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-5 text-center">
                                <label class="text">Nome Caixa</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="nome_caixa" placeholder="Nome Caixa"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Código Barras</label>
                                <input type="text" class="form-control custom-radius" sistema-mask="codigo" maxlength="13" placeholder="Código Barras" id="codigo_barras"/>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Tipo</label>
                                <select class="form-control custom-radius" id="tipo">
                                    <option value="TODOS">TODOS</option>
                                    <option value="PUBLICO">PÚBLICO</option>
                                    <option value="PRIVADO">PRIVADO</option>
                                </select>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Status</label>
                                <select class="form-control custom-radius" id="status">
                                    <option value="TODOS">TODOS</option>
                                    <option value="ATIVO">ATIVO</option>
                                    <option value="INATIVO">INATIVO</option>
                                </select>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <textarea class="form-control custom-radius" placeholder="Descrição" id="descricao"></textarea>
                        </div>
                        <br/>
                        <div class="row">
                            <?php
                                $abrir_modal_organizacao = 'caixa';
                                $abrir_modal_amrario = 'caixa';
                                $abrir_modal_prateleira = 'caixa';
                                require_once 'includes/modal_organizacao.php';
                                require_once 'includes/modal_armario.php';
                                require_once 'includes/modal_prateleira.php';
                            ?>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-2 text-center">
                                <select class="form-control custom-radius" id="limite_retorno">
                                    <option value="25">25 CAIXAS</option>
                                    <option value="50">50 CAIXAS</option>
                                    <option value="75">75 CAIXAS</option>
                                    <option value="100">100 CAIXAS</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="push-9 col-3 text-center">
                            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                    <button type="button" class="btn btn-info custom-radius botao_grande btn-lg" onclick="pesquisar_caixa();">PESQUISAR CAIXA</button>
                                    <div class="btn-group" role="group">
                                        <button type="button"
                                            class="btn btn-secondary dropdown-toggle custom-radius botao_grande btn-lg" data-toggle="dropdown" aria-expanded="false"> PREFERÊNCIAS </button>
                                        <div class="dropdown-menu">
                                            <div class="dropdown-item">
                                                <input class="form-check-item" type="checkbox" role="switch" id="visualizar_nome_caixa_completo" <?php echo $usuario_preferencia_nome_caixa; ?>  onclick="alterar_preferencia_nome_completo_caixa();" />
                                                <label class="form-check-label" for="visualizar_nome_caixa_completo">Ver nome/descrição completos</label>
                                            </div>
                                            <div class="dropdown-item">
                                                <input class="form-check-item" type="checkbox" role="switch" id="pesquisar_caixa_automaticamente" <?php echo $usuario_preferencia_pesquisar_caixa_automaticamente; ?> onclick="alterar_preferencia_pesquisar_caixa_automaticamente();"/>
                                                <label class="form-check-label" for="pesquisar_caixa_automaticamente">Pesquisar Caixa Automáticamente</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="tabela_caixa">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">Nome</th>
                                                <th scope="col">Descrição</th>
                                                <th scope="col">Prateleira</th>
                                                <th scope="col">Tipo</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Código de barras</th>
                                                <th scope="col">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="7" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA PESQUISA</td>
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
            validar_acesso_administrador('<?php echo TIPO_USUARIO; ?>');

            if(PESQUISAR_CAIXA_AUTOMATICAMENTE == 'CHECKED'){
                pesquisar_caixa();
            }
        }
    </script>
<?php
    require_once 'includes/footer.php';
    exit;
});

/**
 * Rota responsável por realizar a impressão dos código de barras.
 */
router_add('impressao_codigo_barra_caixa', function () {
    $codigo_caixa = (int) (isset($_REQUEST['codigo_caixa']) ? intval($_REQUEST['codigo_caixa'], 10) : 0);
    $filtro = (array) ['filtro' => (array) ['id_caixa', '===', (int) $codigo_caixa]];
    $nome_caixa = (string) '';
    $codigo_barra = (string) '';

    $objeto_caixa = new Caixa();
    $retorno_pesquisa_caixa = (array) $objeto_caixa->pesquisar($filtro);

    if (array_key_exists('nome_caixa', $retorno_pesquisa_caixa) == true) {
        $nome_caixa = (string) $retorno_pesquisa_caixa['nome_caixa'];
    }

    if (array_key_exists('codigo_barras', $retorno_pesquisa_caixa) == true) {
        $codigo_barra = (string) $retorno_pesquisa_caixa['codigo_barras'];
    }

    require_once 'includes/head_relatorio.php';
 ?>
    <script>
        const CODIGO_BARRAS = "<?php echo $codigo_barra; ?>";

        function gerar_codigo_barras() {
            JsBarcode("#barcode")
                .options({
                    font: "OCR-B"
                })
                .CODE128(CODIGO_BARRAS, {
                    fontSize: 25,
                    textMargin: 0
                })
                .blank(20)
                .render();
        }

        function fechar_janela() {
            window.close();
        }

        function imprimir_conteudo() {
            document.querySelector('#botao_fechar').style.display = 'none';
            document.querySelector('#botao_imprimir').style.display = 'none';

            let card_impressao = document.querySelector('#card_impressao');

            card_impressao.classList.remove();
            card_impressao.classList.add('col-12');

            window.print();

            window.setTimeout(function() {
                document.querySelector('#botao_fechar').style.display = 'block';
                document.querySelector('#botao_imprimir').style.display = 'block';

                card_impressao.classList.remove();
                card_impressao.classList.add('col-8');
            }, 500);
        }
    </script>
    <div class="row">
        <div class="col-8" id="card_impressao">
            <div class="card text-center">
                <div class="card-header">
                    <?php echo $nome_caixa; ?>
                </div>
                <div class="card-body">
                    <svg id="barcode"></svg>
                </div>
            </div>
        </div>
        <div class="col-2" id="botao_imprimir">
            <br />
            <button class="btn btn-info btn-lg" onclick="imprimir_conteudo();">IMPRIMIR</button>
        </div>
        <div class="col-2" id="botao_fechar">
            <br />
            <button class="btn btn-danger btn-lg" onclick="fechar_janela();">FECHAR</button>
        </div>
    </div>
    <script>
        window.onload = function() {
            gerar_codigo_barras();
        }
    </script>
 <?php
    require_once 'includes/footer_relatorio.php';
});

/**
 * Rota responsável por capturar as variáveis que o ususário informa das caixas para cadastro dentro do sistema.
 */
router_add('salvar_dados_caixa', function () {
    $codigo_caixa = (string) (isset($_REQUEST['codigo_caixa']) ? (string) $_REQUEST['codigo_caixa'] : '');
    require_once 'includes/head.php';
    ?>
    <script>
        const CODIGO_EMPRESA = "<?php echo CODIGO_EMPRESA; ?>";
        const CODIGO_USUARIO = "<?php echo CODIGO_USUARIO;?>";
        const CODIGO_SISTEMA = "<?php echo CODIGO_SISTEMA; ?>";

        let CODIGO_CAIXA = "<?php echo $codigo_caixa; ?>";

        /** 
         * Função responsável por validar os dados e enviar para o back acionar a rota de cadastro de caixas.
         */
        function salvar_dados() {
            let codigo_caixa = document.querySelector('#codigo_caixa').value;
            let codigo_prateleira = document.querySelector('#codigo_prateleira').value;
            let nome_caixa = sistema.string(document.querySelector('#nome_caixa').value);
            let descricao = sistema.string(document.querySelector('#descricao').value);
            let codigo_barras = sistema.string(document.querySelector('#codigo_barras').value);
            let tipo = sistema.string(document.querySelector('#tipo').value);
            let status = document.querySelector('#status').value;

            sistema.request.post('/caixa.php', {
                'rota': 'salvar_dados',
                'codigo_caixa': CODIGO_CAIXA,
                'codigo_empresa': CODIGO_EMPRESA,
                'codigo_usuario': CODIGO_USUARIO,
                'codigo_prateleira': codigo_prateleira,
                'nome_caixa': nome_caixa,
                'descricao': descricao,
                'codigo_barras': codigo_barras,
                'tipo': tipo,
                'status':status
            }, function(retorno) {
                validar_retorno(retorno, '/caixa.php');
            });
        }

        /** 
         * Função responsável por retornar a rota index do módulo de cadastro de caixas.
         */
        function voltar() {
            window.location.href = sistema.url('/caixa.php', {
                'rota': 'index'
            });
        }

        /** 
         * Função responsável por limpar todos os campos.
         */
        function limpar_campos(){
            document.querySelector('#codigo_caixa').value = '';
            document.querySelector('#nome_caixa').value = '';
            document.querySelector('#codigo_prateleira').value = '';
            document.querySelector('#codigo_prateleira').value = '';
            document.querySelector('#tipo').value = '';
            document.querySelector('#descricao').value = '';
            document.querySelector('#status').value = '';

            sistema.request.post('/index.php', {'rota': 'buscar_codigo_barras'}, function(retorno){
                document.querySelector('#codigo_barras').value = retorno.codigo_barras;
            }, false);
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Cadastro de Caixa</h4>
                        <br />
                        <div class="row">
                            <input type="hidden" id="codigo_caixa" class="form-control custom-radius text-center" placeholder="Código" disabled="true" />
                            <div class="col-6 text-center">
                                <label class="text">Nome Caixa</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="nome_caixa" placeholder="Nome Caixa" />
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Status</label>
                                <select class="form-control custom-radius" id="status">
                                    <option value="">Selecione uma opção</option>
                                    <option value="ATIVO">ATIVO</option>
                                    <option value="INATIVO">INATIVO</option>
                                </select>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Tipo</label>
                                <select class="form-control custom-radius" id="tipo">
                                    <option value="">Selecione uma opção</option>
                                    <option value="PUBLICO">PÚBLICO</option>
                                    <option value="PRIVADO">PRIVADO</option>
                                </select>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Código Barras</label>
                                <input type="text" class="form-control custom-radius text-center" sistema-mask="codigo" maxlength="13" placeholder="Código Barras" value="<?php echo codigo_barras(); ?>" disabled="true" id="codigo_barras" />
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-12">
                                <textarea class="form-control custom-radius" id="descricao" placeholder="Descrição"></textarea>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <?php
                                $abrir_modal_organizacao = 'caixa';
                                $abrir_modal_amrario = 'caixa';
                                $abrir_modal_prateleira = 'caixa';
                                require_once 'includes/modal_organizacao.php';
                                require_once 'includes/modal_armario.php';
                                require_once 'includes/modal_prateleira.php';
                            ?>
                        </div>
                        <br/>
                        <?php
                        require_once 'includes/botao_cadastro.php';
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.onload = async function() {
            validar_acesso_administrador('<?php echo $_SESSION['tipo_usuario']; ?>');

            window.setTimeout(function() {
                if (CODIGO_CAIXA != '') {
                    sistema.request.post('/caixa.php', {
                        'rota': 'pesquisar_caixa',
                        'codigo_caixa': CODIGO_CAIXA
                    }, function(retorno) {
                        document.querySelector('#codigo_caixa').value = retorno.dados._id.$oid;
                        document.querySelector('#codigo_prateleira').value = retorno.dados.prateleira.$oid;
                        document.querySelector('#nome_caixa').value = retorno.dados.nome_caixa;
                        document.querySelector('#descricao').value = retorno.dados.descricao;
                        document.querySelector('#codigo_barras').value = retorno.dados.codigo_barras;
                        document.querySelector('#tipo').value = retorno.dados.tipo;
                        document.querySelector('#status').value = retorno.dados.status;
                    }, false);
                }
            }, 500);
        }
    </script>
 <?php
    require_once 'includes/footer.php';
    exit;
});

//@audit salvar_dados
router_add('salvar_dados', function () {
    $objeto_caixa = new Caixa();

    echo json_encode((array) ['status' => (bool) $objeto_caixa->salvar_dados($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit pesquisar_caixa
router_add('pesquisar_caixa', function () {
    $id_caixa = (string) (isset($_REQUEST['codigo_caixa']) ? (string) $_REQUEST['codigo_caixa'] : '');
    $objeto_caixa = new Caixa();
    $filtro = (array) [];
    $dados = (array) ['filtro' => (array) []];

    if ($id_caixa != '') {
        array_push($filtro, ['_id', '===', convert_id($id_caixa)]);
        $dados['filtro'] = (array) ['and' => (array) $filtro];
    }

    echo json_encode(['dados' => (array) $objeto_caixa->pesquisar($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});

/**
 * Rota responsável por realizar a pesquisa de todas as caixas cadastradas no sistema e retornar as informações para a rota que realizou o chamado
 */
router_add('pesquisar_caixa_todas', function () {
    $objeto_caixa = new Caixa();
    $objeto_preferencia = new Preferencia();

    $prateleira = (string) (isset($_REQUEST['prateleira']) ? (string) $_REQUEST['prateleira'] : '');
    $empresa = (string) (isset($_REQUEST['empresa']) ? (string) $_REQUEST['empresa']:'');
    $usuario = (string) (isset($_REQUEST['usuario']) ? (string) $_REQUEST['usuario']:'');
    $sistema = (string) (isset($_REQUEST['sistema']) ? (string) $_REQUEST['sistema']:'');
    
    $nome_caixa = (string) (isset($_REQUEST['nome_caixa']) ? (string) strtoupper($_REQUEST['nome_caixa']) : '');
    $codigo_barras = (string) (isset($_REQUEST['codigo_barras']) ? (string) $_REQUEST['codigo_barras'] : '');
    $descricao = (string) (isset($_REQUEST['descricao']) ? (string) $_REQUEST['descricao']:'');
    $tipo = (string) (isset($_REQUEST['tipo']) ? (string) $_REQUEST['tipo']:'TODOS');
    $status = (string) (isset($_REQUEST['status']) ? (string) $_REQUEST['status']:'TODOS');

    $limite_retorno = (int) (isset($_REQUEST['limite_retorno']) ? (int) intval($_REQUEST['limite_retorno'], 10):0);
    $preferencia_usuario = (int) (isset($_REQUEST['preferencia_usuario']) ? (int) intval($_REQUEST['preferencia_usuario'], 10):0);

    $filtro = (array) [];
    $filtro_todos_publico = (array) [];
    $filtro_todos_privado = (array) [];

    $dados = (array) ['filtro' => (array) [], 'ordenacao' => (array) ['nome_caixa' => (bool) true], 'limite' => (int) $limite_retorno];
    $retorno = (array) [];

    if($prateleira != ''){
        array_push($filtro, (array) ['prateleira', '===', convert_id($prateleira)]);
        array_push($filtro_todos_publico, (array) ['prateleira', '===', convert_id($prateleira)]);
        array_push($filtro_todos_privado, (array) ['prateleira', '===', convert_id($prateleira)]);
    }

    if($empresa != ''){
        array_push($filtro, (array) ['empresa', '===', convert_id($empresa)]);
        array_push($filtro_todos_publico, (array) ['empresa', '===', convert_id($empresa)]);
        array_push($filtro_todos_privado, (array) ['empresa', '===', convert_id($empresa)]);
    }

    if($nome_caixa != ''){
        array_push($filtro, (array) ['nome_caixa', '=', (string) $nome_caixa]);
        array_push($filtro_todos_publico, (array) ['nome_caixa', '=', (string) $nome_caixa]);
        array_push($filtro_todos_privado, (array) ['nome_caixa', '=', (string) $nome_caixa]);
    }

    if($codigo_barras != ''){
        array_push($filtro, (array) ['codigo_barras', '===', (string) $codigo_barras]);
        array_push($filtro_todos_publico, (array) ['codigo_barras', '===', (string) $codigo_barras]);
        array_push($filtro_todos_privado, (array) ['codigo_barras', '===', (string) $codigo_barras]);
    }

    if($descricao != ''){
        array_push($filtro, (array) ['descricao', '=', (string) $descricao]);
        array_push($filtro_todos_publico, (array) ['descricao', '=', (string) $descricao]);
        array_push($filtro_todos_privado, (array) ['descricao', '=', (string) $descricao]);
    }

    if($status != 'TODOS'){
        array_push($filtro, (array) ['status', '===', (string) $status]);
        array_push($filtro_todos_privado, ['status', '===', (string) $status]);
        array_push($filtro_todos_publico, ['status', '===', (string) $status]);
    }

    if($tipo == 'PRIVADO'){
        array_push($filtro, (array) ['usuario', '===', convert_id($usuario)]);
    }

    if($tipo == 'PRIVADO' || $tipo == 'PUBLICO'){
        array_push($filtro, (array) ['tipo', '===', (string) $tipo]);
        $dados['filtro'] = (array) ['and' => (array) $filtro];

        $retorno = (array) $objeto_caixa->validar_dados_pesquisa_caixa($dados, $retorno);
    }else{
        array_push($filtro_todos_privado, (array) ['usuario', '===', convert_id($usuario)]);
        array_push($filtro_todos_privado, (array) ['tipo', '===', (string) 'PRIVADO']);
        array_push($filtro_todos_publico, (array) ['tipo', '===', (string) 'PUBLICO']);

        $dados['filtro'] = (array) ['and' => (array) $filtro_todos_privado];
        $retorno = (array) $objeto_caixa->validar_dados_pesquisa_caixa($dados, $retorno);

        $dados['filtro'] = (array) ['and' => (array) $filtro_todos_publico];
        $retorno = (array) $objeto_caixa->validar_dados_pesquisa_caixa($dados, $retorno);
    }

    
    $objeto_preferencia->alterar_quantidade_retorno($preferencia_usuario, $limite_retorno, 'QUANTIDADE_LIMITE_CAIXA', $usuario, $sistema);

    echo json_encode(['dados' => (array) $retorno], JSON_UNESCAPED_UNICODE);
    exit;
});
?>