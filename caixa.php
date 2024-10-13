<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Caixa.php';

/**
 * Rota responsável por apresentar o formulário de pesquisa onde o usuário pode visalizar todas as informações das caixas que ele cadastrou ou tem acesso para visualizar as informações.
 */
router_add('index', function () {
    require_once 'includes/head.php';
 ?>
    <script>
        const CODIGO_EMPRESA = <?php echo $_SESSION['id_empresa']; ?>;
        const CODIGO_USUARIO = <?php echo $_SESSION['id_usuario'];?>;

        function pesquisar_caixa() {
            sistema.request.post('/caixa.php', {
                'rota': 'pesquisar_caixa_todas',
                'codigo_caixa': sistema.int(document.querySelector('#codigo_caixa').value),
                'codigo_empresa':CODIGO_EMPRESA,
                'codigo_usuario':CODIGO_USUARIO,
                'codigo_prateleira': sistema.int(document.querySelector('#codigo_prateleira').value),
                'nome_caixa': sistema.string(document.querySelector('#nome_caixa').value),
                'codigo_barras': sistema.string(document.querySelector('#codigo_barras').value),
                'forma_visualizacao':sistema.string(document.querySelector('#forma_visualizacao').value)
            }, function(retorno) {
                let caixas = retorno.dados;
                let tamanho_retorno = caixas.length;
                let tabela = document.querySelector('#tabela_caixa tbody');

                tabela = sistema.remover_linha_tabela(tabela);

                if (tamanho_retorno == 0) {
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA CAIXA ENCONTRADA COM OS FILTROS INFORMADOS', 'inner', true, 8));
                    tabela.appendChild(linha);
                } else {
                    sistema.each(caixas, function(index, caixa) {
                        let linha = document.createElement('tr');

                        linha.appendChild(sistema.gerar_td(['text-center'], str_pad(caixa.id_caixa, 3, '0'), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], caixa.nome_caixa, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], caixa.descricao, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], caixa.nome_prateleira, 'inner'));

                        if(caixa.forma_visualizacao == 'PUBLICO'){
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_forma_visualizacao_caixa_' + caixa.id_caixa, caixa.forma_visualizacao, ['btn', 'btn-primary'], function visualizar_caixa_tipo() {}), 'append'));
                        }else{
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_forma_visualizacao_caixa_' + caixa.id_caixa, caixa.forma_visualizacao, ['btn', 'btn-secondary'], function visualizar_caixa_tipo() {}), 'append'));
                        }

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('imprimir_codigo_barra_' + caixa.id_caixa, caixa.codigo_barras, ['btn', 'btn-success'], function imprimir_caixa() {caixa_imprimir_codigo_barras(caixa.id_caixa);}), 'append'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('alterar_tipo_caixa_'+caixa.id_caixa, 'ALTERAR', ['btn', 'btn-info'], function alterar_tipo_caixa(){alterar_forma_visualizacao_caixa(caixa.id_caixa, caixa.id_empresa, caixa.forma_visualizacao)}), 'append'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_caixa_' + caixa.id_caixa, 'VISUALIZAR', ['btn', 'btn-info'], function visualizar_caixa() {cadastrar_caixa(caixa.id_caixa);}), 'append'));

                        tabela.appendChild(linha);
                    });
                }
            }, false);
        }

        /** 
         * Função responsável por alterar a forma de visulização da caixa de pública para privada e deprivada para público.
         * @param {int} id_caixa
         * #param {int} id_empresa
         * @param {string} forma_visualizacao
         */
        function alterar_forma_visualizacao_caixa(id_caixa, id_empresa, forma_visualizacao){
            sistema.request.post('/caixa.php', {'rota': 'alterar_tipo_caixa', 'codigo_caixa': id_caixa, 'codigo_empresa': id_empresa, 'forma_visualizacao': forma_visualizacao}, function(retorno){
                validar_retorno(retorno);
                pesquisar_caixa();
            });
        }

        function caixa_imprimir_codigo_barras(codigo_caixa) {
            window.open(sistema.url('/caixa.php', {
                'rota': 'impressao_codigo_barra_caixa',
                'codigo_caixa': codigo_caixa
            }), 'Impressão de código de barras', 'width=740px, height=500px, scrollbars=yes');
        }

        function cadastrar_caixa(codigo_caixa) {
            window.location.href = sistema.url('/caixa.php', {
                'rota': 'salvar_dados_caixa',
                'codigo_caixa': codigo_caixa
            });
        }

        function pesquisar_prateleiras() {
            sistema.request.post('/prateleira.php', {
                'rota': 'pesquisar_prateleira_todas'
            }, function(retorno) {
                let select = document.querySelector('#codigo_prateleira');

                sistema.each(retorno.dados, function(index, prateleira) {
                    select.appendChild(sistema.gerar_option(prateleira.id_prateleira, prateleira.nome_prateleira));
                });
            });
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
                                <button class="btn btn-secondary custom-radius btn-lg" onclick="cadastrar_caixa(0);">Cadastrar Caixa</button>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-1 text-center">
                                <label class="text">Código</label>
                                <input type="text" sistema-mask="codigo" id="codigo_caixa" class="form-control custom-radius" placeholder="Código" />
                            </div>
                            <div class="col-4 text-center">
                                <label class="text">Nome Caixa</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="nome_caixa" placeholder="Nome Caixa"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Código Barras</label>
                                <input type="text" class="form-control custom-radius" sistema-mask="codigo" maxlength="13" placeholder="Código Barras" id="codigo_barras"/>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Prateleira</label>
                                <select class="form-control custom-radius" id="codigo_prateleira">
                                    <option value="">TODAS PRATELEIRAS</option>

                                </select>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">forma Visualização</label>
                                <select class="form-control custom-radius" id="forma_visualizacao">
                                    <option value="TODOS">TODOS</option>
                                    <option value="PUBLICO">PÚBLICO</option>
                                    <option value="PRIVADO">PRIVADO</option>
                                </select>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <textarea class="form-control custom-radius" placeholder="Descrição" id="descricao"></textarea>
                        </div>
                        <br />
                        <div class="row">
                            <div class="push-10 col-2">
                                <button class="btn btn-info custom-radius btn-lg botao_grande" onclick="pesquisar_caixa();">Pesquisar</button>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="tabela_caixa">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">#</th>
                                                <th scope="col">Nome</th>
                                                <th scope="col">Descrição</th>
                                                <th scope="col">Prateleira</th>
                                                <th scope="col">Tipo</th>
                                                <th scope="col">Código de barras</th>
                                                <th scope="col">Alt. Tipo</th>
                                                <th scope="col">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="8" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA PESQUISA</td>
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
            pesquisar_prateleiras();
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
    $codigo_caixa = (int) (isset($_REQUEST['codigo_caixa']) ? (int) intval($_REQUEST['codigo_caixa'], 10) : 0);
    require_once 'includes/head.php';
 ?>
    <script>
        const CODIGO_EMPRESA = <?php echo $_SESSION['id_empresa']; ?>;
        const CODIGO_USUARIO = <?php echo $_SESSION['id_usuario']; ?>;

        let CODIGO_CAIXA = <?php echo $codigo_caixa; ?>;

        /** 
         * Função responsável por validar os dados e enviar para o back acionar a rota de cadastro de caixas.
         */
        function salvar_dados() {
            let codigo_caixa = sistema.int(document.querySelector('#codigo_caixa').value);
            let codigo_prateleira = sistema.int(document.querySelector('#codigo_prateleira').value);
            let nome_caixa = sistema.string(document.querySelector('#nome_caixa').value);
            let descricao = sistema.string(document.querySelector('#descricao').value);
            let codigo_barras = sistema.string(document.querySelector('#codigo_barras').value);
            let forma_visualizacao = sistema.string(document.querySelector('#forma_visualizacao').value);

            sistema.request.post('/caixa.php', {
                'rota': 'salvar_dados',
                'codigo_caixa': codigo_caixa,
                'codigo_empresa': CODIGO_EMPRESA,
                'codigo_usuario': CODIGO_USUARIO,
                'codigo_prateleira': codigo_prateleira,
                'nome_caixa': nome_caixa,
                'descricao': descricao,
                'codigo_barras': codigo_barras,
                'forma_visualizacao': forma_visualizacao
            }, function(retorno) {
                sistema.verificar_status(retorno.status, sistema.url('/caixa.php', {
                    'rota': 'index'
                }));
            });
        }

        /** 
         * Função responsável por pesquisar as prateleiras cadastradas no sistema.
         * E retornar apenas as que o usuário pode ter acesso dentro da empresa que o mesmo está cadastrado.
         */
        function pesquisar_prateleiras() {
            sistema.request.post('/prateleira.php', {
                'rota': 'pesquisar_prateleira_todas',
                'codigo_empresa':CODIGO_EMPRESA,
                'codigo_usuario':CODIGO_USUARIO
            }, function(retorno) {
                let select = document.querySelector('#codigo_prateleira');

                sistema.each(retorno.dados, function(index, prateleira) {
                    select.appendChild(sistema.gerar_option(prateleira.id_prateleira, prateleira.nome_prateleira));
                });
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
            document.querySelector('#forma_visualizacao').value = '';
            document.querySelector('#descricao').value = '';

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
                            <div class="col-1 text-center">
                                <label class="text">Código</label>
                                <input type="text" sistema-mask="codigo" id="codigo_caixa" class="form-control custom-radius text-center" placeholder="Código" disabled="true" />
                            </div>
                            <div class="col-5 text-center">
                                <label class="text">Nome Caixa</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="nome_caixa" placeholder="Nome Caixa" />
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Prateleira</label>
                                <select class="form-control custom-radius" id="codigo_prateleira">
                                    <option value="0">Selecione uma Prateleira</option>
                                </select>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Forma Visualização</label>
                                <select class="form-control custom-radius" id="forma_visualizacao">
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
                        <br />
                        <div class="row">
                            <div class="col-12">
                                <textarea class="form-control custom-radius" id="descricao" placeholder="Descrição"></textarea>
                            </div>
                        </div>
                        <br />
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

            await pesquisar_prateleiras();

            window.setTimeout(function() {
                if (CODIGO_CAIXA != 0) {
                    sistema.request.post('/caixa.php', {
                        'rota': 'pesquisar_caixa',
                        'codigo_caixa': CODIGO_CAIXA
                    }, function(retorno) {
                        document.querySelector('#codigo_caixa').value = retorno.dados.id_caixa;
                        document.querySelector('#codigo_prateleira').value = retorno.dados.id_prateleira;
                        document.querySelector('#nome_caixa').value = retorno.dados.nome_caixa;
                        document.querySelector('#descricao').value = retorno.dados.descricao;
                        document.querySelector('#codigo_barras').value = retorno.dados.codigo_barras;
                        document.querySelector('#forma_visualizacao').value = retorno.dados.forma_visualizacao;
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

    echo json_encode(['status' => (bool) $objeto_caixa->salvar($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit pesquisar_caixa
router_add('pesquisar_caixa', function () {
    $id_caixa = (int) (isset($_REQUEST['codigo_caixa']) ? (int) intval($_REQUEST['codigo_caixa'], 10) : 0);
    $objeto_caixa = new Caixa();
    $filtro = (array) [];
    $dados = (array) ['filtro' => (array) []];

    if ($id_caixa != '') {
        array_push($filtro, ['id_caixa', '===', (int) $id_caixa]);
        $dados['filtro'] = (array) ['and' => (array) $filtro];
    }

    echo json_encode(['dados' => (array) $objeto_caixa->pesquisar($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});

/**
 * Rota responsável por realizar a pesquisa de todas as caixas cadastradas no sistema e retornar as informações para a rota que realizou o chamado
 */
router_add('pesquisar_caixa_todas', function () {
    $id_caixa = (int) (isset($_REQUEST['codigo_caixa']) ? (int) intval($_REQUEST['codigo_caixa'], 10) : 0);
    $id_prateleira = (int) (isset($_REQUEST['codigo_prateleira']) ? (int) intval($_REQUEST['codigo_prateleira'], 10) : 0);
    $id_empresa = (int) (isset($_REQUEST['codigo_empresa']) ? (int) intval($_REQUEST['codigo_empresa'], 10):0);
    $id_usuario = (int) (isset($_REQUEST['codigo_usuario']) ? (int) intval($_REQUEST['codigo_usuario'], 10):0);
    
    $nome_caixa = (string) (isset($_REQUEST['nome_caixa']) ? (string) strtoupper($_REQUEST['nome_caixa']) : '');
    $codigo_barras = (string) (isset($_REQUEST['codigo_barras']) ? (string) $_REQUEST['codigo_barras'] : '');
    $descricao = (string) (isset($_REQUEST['descricao']) ? (string) $_REQUEST['descricao']:'');
    $forma_visualizacao = (string) (isset($_REQUEST['forma_visualizacao']) ? (string) $_REQUEST['forma_visualizacao']:'');

    $filtro = (array) [];
    $filtro_todos_publico = (array) [];
    $filtro_todos_privado = (array) [];

    $dados = (array) ['filtro' => (array) [], 'ordenacao' => (array) ['nome_caixa' => (bool) true], 'limite' => (int) 0];
    $retorno = (array) [];

    $objeto_caixa = new Caixa();

    if ($id_caixa != 0) {
        array_push($filtro, (array) ['id_caixa', '===', (int) $id_caixa]);
        array_push($filtro_todos_publico, (array) ['id_caixa', '===', (int) $id_caixa]);
        array_push($filtro_todos_privado, (array) ['id_caixa', '===', (int) $id_caixa]);
    }

    if($id_prateleira != 0){
        array_push($filtro, (array) ['id_prateleira', '===', (int) $id_prateleira]);
        array_push($filtro_todos_publico, (array) ['id_prateleira', '===', (int) $id_prateleira]);
        array_push($filtro_todos_privado, (array) ['id_prateleira', '===', (int) $id_prateleira]);
    }

    if($id_empresa != 0){
        array_push($filtro, (array) ['id_empresa', '===', (int) $id_empresa]);
        array_push($filtro_todos_publico, (array) ['id_empresa', '===', (int) $id_empresa]);
        array_push($filtro_todos_privado, (array) ['id_empresa', '===', (int) $id_empresa]);
    }

    if($id_usuario != 0){
        array_push($filtro, (array) ['id_usuario', '===', (int) $id_usuario]);
        array_push($filtro_todos_publico, (array) ['id_usuario', '===', (int) $id_usuario]);
        array_push($filtro_todos_privado, (array) ['id_usuario', '===', (int) $id_usuario]);
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

    if($forma_visualizacao == 'PRIVADO'){
        array_push($filtro, (array) ['id_usuario', '===', (int) $id_usuario]);
    }

    if($forma_visualizacao == 'PRIVADO' || $forma_visualizacao == 'PUBLICO'){
        array_push($filtro, (array) ['forma_visualizacao', '===', (string) $forma_visualizacao]);
        $dados['filtro'] = (array) ['and' => (array) $filtro];

        $retorno = (array) $objeto_caixa->validar_dados_pesquisa_caixa($dados, $id_empresa, $retorno);
    }else{
        array_push($filtro_todos_privado, (array) ['id_usuario', '===', (int) $id_usuario]);
        array_push($filtro_todos_privado, (array) ['forma_visualizacao', '===', (string) 'PRIVADO']);
        array_push($filtro_todos_publico, (array) ['forma_visualizacao', '===', (string) 'PUBLICO']);

        $dados['filtro'] = (array) ['and' => (array) $filtro_todos_privado];
        $retorno = (array) $objeto_caixa->validar_dados_pesquisa_caixa($dados, $id_empresa, $retorno);

        $dados['filtro'] = (array) ['and' => (array) $filtro_todos_publico];
        $retorno = (array) $objeto_caixa->validar_dados_pesquisa_caixa($dados, $id_empresa, $retorno);
    }

    

    echo json_encode(['dados' => (array) $retorno], JSON_UNESCAPED_UNICODE);
    exit;
});

/**
 * Rota responsável por realizar a transição entre o front e back da alteração de forma de visualização.
 */
router_add('alterar_tipo_caixa', function(){
    $objeto_caixa = new Caixa();
    
    echo json_encode(['status' => (bool) $objeto_caixa->alterar_forma_visualizacao($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});
?>