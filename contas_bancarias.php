<?php
require_once 'Classes/bancoDeDados.php';
require_once 'modelos/ContasBancarias.php';

//@note index
router_add('index', function(){
    require_once 'includes/head.php';
    ?>
    <script>
        function cadastro_conta(codigo_conta){
            window.location.href = sistema.url('/contas_bancarias.php', {'rota': 'cadastro_de_contas', 'codigo_conta': codigo_conta});
        }

        function pesquisar_conta(){
            let codigo_conta = sistema.int(document.querySelector('#codigo_conta').value);
            let nome_conta = document.querySelector('#nome_conta').value;

            sistema.request.post('/contas_bancarias.php', {'rota': 'pesquisar_todas', 'codigo_conta': codigo_conta, 'nome_conta': nome_conta}, function(retorno){
                let tabela = document.querySelector('#tabela_contas tbody');
                let tamanho_retorno = retorno.dados.length;
                
                tabela = sistema.remover_linha_tabela(tabela);

                if(tamanho_retorno == 0){
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA CONTA ENCONTRADA COM OS FILTROS PASSADOS!', 'inner', true, 3));
                    tabela.appendChild(linha);
                }else{
                    sistema.each(retorno.dados, function(index, contas){
                        let linha = document.createElement('tr');

                        linha.appendChild(sistema.gerar_td(['text-center'], contas.id_conta_bancaria, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], contas.nome_conta, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_visualizar_'+contas.id_conta_bancaria, 'VISUALIZAR', ['btn', 'btn-info'], function visualizar_conta_bancaria(){cadastro_conta(contas.id_conta_bancaria);}), 'append'));

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
                        <h4 class="card-title text-center">Pesquisa de Contas Bancárias</h4>
                        <br/>
                        <div class="row">
                            <div class="col-3">
                                <button class="btn btn-secondary" onclick="cadastro_conta(0);">Cadastro de Contas</button>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-3 text-center">
                                <label class="text" for="codigo_conta">Código</label>
                                <input type="text" class="form-control custom-radius" sistema-mask="codigo" placeholder="Código Conta" id="codigo_conta"/>
                            </div>
                            <div class="col-7 text-center">
                                <label class="text" for="nome_conta">Nome da Conta</label>
                                <input type="text" class="form-control custom-radius" id="nome_conta" placeholder="Nome da Conta"/>
                            </div>
                            <div class="col-2 text-center">
                                <button class="btn btn-info botao_vertical_linha" onclick="pesquisar_conta();">Pesquisar</button>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="tabela_contas">
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
    <?php
    require_once 'includes/footer.php';
    exit;
});

//@note cadastro_de_contas
router_add('cadastro_de_contas', function(){
    $codigo_conta = (int) (isset($_REQUEST['codigo_conta']) ? (int) intval($_REQUEST['codigo_conta'], 10):0);
    require_once 'includes/head.php';
    ?>
    <script>
        let CODIGO_CONTA = <?php echo $codigo_conta; ?>;

        function pesquisar_conta(){
            sistema.request.post('/contas_bancarias.php', {'rota': 'pesquisar', 'codigo_conta': CODIGO_CONTA}, function(retorno){
                document.querySelector('#codigo_conta').value = retorno.id_conta_bancaria;
                document.querySelector('#nome_conta').value = retorno.nome_conta;
            });
        }

        function salvar_dados(){
            let codigo_conta = sistema.int(document.querySelector('#codigo_conta').value);
            let nome_conta = document.querySelector('#nome_conta').value;

            sistema.request.post('/contas_bancarias.php', {'rota': 'salvar', 'codigo_conta': codigo_conta, 'nome_conta': nome_conta}, function(retorno){
                sistema.verificar_status(retorno.status, sistema.url('/contas_bancarias.php', {'rota': 'index'}));
            });
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Cadastro de Contas</h4>
                        <br/>
                        <div class="row">
                            <div class="col-3 text-center">
                                <label class="text" for="codigo_conta">Código Conta</label>
                                <input type="text" class="form-control custom-radius text-center" id="codigo_conta" value="0" readonly="true" placeholder="Código Conta"/>
                            </div>
                            <div class="col-9 text-center">
                                <label class="text" for="nome_conta">Nome Conta</label>
                                <input type="text" class="form-control custom-radius" id="nome_conta" placeholder="Nome da Conta"/>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-2 text-center">
                                <button class="btn btn-info" onclick="salvar_dados();">Salvar Dados</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.onload = function(){
            if(CODIGO_CONTA != 0){
                pesquisar_conta();
            }
        }
    </script>
    <?php
    require_once 'includes/footer.php';
    exit;
});

//@audit pesquisar_todas
router_add('pesquisar_todas', function(){
    $objeto_conta_bancaria = new ContasBancarias();
    $id_conta_bancaria = (int) (isset($_REQUEST['codigo_conta']) ? (int) intval($_REQUEST['codigo_conta'], 10):0);
    $nome_conta = (string) (isset($_REQUEST['nome_conta']) ? (string) strtoupper($_REQUEST['nome_conta']):'');
    $filtro = (array) [];
    $dados = (array) ['filtro' => (array) [], 'ordenacao' => (array) ['nome_conta' => (bool) true], 'limite' => (int) 0];

    array_push($filtro, ['nome_conta', '=', (string) $nome_conta]);

    if($id_conta_bancaria != 0){
        array_push($filtro, ['id_conta_bancaria', '===', (int) $id_conta_bancaria]);
    }

    if(empty($filtro) == false){
        $dados['filtro'] = (array) ['and' => (array) $filtro];
    }

    echo json_encode(['dados' => (array) $objeto_conta_bancaria->pesquisar_todos($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit pesquisar
router_add('pesquisar', function(){
    $id_conta_bancaria = (int) (isset($_REQUEST['codigo_conta']) ? (int) intval($_REQUEST['codigo_conta'], 10):0);
    $filtro = (array) [];
    $dados = (array) ['filtro' => (array) []];
    $objeto_contas_bancarias = new ContasBancarias();

    if($id_conta_bancaria != 0){
        array_push($filtro, ['id_conta_bancaria', '===', (int) $id_conta_bancaria]);
    }

    if(empty($filtro) == false){
        $dados['filtro'] = (array) ['and' => (array) $filtro];
    }

    echo json_encode($objeto_contas_bancarias->pesquisar($dados), JSON_UNESCAPED_UNICODE);
});

//@audit salvar
router_add('salvar', function(){
    $objeto_contas_bancarias = new ContasBancarias();

    echo json_encode(['status' => (bool) $objeto_contas_bancarias->salvar($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});
?>