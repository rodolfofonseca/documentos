<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Armario.php';
require_once 'Modelos/Organizacao.php';
require_once 'Modelos/Preferencia.php';

/**
 * Rota index
 * TODO primeira rota dentro do sistema
 */
router_add('index', function () {
    require_once 'includes/head.php';

    $objeto_preferencia = new Preferencia();

    $usuario_preferencia_nome_armario = (string) 'CHECKED';
    $usuario_preferencia_pesquisar_armario_automaticamente = (string) 'CHECKED';
    $usuario_preferencia_quantidade_armario = (int) intval(25, 10);
    
    $dados_usuario_logado_sistema = (array) ['usuario' => convert_id($_SESSION['id_usuario']), 'sistema' => convert_id($_SESSION['id_sistema']), 'nome_preferencia' => (string) ''];

    $dados_usuario_logado_sistema['nome_preferencia'] = (string) 'NOME_COMPLETO_ARMARIO';
    $usuario_preferencia_nome_armario = (string) $objeto_preferencia->pesquisar_preferencia_usuario($dados_usuario_logado_sistema);

    $dados_usuario_logado_sistema['nome_preferencia'] = (string) 'PESQUISAR_ARMARIO_AUTOMATICAMENTE';
    $usuario_preferencia_pesquisar_armario_automaticamente = (string) $objeto_preferencia->pesquisar_preferencia_usuario($dados_usuario_logado_sistema);

    $dados_usuario_logado_sistema['nome_preferencia'] = (string) 'QUANTIDADE_LIMITE_ARMARIO';
    $usuario_preferencia_quantidade_armario = (int) intval($objeto_preferencia->pesquisar_preferencia_usuario($dados_usuario_logado_sistema), 10);

    if($usuario_preferencia_quantidade_armario == 0 || $usuario_preferencia_quantidade_armario == 50 || $usuario_preferencia_quantidade_armario == 75 || $usuario_preferencia_quantidade_armario == 100){
        $usuario_preferencia_quantidade_armario = 25;
    }
 
    ?>
    <script>
        const CODIGO_EMPRESA = "<?php echo $_SESSION['id_empresa']; ?>";
        const CODIGO_USUARIO = "<?php echo $_SESSION['id_usuario'];?>";
        const CODIGO_SISTEMA = "<?php echo $_SESSION['id_sistema']; ?>";
        const PREFERENCIA_QUANTIDADE_ARMARIO = <?php echo intval($usuario_preferencia_quantidade_armario, 10); ?>;
        const PESQUISAR_ARMARIO_AUTOMATICAMENTE = "<?php echo $usuario_preferencia_pesquisar_armario_automaticamente; ?>";
        
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
            let codigo_organizacao = document.querySelector('#codigo_organizacao').value;

            let nome_armario = document.querySelector('#nome_armario').value;
            let descricao = document.querySelector('#descricao').value;
            let forma_visualizacao = document.querySelector('#tipo').value;
            let codigo_barras = document.querySelector('#codigo_barras').value;
            let status = document.querySelector('#status').value;

            let limite_retorno = sistema.int(document.querySelector('#limite_retorno').value);
            let visualizar_nome_armario_completo = document.querySelector('#visualizar_nome_armario_completo');

            sistema.request.post('/armario.php', {
                'rota': 'pesquisar_armario_todos',
                'empresa':CODIGO_EMPRESA,
                'usuario':CODIGO_USUARIO,
                'sistema':CODIGO_SISTEMA,
                'organizacao': codigo_organizacao,
                'nome_armario': nome_armario,
                'descricao':descricao,
                'tipo':forma_visualizacao,
                'codigo_barras': codigo_barras,
                'status':status,
                'limite_retorno': limite_retorno,
                'preferencia_usuario_retorno': PREFERENCIA_QUANTIDADE_ARMARIO
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

                        if(visualizar_nome_armario_completo.checked == true){
                            linha.appendChild(sistema.gerar_td(['text-left'], armario.nome_armario, 'inner'));
                            linha.appendChild(sistema.gerar_td(['text-left'], armario.descricao, 'inner'));
                            linha.appendChild(sistema.gerar_td(['text-left'], armario.nome_organizacao, 'inner'));
                        }else{
                            linha.appendChild(sistema.gerar_td(['text-left'], armario.nome_armario.substring(0, 15), 'inner'));
                            linha.appendChild(sistema.gerar_td(['text-center'], armario.descricao.substring(0, 40), 'inner'));
                            linha.appendChild(sistema.gerar_td(['text-center'], armario.nome_organizacao.substring(0, 15), 'inner'));
                        }


                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('imprimir_codigo_barra_armario_' + armario._id.$oid, armario.codigo_barras, ['btn', 'btn-success'], function impressao() {imprimir_codigo_barra_armario(armario._id.$oid)}), 'append'));

                        if (armario.forma_visualizacao == 'PUBLICO') {
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_tipo_armario_' + armario.codigo_barras, 'PÚBLICO', ['btn', 'btn-info'], function tipo_armario_tipo() { }), 'append'));

                        } else {
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_tipo_armario_' + armario.codigo_barras, 'PRIVADO', ['btn', 'btn-secondary'], function tipo_armario_tipo() { }), 'append'));
                        }

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_armario_' + armario._id.$oid, 'VISUALIZAR', ['btn', 'btn-info'], function visualizar() {cadastro_armario(armario._id.$oid)}), 'append'));
                        
                        tabela.appendChild(linha);
                    });
                }
            }, false);
        }

        /**
         * Função responsável por realizar a exclusão do armário no sistema
         */
        function excluir_armario_sistema(id_armario){
            sistema.request.post('/armario.php', {'rota':'excluir_armario', 'codigo_armario':id_armario}, function(retorno){
                validar_retorno(retorno, '', 1);
                pesquisar_armario();
            });
        }

        function imprimir_codigo_barra_armario(codigo_armario) {
            window.open(sistema.url('/armario.php', {
                'rota': 'impressao_codigo_barras_armario',
                'codigo_armario': codigo_armario
            }), 'Impressão de código de barras', 'width=740px, height=500px, scrollbars=yes');
        }

        /** 
         * Função responsável por alterar na base de dados a preferência do usuário a respeito de visualizar o nome completo do armário.
        */
        function alterar_preferencia_nome_completo_armario(){
            let check_visualizar_nome_armario = document.querySelector('#visualizar_nome_armario_completo');
            let preferencia = '';

            if(check_visualizar_nome_armario.checked == true){
                preferencia = 'CHECKED';
            }else{
                preferencia = '';
            }

            sistema.request.post('/preferencia.php', {'rota': 'salvar_preferencia_usuario', 'codigo_sistema': CODIGO_SISTEMA, 'codigo_usuario': CODIGO_USUARIO, 'nome_preferencia': 'NOME_COMPLETO_ARMARIO', 'preferencia': preferencia}, function(retorno){}, false);
        }

        /** 
         * Função por alterar na base de dados a preferência do usuário a respeito de pesquisar a organização automáticamente.
        */
        function alterar_preferencia_pesquisar_armario_automaticamente(){
            let check_pesquisar_armario_automaticamente = document.querySelector('#pesquisar_armario_automaticamente');
            let preferencia = '';

            if(check_pesquisar_armario_automaticamente.checked == true){
                preferencia = 'CHECKED';
            }else{
                preferencia = '';
            }

            sistema.request.post('/preferencia.php', {'rota':'salvar_preferencia_usuario', 'codigo_sistema': CODIGO_SISTEMA, 'codigo_usuario': CODIGO_USUARIO, 'nome_preferencia': 'PESQUISAR_ARMARIO_AUTOMATICAMENTE', 'preferencia': preferencia}, function(retorno){}, false);
        }

        /**  
         * Função responsável pro colocar no select a quantidade de documentos que o usuário deseja visualizar.
        */
        function colocar_preferencia_quantidade_armario(){
            let objeto_limite_armario = document.querySelector('#limite_retorno');
            objeto_limite_armario.value =PREFERENCIA_QUANTIDADE_ARMARIO;
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
                                <button class="btn btn-secondary custom-radius btn-lg" onclick="cadastro_armario('');">Cadastro de Armários</button>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-6 text-center">
                                <label class="text">Nome Armário</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="nome_armario" placeholder="Nome Armário" onkeyup="pesquisar_armario();" />
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Código Barras</label>
                                <input type="text" class="form-control custom-radius" sistema-mask="codigo" id="codigo_barras" placeholder="Código Barras" onkeyup="pesquisar_armario();"/>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text>">Status</label>
                                <select class="form-control custom-radius" id="status" onchange="pesquisar_armario();">
                                    <option value="TODOS">TODOS</option>
                                    <option value="ATIVO">ATIVO</option>
                                    <option value="INATIVO">INATIVO</option>
                                </select>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Forma de Visualização</label>
                                <select class="form-control custom-radius" id="tipo" onchange="pesquisar_armario();">
                                    <option value="TODOS">TODOS</option>
                                    <option value="PUBLICO">PÚBLICO</option>
                                    <option value="PRIVADO">PRIVADO</option>
                                </select>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <textarea class="custom-radius form-control" id="descricao" placeholder="Descrição" onkeyup="pesquisar_armario();"></textarea>
                        </div>
                        <br/>
                        <div class="row">
                            <?php require_once'includes/modal_organizacao.php'; ?>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-2 text-center">
                                <select class="form-control custom-radius" id="limite_retorno">
                                    <option value="25">25 ARMÁRIOS</option>
                                    <option value="50">50 ARMÁRIOS</option>
                                    <option value="75">75 ARMÁRIOS</option>
                                    <option value="100">100 ARMÁRIOS</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="push-9 col-3 text-center">
                                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                    <button type="button" class="btn btn-info custom-radius botao_grande btn-lg" onclick="pesquisar_armario();">PESQUISAR ARMÁRIOS</button>
                                    <div class="btn-group" role="group">
                                        <button type="button"
                                            class="btn btn-secondary dropdown-toggle custom-radius botao_grande btn-lg" data-toggle="dropdown" aria-expanded="false"> PREFERÊNCIAS </button>
                                        <div class="dropdown-menu">
                                            <div class="dropdown-item">
                                                <input class="form-check-item" type="checkbox" role="switch" id="visualizar_nome_armario_completo" <?php echo $usuario_preferencia_nome_armario; ?>  onclick="alterar_preferencia_nome_completo_armario();" />
                                                <label class="form-check-label" for="visualizar_nome_armario_completo">Ver nome/descrição completos</label>
                                            </div>
                                            <div class="dropdown-item">
                                                <input class="form-check-item" type="checkbox" role="switch" id="pesquisar_armario_automaticamente" <?php echo $usuario_preferencia_pesquisar_armario_automaticamente; ?> onclick="alterar_preferencia_pesquisar_armario_automaticamente();"/>
                                                <label class="form-check-label" for="pesquisar_armario_automaticamente">Pesquisar armários Automáticamente</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="tabela_armario">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">Nome</th>
                                                <th scope="col">Descrição</th>
                                                <th scope="col">Organização</th>
                                                <th scope="col">Código Barras</th>
                                                <th scope="col">Visualização</th>
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

            if(PESQUISAR_ARMARIO_AUTOMATICAMENTE == 'CHECKED'){
                pesquisar_armario();
            }
        }
    </script>
 <?php
    require_once 'includes/footer.php';
    exit;
});

//@note salvar_alterar_dados
router_add('salvar_alterar_dados', function () {
    $id_armario = (string) (isset($_REQUEST['codigo_armario']) ? (string) $_REQUEST['codigo_armario']: '');

    require_once 'includes/head.php';
 ?>
    <script>
        const CODIGO_EMPRESA = "<?php echo $_SESSION['id_empresa']; ?>";
        const CODIGO_USUARIO = "<?php echo $_SESSION['id_usuario']; ?>";

        let CODIGO_ARMARIO = "<?php echo $id_armario; ?>";

        /** 
         * Função responsável por pegar as informaçõe informadas pelo usuário e preparar em um objeto para então enviar para o back para ser salvo no banco de dados.
         */
        function salvar_dados() {
            let codigo_organizacao = document.querySelector('#codigo_organizacao').value;
            let nome_armario = document.querySelector('#nome_armario').value;
            let descricao = document.querySelector('#descricao').value;
            let status = document.querySelector('#status').value;
            let codigo_barras = document.querySelector('#codigo_barras').value;
            let forma_visualizacao = document.querySelector('#tipo').value;

            if (codigo_organizacao != '') {
                sistema.request.post('/armario.php', {
                    'rota': 'salvar_dados',
                    'codigo_armario': CODIGO_ARMARIO,
                    'empresa': CODIGO_EMPRESA,
                    'usuario': CODIGO_USUARIO,
                    'organizacao': codigo_organizacao,
                    'nome_armario': nome_armario,
                    'descricao': descricao,
                    'status':status,
                    'codigo_barras': codigo_barras,
                    'tipo': forma_visualizacao
                }, function(retorno) {
                    sistema.verificar_status(retorno.status, sistema.url('/armario.php', {
                        'rota': 'index'
                    }));
                });
            } else {
                Swal.fire({icon: "error", title: "Oops...", text: "O código da organização não pode ser vazio!"});
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
            document.querySelector('#nome_armario').value = '';
            document.querySelector('#codigo_organizacao').value = '';
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
                            <div class="col-6 text-center">
                                <label class="text">Nome Armário</label>
                                <input type="text" id="nome_armario" class="form-control custom-radius text-uppercase" placeholder="Nome Armário" />
                            </div>
                            <div class="col-2">
                                <label class="text">Status</label>
                                <select class="form-control custom-radius" id="status">
                                    <option value="">Selecione uma opção</option>
                                    <option value="ATIVO">ATIVO</option>
                                    <option value="INATIVO">INATIVO</option>
                                </select>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Forma Visualização</label>
                                <select class="form-control custom-radius" id="tipo">
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
                            <?php
                                $abrir_modal_organizacao = 'armario';
                                require_once 'includes/modal_organizacao.php';
                            ?>
                        </div>
                        <br />
                        <?php
                        require_once 'includes/botao_cadastro.php'
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.onload = async function() {
            validar_acesso_administrador('<?php echo $_SESSION['tipo_usuario']; ?>');

            window.setTimeout(function(){
                if (CODIGO_ARMARIO != '') {
                    sistema.request.post('/armario.php', {
                        'rota': 'pesquisar_armario',
                        'codigo_armario': CODIGO_ARMARIO
                    }, function(retorno) {
                        let armario = retorno.dados;
                        document.querySelector('#nome_armario').value = armario.nome_armario;
                        document.querySelector('#descricao').value = armario.descricao;
                        document.querySelector('#tipo').value = armario.tipo;
                        document.querySelector('#status').value = armario.status;
                        document.querySelector('#codigo_barras').value = armario.codigo_barras;
                        document.querySelector('#codigo_organizacao').value = armario.organizacao.$oid;
                    });
                }
            }, 500);

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
    $id_armario = (string) (isset($_REQUEST['codigo_armario']) ? (string) $_REQUEST['codigo_armario'] : '');
    $filtro = (array) [];
    $dados = (array) [];

    array_push($filtro, ['_id', '===', convert_id($id_armario)]);
    $dados['filtro'] = (array) ['and' => (array) $filtro];
    echo json_encode(['dados' => (array) $objeto_armario->pesquisar($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});


/**
 * Rota responsável por pesquisar todos os armários cadastrados no banco de dados.
 */
router_add('pesquisar_armario_todos', function () {
    $objeto_armario = new Armario();
    $objeto_preferencia = new Preferencia();
    
    $id_empresa = (string) (isset($_REQUEST['empresa']) ? (string) $_REQUEST['empresa']:'');
    $id_usuario = (string) (isset($_REQUEST['usuario']) ? (string) $_REQUEST['usuario']:'');
    $id_organizacao = (string) (isset($_REQUEST['organizacao']) ? (string) $_REQUEST['organizacao']:'');
    $id_sistema = (string) (isset($_REQUEST['sistema']) ? (string) $_REQUEST['sistema']:'');

    $nome_armario = (string) (isset($_REQUEST['nome_armario']) ? (string) strtoupper($_REQUEST['nome_armario']) : '');
    $descricao = (string) (isset($_REQUEST['descricao']) ? (string) $_REQUEST['descricao']:'');
    $tipo = (string) (isset($_REQUEST['tipo']) ? (string) $_REQUEST['tipo'] : 'TODOS');
    $codigo_barras = (string) (isset($_REQUEST['codigo_barras']) ? (string) $_REQUEST['codigo_barras']:'');
    $status = (string) (isset($_REQUEST['status']) ? (string) $_REQUEST['status']:'TODOS');

    $limite_retorno = (int) (isset($_REQUEST['limite_retorno']) ? (int) intval($_REQUEST['limite_retorno'], 25):0);
    $preferencia_usuario_retorno = (int) (isset($_REQUEST['preferencia_usuario_retorno'])? (int) intval($_REQUEST['preferencia_usuario_retorno'], 25):0);
    
    $filtro = (array) [];
    $filtro_todos_publico = (array) [];
    $filtro_todos_privado = (array) [];

    $dados = (array) ['filtro' => (array) [], 'ordenacao' => (array) ['nome_armario' => (bool) true], 'limite' => (int) $limite_retorno];
    
    $retorno = (array) [];

    if($id_empresa != ''){
        array_push($filtro_todos_publico, (array) ['empresa', '===', convert_id($id_empresa)]);
        array_push($filtro_todos_privado, (array) ['empresa', '===', convert_id($id_empresa)]);
        array_push($filtro, (array) ['empresa', '===', convert_id($id_empresa)]);
    }
    
    if($id_organizacao != ''){
        array_push($filtro_todos_publico, (array) ['organizacao', '===', convert_id($id_organizacao)]);
        array_push($filtro_todos_privado, (array) ['organizacao', '===', convert_id($id_organizacao)]);
        array_push($filtro, (array) ['organizacao', '===', convert_id($id_organizacao)]);
    }

    array_push($filtro, (array) ['nome_armario', '=', (string) strtoupper($nome_armario)]);
    array_push($filtro_todos_publico, (array) ['nome_armario', '=', (string) strtoupper($nome_armario)]);
    array_push($filtro_todos_privado, (array) ['nome_armario', '=', (string) strtoupper($nome_armario)]);
    
    array_push($filtro, (array) ['descricao', '=', (string) $descricao]);
    array_push($filtro_todos_publico, (array) ['descricao', '=', (string) $descricao]);
    array_push($filtro_todos_privado, (array) ['descricao', '=', (string) $descricao]);
    
    if($codigo_barras != ''){
        array_push($filtro, (array) ['codigo_barras', '=', (string) $codigo_barras]);
        array_push($filtro_todos_publico, (array) ['codigo_barras', '=', (string) $codigo_barras]);
        array_push($filtro_todos_privado, (array) ['codigo_barras', '=', (string) $codigo_barras]);
    }
    
    if($tipo == 'PRIVADO'){
        array_push($filtro, (array) ['usuario', '===', convert_id($id_usuario)]);
    }

    if($status != 'TODOS'){
        array_push($filtro, (array) ['status', '===', (string) $status]);
    }
    
    if($tipo == 'PRIVADO' || $tipo == 'PUBLICO'){
        array_push($filtro, ['tipo', '===', (string) $tipo]);

        $dados['filtro'] = (array) ['and' => (array) $filtro];
        $retorno_pesquisa_todos = (array) $objeto_armario->pesquisar_todos($dados);

        if(empty($retorno_pesquisa_todos) == false){
           $retorno = (array) $objeto_armario->validar_campos_filtro($retorno_pesquisa_todos, $retorno);
        }
    }else{
        array_push($filtro_todos_privado, (array) ['usuario', '===', convert_id($id_usuario)]);
        array_push($filtro_todos_privado, (array) ['tipo', '===', (string) 'PRIVADO']);
        array_push($filtro_todos_publico, (array) ['tipo', '===', (string) 'PUBLICO']);

        $dados['filtro'] = (array) ['and' => (array) $filtro_todos_privado];
        $retorno_pesquisa_privado = (array) $objeto_armario->pesquisar_todos($dados);

        $dados['filtro'] = (array) ['and' => (array) $filtro_todos_publico];
        $retorno_pesquisa_publico = (array) $objeto_armario->pesquisar_todos((array) $dados);

        if(empty($retorno_pesquisa_privado) == false){
            $retorno = (array) $objeto_armario->validar_campos_filtro($retorno_pesquisa_privado, $retorno);
        }

        if(empty($retorno_pesquisa_publico) == false){
           $retorno = (array) $objeto_armario->validar_campos_filtro($retorno_pesquisa_publico, $retorno);
        }
    }

    $objeto_preferencia->alterar_quantidade_retorno($preferencia_usuario_retorno, $limite_retorno, 'QUANTIDADE_LIMITE_ARMARIO', $id_usuario, $id_sistema);

    echo json_encode(['dados' => (array) $retorno], JSON_UNESCAPED_UNICODE);
    exit;
});
?>