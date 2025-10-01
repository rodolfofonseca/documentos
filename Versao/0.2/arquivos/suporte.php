<?php
require_once 'Classes/bancoDeDados.php';
require_once 'modelos/TicketSuporte.php';
require_once 'modelos/MensagemTicketSuporte.php';

//@note index
router_add('index', function(){
    require_once 'includes/head.php';
    ?>
    <script>
        const CODIGO_EMPRESA = <?php echo CODIGO_EMPRESA; ?>;

        /** 
         * Função responsável por chamar a rota do ticket de suporte passando o identificador para que o sistema possa apresentar as informações de acordo com o identificador passado.
        */
        function abrir_ticket(id_ticket){
            window.location.href = sistema.url('/suporte.php', {'rota':'ticket_suporte', 'id_ticket':id_ticket});
        }

        /** 
         * Função responsável por pesquisar as informações do ticket de suporte.
        */
        function pesquisar_ticket(){
            sistema.request.post('/suporte.php', {'rota': 'pesquisar_ticket', 'codigo_empresa': CODIGO_EMPRESA}, function(retorno){
                let tabela = document.querySelector('#tabela_ticket tbody');

                tabela = sistema.remover_linha_tabela(tabela);

                if(retorno.status == false){
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM TICKET ENCONTRADO!', 'inner', true, 6));
                    tabela.appendChild(linha);
                }else{
                    let tickets = retorno.dados;

                    sistema.each(tickets, function(index, ticket){
                        let linha = document.createElement('tr');

                        linha.appendChild(sistema.gerar_td(['text-center'], str_pad(ticket.id_ticket_suporte, 3, '0'), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], ticket.login_usuario, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], ticket.programador_responsavel, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], retornar_data(ticket.data_abertura), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], ticket.status, 'inner'));
                        
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_ticket_suporte_'+ticket.id_ticket_suporte, 'VISUALIZAR', ['btn', 'btn-info'], function function_visualizar_ticket(){abrir_ticket(ticket.id_ticket_suporte)}), 'append'));

                        tabela.appendChild(linha);
                    });
                }
            });
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">TICKETS DE SUPORTE</h4>
                        <br/>
                        <div class="row">
                            <div class="col-3">
                                <button class="btn btn-secondary btn-lg custom-radius" onclick="abrir_ticket(0);">Abrir Ticket</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsivel">
                                    <table class="table table-hover table-striped" id="tabela_ticket">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">#</th>
                                                <th scope="col">USUÁRIO</th>
                                                <th scope="col">PROGRAMADOR</th>
                                                <th scope="col">DATA</th>
                                                <th scope="col">STATUS</th>
                                                <th scope="col">VISUALIZAR</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="6" class="text-center">NÃO FORAM ENCONTRADOS TICKETS!</td>
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
        window.onload = function(){
            pesquisar_ticket();
        }
    </script>
    <?php
    require_once 'includes/footer.php';

    exit;
});

//@note ticket_suporte
router_add('ticket_suporte', function(){
    $id_ticket = (int) ($_REQUEST['id_ticket'] ? (int) intval($_REQUEST['id_ticket'], 10) :0);

    
    if($id_ticket != 0){

    }

    require_once 'includes/head.php';
    ?>
    <script>
        const ID_TICKET = <?php echo $id_ticket; ?>;
        const NOME_USUARIO = "<?php echo NOME_USUARIO; ?>";
        const CODIGO_EMPRESA = <?php echo CODIGO_EMPRESA; ?>;
        const CODIGO_USUARIO = <?php echo CODIGO_USUARIO; ?>;

        function colocar_dados_campos(){
            document.querySelector('#id_ticket').value = ID_TICKET;
            document.querySelector('#login_usuario').value = NOME_USUARIO;
            
            if(ID_TICKET == 0){
                document.querySelector('#codigo_barras').value = "<?php echo codigo_barras(); ?>";
                document.querySelector('#status').value = "AGUARDANDO";
                document.querySelector('#data_abertura').value = "<?php echo date('d/m/Y'); ?>";
            }else{
                sistema.request.post('/suporte.php', {'rota': 'pesquisar_ticket_selecionado', 'codigo_ticket':ID_TICKET}, function(retorno){
                    
                    if(retorno.status == true){
                        let ticket_suporte = retorno.dados.ticket_suporte;
                        let mensagem = retorno.dados.mensagem_ticket;

                        let tabela = document.querySelector('#tabela_mensagem tbody')
                        tabela = sistema.remover_linha_tabela(tabela);

                        document.querySelector('#data_abertura').value = retornar_data(ticket_suporte.data_abertura);
                        document.querySelector('#data_fechamento').value = retornar_data(ticket_suporte.data_fechamento);
                        document.querySelector('#programador_responsavel').value = ticket_suporte.programador_responsavel;
                        document.querySelector('#codigo_barras').value = ticket_suporte.codigo_barras;
                        document.querySelector('#status').value = ticket_suporte.status;

                        sistema.each(mensagem, function(contador, mensagem_ticket){
                            let linha = document.createElement('tr');

                            linha.appendChild(sistema.gerar_td(['text-center'], mensagem_ticket.id_mensagem_ticket, 'inner'));
                            linha.appendChild(sistema.gerar_td(['text-center'], mensagem_ticket.login_usuario, 'inner'));
                            linha.appendChild(sistema.gerar_td(['text-center'], mensagem_ticket.mensagem, 'inner'));
                            linha.appendChild(sistema.gerar_td(['text-center'], retornar_data(mensagem_ticket.data_mensagem), 'inner'));

                            tabela.appendChild(linha);
                        });
                    }else{
                        Swal.fire({ title: "FALHA NA OPERAÇÃO!", text: "Erro durante o processo, tente mais tarde!", icon: "error" });
                    }
                });
            }
        }

        function salvar_dados(){
            let programador_responsavel = document.querySelector('#programador_responsavel').value;
            let data_abertura = document.querySelector('#data_abertura').value;
            let data_fechamento = document.querySelector('#data_fechamento').value;
            let codigo_barras = document.querySelector('#codigo_barras').value;
            let status = document.querySelector('#status').value;
            let mensagem = document.querySelector('#mensagem').value;

            sistema.request.post('/suporte.php', {'rota':'salvar_ticket_suporte', 'codigo_ticket_suporte': ID_TICKET, 'codigo_empresa': CODIGO_EMPRESA, 'codigo_usuario': CODIGO_USUARIO, 'login_usuario': NOME_USUARIO, 'programador_responsavel': programador_responsavel, 'data_abertura': data_abertura, 'data_fechamento': data_fechamento, 'codigo_barras_mensagem': codigo_barras, 'status': status, 'mensagem': mensagem, 'codigo_barras': codigo_barras}, function(retorno){
                validar_retorno(retorno, '/suporte.php');
            });
        }

        function voltar(){
            window.location.href = sistema.url('/suporte.php', {'rota': 'index'});
        }

        function limpar_campos(){
            window.location.href = sistema.url('/suporte.php', {'rota': 'ticket_suporte', 'id_ticket': 0});
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">TICKET SUPORTE</h4>
                        <br/>
                        <div class="row">
                            <div class="col-1 text-center">
                                <label class="text">Código</label>
                                <input type="text" class="form-control custom-radius text-center" id="id_ticket" sistema-mask="codigo" placeholder="ID DO TICKET" readonly="true"/>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Nome Usuário</label>
                                <input type="text" class="form-control custom-radius text-center" id="login_usuario" placeholder="USUÁRIO" readonly="true"/>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Programador responsável</label>
                                <input type="text" class="form-control custom-radius text-center" id="programador_responsavel" placeholder="PROGRAMADOR RESPONSÁVEL" readonly="true"/>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Abertura</label>
                                <input type="text" class="form-control custom-radius text-center" id="data_abertura" placeholder="DATA CADASTRO" readonly="true"/>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Fechamento</label>
                                <input type="text" class="form-control custom-radius text-center" id="data_fechamento" placeholder="DATA ENCERRAMENTO" readonly="true"/>
                            </div>
                            <div class="col-2 text">
                                <label class="text">Código Barras</label>
                                <input type="text" class="form-control custom-radius text-center" id="codigo_barras" placeholder="CÓDIGO BARRAS" readonly="true"/>
                            </div>
                            <div class="col-1 text-center">
                                <label class="text">Status</label>
                                <input type="text" class="form-control custom-radius text-center" id="status" placeholder="STATUS" readonly="true"/>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-12">
                                <label class="text">MENSAGEM</label>
                                <textarea class="form-control custom-radius botao_grande btn-lg" id="mensagem" placeholder="MENSAGEM"></textarea>
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
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsivel">
                                    <table class="table table-hover table-striped" id="tabela_mensagem">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">#</th>
                                                <th scope="col">USUÁRIO</th>
                                                <th scope="col">MENSAGEM</th>
                                                <th scope="col">DATA</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="4" class="text-center">NÃO FORAM ENCONTRADA MENSAGENS</td>
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
    <br/>
    <?php
    require_once 'includes/footer.php';
    ?>
    <script>
        window.onload = function(){
            validar_acesso_administrador('<?php echo TIPO_USUARIO; ?>');

            colocar_dados_campos();
        }
    </script>
    <?php
    exit;
});

//@audit salvar_ticket_suporte
router_add('salvar_ticket_suporte', function(){
    $objeto_ticket_suporte = new TicketSuporte();
    $objeto_mensagem_ticket = new MensagemTicketSuporte();

    $id_ticket_suporte = (int) (isset($_REQUEST['codigo_ticket_suporte']) ? (int) intval($_REQUEST['codigo_ticket_suporte'], 10):0);
    $id_empresa = (int) (isset($_REQUEST['codigo_empresa']) ? (int) intval($_REQUEST['codigo_empresa'], 10):0);
    $id_usuario = (int) (isset($_REQUEST['codigo_usuario']) ? (int) intval($_REQUEST['codigo_usuario'], 10):0);

    $login_usuario = (string) (isset($_REQUEST['login_usuario']) ? (string) $_REQUEST['login_usuario']:'');
    $programador_responsavel = (string) (isset($_REQUEST['programador_responsavel']) ? (string) $_REQUEST['programador_responsavel']:'');
    $data_abertura = (string) (isset($_REQUEST['data_abertura']) ? (string) $_REQUEST['data_abertura']:'');
    $data_fechamento = (string) (isset($_REQUEST['data_fechamento']) ? (string) $_REQUEST['data_fechamento']:'');
    $codigo_barras = (string) (isset($_REQUEST['codigo_barras']) ? (string) $_REQUEST['codigo_barras']:'');
    $status = (string) (isset($_REQUEST['status']) ? (string) $_REQUEST['status']:'');

    $mensagem = (string) (isset($_REQUEST['mensagem']) ? (string) $_REQUEST['mensagem']:'');
    $data_log = (string) (isset($_REQUEST['data_log']) ? (string) $_REQUEST['data_log']:'');
    $codigo_barras_mensagem = (string) (isset($_REQUEST['codigo_barras_mensagem']) ? (string) $_REQUEST['codigo_barras_mensagem']:'');

    $dados_ticket_suporte = (array) ['codigo_ticket_suporte' => (int) $id_ticket_suporte, 'codigo_empresa' => (int) $id_empresa, 'codigo_usuario' => (int) $id_usuario, 'login_usuario' => (string) $login_usuario, 'programador_responsavel' => (string) $programador_responsavel, 'data_abertura' => (string) $data_abertura, 'data_fechamento' => (string) $data_fechamento, 'codigo_barras' => (string) $codigo_barras, 'status' => (string) $status];
    
    $retorno_ticket_suporte = (array) $objeto_ticket_suporte->salvar_dados((array) $dados_ticket_suporte);

    if($retorno_ticket_suporte['status'] == false){
        echo json_encode((array) ['status' => (bool) false, 'icone' => (string) 'error', 'mensagem' => (string) 'Erro durante o processo de salvar o log do sistema', 'titulo' => (string) 'ERRO NO PROCESSO'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if(array_key_exists('dados', $retorno_ticket_suporte) == true){
        $id_ticket_suporte = (int) intval($retorno_ticket_suporte['dados']['id_ticket_suporte'], 10);
    }

    $dados_mensagem_ticket_suporte = (array) ['codigo_ticket_suporte' => (int) $id_ticket_suporte, 'codigo_usuario' => (int) $id_usuario, 'login_usuario' => (string) $login_usuario, 'mensagem' => (string) $mensagem, 'data_log' => (string) $data_log, 'codigo_barras' => (string) $codigo_barras_mensagem];

    $retorno_mensagem_ticket_suporte = (array) $objeto_mensagem_ticket->salvar_dados((array) $dados_mensagem_ticket_suporte);

    $retorno = (array) [];

    if($retorno_mensagem_ticket_suporte == true){
        $retorno = (array) ['status' => (bool) true, 'icone' => (string) 'success', 'mensagem' => (string) 'Sucesso ao salvar a mensagem para o suporte', 'titulo' => (string) 'SUCESSO NA OPERAÇÃO'];
    }else{
        $retorno = (array) ['status' => (bool) false, 'icone' => (string) 'error', 'mensagem' => (string) 'Aconteceu um erro inesperado durante o processo de salvar a mensagem ao suporte', 'titulo' => (string) 'ERRO DURANTE O PROCESSO'];
    }

    echo json_encode((array) $retorno, JSON_UNESCAPED_UNICODE);

    exit;
});

//@audit pesquisar_ticket
router_add('pesquisar_ticket', function(){
    $objeto_ticket = new TicketSuporte();

    $codigo_empresa = (int) (isset($_REQUEST['codigo_empresa'])? (int) intval($_REQUEST['codigo_empresa'], 10):0);

    $dados = (array) ['filtro' => (array) ['id_empresa', '===', (int) $codigo_empresa], 'ordenacao' => (array) ['data_abertura' => (bool) false], 'limite' => (int) 10];
    $retorno = (array) [];

    $status = (bool) false;

    $retorno = (array) $objeto_ticket->pesquisar_todos((array) $dados);

    if(empty($retorno) == false){
        $status = (bool) true;
    }

    echo json_encode(['status' => (bool) $status, 'dados' => (array) $retorno], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit pesquisar_ticket_selecionado
router_add('pesquisar_ticket_selecionado', function(){
    $id_ticket = (int) (isset($_REQUEST['codigo_ticket']) ? (int) intval($_REQUEST['codigo_ticket'], 10):0);
    $objeto_ticket_suporte = new TicketSuporte();
    $objeto_mensagem_ticket_suporte = new MensagemTicketSuporte();
    $status = (bool) false;

    $retorno = (array) ['ticket_suporte' => (array) [], 'mensagem_ticket' => (array) []];

    if($id_ticket != 0){
        $filtro = (array) ['filtro' => (array) ['id_ticket_suporte', '===', (int) intval($id_ticket, 10)]];

        $retorno_ticket_suporte = (array) $objeto_ticket_suporte->pesquisar($filtro);

        if(empty($retorno_ticket_suporte) == false){
            $retorno['ticket_suporte'] = (array) $retorno_ticket_suporte;

            $retorno_mensagem = (array) $objeto_mensagem_ticket_suporte->pesquisar_todos((array) ['filtro' => (array) ['id_ticket_suporte', '===', (int) intval($id_ticket, 10)], 'ordenacao' => (array) ['id_mensagem_ticket' => (bool) false], 'limite' => (int) 10]);

            if(empty($retorno_mensagem) == false){
                $retorno['mensagem_ticket'] = (array) $retorno_mensagem;

                $status = (bool) true;
            }
        }
    }

    echo json_encode((array) ['status' => (bool) $status, 'dados' => (array) $retorno], JSON_UNESCAPED_UNICODE);
    exit;
});

?>