<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Armario.php';

/**
 * Rota index
 * TODO primeira rota dentro do sistema
 */
router_add('index', function () {
    require_once 'includes/head.php';
?>
    <script>
        function cadastro_armario(codigo_armario) {
            window.location.href = sistema.url('/armario.php', {
                'rota': 'salvar_alterar_dados',
                'codigo_armario': codigo_armario
            });
        }

        function pesquisar_armario() {
            let codigo_armario = parseInt(document.querySelector('#codigo_armario').value, 10);
            let nome_armario = document.querySelector('#nome_armario').value;

            if (isNaN(codigo_armario)) {
                codigo_armario = 0;
            }

            sistema.request.post('/armario.php', {
                'rota': 'pesquisar_armario_todos',
                'codigo_armario': codigo_armario,
                'nome_armario': nome_armario
            }, function(retorno) {
                let retorno_armario = retorno.dados;
                let tamanho_retorno = retorno_armario.length;
                let tabela = document.querySelector('#tabela_armario tbody');
                let tamanho_tabela = tabela.rows.length;

                if (tamanho_tabela > 0) {
                    tabela = sistema.remover_linha_tabela(tabela);
                }

                if (tamanho_retorno < 1) {
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM ARMÁRIO ENCONTRADO', 'inner', true, 5));
                    tabela.appendChild(linha);
                } else {
                    sistema.each(retorno_armario, function(contador, armario) {
                        let linha = document.createElement('tr');
                        linha.appendChild(sistema.gerar_td(['text-center'], armario.id_armario, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], armario.nome_armario, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], armario.codigo_barras, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_armario_' + armario.id_armario, 'VISUALIZAR', ['btn', 'btn-info'], function visualizar() {
                            cadastro_armario(armario.id_armario)
                        }), 'append'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('imprimir_codigo_barra_armario_' + armario.id_armario, 'IMPRIMIR CÓDIGO DE BARRAS', ['btn', 'btn-success'], function impressao() {
                            imprimir_codigo_barra_armario(armario.id_armario)
                        }), 'append'));
                        tabela.appendChild(linha);
                    });
                }
            }, false);
        }

        function imprimir_codigo_barra_armario(codigo_armario) {
            window.open(sistema.url('/armario.php', {
                'rota': 'impressao_codigo_barras_armario',
                'codigo_armario': codigo_armario
            }), 'Impressão de código de barras', 'width=740px, height=500px, scrollbars=yes');
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Pesquisa de Armários</h4>
                        <div class="row">
                            <div class="col-3">
                                <button class="btn btn-secondary" onclick="cadastro_armario(0);">Cadastro de Armários</button>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-2 text-center">
                                <label class="text">Código</label>
                                <input type="text" sistema-mask="codigo" class="form-control custom-radius" id="codigo_armario" placeholder="Código" onkeyup="pesquisar_armario();" />
                            </div>
                            <div class="col-8 text-center">
                                <label class="text">Nome Armário</label>
                                <input type="text" class="form-control custom-radius" id="nome_armario" placeholder="Nome Armário" onkeyup="pesquisar_armario();" />
                            </div>
                            <div class="col-2">
                                <button class="btn btn-info botao_vertical_linha" onclick="pesquisar_armario();">Pesquisar</button>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="tabela_armario">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">#</th>
                                                <th scope="col">Nome</th>
                                                <th scope="col">Código Barras</th>
                                                <th scope="col">Ação</th>
                                                <th scope="col">Imprimir Código de Barras</th>
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
            pesquisar_armario();
        }
    </script>
<?php
    require_once 'includes/footer.php';
    exit;
});

//@note salvar_alterar_dados
router_add('salvar_alterar_dados', function () {
    $id_armario = (int) (isset($_REQUEST['codigo_armario']) ? (int) intval($_REQUEST['codigo_armario'], 10) : 0);

    require_once 'includes/head.php';
?>
    <script>
        let CODIGO_ARMARIO = <?php echo $id_armario; ?>;

        function salvar_dados() {
            let codigo_armario = parseInt(document.querySelector('#codigo_armario').value, 10);
            let nome_armario = document.querySelector('#nome_armario').value;
            let codigo_barras = document.querySelector('#codigo_barras').value;

            if (isNaN(codigo_armario)) {
                codigo_armario = 0;
            }

            sistema.request.post('/armario.php', {
                'rota': 'salvar_dados',
                'codigo_armario': codigo_armario,
                'nome_armario': nome_armario,
                'codigo_barras': codigo_barras
            }, function(retorno) {
                sistema.verificar_status(retorno.status, sistema.url('/armario.php', {
                    'rota': 'index'
                }));
            });
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Cadastro de Armário</h4>
                        <div class="row">
                            <div class="col-2 text-center">
                                <label class="text">Código</label>
                                <input type="text" class="form-control custom-radius text-center" id="codigo_armario" placeholder="Código" readonly="true" />
                            </div>
                            <div class="col-7 text-center">
                                <label class="text">Nome Armário</label>
                                <input type="text" id="nome_armario" class="form-control custom-radius" placeholder="Nome Armário" />
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Código Barras</label>
                                <input type="text" class="form-control custom-radius text-center" id="codigo_barras" placeholder="Código barras" readonly="true" value="<?php echo codigo_barras(); ?>" />
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-2 push-10">
                                <button class="btn btn-info" onclick="salvar_dados();">Salvar Dados</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.onload = function() {
            if (CODIGO_ARMARIO != 0) {
                sistema.request.post('/armario.php', {
                    'rota': 'pesquisar_armario',
                    'codigo_armario': CODIGO_ARMARIO
                }, function(retorno) {
                    document.querySelector('#codigo_armario').value = retorno.dados.id_armario;
                    document.querySelector('#nome_armario').value = retorno.dados.nome_armario;
                    document.querySelector('#codigo_barras').value = retorno.dados.codigo_barras;
                });
            }
        }
    </script>
<?php
    require_once 'includes/footer.php';
    exit;
});

//@audit impressao_codigo_barras_armario
router_add('impressao_codigo_barras_armario', function () {
    $codigo_armario = (int) (isset($_REQUEST['codigo_armario']) ? intval($_REQUEST['codigo_armario'], 10) : 0);
    $filtro = ['filtro' => (array) ['id_armario', '===', (int) $codigo_armario]];

    $objeto_armario = new Armario();
    $retorno_pesquisa = (array) $objeto_armario->pesquisar($filtro);

    $nome_armario = (string) '';
    $codigo_barras = (string) '';

    if (array_key_exists('codigo_barras', $retorno_pesquisa) == true) {
        $codigo_barras = (string) $retorno_pesquisa['codigo_barras'];
    }

    if (array_key_exists('nome_armario', $retorno_pesquisa) == true) {
        $nome_armario = (string) $retorno_pesquisa['nome_armario'];
    }

    require_once 'includes/head_relatorio.php';
?>
    <script>
        const CODIGO_BARRAS = "<?php echo $codigo_barras; ?>";

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
                    <?php echo $nome_armario; ?>
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
    <?php
    require_once 'includes/footer_relatorio.php';
    ?>
    <script>
        window.onload = function() {
            gerar_codigo_barras();
        }
    </script>
<?php
});

//@audit salvar_dados
router_add('salvar_dados', function () {
    $objeto_armario = new Armario();
    echo json_encode(['status' => (bool) $objeto_armario->salvar_dados($_REQUEST)], JSON_UNESCAPED_UNICODE);
});

//@audit pesquisar_armario
router_add('pesquisar_armario', function () {
    $objeto_armario = new Armario();
    $id_armario = (int) (isset($_REQUEST['codigo_armario']) ? intval($_REQUEST['codigo_armario'], 10) : 0);
    $filtro = (array) [];
    $dados = (array) [];

    array_push($filtro, ['id_armario', '===', (int) $id_armario]);
    $dados['filtro'] = (array) ['and' => (array) $filtro];
    echo json_encode(['dados' => (array) $objeto_armario->pesquisar($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit pesquisar_armario_todos
router_add('pesquisar_armario_todos', function () {
    $objeto_armario = new Armario();
    $id_armario = (int) (isset($_REQUEST['codigo_armario']) ? (int) intval($_REQUEST['codigo_armario'], 10) : 0);
    $nome_armario = (string) (isset($_REQUEST['nome_armario']) ? (string) strtoupper($_REQUEST['nome_armario']) : '');
    $filtro = (array) [];
    $dados = (array) [];

    if ($id_armario != 0) {
        array_push($filtro, ['id_armario', '===', (int) $id_armario]);
    }

    array_push($filtro, ['nome_armario', '=', (string) $nome_armario]);

    $dados['filtro'] = (array) ['and' => (array) $filtro];
    $dados['ordenacao'] = (array) ['nome_armario' => (bool) true];
    $dados['limite'] = (int) 10;

    echo json_encode(['dados' => (array) $objeto_armario->pesquisar_todos($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});
?>