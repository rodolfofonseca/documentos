<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Organizacao.php';
require_once 'Modelos/Preferencia.php';

/**
 * Rota responsável por mostrar ao usuário os filtros de pesquisa de organização, juntamente com o botão para cadastrar novas organizações dentro do sistema.
 */
router_add('index', function(){
    require_once 'includes/head.php';
    
    $objeto_preferencia = new Preferencia();

    $usuario_preferencia_nome_organizacao = (string) 'CHECKED';
    $usuario_preferencia_pesquisar_organizacao_automaticamente = (string) 'CHECKED';
    $usuario_preferencia_quantidade_organizacao = (int) intval(25, 10);

    //MONTANDO O FILTRO DE PESQUISA PARA SABER SE O USUÁRIO QUE ESTÁ LOGADO NO SISTEMA PREFERE VER O NOME COMPLETO DA ORGANIZAÇÃO OU NÃO
    $filtro_pesquisa = (array) ['and' => (array) [['sistema', '===', convert_id($_SESSION['id_sistema'])], ['usuario', '===', convert_id($_SESSION['id_usuario'])], ['nome_preferencia', '===', (string) 'NOME_COMPLETO_ORGANIZACAO']]];
    $retorno_pesquisa_preferencia = (array) $objeto_preferencia->pesquisar((array) ['filtro' => (array) $filtro_pesquisa]);

    if(empty($retorno_pesquisa_preferencia) == true){
        $usuario_preferencia_nome_organizacao = (string) '';
    }else{
        if(array_key_exists('preferencia', $retorno_pesquisa_preferencia) == true){
            $usuario_preferencia_nome_organizacao = (string) $retorno_pesquisa_preferencia['preferencia'];
        }
    }

    //MONTANDO O FILTRO DE PESQUISA PARA SABER SE O USUÁRIO QUE ESTÁ LOGADO NO SISTEMA PREFERE PESQUISAR AS ORGANIZAÇÕES AUTOMATICAMENTE AO ABRIR A PÁGINA
    $filtro_pesquisa_organizacao_automaticamente = (array) ['and' => (array) [['sistema', '===', convert_id($_SESSION['id_sistema'])], ['usuario', '===', convert_id($_SESSION['id_usuario'])], ['nome_preferencia', '===', (string) 'PESQUISAR_ORGANIZACAO_AUTOMATICAMENTE']]];
    $retorno_pesquisa_preferencia_organizacao_automaticamente = (array) $objeto_preferencia->pesquisar((array) ['filtro' => (array) $filtro_pesquisa_organizacao_automaticamente]);

    if(empty($retorno_pesquisa_preferencia_organizacao_automaticamente) == true){
        $usuario_preferencia_pesquisar_organizacao_automaticamente = (string) '';
    }else{
        if(array_key_exists('preferencia', $retorno_pesquisa_preferencia) == true){
            $usuario_preferencia_pesquisar_organizacao_automaticamente = (string) $retorno_pesquisa_preferencia['preferencia'];
        }
    }

    //MONTANDO FILTRO DE PESQUISA PARA SABER A QUANTIDADE DE ORGANIZAÇÃO QUE O USUÁRIO QUE ESTÁ LOGADO NO SISTEMA PREFERE QUE O MESMO RETORNE DURANTE AS PESQUISAS.
    $filtro_pesquisa = (array) ['and' => (array) [['sistema', '===', convert_id($_SESSION['id_sistema'])], ['usuario', '===', convert_id($_SESSION['id_usuario'])], ['nome_preferencia', '===', (string) 'QUANTIDADE_LIMITE_ORGANIZACAO']]];
    $retorno_pesquisa_preferencia = (array) $objeto_preferencia->pesquisar((array) ['filtro' => (array) $filtro_pesquisa]);

    if(empty($retorno_pesquisa_preferencia) == true){
        $retorno_salvar_dados = (bool) $objeto_preferencia->salvar_dados((array) ['codigo_usuario' => (string) $_SESSION['id_usuario'], 'codigo_sistema' => (string) $_SESSION['id_sistema'], 'nome_preferencia' => (string) 'QUANTIDADE_LIMITE_ORGANIZACAO', 'preferencia' => (string) $usuario_preferencia_quantidade_organizacao]);
    }else{
        if(array_key_exists('preferencia', $retorno_pesquisa_preferencia) == true){
            $usuario_preferencia_quantidade_organizacao = (int) intval($retorno_pesquisa_preferencia['preferencia'], 10);
        }
    }
    ?>
    <script>
        const ID_EMPRESA = "<?php echo $_SESSION['id_empresa']; ?>";
        const ID_USUARIO = "<?php echo $_SESSION['id_usuario']; ?>";
        const ID_SISTEMA = "<?php echo $_SESSION['id_sistema']; ?>";
        const PREFERENCIA_QUANTIDADE_ORGANIZACAO = <?php echo intval($usuario_preferencia_quantidade_organizacao, 10); ?>;
        const PESQUISAR_ORGANIZACAO_AUTOMATICAMENTE = "<?php echo $usuario_preferencia_pesquisar_organizacao_automaticamente; ?>";

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
            let nome_organizacao = document.querySelector('#nome_organizacao').value;
            let descricao_organizacao = document.querySelector('#descricao').value;
            let forma_visualizacao = document.querySelector('#forma_visualizacao').value;
            let status_organizacao = document.querySelector('#status_organizacao').value;

            let limite_organizacao = sistema.int(document.querySelector('#limite_retorno').value);
            let visualizar_nome_organizacao_completo = document.querySelector('#visualizar_nome_organizacao_completo');

            sistema.request.post('/organizacao.php', {'rota': 'pesquisar_todos', 'codigo_usuario': ID_USUARIO, 'codigo_empresa':ID_EMPRESA, 'nome_organizacao': nome_organizacao, 'descricao':descricao_organizacao, 'forma_visualizacao': forma_visualizacao, 'status_organizacao': status_organizacao, 'limite_retorno': limite_retorno, 'preferencia_usuario_retorno': PREFERENCIA_QUANTIDADE_ORGANIZACAO, 'codigo_sistema':ID_SISTEMA}, function(retorno){
                let retorno_organizacao = retorno.dados;
                let tamanho_retorno = retorno_organizacao.length;
                let tabela = document.querySelector('#tabela_organizacao tbody');
                let tamanho_tabela = tabela.rows.length;

                if(tamanho_tabela > 0){
                    tabela = sistema.remover_linha_tabela(tabela);
                }

                if(tamanho_retorno < 1){
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM ORGANIZAÇÃO ENCONTRADA', 'inner', true, 6));
                    tabela.appendChild(linha);
                }else{
                    sistema.each(retorno_organizacao, function(contador, organizacao){
                        let linha = document.createElement('tr');
                        let id_organizacao = organizacao._id.$oid;

                        if(visualizar_nome_organizacao_completo.checked == true){
                            linha.appendChild(sistema.gerar_td(['text-left'], organizacao.nome_organizacao.substring(0, 20), 'inner'))
                            
                            if(organizacao.descricao != undefined){
                                linha.appendChild(sistema.gerar_td(['text-left'], organizacao.descricao.substring(0,60), 'inner'));
                            }else{
                                linha.appendChild(sistema.gerar_td(['text-left'], '', 'inner'));
                            }
                        }else{
                            linha.appendChild(sistema.gerar_td(['text-left'], organizacao.nome_organizacao, 'inner'));
                            
                            if(organizacao.descricao != undefined){
                                linha.appendChild(sistema.gerar_td(['text-left'], organizacao.descricao, 'inner'));
                            }else{
                                linha.appendChild(sistema.gerar_td(['text-left'], '', 'inner'));
                            }
                        }
                        
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_imprimir_codigo_barras_'+id_organizacao, organizacao.codigo_barras, ['btn', 'btn-success'], function imprimir_codigo_barras(){imprimir_codigo(organizacao.codigo_barras, organizacao.nome_organizacao);}), 'append'));

                        if(ID_USUARIO == organizacao.usuario.$oid){
                            if(organizacao.forma_visualizacao == 'PUBLICO'){
                                linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_forma_visualizacao_'+id_organizacao, 'PÚBLICO', ['btn', 'btn-info'], function alterar_tipo_organizacao(){alterar_tipo(id_organizacao, organizacao.forma_visualizacao, organizacao.descricao, organizacao.codigo_barras, organizacao.nome_organizacao);}),'append'));
                            }else{
                                linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_forma_visualizacao_'+id_organizacao, 'PRIVADO', ['btn', 'btn-secondary'], function alterar_tipo_organizacao(){alterar_tipo(id_organizacao, organizacao.forma_visualizacao, organizacao.descricao, organizacao.codigo_barras, organizacao.nome_organizacao);}),'append'));
                            }                   

                            if(organizacao.status_organizacao == 'ATIVO'){
                                linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_status_organizacao_'+id_organizacao, 'ATIVO', ['btn', 'btn-success'], function botao_status_organizacao(){alterar_status_organizacao(id_organizacao, 'ATIVO');}), 'append'));
                            }else{
                                linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_status_organizacao_'+id_organizacao, 'INATIVO', ['btn', 'btn-danger'], function botao_status_organizacao(){alterar_status_organizacao(id_organizacao, 'INATIVO');}), 'append'));
                            }
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_organizacao_'+id_organizacao, 'VISUALIZAR', ['btn', 'btn-info'], function visualizar(){cadastro_organizacao(id_organizacao)}),'append'));
                        }else{
                            if(organizacao.forma_visualizacao == 'PUBLICO'){
                                linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_forma_visualizacao_'+id_organizacao, 'PÚBLICO', ['btn', 'btn-info'], function alterar_tipo_organizacao(){}),'append'));
                            }else{
                                linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_forma_visualizacao_'+id_organizacao, 'PRIVADO', ['btn', 'btn-secondary'], function alterar_tipo_organizacao(){}),'append'));
                            }

                            if(organizacao.status_organizacao == 'ATIVO'){
                                linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_status_organizacao_'+id_organizacao, 'ATIVO', ['btn', 'btn-success'], function botao_status_organizacao(){}), 'append'));
                            }else{
                                linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_status_organizacao_'+id_organizacao, 'INATIVO', ['btn', 'btn-danger'], function botao_status_organizacao(){}), 'append'));
                            }
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_organizacao_'+id_organizacao, 'VISUALIZAR', ['btn', 'btn-info', 'disabled'], function visualizar(){}),'append'));
                        }
                        
                        tabela.appendChild(linha);
                    });
                }
            }, false);
        }

        function alterar_status_organizacao(id_organizacao, status){
            sistema.request.post('/organizacao.php', {'rota': 'alterar_status_organizacao', 'codigo_organizacao':id_organizacao, 'status_organizacao': status}, function(retorno){
                validar_retorno(retorno, '/organizacao.php');
                pesquisar_organizacao();
            });
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
         */
        function alterar_tipo(id_organizacao, forma_visualizacao){

            sistema.request.post('/organizacao.php', {'rota':'alterar_tipo_organizacao', 'codigo_organizacao': id_organizacao, 'forma_visualizacao':forma_visualizacao}, function(retorno){
                validar_retorno(retorno, '/organizacao.php');
                pesquisar_organizacao();
            });
        }

        /**  
         * Função responsável por alterar na base de dados a preferência do usuário a respeito de visualizar o nome da organização completa.
        */
        function alterar_preferencia_nome_completo_organizacao(){
            let check_visualizar_nome_organizacao = document.querySelector('#visualizar_nome_organizacao_completo');
            let preferencia = '';

            if(check_visualizar_nome_organizacao.checked == true){
                preferencia = 'CHECKED';
            }else{
                preferencia = '';
            }

            sistema.request.post('/preferencia.php', {'rota':'salvar_preferencia_usuario', 'codigo_sistema': ID_SISTEMA, 'codigo_usuario': ID_USUARIO, 'nome_preferencia': 'NOME_COMPLETO_ORGANIZACAO', 'preferencia': preferencia}, function(retorno){}, false);
        }

        /** 
         * Função responsável por adicionar ao campo select a quantidade de documentos que o usuário deseja visualizar dentro do sistema.
        */
        function colocar_preferencia_quantidade_organizacao(){
            let objeto_limite_organizacao = document.querySelector('#limite_retorno');
            objeto_limite_organizacao.value = PREFERENCIA_QUANTIDADE_ORGANIZACAO;
        }

        function alterar_preferencia_pesquisar_organizacao_automaticamente(){
            let check_pesquisar_organizacao_automaticamente = document.querySelector('#pesquisar_organizacao_automaticamente');
            let preferencia = '';

            if(check_pesquisar_organizacao_automaticamente.checked == true){
                preferencia = 'CHECKED';
            }else{
                preferencia = '';
            }

            sistema.request.post('/preferencia.php', {'rota': 'salvar_preferencia_usuario', 'codigo_sistema': ID_SISTEMA, 'codigo_usuario': ID_USUARIO, 'nome_preferencia': 'PESQUISAR_ORGANIZACAO_AUTOMATICAMENTE', 'preferencia': preferencia}, function(retorno){}, false);
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
                                <button class="btn btn-secondary btn-lg custom-radius" onclick="cadastro_organizacao('');">Cadastro de Organização</button>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-4 text-center">
                                <label class="text">Nome Organização</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="nome_organizacao" placeholder="Nome Organização" onkeyup="pesquisar_organizacao();"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Código Barras</label>
                                <input type="text" class="form-control custom-radius" sistema-mask="codigo" id="codigo_barras" placeholder="Código Barras" onkeyup="pesquisar_organizacao();"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Tipo</label>
                                <select class="form-control custom-radius" id="forma_visualizacao">
                                    <option value="TODOS">TODOS</option>
                                    <option value="PUBLICO">PÚBLICO</option>
                                    <option value="PRIVADO">PRIVADO</option>
                                </select>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Status</label>
                                <select class="form-control custom-radius" id="status_organizacao">
                                    <option value="TODOS">TODOS</option>
                                    <option value="ATIVO">ATIVO</option>
                                    <option value="INATIVO">INATIVO</option>
                                </select>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-12 text-center">
                                <label class="text">Descrição Organização</label>
                                <input type="text" class="form-control custom-radius" id="descricao" placeholder="Descrição da Organização" onkeyup="pesquisar_organizacao();"/>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-2 text-center">
                                <select class="form-control custom-radius" id="limite_retorno">
                                    <option value="25">25 ORGANIZAÇÕES</option>
                                    <option value="50">50 ORGANIZAÇÕES</option>
                                    <option value="75">75 ORGANIZAÇÕES</option>
                                    <option value="100">100 ORGANIZAÇÕES</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="push-9 col-3 text-center">
                                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                    <button type="button" class="btn btn-info custom-radius botao_grande btn-lg" onclick="pesquisar_organizacao();">PESQUISAR ORGANIZAÇÃO</button>
                                    <div class="btn-group" role="group">
                                        <button type="button"
                                            class="btn btn-secondary dropdown-toggle custom-radius botao_grande btn-lg" data-toggle="dropdown" aria-expanded="false"> PREFERÊNCIAS </button>
                                        <div class="dropdown-menu">
                                            <div class="dropdown-item">
                                                <input class="form-check-item" type="checkbox" role="switch" id="visualizar_nome_organizacao_completo" <?php echo $usuario_preferencia_nome_organizacao; ?>  onclick="alterar_preferencia_nome_completo_organizacao();" />
                                                <label class="form-check-label" for="visualizar_nome_organizacao_completo">Ver nome/descrição completos</label>
                                            </div>
                                            <div class="dropdown-item">
                                                <input class="form-check-item" type="checkbox" role="switch" id="pesquisar_organizacao_automaticamente" <?php echo $usuario_preferencia_pesquisar_organizacao_automaticamente; ?> onclick="alterar_preferencia_pesquisar_organizacao_automaticamente();"/>
                                                <label class="form-check-label" for="pesquisar_organizacao_automaticamente">Pesquisar organização Automáticamente</label>
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
                                    <table class="table table-hover table-striped" id="tabela_organizacao">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">Nome</th>
                                                <th scope="col">Descrição</th>
                                                <th scope="col">Código Barras</th>
                                                <th scope="col">Tipo</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Visualizar</th>
                                            </tr>
                                        </thead>
                                        <tbody><tr><td colspan="6" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA PESQUISA</td></tr></tbody>
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

            if(PESQUISAR_ORGANIZACAO_AUTOMATICAMENTE == 'CHECKED'){
                pesquisar_organizacao();
            }
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

    $id_organizacao = (string) (isset($_REQUEST['codigo_organizacao']) ? $_REQUEST['codigo_organizacao']:'');
    ?>
    <script>
        var ID_ORGANIZACAO = "<?php echo $id_organizacao; ?>";
        const ID_EMPRESA = "<?php echo $_SESSION['id_empresa']; ?>";
        const ID_USUARIO = "<?php echo $_SESSION['id_usuario']; ?>";
        const TIPO_USUARIO = '<?php echo TIPO_USUARIO; ?>';

        /**
         * Função responsável por pegar as informacoes dos campos preenxidos pelo usuário e enviar para o back.
         */
        function salvar_dados(){
            let codigo_organizacao = document.querySelector('#codigo_organizacao').value;
            let nome_organizacao = document.querySelector('#descricao_organizacao').value;
            let descricao = document.querySelector('#descricao').value;
            let forma_visualizacao = document.querySelector('#forma_visualizacao').value;
            let codigo_barras = document.querySelector('#codigo_barras').value;

            sistema.request.post('/organizacao.php', {'rota': 'enviar_dados', 'codigo_organizacao': codigo_organizacao, 'codigo_empresa': ID_EMPRESA, 'codigo_usuario': ID_USUARIO, 'nome_organizacao': nome_organizacao, 'descricao': descricao,'forma_visualizacao': forma_visualizacao, 'codigo_barras': codigo_barras}, function(retorno){
                validar_retorno(retorno, '/organizacao.php');
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
                            <input type="hidden" class="form-control custom-radius text-center" id="codigo_organizacao" placeholder="Código" readonly="true"/>
                            <div class="col-6 text-center">
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
                        <?php
                        require_once 'includes/botao_cadastro.php';
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.onload = function(){
            validar_acesso_administrador('<?php echo $_SESSION['tipo_usuario']; ?>');


            if(ID_ORGANIZACAO != ''){
                sistema.request.post('/organizacao.php', {'rota':'pesquisar_organizacao', 'codigo_organizacao': ID_ORGANIZACAO}, function(retorno){
                    document.querySelector('#codigo_organizacao').value = retorno.dados._id;
                    document.querySelector('#descricao_organizacao').value = retorno.dados.nome_organizacao;
                    document.querySelector('#descricao').value = retorno.dados.descricao;
                    document.querySelector('#forma_visualizacao').value = retorno.dados.forma_visualizacao;

                    if(retorno.status_organizacao != 'ATIVO'){
                        document.querySelector('#botao_salvar_dados').disabled = true;
                        document.querySelector('#botao_limpar_campos').disabled = true;
                    }
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
    $id_organizacao = (isset($_REQUEST['codigo_organizacao']) ? $_REQUEST['codigo_organizacao']:'');
    $id_empresa = (isset($_REQUEST['codigo_empresa']) ? $_REQUEST['codigo_empresa']:'');

    $filtro = (array) [];
    $dados = (array) [];

    if($id_organizacao != ''){
        array_push($filtro, ['_id', '===', convert_id($id_organizacao)]);
    }

    if($id_empresa != ''){
        array_push($filtro, ['empresa', '===', convert_id($id_empresa)]);
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
    
    //IDENTIFICADORES DO SISTEMA
    $id_organizacao = (string) (isset($_REQUEST['codigo_organizacao']) ? (string) $_REQUEST['codigo_organizacao']:'');
    $id_usuario = (string) (isset($_REQUEST['codigo_usuario']) ? (string) $_REQUEST['codigo_usuario']:'');
    $id_empresa = (string) (isset($_REQUEST['codigo_empresa']) ? (string) $_REQUEST['codigo_empresa']:'');
    $id_sistema = (string) (isset($_REQUEST['codigo_sistema']) ? (string) $_REQUEST['codigo_sistema']:'');


    $nome_organizacao = (string) (isset($_REQUEST['nome_organizacao']) ? (string) strtoupper($_REQUEST['nome_organizacao']):'');
    $descricao = (string) (isset($_REQUEST['descricao']) ? (string) $_REQUEST['descricao']:'');
    $forma_visualizacao = (string) (isset($_REQUEST['forma_visualizacao']) ? (string) $_REQUEST['forma_visualizacao']:'PUBLICO');
    $status_organizacao = (string) (isset($_REQUEST['status_organizacao']) ? (string) $_REQUEST['status_organizacao']:'TODOS');

    //QUANTIDADE DE RETORNO / PREFERÊNCIA USUÁRIO DE RETORNO
    $limite_retorno = (int) (isset($_REQUEST['limite_retorno']) ? (int) intval($_REQUEST['limite_retorno'], 10):25);
    $preferencia_usuario_retorno = (int) (isset($_REQUEST['preferencia_usuario_retorno']) ? (int) intval($_REQUEST['preferencia_usuario_retorno'], 10):25);

    $filtro = (array) [];
    $filtro_todos_publico = (array) [];
    $filtro_todos_privado = (array) [];
    $dados = (array) ['filtro' => (array) [], 'ordenacao' => (array) ['nome_organizacao' => (bool) true], 'limite' => (int) $limite_retorno];
    $retorno = (array) [];
    $retorno_pesquisa = (array) [];

    if($id_organizacao != ''){
        array_push($filtro, ['_id', '===', convert_id($id_organizacao)]);
        array_push($filtro_todos_publico, ['_id', '===', convert_id($id_organizacao)]);
        array_push($filtro_todos_privado, ['_id', '===', convert_id($id_organizacao)]);
    }
    
    if($id_empresa != ''){
        array_push($filtro, ['empresa', '===', convert_id($id_empresa)]);
        array_push($filtro_todos_publico, ['empresa', '===', convert_id($id_empresa)]);
        array_push($filtro_todos_privado, ['empresa', '===', convert_id($id_empresa)]);
    }

    if($status_organizacao != 'TODOS'){
        array_push($filtro, ['status_organizacao', '===', (string) $status_organizacao]);
        array_push($filtro_todos_publico, ['status_organizacao', '===', (string) $status_organizacao]);
        array_push($filtro_todos_privado, ['status_organizacao', '===', (string) $status_organizacao]);
    }

    array_push($filtro, ['nome_organizacao', '=', (string) $nome_organizacao]);
    array_push($filtro_todos_publico, ['nome_organizacao', '=', (string) $nome_organizacao]);
    array_push($filtro_todos_privado, ['nome_organizacao', '=', (string) $nome_organizacao]);

    array_push($filtro, (array) ['descricao', '=', (string) $descricao]);
    array_push($filtro_todos_publico, (array) ['descricao', '=', (string) $descricao]);
    array_push($filtro_todos_privado, (array) ['descricao', '=', (string) $descricao]);

    if($forma_visualizacao == 'PRIVADO'){
        array_push($filtro, ['usuario', '===', convert_id($id_usuario)]);
    }
    
    if($forma_visualizacao == 'PRIVADO' || $forma_visualizacao == 'PUBLICO'){
        array_push($filtro, ['forma_visualizacao', '===', (string) $forma_visualizacao]);
        
        $dados['filtro'] = (array) ['and' => (array) $filtro];
        
        $retorno = (array) $objeto_organizacao->pesquisar_todos($dados);
    }else{
        array_push($filtro_todos_privado, ['usuario', '===', convert_id($id_usuario)]);
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

    $objeto_preferencia = new Preferencia();
    $objeto_preferencia->alterar_quantidade_retorno($preferencia_usuario_retorno, $limite_retorno, 'QUANTIDADE_LIMITE_ORGANIZACAO', $id_usuario, $id_sistema);

    echo json_encode(['dados' => (array) $retorno], JSON_UNESCAPED_UNICODE);
    exit;
});

/**
 * Rota responsável por pegar as informações que vem do front e enviar para o back para que a mesma possa estar salvando na base de dados.
 */
router_add('enviar_dados', function(){
    $objeto_organizacao = new Organizacao();
    
    echo json_encode((array) ['status' => (bool) $objeto_organizacao->salvar_dados($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit alterar_status_organizacao
router_add('alterar_status_organizacao', function(){
    $objeto_organizacao = new Organizacao();
    
    echo json_encode((array) ['status' => (bool) $objeto_organizacao->alterar_status_organizacao($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});

router_add('alterar_tipo_organizacao', function(){
    $objeto_organizacao = new Organizacao();
    
    echo json_encode((array) ['status' => (bool) $objeto_organizacao->alterar_tipo_organizacao($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});

router_add('pesquisar_todos', function(){
    $objeto_organizacao = new Organizacao();

    $filtro = (array) ['filtro'];

    echo json_encode(['dados' => (array) $objeto_organizacao->pesquisar_todos($filtro), JSON_UNESCAPED_UNICODE]);
    exit;
});
?>