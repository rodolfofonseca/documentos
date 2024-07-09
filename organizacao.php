<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Organizacao.php';

//@note index
router_add('index', function(){
    require_once 'includes/head.php';
    ?>
    <script>
        function cadastro_organizacao(codigo_organizacao){
            window.location.href = sistema.url('/organizacao.php', {'rota': 'salvar_dados', 'codigo_organizacao': codigo_organizacao});
        }

        function pesquisar_organizacao(){
            let codigo_organizacao = parseInt(document.querySelector('#codigo_organizacao').value, 10);
            let nome_organizacao = document.querySelector('#descricao_organizacao').value;

            if(isNaN(codigo_organizacao)){
                codigo_organizacao = 0;
            }

            sistema.request.post('/organizacao.php', {'rota': 'pesquisar_todos', 'codigo_organizacao': codigo_organizacao, 'nome_organizacao': nome_organizacao}, function(retorno){
                let retorno_organizacao = retorno.dados;
                let tamanho_retorno = retorno_organizacao.length;
                let tabela = document.querySelector('#tabela_organizacao tbody');
                let tamanho_tabela = tabela.rows.length;

                if(tamanho_tabela > 0){
                    tabela = sistema.remover_linha_tabela(tabela);
                }

                if(tamanho_retorno < 1){
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM ORGANIZAÇÃO ENCONTRADA', 'inner', true, 3));
                    tabela.appendChild(linha);
                }else{
                    sistema.each(retorno_organizacao, function(contador, organizacao){
                        let linha = document.createElement('tr');
                        
                        linha.appendChild(sistema.gerar_td(['text-center'], organizacao.id_organizacao, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], organizacao.nome_organizacao, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_organizacao_'+organizacao.id_organizacao, 'VISUALIZAR', ['btn', 'btn-info'], function visualizar(){cadastro_organizacao(organizacao.id_organizacao)}),'append'));
                        
                        tabela.appendChild(linha);
                    });
                }
            }, false);
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Cadastro de Organização</h4>
                        <div class="row">
                            <div class="col-3">
                                <button class="btn btn-secondary" onclick="cadastro_organizacao(0);">Cadastro de Organização</button>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-2 text-center">
                                <label class="text">Código</label>
                                <input type="text" sistema-mask="codigo" class="form-control custom-radius" id="codigo_organizacao" placeholder="Código" onkeyup="pesquisar_organizacao();"/>
                            </div>
                            <div class="col-8 text-center">
                                <label class="text">Descrição Organização</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="descricao_organizacao" placeholder="Descrição Organização" onkeyup="pesquisar_organizacao();"/>
                            </div>
                            <div class="col-2">
                                <button class="btn btn-info botao_vertical_linha" onclick="pesquisar_organizacao();">Pesquisar</button>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="tabela_organizacao">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">#</th>
                                                <th scope="col">Nome</th>
                                                <th scope="col">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody><tr><td colspan="3" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA PESQUISA</td></tr></tbody>
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
            pesquisar_organizacao();
        }
    </script>
    <?php
    require_once 'includes/footer.php';
});

/**
 * Rota responsável por apresentar o formulário de cadastro de novas organizações dentro do sistema.
 * E realizar o envio das informações preenxidas pelo usuário para o back.
 */
router_add('salvar_dados', function(){
    require_once 'includes/head.php';

    $id_organizacao = (int) (isset($_REQUEST['codigo_organizacao']) ? intval($_REQUEST['codigo_organizacao'], 10):0);
    $id_usuario = (int) intval($_SESSION['id_usuario'], 10);
    $id_empresa = (int) intval($_SESSION['id_empresa'], 10);
    ?>
    <script>
        var ID_ORGANIZACAO = <?php echo $id_organizacao; ?>;
        const ID_EMPRESA = <?php echo $id_empresa; ?>;
        const ID_USUARIO = <?php echo $id_usuario; ?>;
        const TIPO_USUARIO = '<?php echo $tipo_usuario; ?>';

        /**
         * Função responsável por pegar as informacoes dos campos preenxidos pelo usuário e enviar para o back.
         */
        function salvar_dados(){
            let codigo_organizacao = parseInt(document.querySelector('#codigo_organizacao').value, 10);
            let nome_organizacao = document.querySelector('#descricao_organizacao').value;
            let forma_visualizacao = document.querySelector('#forma_visualizacao').value;
            let codigo_barras = document.querySelector('#codigo_barras').value;

            if(isNaN(codigo_organizacao)){
                codigo_organizacao = 0;
            }

            sistema.request.post('/organizacao.php', {'rota': 'enviar_dados', 'codigo_organizacao': codigo_organizacao, 'codigo_empresa': ID_EMPRESA, 'codigo_usuario': ID_USUARIO, 'nome_organizacao': nome_organizacao, 'forma_visualizacao': forma_visualizacao, 'codigo_barras': codigo_barras}, function(retorno){
                validar_retorno(retorno);
                limpar_campos();
            });        
        }

        /**
         * Função responsável por limpar todos os campos do sistema.
         */
        function limpar_campos(){
            document.querySelector('#codigo_organizacao').value = '';
            document.querySelector('#descricao_organizacao').value = '';
            document.querySelector('#forma_visualizacao').value = '';
            
            sistema.request.post('/index.php', {'rota': 'buscar_codigo_barras'}, function(retorno){
                document.querySelector('#codigo_barras').value = retorno.codigo_barras;
            }, false);
        }

        /**
         * Função responsável por retornar o usuário para a rota index do módulo de organização.
         */
        function voltar(){
            window.location.href = sistema.url('/organizacao.php', {'rota': 'index'});
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Cadastro de Organização</h4>
                        <div class="row">
                            <div class="col-2 text-center">
                                <label class="text">Código</label>
                                <input type="text" sistema-mask="codigo" class="form-control custom-radius text-center" id="codigo_organizacao" placeholder="Código" readonly="true"/>
                            </div>
                            <div class="col-4 text-center">
                                <label class="text">Descrição Organização</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="descricao_organizacao" placeholder="Descrição Organização"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Tipo</label>
                                <select class="form-control custom-radius" id="forma_visualizacao">
                                    <option value="">Selecione uma Opção</option>
                                    <option value="PUBLICO">PÚBLICO</option>
                                    <option value="PRIVADO">PRIVADO</option>
                                </select>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Código de Barras</label>
                                <input type="text" class="form-control custom-radius text-center" id="codigo_barras" readonly="true" value="<?php echo codigo_barras(); ?>"/>
                            </div>
                        </div>
                        <br/>
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
        window.onload = function(){
            if(TIPO_USUARIO != 'ADMINISTRADOR'){
                window.location.href = sistema.url('/dashboard.php', {'rota':'index'});
            }


            if(ID_ORGANIZACAO != 0){
                sistema.request.post('/organizacao.php', {'rota':'pesquisar_organizacao', 'codigo_organizacao': ID_ORGANIZACAO}, function(retorno){
                    document.querySelector('#codigo_organizacao').value = retorno.dados.id_organizacao;
                    document.querySelector('#descricao_organizacao').value = retorno.dados.nome_organizacao;
                });
            }
        }
    </script>
    <?php
    require_once 'includes/footer.php';
    exit;
});

//@audit pesquisar_organizacao
router_add('pesquisar_organizacao', function(){
    $objeto_organizacao = new Organizacao();
    $id_organizacao = (int) (isset($_REQUEST['codigo_organizacao']) ? (int) intval($_REQUEST['codigo_organizacao'], 10):0);
    $filtro = (array) [];
    $dados = (array) [];

    if($id_organizacao != 0){
        array_push($filtro, ['id_organizacao', '===', (int) $id_organizacao]);
    }

    $dados['filtro'] = (array) ['and' => (array) $filtro];

    echo json_encode(['dados' => (array) $objeto_organizacao->pesquisar($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit pesquisar_todos
router_add('pesquisar_todos', function(){
    $objeto_organizacao = new Organizacao();
    $id_organizacao = (int) (isset($_REQUEST['codigo_organizacao']) ? (int) intval($_REQUEST['codigo_organizacao'], 10):0);
    $nome_organizacao = (string) (isset($_REQUEST['nome_organizacao']) ? (string) strtoupper($_REQUEST['nome_organizacao']):'');
    $filtro = (array) [];
    $dados = (array) [];

    if($id_organizacao != 0){
        array_push($filtro, ['id_organizacao', '===', (int) $id_organizacao]);
    }

    array_push($filtro, ['nome_organizacao', '=', (string) $nome_organizacao]);

    $dados['filtro']  = (array) ['and' => (array) $filtro];
    $dados['ordenacao'] = (array) [];
    $dados['limite'] = (int) 10;

    echo json_encode(['dados' => (array) $objeto_organizacao->pesquisar_todos($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit enviar_dados
router_add('enviar_dados', function(){
    $objeto_organizacao = new Organizacao();
    
    echo json_encode(['status' => (bool) $objeto_organizacao->salvar($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});
?>