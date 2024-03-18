<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Prateleira.php';

//Rota index
router_add('index', function(){
    require_once 'includes/head.php';
    ?>
    <script>
        function pesquisar_armarios(){
            sistema.request.post('/armario.php', {'rota': 'pesquisar_armario_todos'}, function(retorno){
                let armario = document.querySelector('#codigo_armario');

                sistema.each(retorno.dados, function(index, armarios){
                    armario.appendChild(sistema.gerar_option(armarios.id_armario, armarios.nome_armario));
                });
            });
        }

        function cadastro_prateleira(codigo_prateleria){
            window.location.href = sistema.url('/prateleira.php', {'rota': 'salvar_alterar_dados', 'codigo_prateleira': codigo_prateleria});
        }

        function pesquisar_prateleiras(){
            let codigo_armario = parseInt(document.querySelector('#codigo_armario').value, 10);
            let codigo_barras = document.querySelector('#codigo_barras').value;
            let nome_prateleira = document.querySelector('#nome_prateleira').value;

            if(isNaN(codigo_armario)){
                codigo_armario = 0;
            }

            sistema.request.post('/prateleira.php', {'rota': 'pesquisar_prateleira_todas', 'codigo_armario': codigo_armario, 'nome_prateleira': nome_prateleira, 'codigo_barras': codigo_barras}, function(retorno){
                let retorno_prateleira = retorno.dados;
                let tamanho_retorno = retorno_prateleira.length;
                let tabela = document.querySelector('#tabela_prateleira tbody');
                
                tabela = sistema.remover_linha_tabela(tabela);

                if(tamanho_retorno < 1){
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA PRATELEIRA ENCONTRADA COM OS FILTROS INFORMADOS', 'inner', true, 4));
                    tabela.appendChild(linha);
                }else{
                    sistema.each(retorno_prateleira, function(contador, prateleiras){
                        let linha = document.createElement('tr');
                        
                        linha.appendChild(sistema.gerar_td(['text-center'], prateleiras.id_prateleira, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], prateleiras.nome_prateleira), 'inner');
                        linha.appendChild(sistema.gerar_td(['tex-center'], prateleiras.codigo_barras, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_prateleira_'+prateleiras.id_prateleira, 'VISUALIZAR', ['btn', 'btn-info'], function visualizar(){cadastro_prateleira(prateleiras.id_prateleira);}), 'append'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('imprimir_codigo_barras_prateleira_'+prateleiras.id_prateleira, 'IMIPRIMIR CÓDIGO DE BARRAS', ['btn', 'btn-success'], function impressao(){imprimir_codigo_barras_prateleiras(prateleiras.id_prateleira)}), 'append'));

                        tabela.appendChild(linha);
                    });
                }
            }, false);
        }

        function imprimir_codigo_barras_prateleiras(codigo_prateleira){
            window.open(sistema.url('/prateleira.php', {'rota':'impressao_codigo_barras_prateleira', 'codigo_prateleira':codigo_prateleira}), 'Impressão de código de barras', 'width=740px, height=500px, scrollbars=yes');
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Pesquisa de Prateleiras</h4>
                        <div class="row">
                            <div class="col-3">
                                <button class="btn btn-secondary" onclick="cadastro_prateleira(0);">Cadastrar Prateleira</button>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-3 text-center">
                                <label class="text">Armário</label>
                                <select id="codigo_armario" class="form-control custom-radius" onblur="pesquisar_prateleiras();">
                                    <option value="0">Selecione um armário</option>
                                </select>
                            </div>
                            <div class="col-4 text-center">
                                <label class="text">Nome Prateleira</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="nome_prateleira" placeholder="Nome Prateleira" onkeyup="pesquisar_prateleiras();"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Código Barras</label>
                                <input type="text" id="codigo_barras" class="form-control custom-radius" sistema-mask="codigo" placeholder="Código Barras" maxlength="13" onkeyup="pesquisar_prateleiras();"/>
                            </div>
                            <div class="col-2 text-center">
                                <button class="btn btn-info botao_vertical_linha" onclick="pesquisar_prateleiras();">Pesquisar</button>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="tabela_prateleira">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">#</th>
                                                <th scope="col">Nome</th>
                                                <th scope="col">Código Barras</th>
                                                <th scope="col">Ação</th>
                                                <th scope="col">Imprimir cíódigo de barras</th>
                                            </tr>
                                        </thead>
                                        <tbody><tr><td colspan="5" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA PESQUISA</td></tr></tbody>
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
        window.onload = function(){
            pesquisar_armarios();
            pesquisar_prateleiras();
        }
    </script>
    <?php
    require_once 'includes/footer.php';
    exit;
});

//@note salvar_alterar_dados
router_add('salvar_alterar_dados', function(){
    $codigo_prateleira = (int) (isset($_REQUEST['codigo_prateleira']) ? (int) intval($_REQUEST['codigo_prateleira'], 10):0);
    require_once 'includes/head.php';
    ?>
    <script>
        let CODIGO_PRATELEIRA = <?php echo $codigo_prateleira; ?>;

        function pesquisar_armarios(){
            sistema.request.post('/armario.php', {'rota': 'pesquisar_armario_todos'}, function(retorno){
                let armario = document.querySelector('#codigo_armario');

                sistema.each(retorno.dados, function(index, armarios){
                    armario.appendChild(sistema.gerar_option(armarios.id_armario, armarios.nome_armario));
                });
            });
        }

        function salvar_dados(){
            let codigo_prateleira = parseInt(document.querySelector('#codigo_prateleira').value, 10);
            let codigo_armario = parseInt(document.querySelector('#codigo_armario').value, 10);
            let nome_prateleira = document.querySelector('#nome_prateleira').value;
            let codigo_barras = document.querySelector('#codigo_barras').value;

            if(isNaN(codigo_prateleira)){
                codigo_prateleira = 0;
            }

            if(isNaN(codigo_armario)){
                codigo_armario = 0;
            }

            sistema.request.post('/prateleira.php', {'rota': 'salvar_dados', 'codigo_prateleira': codigo_prateleira, 'codigo_armario': codigo_armario, 'nome_prateleira': nome_prateleira, 'codigo_barras': codigo_barras}, function(retorno){
                if(sistema.verificar_status(retorno.status, sistema.url('/prateleira.php', {'rota': 'index'})));
            });
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Cadastro de Prateleiras</h4>
                        <br/>
                        <div class="row">
                            <div class="col-2 text-center">
                                <label class="text">Código</label>
                                <input type="text" class="form-control custom-radius text-center" id="codigo_prateleira" disabled="true" placeholder="Código"/>
                            </div>
                            <div class="col-4 text-center">
                                <label class="text">Nome Prateleira</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="nome_prateleira" placeholder="Nome Prateleira"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Armário</label>
                                <select id="codigo_armario" class="form-control custom-radius"><option value="0">Selecione um Armário</option></select>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Código Barras</label>
                                <input type="text" class="form-control custom-radius text-center" id="codigo_barras" value="<?php echo codigo_barras(); ?>" disabled="true"/>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-2 text-center">
                                <button class="btn btn-info" onclick="salvar_dados();">Salvar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.onload = function(){
            pesquisar_armarios();
            
            if(CODIGO_PRATELEIRA != 0){
                sistema.request.post('/prateleira.php', {'rota': 'pesquisar_prateleira', 'codigo_prateleira': CODIGO_PRATELEIRA}, function(retorno){
                    document.querySelector('#codigo_prateleira').value = retorno.dados.id_prateleira;
                    document.querySelector('#codigo_armario').value = retorno.dados.id_armario;
                    document.querySelector('#codigo_barras').value = retorno.dados.codigo_barras;
                    document.querySelector('#nome_prateleira').value = retorno.dados.nome_prateleira;
                });
            }
        }
    </script>
    <?php
    require_once 'includes/footer.php';
    exit;
});

//@audit salvar_dados
router_add('salvar_dados', function(){
    $objeto_prateleria = new Prateleira();

    echo json_encode(['status' => (bool) $objeto_prateleria->salvar_dados($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit pesquisar_prateleira
router_add('pesquisar_prateleira', function(){
    $objeto_prateleira = new Prateleira();
    $codigo_prateleira = (int) (isset($_REQUEST['codigo_prateleira']) ? (int) intval($_REQUEST['codigo_prateleira'], 10):0);
    $filtro = (array) [];
    $dados = (array) [];

    if($codigo_prateleira != 0){
        array_push($filtro, ['id_prateleira', '===', (int) $codigo_prateleira]);
    }
    
    $dados['filtro'] = (array) ['and' => (array) $filtro];

    echo json_encode(['dados' => (array) $objeto_prateleira->pesquisar($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit pesquisar_prateleira_todas
router_add('pesquisar_prateleira_todas', function(){
    $objeto_prateleira = new Prateleira();
    $codigo_prateleira = (int) (isset($_REQUEST['codigo_prateleira']) ? (int) intval($_REQUEST['codigo_prateleira'], 10):0);
    $nome_prateleira = (string) (isset($_REQUEST['nome_prateleira']) ? (string) strtoupper($_REQUEST['nome_prateleira']):'');
    $codigo_barras = (string) (isset($_REQUEST['codigo_barras']) ? (string) $_REQUEST['codigo_barras']:'');
    $codigo_armario = (int) (isset($_REQUEST['codigo_armario']) ? (int) intval($_REQUEST['codigo_armario'], 10):0);

    $filtro = (array) [];
    $dados = (array) [];
    
    array_push($filtro, ['nome_prateleira', '=', (string) $nome_prateleira]);
    
    if($codigo_prateleira != 0){
        array_push($filtro, ['id_prateleira', '===', (int) $codigo_prateleira]);
    }

    if($codigo_barras != ''){
        array_push($filtro, ['codigo_barras', '===', (string) $codigo_barras]);
    }

    if($codigo_armario != 0){
        array_push($filtro, ['id_armario', '===', (int) $codigo_armario]);
    }

    $dados['filtro'] = (array) ['and' => (array) $filtro];
    $dados['ordenacao'] = (array) ['nome_prateleira' => (bool) true];
    $dados['limite'] = (int) 10;

    echo json_encode(['dados' => (array) $objeto_prateleira->pesquisar_todos($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});

/**
 * Rota reponsável pela impressão de código de barras
 * TODO imprime apenas um código de barras por vez.
 */

 router_add('impressao_codigo_barras_prateleira', function(){
    $codigo_prateleira = (int) (isset($_REQUEST['codigo_prateleira']) ? intval($_REQUEST['codigo_prateleira'], 10):0);
    $filtro = ['filtro' => (array) ['codigo_prateleira', '===', (int) $codigo_prateleira]];

    $objeto_prateleira = new Prateleira();
    $retorno_pesquisa_prateleira = (array) $objeto_prateleira->pesquisar($filtro);

    $nome_prateleira = (string) '';
    $codigo_barras = (string) '';

    if(array_key_exists('nome_prateleira', $retorno_pesquisa_prateleira) == true){
        $nome_prateleira = (string) $retorno_pesquisa_prateleira['nome_prateleira'];
    }

    if(array_key_exists('codigo_barras', $retorno_pesquisa_prateleira) == true){
        $codigo_barras = (string) $retorno_pesquisa_prateleira['codigo_barras'];
    }

    require_once 'includes/head_relatorio.php';
    ?>
    <script>
        const CODIGO_BARRAS = "<?php echo $codigo_barras; ?>";

        function gerar_codigo_barras(){
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

        function fechar_janela(){
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
                    <?php echo $nome_prateleira; ?>
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
        window.onload = function(){
            gerar_codigo_barras();
        }
    </script>
    <?php
    require_once 'includes/footer_relatorio.php';
 });
?>