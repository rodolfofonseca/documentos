<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Organizacao.php';

/**
 * Rota responsável por mostrar ao usuário os filtros de pesquisa de organização, juntamente com o botão para cadastrar novas organizações dentro do sistema.
 */
router_add('index', function(){
    require_once 'includes/head.php';
    ?>
    <script>
        const ID_EMPRESA = <?php echo intval($_SESSION['id_empresa'], 10); ?>;
        const ID_USUARIO = <?php echo intval($_SESSION['id_usuario'], 10); ?>;

        /**
         * Função responsável por abrir a rota de cadastro de organização.
         */
        function cadastro_organizacao(codigo_organizacao){
            window.location.href = sistema.url('/organizacao.php', {'rota': 'salvar_dados', 'codigo_organizacao': codigo_organizacao});
        }

        /** 
         * Função responsável por pesquisar as organizações no banco de dados e montar a tabela com as informações pesquisadas.
         */
        function pesquisar_organizacao(){
            let codigo_organizacao = parseInt(document.querySelector('#codigo_organizacao').value, 10);
            let nome_organizacao = document.querySelector('#descricao_organizacao').value;
            let descricao_organizacao = document.querySelector('#descricao').value;
            let forma_visualizacao = document.querySelector('#forma_visualizacao').value;

            if(isNaN(codigo_organizacao)){
                codigo_organizacao = 0;
            }

            sistema.request.post('/organizacao.php', {'rota': 'pesquisar_todos', 'codigo_organizacao': codigo_organizacao, 'codigo_usuario': ID_USUARIO, 'codigo_empresa':ID_EMPRESA, 'nome_organizacao': nome_organizacao, 'descricao':descricao_organizacao, 'forma_visualizacao': forma_visualizacao}, function(retorno){
                let retorno_organizacao = retorno.dados;
                let tamanho_retorno = retorno_organizacao.length;
                let tabela = document.querySelector('#tabela_organizacao tbody');
                let tamanho_tabela = tabela.rows.length;

                if(tamanho_tabela > 0){
                    tabela = sistema.remover_linha_tabela(tabela);
                }

                if(tamanho_retorno < 1){
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM ORGANIZAÇÃO ENCONTRADA', 'inner', true, 7));
                    tabela.appendChild(linha);
                }else{
                    sistema.each(retorno_organizacao, function(contador, organizacao){
                        let linha = document.createElement('tr');
                        
                        linha.appendChild(sistema.gerar_td(['text-center'], organizacao.id_organizacao, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], organizacao.nome_organizacao, 'inner'));

                        if(organizacao.descricao != undefined){
                            linha.appendChild(sistema.gerar_td(['text-left'], organizacao.descricao, 'inner'));
                        }else{
                            linha.appendChild(sistema.gerar_td(['text-left'], '', 'inner'));
                        }

                        if(organizacao.forma_visualizacao == 'PUBLICO'){
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_forma_visualizacao_'+organizacao.id_organizacao, 'PÚBLICO', ['btn', 'btn-info'], function visualizacao_informacoes(){}),'append'));
                        }else{
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_forma_visualizacao_'+organizacao.id_organizacao, 'PRIVADO', ['btn', 'btn-secondary'], function visualizacao_informacoes(){}),'append'));
                        }

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_imprimir_codigo_barras_'+organizacao.id_organizacao, organizacao.codigo_barras, ['btn', 'btn-success'], function imprimir_codigo_barras(){imprimir_codigo(organizacao.codigo_barras, organizacao.nome_organizacao);}), 'append'));

                        if(ID_USUARIO == organizacao.id_usuario){
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_excluir_organizacao_'+organizacao.id_organizacao, 'EXCLUIR', ['btn', 'btn-danger'], function botao_excluir_organizacao(){excluir_organizacao(organizacao.id_organizacao);}), 'append'));
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_alterar_tipo_organizacao_'+organizacao.id_organizacao, 'ALT. TIPO', ['btn', 'btn-primary'], function alterar_tipo_organizacao(){alterar_tipo(organizacao.id_organizacao, organizacao.forma_visualizacao, organizacao.descricao, organizacao.codigo_barras, organizacao.nome_organizacao);}), 'append'));
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_organizacao_'+organizacao.id_organizacao, 'VISUALIZAR', ['btn', 'btn-info'], function visualizar(){cadastro_organizacao(organizacao.id_organizacao)}),'append'));
                        }else{
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_excluir_organizacao_'+organizacao.id_organizacao, 'EXCLUIR', ['btn', 'btn-danger', 'disabled'], function excluir_organizacao(){}), 'append'));
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_alterar_tipo_organizacao_'+organizacao.id_organizacao, 'ALT. TIPO', ['btn', 'btn-primary', 'disabled'], function alterar_tipo_organizacao(){}), 'append'));
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_organizacao_'+organizacao.id_organizacao, 'VISUALIZAR', ['btn', 'btn-info', 'disabled'], function visualizar(){}),'append'));
                        }
                        
                        tabela.appendChild(linha);
                    });
                }
            }, false);
        }

        /**
         *  Função responsável por abrir a janela de impressão do código de barras da organização.
         * @param string codigo_barras código de barras da organização
         * @param string nome_organizacao nome da organização
         * 
         */
        function imprimir_codigo(codigo_barras, nome_organizacao){
            window.open(sistema.url('/organizacao.php', {'rota':'imprimir_codigo_barras', 'codigo_barras': codigo_barras, 'nome_organizacao':nome_organizacao}), 'Impressão de código de barras', 'width=740px, height=500px, scrollbars=yes');
        }

        /**
         * Função responsável por realizar a alteração do tipo de organização.
         * @param integer id_organizacao identificador do tipo de organização dentro do sistema
         * @param string forma_visualizacao identifica a forma como a organização deve ser visualizada dentro do sistema.
         * @param string descricao descrição da organização
         * @param string codigo_barras código string único dentro do sistema. 
         * @param string nome_organizacao nome que o usuário do sistema deu para a organização
         */
        function alterar_tipo(id_organizacao, forma_visualizacao, descricao, codigo_barras, nome_organizacao){
            if(forma_visualizacao == 'PUBLICO'){
                forma_visualizacao = 'PRIVADO';
            }else{
                forma_visualizacao = 'PUBLICO';
            }

            if(descricao == undefined){
                descricao = '';
            }

            sistema.request.post('/organizacao.php', {'rota':'enviar_dados', 'codigo_organizacao': id_organizacao, 'codigo_empresa': ID_EMPRESA, 'codigo_usuario': ID_USUARIO, 'nome_organizacao':nome_organizacao, 'descricao':descricao, 'codigo_barras':codigo_barras, 'forma_visualizacao':forma_visualizacao}, function(retorno){
                validar_retorno(retorno);
                pesquisar_organizacao();
            });
        }

        /**
         * Função responsável por excluir do sistema as organizações cadastradas.
         */
        function excluir_organizacao(codigo_organizacao){
            sistema.request.post('/organizacao.php', {'rota': 'excluir_organizacao', 'codigo_organizacao':codigo_organizacao}, function(retorno){
                validar_retorno(retorno, '', 1);
                pesquisar_organizacao();
            });
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
                                <button class="btn btn-secondary btn-lg custom-radius" onclick="cadastro_organizacao(0);">Cadastro de Organização</button>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-1 text-center">
                                <label class="text">Código</label>
                                <input type="text" sistema-mask="codigo" class="form-control custom-radius" id="codigo_organizacao" placeholder="Código" onkeyup="pesquisar_organizacao();"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Nome Organização</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="descricao_organizacao" placeholder="Nome Organização" onkeyup="pesquisar_organizacao();"/>
                            </div>
                            <div class="col-4 text-center">
                                <label class="text">Descrição Organização</label>
                                <input type="text" class="form-control custom-radius" id="descricao" placeholder="Descrição da Organização" onkeyup="pesquisar_organizacao();"/>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Tipo</label>
                                <select class="form-control custom-radius" id="forma_visualizacao">
                                    <option value="TODOS">TODOS</option>
                                    <option value="PUBLICO">PÚBLICO</option>
                                    <option value="PRIVADO">PRIVADO</option>
                                </select>
                            </div>
                            <div class="col-2">
                                <button class="btn btn-info botao_vertical_linha botao_grande custom-radius" onclick="pesquisar_organizacao();">Pesquisar</button>
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
                                                <th scope="col">Descrição</th>
                                                <th scope="col">Tipo</th>
                                                <th scope="col">Código Barras</th>
                                                <th scope="col">Excluir</th>
                                                <th scope="col">Alt. Tipo</th>
                                                <th scope="col">Alt. Dados</th>
                                            </tr>
                                        </thead>
                                        <tbody><tr><td colspan="8" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA PESQUISA</td></tr></tbody>
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
            validar_acesso_administrador('<?php echo $_SESSION['tipo_usuario']; ?>');
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
    ?>
    <script>
        var ID_ORGANIZACAO = <?php echo $id_organizacao; ?>;
        const ID_EMPRESA = <?php echo intval($_SESSION['id_empresa'], 10); ?>;
        const ID_USUARIO = <?php echo intval($_SESSION['id_usuario'], 10); ?>;
        const TIPO_USUARIO = '<?php echo $tipo_usuario; ?>';

        /**
         * Função responsável por pegar as informacoes dos campos preenxidos pelo usuário e enviar para o back.
         */
        function salvar_dados(){
            let codigo_organizacao = parseInt(document.querySelector('#codigo_organizacao').value, 10);
            let nome_organizacao = document.querySelector('#descricao_organizacao').value;
            let descricao = document.querySelector('#descricao').value;
            let forma_visualizacao = document.querySelector('#forma_visualizacao').value;
            let codigo_barras = document.querySelector('#codigo_barras').value;

            if(isNaN(codigo_organizacao)){
                codigo_organizacao = 0;
            }

            sistema.request.post('/organizacao.php', {'rota': 'enviar_dados', 'codigo_organizacao': codigo_organizacao, 'codigo_empresa': ID_EMPRESA, 'codigo_usuario': ID_USUARIO, 'nome_organizacao': nome_organizacao, 'descricao': descricao,'forma_visualizacao': forma_visualizacao, 'codigo_barras': codigo_barras}, function(retorno){
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
            document.querySelector('#descricao').value = '';
            
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
                                <label class="text">Nome Organização</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="descricao_organizacao" placeholder="Nome Organização"/>
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
                            <div class="col-12 text-center">
                                <label class="text text-center">Descrição da Organização</label>
                                <textarea class="form-control custom-radius" id="descricao" placeholder="Descrição da Organização"></textarea>
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
            validar_acesso_administrador('<?php echo $_SESSION['tipo_usuario']; ?>');


            if(ID_ORGANIZACAO != 0){
                sistema.request.post('/organizacao.php', {'rota':'pesquisar_organizacao', 'codigo_organizacao': ID_ORGANIZACAO}, function(retorno){
                    document.querySelector('#codigo_organizacao').value = retorno.dados.id_organizacao;
                    document.querySelector('#descricao_organizacao').value = retorno.dados.nome_organizacao;
                    document.querySelector('#descricao').value = retorno.dados.descricao;
                    document.querySelector('#forma_visualizacao').value = retorno.dados.forma_visualizacao;
                });
            }
        }
    </script>
    <?php
    require_once 'includes/footer.php';
    exit;
});

router_add('imprimir_codigo_barras', function(){
    $codigo_barras = (string) (isset($_REQUEST['codigo_barras']) ? (string) $_REQUEST['codigo_barras']:'');
    $nome_organizacao = (string) (isset($_REQUEST['nome_organizacao']) ? (string) $_REQUEST['nome_organizacao']:'');

    require_once 'includes/head_relatorio.php';
    ?>
    <script>
        const CODIGO_BARRAS = "<?php echo $codigo_barras; ?>";

        /** 
         * Função responsável por fechar a janela de impressão de relatório.
        */
        function fechar_janela(){
            window.close();
        }

        /** 
         * Função responsável por imprimir o conteúdo da janela.
         */
        function imprimir_conteudo(){
            document.querySelector('#botao_fechar').style.display = 'none';
            document.querySelector('#botao_imprimir').style.display = 'none';

            let card_impresao = document.querySelector('#card_impresao');

            card_impresao.classList.remove();
            card_impresao.classList.add('col-12');

            window.print();

            window.setTimeout(function(){
                document.querySelector('#botao_fechar').style.display = 'block';
                document.querySelector('#botao_imprimir').style.display = 'block';

                card_impresao.classList.remove();
                card_impresao.classList.add('col-8');
            }, 500);
        }

        /** 
         * Função responsável por gerar o código de barras
         */
        function gerar_codigo_barras(){
            JsBarcode('#barcode').options({font:"OCR-B"}).CODE128(CODIGO_BARRAS, {fontSize:25, textMargin:0}).blank(20).render();
        }
    </script>
    <div class="row">
        <div class="col-8" id="card_impresao">
            <div class="card text-center">
                <div class="card-header">
                    <?php echo $nome_organizacao; ?>
                </div>
                <div class="card-body">
                    <svg id="barcode"></svg>
                </div>
            </div>
        </div>
        <div class="col-2" id="botao_imprimir">
            <br/>
            <button class="btn btn-info btn-lg" onclick="imprimir_conteudo();">IMPRIMIR</button>
        </div>
        <div class="col-2" id="botao_fechar">
            <br/>
            <button class="btn btn-danger btn-lg" onclick="fechar_janela();">FECHAR</button>
        </div>
    </div>
    <?php
    require_once 'includes/footer_relatorio.php';
    ?>
    <script>
        window.onload = function(){
            gerar_codigo_barras();
        }
    </script>
    <?php
    exit;
});

/**
 * Rota responsável por pesquisar os dados da organização.
 */
router_add('pesquisar_organizacao', function(){
    $objeto_organizacao = new Organizacao();
    $id_organizacao = (int) (isset($_REQUEST['codigo_organizacao']) ? (int) intval($_REQUEST['codigo_organizacao'], 10):0);
    $id_empresa = (int) (isset($_REQUEST['codigo_empresa']) ? (int) intval($_REQUEST['codigo_empresa'], 10):0);

    $filtro = (array) [];
    $dados = (array) [];

    if($id_organizacao != 0){
        array_push($filtro, ['id_organizacao', '===', (int) $id_organizacao]);
    }

    if($id_empresa != 0){
        array_push($filtro, ['id_empresa', '===', (int) $id_empresa]);
    }

    $dados['filtro'] = (array) ['and' => (array) $filtro];

    echo json_encode(['dados' => (array) $objeto_organizacao->pesquisar($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});

/**
 * Rota responsável por pesquisar os dados de todas as organizações do sistema, e mostrar apenas as que o usuário que está acessando o sistema tem permissão para estar visualizando.
 */
router_add('pesquisar_todos', function(){
    $objeto_organizacao = new Organizacao();
    $id_organizacao = (int) (isset($_REQUEST['codigo_organizacao']) ? (int) intval($_REQUEST['codigo_organizacao'], 10):0);
    $id_usuario = (int) (isset($_REQUEST['codigo_usuario']) ? (int) intval($_REQUEST['codigo_usuario'], 10):0);
    $id_empresa = (int) (isset($_REQUEST['codigo_empresa']) ? (int) intval($_REQUEST['codigo_empresa'], 10):0);
    $nome_organizacao = (string) (isset($_REQUEST['nome_organizacao']) ? (string) strtoupper($_REQUEST['nome_organizacao']):'');
    $descricao = (string) (isset($_REQUEST['descricao']) ? (string) $_REQUEST['descricao']:'');
    $forma_visualizacao = (string) (isset($_REQUEST['forma_visualizacao']) ? (string) $_REQUEST['forma_visualizacao']:'PUBLICO');

    $filtro = (array) [];
    $filtro_todos_publico = (array) [];
    $filtro_todos_privado = (array) [];
    $dados = (array) ['filtro' => (array) [], 'ordenacao' => (array) ['nome_organizacao' => (bool) true], 'limite' => (int) 10];
    $retorno = (array) [];
    $retorno_pesquisa = (array) [];

    if($id_organizacao != 0){
        array_push($filtro, ['id_organizacao', '===', (int) $id_organizacao]);
        array_push($filtro_todos_publico, ['id_organizacao', '===', (int) $id_organizacao]);
        array_push($filtro_todos_privado, ['id_organizacao', '===', (int) $id_organizacao]);
    }
    
    if($id_empresa != 0){
        array_push($filtro, ['id_empresa', '===', (int) $id_empresa]);
        array_push($filtro_todos_publico, ['id_empresa', '===', (int) $id_empresa]);
        array_push($filtro_todos_privado, ['id_empresa', '===', (int) $id_empresa]);
    }

    array_push($filtro, ['nome_organizacao', '=', (string) $nome_organizacao]);
    array_push($filtro_todos_publico, ['nome_organizacao', '=', (string) $nome_organizacao]);
    array_push($filtro_todos_privado, ['nome_organizacao', '=', (string) $nome_organizacao]);

    array_push($filtro, (array) ['descricao', '=', (string) $descricao]);
    array_push($filtro_todos_publico, (array) ['descricao', '=', (string) $descricao]);
    array_push($filtro_todos_privado, (array) ['descricao', '=', (string) $descricao]);

    if($forma_visualizacao == 'PRIVADO'){
        array_push($filtro, ['id_usuario', '===', (int) $id_usuario]);
    }
    
    if($forma_visualizacao == 'PRIVADO' || $forma_visualizacao == 'PUBLICO'){
        array_push($filtro, ['forma_visualizacao', '===', (string) $forma_visualizacao]);
        
        $dados['filtro'] = (array) ['and' => (array) $filtro];
        
        $retorno = (array) $objeto_organizacao->pesquisar_todos($dados);
    }else{
        array_push($filtro_todos_privado, ['id_usuario', '===', (int) $id_usuario]);
        array_push($filtro_todos_publico, ['forma_visualizacao', '===', (string) 'PUBLICO']);
        array_push($filtro_todos_privado, ['forma_visualizacao', '===', (string) 'PRIVADO']);
        
        $dados['filtro'] = (array) ['and' => (array) $filtro_todos_privado];
        $retorno_pesquisa_privado = (array) $objeto_organizacao->pesquisar_todos($dados);
        
        $dados['filtro'] = (array) ['and' => (array) $filtro_todos_publico];
        $retorno_pesquisa_publico = (array) $objeto_organizacao->pesquisar_todos($dados);

        if(empty($retorno_pesquisa_privado) == false){
            foreach($retorno_pesquisa_privado as $retorno_pesquisa){
                array_push($retorno, $retorno_pesquisa);
            }
        }

        if(empty($retorno_pesquisa_publico) == false){
            foreach($retorno_pesquisa_publico as $retorno_pesquisa){
                array_push($retorno, $retorno_pesquisa);
            }
        }
    }

    echo json_encode(['dados' => (array) $retorno], JSON_UNESCAPED_UNICODE);
    exit;
});

/**
 * Rota responsável por pegar as informações que vem do front e enviar para o back para que a mesma possa estar salvando na base de dados.
 */
router_add('enviar_dados', function(){
    $objeto_organizacao = new Organizacao();
    
    echo json_encode(['status' => (bool) $objeto_organizacao->salvar($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit excluir_organizacao
router_add('excluir_organizacao', function(){
    $objeto_organizacao = new Organizacao();
    echo json_encode( (array) $objeto_organizacao->excluir_organizacao($_REQUEST), JSON_UNESCAPED_UNICODE);
    exit;
});
?>