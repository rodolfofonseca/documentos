<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Caixa.php';

//@note index
router_add('index', function () {
    require_once 'includes/head.php';
?>
    <script>
        function pesquisar_caixa() {
            let codigo_caixa = sistema.int(document.querySelector('#codigo_caixa').value);
            let codigo_prateleira = sistema.int(document.querySelector('#codigo_prateleira').value);
            let nome_caixa = sistema.string(document.querySelector('#nome_caixa').value);
            let codigo_barras = sistema.string(document.querySelector('#codigo_barras').value);

            sistema.request.post('/caixa.php', {
                'rota': 'pesquisar_caixa_todas',
                'codigo_caixa': codigo_caixa,
                'codigo_prateleira': codigo_prateleira,
                'nome_caixa': nome_caixa,
                'codigo_barras': codigo_barras
            }, function(retorno) {
                let caixas = retorno.dados;
                let tamanho_retorno = caixas.length;
                let tabela = document.querySelector('#tabela_caixa tbody');

                tabela = sistema.remover_linha_tabela(tabela);

                if (tamanho_retorno == 0) {
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA CAIXA ENCONTRADA COM OS FILTROS INFORMADOS', 'inner', true, 4));
                    tabela.appendChild(linha);
                } else {
                    sistema.each(caixas, function(index, caixa) {
                        let linha = document.createElement('tr');

                        linha.appendChild(sistema.gerar_td(['text-center'], caixa.id_caixa, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], caixa.nome_caixa, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], caixa.descricao, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_caixa_' + caixa.id_caixa, 'VISUALIZAR', ['btn', 'btn-info'], function visualizar_caixa() {
                            cadastrar_caixa(caixa.id_caixa);
                        }), 'append'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('imprimir_codigo_barra_' + caixa.id_caixa, 'IMPRIMIR CÓDIGO', ['btn', 'btn-success'], function imprimir_caixa() {
                            caixa_imprimir_codigo_barras(caixa.id_caixa);
                        }), 'append'));

                        tabela.appendChild(linha);
                    });
                }
            }, false);
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
                                <button class="btn btn-secondary" onclick="cadastrar_caixa(0);">Cadastrar Caixa</button>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-2 text-center">
                                <label class="text">Código Caixa</label>
                                <input type="text" sistema-mask="codigo" id="codigo_caixa" class="form-control custom-radius" placeholder="Código Caixa" onkeyup="pesquisar_caixa();" />
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Prateleira</label>
                                <select class="form-control custom-radius" id="codigo_prateleira" onblur="pesquisar_caixa();"></select>
                            </div>
                            <div class="col-4 text-center">
                                <label class="text">Nome Caixa</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="nome_caixa" placeholder="Nome Caixa" onkeyup="pesquisar_caixa();" />
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Código Barras</label>
                                <input type="text" class="form-control custom-radius" sistema-mask="codigo" maxlength="13" placeholder="Código Barras" id="codigo_barras" onkeyup="pesquisar_caixa();" />
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="push-10 col-2">
                                <button class="btn btn-info" onclick="pesquisar_caixa();">Pesquisar</button>
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
                                                <th scope="col">Ação</th>
                                                <th scope="col">Imprimir código de barras</th>
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
            pesquisar_prateleiras();
            pesquisar_caixa();
        }
    </script>
<?php
    require_once 'includes/footer.php';
    exit;
});

/**
 * Rota responsável por salvar as informações das caixas
 */
router_add('salvar_dados_caixa', function () {
    $codigo_caixa = (int) (isset($_REQUEST['codigo_caixa']) ? (int) intval($_REQUEST['codigo_caixa'], 10) : 0);
    require_once 'includes/head.php';
?>
    <script>
        let CODIGO_CAIXA = <?php echo $codigo_caixa; ?>;

        function salvar_dados() {
            let codigo_caixa = sistema.int(document.querySelector('#codigo_caixa').value);
            let codigo_prateleira = sistema.int(document.querySelector('#codigo_prateleira').value);
            let nome_caixa = sistema.string(document.querySelector('#nome_caixa').value);
            let descricao = sistema.string(document.querySelector('#descricao').value);
            let codigo_barras = sistema.string(document.querySelector('#codigo_barras').value);

            sistema.request.post('/caixa.php', {
                'rota': 'salvar_dados',
                'codigo_caixa': codigo_caixa,
                'codigo_prateleira': codigo_prateleira,
                'nome_caixa': nome_caixa,
                'descricao': descricao,
                'codigo_barras': codigo_barras
            }, function(retorno) {
                sistema.verificar_status(retorno.status, sistema.url('/caixa.php', {
                    'rota': 'index'
                }));
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

        function voltar() {
            window.location.href = sistema.url('/caixa.php', {
                'rota': 'index'
            });
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
                            <div class="col-2 text-center">
                                <label class="text">Código Caixa</label>
                                <input type="text" sistema-mask="codigo" id="codigo_caixa" class="form-control custom-radius" placeholder="Código Caixa" disabled="true" />
                            </div>
                            <div class="col-4 text-center">
                                <label class="text">Nome Caixa</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="nome_caixa" placeholder="Nome Caixa" />
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Prateleira</label>
                                <select class="form-control custom-radius" id="codigo_prateleira">
                                    <option value="0">Selecione uma Prateleira</option>
                                </select>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Código Barras</label>
                                <input type="text" class="form-control custom-radius text-center" sistema-mask="codigo" maxlength="13" placeholder="Código Barras" value="<?php echo codigo_barras(); ?>" disabled="true" id="codigo_barras" />
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-12">
                                <textarea class="form-control custom-radius" id="descricao"></textarea>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-2">
                                <button class="btn btn-info" onclick="salvar_dados();">Salvar dados</button>
                            </div>
                            <div class="col-2">
                                <button class="btn btn-secondary" onclick="voltar();">Voltar</button>
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
                });
            }
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

//@audit pesquisar_caixa_todas
router_add('pesquisar_caixa_todas', function () {
    $id_caixa = (int) (isset($_REQUEST['codigo_caixa']) ? (int) intval($_REQUEST['codigo_caixa'], 10) : 0);
    $id_prateleira = (int) (isset($_REQUEST['codigo_prateleira']) ? (int) intval($_REQUEST['codigo_prateleira'], 10) : 0);
    $nome_caixa = (string) (isset($_REQUEST['nome_caixa']) ? (string) strtoupper($_REQUEST['nome_caixa']) : '');
    $codigo_barras = (string) (isset($_REQUEST['codigo_barras']) ? (string) $_REQUEST['codigo_barras'] : '');

    $filtro = (array) [];
    $dados = (array) ['filtro' => (array) [], 'ordenacao' => (array) ['id_caixa' => (bool) true], 'limite' => (int) 0];

    $objeto_caixa = new Caixa();

    if ($id_caixa != 0) {
        array_push($filtro, ['id_caixa', '===', (int) $id_caixa]);
    }

    if ($id_prateleira != 0) {
        array_push($filtro, ['id_prateleira', '===', (int) $id_prateleira]);
    }

    if ($codigo_barras != '') {
        array_push($filtro, ['codigo_barras', '===', (string) $codigo_barras]);
    }

    array_push($filtro, ['nome_caixa', '=', (string) $nome_caixa]);

    $dados['filtro'] = (array) ['and' => (array) $filtro];

    echo json_encode(['dados' => (array) $objeto_caixa->pesquisar_todos($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});

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
?>