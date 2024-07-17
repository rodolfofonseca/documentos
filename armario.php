<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Armario.php';
require_once 'Modelos/Organizacao.php';

/**
 * Função responsável por valdiar as informações do armário pesquisados no banco de dados e montar um array com todos os dados que serão necessários para apresentação em tela.
 * @param array $retorno_pesquisa Array contendo todos os dados brutos dos armários pesquisados no banco de dados
 * @param array $modelo Array contendo o modelo com todos os campos que o usuário precisa para visualizar.
 * @param int $id_empresa Identificador da empresa que o usuário pertence
 * @param array $retorno Array onde será armazenado os novos campos formatados.
 * @return array $retorno Array com todas as informações já montados pronto para visualização em tela.
 */
function validar_dados_pesquisa($retorno_pesquisa, $modelo, $id_empresa, $retorno){
    if(empty($retorno_pesquisa) == false){
        foreach($retorno_pesquisa as $retorno_pesquisa){
            $retorno_temporario = (array) model_parse($modelo, $retorno_pesquisa);

            $objeto_organizacao = new Organizacao();
            $dados_organizacao = (array) $objeto_organizacao->pesquisar((array) ['filtro' => (array) ['and' => (array) [(array) ['id_organizacao', '===', (int) $retorno_temporario['id_organizacao']], (array) ['id_empresa', '===', (int) $id_empresa]]]]);

            if(empty($dados_organizacao) == false){
                if(array_key_exists('nome_organizacao', $dados_organizacao) == true){
                    $retorno_temporario['nome_organizacao'] = (string) $dados_organizacao['nome_organizacao'];
                }
            }

            array_push($retorno, $retorno_temporario);
        }
    }

    return (array) $retorno;
}

/**
 * Rota index
 * TODO primeira rota dentro do sistema
 */
router_add('index', function () {
    require_once 'includes/head.php';
 ?>
    <script>
        const CODIGO_EMPRESA = <?php echo $_SESSION['id_empresa']; ?>;
        const CODIGO_USUARIO = <?php echo $_SESSION['id_usuario'];?>;
        
        function cadastro_armario(codigo_armario) {
            window.location.href = sistema.url('/armario.php', {
                'rota': 'salvar_alterar_dados',
                'codigo_armario': codigo_armario
            });
        }

        /** 
         * Função responsável por enviar todos os dados para o a rota que realiza a validação dos dados.
         */
        function pesquisar_armario() {
            let codigo_armario = parseInt(document.querySelector('#codigo_armario').value, 10);
            let nome_armario = document.querySelector('#nome_armario').value;
            let descricao = document.querySelector('#descricao').value;
            let forma_visualizacao = document.querySelector('#forma_visualizacao').value;

            if (isNaN(codigo_armario)) {
                codigo_armario = 0;
            }

            sistema.request.post('/armario.php', {
                'rota': 'pesquisar_armario_todos',
                'codigo_armario': codigo_armario,
                'codigo_empresa':CODIGO_EMPRESA,
                'codigo_usuario':CODIGO_USUARIO,
                'nome_armario': nome_armario,
                'descricao':descricao,
                'forma_visualizacao':forma_visualizacao
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
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM ARMÁRIO ENCONTRADO', 'inner', true, 6));
                    tabela.appendChild(linha);
                } else {
                    sistema.each(retorno_armario, function(contador, armario) {
                        let linha = document.createElement('tr');
                        linha.appendChild(sistema.gerar_td(['text-center'], str_pad(armario.id_armario, 3, '0'), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], armario.nome_armario, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], armario.descricao, 'inner'));

                        linha.appendChild(sistema.gerar_td(['text-center'], armario.nome_organizacao, 'inner'));

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('imprimir_codigo_barra_armario_' + armario.id_armario, armario.codigo_barras, ['btn', 'btn-success'], function impressao() {imprimir_codigo_barra_armario(armario.id_armario)}), 'append'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_armario_' + armario.id_armario, 'VISUALIZAR', ['btn', 'btn-info'], function visualizar() {cadastro_armario(armario.id_armario)}), 'append'));
                        
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
                                <button class="btn btn-secondary custom-radius btn-lg" onclick="cadastro_armario(0);">Cadastro de Armários</button>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-1 text-center">
                                <label class="text">Código</label>
                                <input type="text" sistema-mask="codigo" class="form-control custom-radius" id="codigo_armario" placeholder="Código" onkeyup="pesquisar_armario();" />
                            </div>
                            <div class="col-9 text-center">
                                <label class="text">Nome Armário</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="nome_armario" placeholder="Nome Armário" onkeyup="pesquisar_armario();" />
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Forma de Visualização</label>
                                <select class="form-control custom-radius" id="forma_visualizacao">
                                    <option value="TODOS">TODOS</option>
                                    <option value="PUBLICO">PÚBLICO</option>
                                    <option value="PRIVADO">PRIVADO</option>
                                </select>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <textarea class="custom-radius form-control" id="descricao" placeholder="Descrição"></textarea>
                        </div>
                        <br />
                        <div class="row">
                            <div class="push-10 col-2">
                                <button class="btn btn-info botao_vertical_linha btn-lg custom-radius" onclick="pesquisar_armario();">Pesquisar</button>
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
                                                <th scope="col">Descrição</th>
                                                <th scope="col">Organização</th>
                                                <th scope="col">Código Barras</th>
                                                <th scope="col">Visualização</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="6" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA PESQUISA</td>
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
    </div>
    <script>
        window.onload = function() {
            validar_acesso_administrador('<?php echo $_SESSION['tipo_usuario']; ?>');
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
        const CODIGO_EMPRESA = <?php echo $_SESSION['id_empresa']; ?>;
        const CODIGO_USUARIO = <?php echo $_SESSION['id_usuario']; ?>;

        let CODIGO_ARMARIO = <?php echo $id_armario; ?>;

        /** 
         * Função responsável por pesquisar a organização na base de dados e colocar no select para que o usuário possa estar selecionando na hora de realziar o cadastro no sistema.
         */
        function pesquisar_organizacao() {
            sistema.request.post('/organizacao.php', {
                'rota': 'pesquisar_todos',
                'codigo_empresa': CODIGO_EMPRESA,
                'codigo_usuario': CODIGO_USUARIO,
                'forma_visualizacao': 'TODOS'
            }, function(retorno) {
                if (retorno.dados.length > 0) {
                    let select = document.querySelector('#id_organizacao');

                    sistema.each(retorno.dados, function(contador, organizacao) {
                        select.options[select.options.length] = new Option(organizacao.nome_organizacao, organizacao.id_organizacao);
                    });
                }
            });
        }


        /** 
         * Função responsável por pegar as informaçõe informadas pelo usuário e preparar em um objeto para então enviar para o back para ser salvo no banco de dados.
         */
        function salvar_dados() {
            CODIGO_ARMARIO = parseInt(document.querySelector('#codigo_armario').value, 10);
            let codigo_organizacao = parseInt(document.querySelector('#id_organizacao').value, 10);
            let nome_armario = document.querySelector('#nome_armario').value;
            let descricao = document.querySelector('#descricao').value;
            let codigo_barras = document.querySelector('#codigo_barras').value;
            let forma_visualizacao = document.querySelector('#forma_visualizacao').value;

            if (isNaN(CODIGO_ARMARIO)) {
                CODIGO_ARMARIO = 0;
            }

            if (isNaN(codigo_organizacao)) {
                codigo_organizacao = 0;
            }

            if (codigo_organizacao != 0) {
                sistema.request.post('/armario.php', {
                    'rota': 'salvar_dados',
                    'codigo_armario': CODIGO_ARMARIO,
                    'codigo_empresa': CODIGO_EMPRESA,
                    'codigo_usuario': CODIGO_USUARIO,
                    'codigo_organizacao': codigo_organizacao,
                    'nome_armario': nome_armario,
                    'descricao': descricao,
                    'codigo_barras': codigo_barras,
                    'forma_visualizacao': forma_visualizacao
                }, function(retorno) {
                    sistema.verificar_status(retorno.status, sistema.url('/armario.php', {
                        'rota': 'index'
                    }));
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "O código da organização não pode ser vazio!"
                });
            }

        }

        /** 
         * Função responsável por redirecionar o usuário para a tela de pesquisa de armários
         */
        function voltar() {
            window.location.href = sistema.url('/armario.php', {
                'rota': 'index'
            });
        }

        function limpar_campos() {
            document.querySelector('#codigo_armario').value = '';
            document.querySelector('#nome_armario').value = '';
            document.querySelector('#id_organizacao').selectedIndex = 0;
            document.querySelector('#forma_visualizacao').selectedIndex = 0;
            document.querySelector('#descricao').value = '';

            sistema.request.post('/index.php', {
                'rota': 'buscar_codigo_barras'
            }, function(retorno) {
                document.querySelector('#codigo_barras').value = retorno.codigo_barras;
            }, false);
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Cadastro de Armário</h4>
                        <div class="row">
                            <div class="col-1 text-center">
                                <label class="text">Código</label>
                                <input type="text" class="form-control custom-radius text-center" id="codigo_armario" placeholder="Código" readonly="true" />
                            </div>
                            <div class="col-5 text-center">
                                <label class="text">Nome Armário</label>
                                <input type="text" id="nome_armario" class="form-control custom-radius text-uppercase" placeholder="Nome Armário" />
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">organização</label>
                                <select class="form-control custom-radius" id="id_organizacao">
                                    <option value="0">Selecione uma opção</option>
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
                                <input type="text" class="form-control custom-radius text-center" id="codigo_barras" placeholder="Código barras" readonly="true" value="<?php echo codigo_barras(); ?>" />
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-12 text-center">
                                <textarea id="descricao" class="form-control custom-radius" placeholder="Descrição da organização" rows="3"></textarea>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-4">
                                <button class="btn btn-info custom-radius btn-lg botao_grande" onclick="salvar_dados();">Salvar Dados</button>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-danger custom-radius btn-lg botao_grande" onclick="limpar_campos();">Limpar Campos</button>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-secondary custom-radius btn-lg botao_grande" onclick="voltar();">Voltar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.onload = async function() {
            validar_acesso_administrador('<?php echo $_SESSION['tipo_usuario']; ?>');

            await pesquisar_organizacao();

            if (CODIGO_ARMARIO != 0) {
                await sistema.request.post('/armario.php', {
                    'rota': 'pesquisar_armario',
                    'codigo_armario': CODIGO_ARMARIO
                }, function(retorno) {
                    document.querySelector('#codigo_armario').value = retorno.dados.id_armario;
                    document.querySelector('#nome_armario').value = retorno.dados.nome_armario;
                    document.querySelector('#descricao').value = retorno.dados.descricao;
                    document.querySelector('#forma_visualizacao').value = retorno.dados.forma_visualizacao;
                    document.querySelector('#codigo_barras').value = retorno.dados.codigo_barras;
                    document.querySelector('#id_organizacao').value = retorno.dados.id_organizacao;
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


/**
 * Rota responsável por pesquisar todos os armários cadastrados no banco de dados.
 */
router_add('pesquisar_armario_todos', function () {
    $objeto_armario = new Armario();
    $id_armario = (int) (isset($_REQUEST['codigo_armario']) ? (int) intval($_REQUEST['codigo_armario'], 10) : 0);
    $id_empresa = (int) (isset($_REQUEST['codigo_empresa']) ? (int) intval($_REQUEST['codigo_empresa'], 10):0);
    $id_usuario = (int) (isset($_REQUEST['codigo_usuario']) ? (int) intval($_REQUEST['codigo_usuario'], 10):0);

    $nome_armario = (string) (isset($_REQUEST['nome_armario']) ? (string) strtoupper($_REQUEST['nome_armario']) : '');
    $descricao = (string) (isset($_REQUEST['descricao']) ? (string) $_REQUEST['descricao']:'');
    $forma_visualizacao = (string) (isset($_REQUEST['forma_visualizacao']) ? (string) $_REQUEST['forma_visualizacao'] : 'TODOS');
    
    $filtro = (array) [];
    $filtro_todos_publico = (array) [];
    $filtro_todos_privado = (array) [];

    $dados = (array) ['filtro' => (array) [], 'ordenacao' => (array) ['nome_armario' => (bool) true], 'limite' => (int) 10];
    $modelo = (array) ['id_armario' => (int) 0, 'id_empresa' => (int) 0, 'id_usuario' => (int) 0, 'id_organizacao' => (int) 0, 'nome_armario' => (string) '', 'nome_organizacao' => (string) '', 'descricao' => (string) '', 'codigo_barras' => (string) '', 'forma_visualizacao' => (string) ''];
    
    $retorno = (array) [];

    if ($id_armario != 0) {
        array_push($filtro, (array) ['id_armario', '===', (int) $id_armario]);
        array_push($filtro_todos_publico, (array) ['id_armario', '===', (int) $id_armario]);
        array_push($filtro_todos_privado, (array) ['id_armario', '===', (int) $id_armario]);
    }

    if($id_empresa != 0){
        array_push($filtro_todos_publico, (array) ['id_empresa', '===', (int) $id_empresa]);
        array_push($filtro_todos_privado, (array) ['id_empresa', '===', (int) $id_empresa]);
        array_push($filtro, (array) ['id_empresa', '===', (int) $id_empresa]);
    }

    array_push($filtro, (array) ['nome_armario', '=', (string) $nome_armario]);
    array_push($filtro_todos_publico, (array) ['nome_armario', '=', (string) $nome_armario]);
    array_push($filtro_todos_privado, (array) ['nome_armario', '=', (string) $nome_armario]);
    
    array_push($filtro, (array) ['descricao', '=', (string) $descricao]);
    array_push($filtro_todos_publico, (array) ['descricao', '=', (string) $descricao]);
    array_push($filtro_todos_privado, (array) ['descricao', '=', (string) $descricao]);
    
    if($forma_visualizacao == 'PRIVADO'){
        array_push($filtro, (array) ['id_usuario', '===', (int) $id_usuario]);
    }
    
    if($forma_visualizacao == 'PRIVADO' || $forma_visualizacao == 'PUBLICO'){
        array_push($filtro, ['forma_visualizacao', '===', (string) $forma_visualizacao]);

        $dados['filtro'] = (array) ['and' => (array) $filtro];
        $retorno_pesquisa = (array) $objeto_armario->pesquisar_todos($dados);
        $retorno = (array) validar_dados_pesquisa($retorno_pesquisa, $modelo, $id_empresa, $retorno);
    }else{
        array_push($filtro_todos_privado, (array) ['id_usuario', '===', (int) $id_usuario]);
        array_push($filtro_todos_privado, (array) ['forma_visualizacao', '===', (string) 'PRIVADO']);
        array_push($filtro_todos_publico, (array) ['forma_visualizacao', '===', (string) 'PUBLICO']);

        $dados['filtro'] = (array) ['and' => (array) $filtro_todos_privado];
        $retorno_pesquisa_privado = (array) $objeto_armario->pesquisar_todos($dados);

        $dados['filtro'] = (array) ['and' => (array) $filtro_todos_publico];
        $retorno_pesquisa_publico = (array) $objeto_armario->pesquisar_todos((array) $dados);

        $retorno = (array) validar_dados_pesquisa($retorno_pesquisa_privado, $modelo, $id_empresa, $retorno);
        $retorno = (array) validar_dados_pesquisa($retorno_pesquisa_publico, $modelo, $id_empresa, $retorno);
    }

    echo json_encode(['dados' => (array) $retorno], JSON_UNESCAPED_UNICODE);
    exit;
});
?>