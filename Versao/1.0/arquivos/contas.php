<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Contas.php';

//@note index
router_add('index', function(){
    require_once 'includes/head.php';
    ?>
    <script>
        function cadastrar_contas(codigo_conta){
            window.location.href = sistema.url('/contas.php', {'rota': 'salvar_dados_contas', 'codigo_conta': codigo_conta});
        }

        function pesquisar_contas(){
            let descricao_conta = document.querySelector('#descricao_conta').value;
            let status = document.querySelector('#status').value;

            sistema.request.post('/contas.php', {'rota': 'pesquisar_contas_todas', 'status': status, 'descricao_conta': descricao_conta}, function(retorno){
                let retorno_dados = retorno.dados;
                let tamanho_retorno = retorno_dados.length;

                let tabela = document.querySelector('#tabela_contas tbody');

                tabela = sistema.remover_linha_tabela(tabela);

                if(tamanho_retorno == 0){
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA CONTA ENCONTRADA COM O FILTRO PASSADO', 'inner', true, 6));
                    tabela.appendChild(linha);
                }else{
                    sistema.each(retorno_dados, function(index, contas){
                        let linha = document.createElement('tr');
                        linha.appendChild(sistema.gerar_td(['text-center'], contas.id_conta, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], contas.nome_conta, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], contas.descricao_conta, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.number_format(contas.valor_cadastro, 2, ',', '.'), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.date_create(contas.data_vencimento, 'dd/mm/YYYY'), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_alterar_'+contas.id_conta, 'ALTERAR', ['btn', 'btn-info'], function alterar(){cadastrar_contas(contas.id_conta);}), 'append'));
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
                        <h4 class="card-title text-center">Pesquisa de Contas</h4>
                        <div class="row">
                            <div class="col-3">
                                <button class="btn btn-secondary" onclick="cadastrar_contas(0);">Cadastro de Contas</button>
                            </div>
                        </div>
                        <br/>
                            <div class="row">
                                <div class="col-5 text-center">
                                    <label class="text">Descrição conta</label>
                                    <input type="text" class="form-control custom-radius" id="descricao_conta" placeholder="Descrição Conta"/>
                                </div>
                                <div class="col-4 text-center">
                                    <label class="text">Status</label>
                                    <select id="status" class="form-control custom-radius">
                                        <option value="ATRASADA">ATRASADA</option>
                                        <option value="AGUARDANDO">AGUARDANDO</option>
                                        <option value="PAGA">PAGA</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <button class="btn btn-info botao_vertical_linha" onclick="pesquisar_contas();">Pesquisar</button>
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
                                                    <th scope="col">Descrição</th>
                                                    <th scope="col">Valor</th>
                                                    <th scope="col">Data Vencimento</th>
                                                    <th scope="col">Ação</th>
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
            pesquisar_contas();
        }
    </script>
    <?php
    require_once 'includes/footer.php';
    exit;
});

//@note salvar_dados_contas
router_add('salvar_dados_contas', function(){
    $codigo_conta = (int) (isset($_REQUEST['codigo_conta']) ? (int) intval($_REQUEST['codigo_conta'], 10):0);
    require_once 'includes/head.php';
    ?>
    <script>
        let CODIGO_CONTA = <?php echo $codigo_conta; ?>;

        function salvar_dados(){
            let codigo_conta = sistema.int(document.querySelector('#codigo_conta').value);
            let codigo_documento_devedor = sistema.int(document.querySelector('#codigo_documento_devedor').value);
            let codigo_documento_pagamento = sistema.int(document.querySelector('#codigo_documento_pagamento').value);
            let codigo_tipo_conta = sistema.int(document.querySelector('#codigo_tipo_conta').value);
            let nome_conta = document.querySelector('#nome_conta').value;
            let status = document.querySelector('#status').value;
            let descricao_conta = document.querySelector('#descricao_conta').value;
            let data_vencimento = document.querySelector('#data_vencimento').value;
            let data_pagamento = document.querySelector('#data_pagamento').value;
            let valor_cadastro = sistema.float(document.querySelector('#valor_cadastro').value);
            let valor_pagamento = sistema.float(document.querySelector('#valor_pagamento').value);

            sistema.request.post('/contas.php', {'rota': 'salvar_dados', 'codigo_conta': codigo_conta, 'codigo_tipo_conta': codigo_tipo_conta, 'codigo_documento_devedor': codigo_documento_devedor, 'codigo_documento_pagamento': codigo_documento_pagamento, 'data_vencimento': data_vencimento, 'data_pagamento': data_pagamento, 'nome_conta': nome_conta, 'descricao_conta': descricao_conta, 'valor_cadastro': valor_cadastro, 'valor_pagamento': valor_pagamento, 'status': status}, function(retorno){
                sistema.verificar_status(retorno.status, sistema.url('/contas.php', {'rota': 'index'}));
            });
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Cadastro de Contas</h4>
                        <div class="row">
                            <div class="col-3 text-center">
                                <label class="text">Código</label>
                                <input type="text" class="form-control custom-radius text-center" id="codigo_conta" readonly="true" placeholder="Código Conta"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Doc Cad.</label>
                                <input type="text" class="form-control custom-radius" id="codigo_documento_devedor" placeholder="Doc Cad" sistema-mask="codigo"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Doc. Pag</label>
                                <input type="text" class="form-control custom-radius" id="codigo_documento_pagamento" placeholder="Doc Pag" sistema-mask="codigo"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Tipo Conta</label>
                                <select id="codigo_tipo_conta" class="form-control custom-radius">
                                    <option value="0">Selecione um Tipo</option>
                                    <option value="1">TERRENO</option>
                                    <option value="2">CARTÃO CRÉDITO</option>
                                    <option value="3">TELEFONE</option>
                                    <option value="4">EMPRÉSTIMO</option>
                                    <option value="5">ÁGUA</option>
                                    <option value="6">LUZ</option>
                                    <option value="7">ROUPAS</option>
                                    <option value="8">ELETRODOMÉSTICOS</option>
                                    <option value="9">OUTROS</option>
                                </select>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-9 text-center">
                                <label class="text">Nome Conta</label>
                                <input type="text" class="form-control custom-radius" id="nome_conta" placeholder="Nome da Conta"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Status</label>
                                <select id="status" class="form-control custom-radius">
                                    <option value="AGUARDANDO">AGUARDANDO</option>
                                    <option value="ATRASADA">ATRASADA</option>
                                    <option value="PAGA">PAGA</option>
                                </select>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-12">
                                <label class="text">Descrição</label>
                                <textarea class="form-control custom-radius" id="descricao_conta" placeholder="Descrição"></textarea>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-3 text-center">
                                <label class="text">Data Vencimento</label>
                                <input type="date" id="data_vencimento" class="form-control custom-radius"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Valor Conta</label>
                                <input type="text" class="form-control custom-radius" sistema-mask="moeda" id="valor_cadastro" placeholder="Valor Cadastro"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Data Pagamento</label>
                                <input type="date" id="data_pagamento" class="form-control custom-radius"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Valor Pagamento</label>
                                <input type="text" class="form-control custom-radius" sistema-mask="moeda" id="valor_pagamento" placeholder="Valor Pagamento"/>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-3">
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
                sistema.request.post('/contas.php', {'rota': 'pesquisar_contas', 'codigo_conta': CODIGO_CONTA}, function(retorno){
                    document.querySelector('#codigo_tipo_conta').value = retorno.dados.id_tipo_conta;
                    document.querySelector('#data_pagamento').value = sistema.date_create(retorno.dados.data_pagamento);
                    document.querySelector('#data_vencimento').value = sistema.date_create(retorno.dados.data_vencimento);
                    document.querySelector('#descricao_conta').value = retorno.dados.descricao_conta;
                    document.querySelector('#codigo_conta').value = retorno.dados.id_conta;
                    document.querySelector('#nome_conta').value = retorno.dados.nome_conta;
                    document.querySelector('#status').value = retorno.dados.status;
                    document.querySelector('#valor_cadastro').value = sistema.number_format(retorno.dados.valor_cadastro, 2, ',', '');
                    document.querySelector('#valor_pagamento').value = sistema.number_format(retorno.dados.valor_pagamento, 2, ',', '');
                    document.querySelector('#codigo_documento_devedor').value = retorno.dados.id_documento_devedor;
                    document.querySelector('#codigo_documento_pagamento').value = retorno.dados.id_documento_pagamento;
                });
            } 
        }
    </script>
    <?php
    require_once 'includes/footer.php';
    exit;
});

//@audit pesquisar_contas
router_add('pesquisar_contas', function(){
    $objeto_contas = new Contas();
    $id_conta = (int) (isset($_REQUEST['codigo_conta']) ? (int) intval($_REQUEST['codigo_conta'], 10):0);
    $filtro = (array) [];
    $dados = (array) [];
    
    if($id_conta != 0){
        array_push($filtro, ['id_conta', '===', (int) $id_conta]);
    }

    $dados['filtro'] = (array) ['and' => (array) $filtro];

    echo json_encode(['dados' => (array) $objeto_contas->pesquisar_conta($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit salvar_dados
router_add('salvar_dados', function(){
    $objeto_conta = new Contas();
    echo json_encode(['status' => (bool) $objeto_conta->salvar($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit pesquisar_contas_todas
router_add('pesquisar_contas_todas', function(){
    $objeto_contas = new Contas();
    $status = (string) (isset($_REQUEST['status']) ? (string) $_REQUEST['status']:'AGUARDANDO');
    $descricao_conta = (string) (isset($_REQUEST['descricao_conta']) ? (string) $_REQUEST['descricao_conta']:'');
    $filtro = (array) [];
    $dados = (array) [];

    if($status != ''){
        array_push($filtro, ['status', '===', (string) $status]);
    }

    array_push($filtro, ['descricao_conta', '=', (string) $descricao_conta]);

    $dados['filtro'] = (array) ['and' => (array) $filtro];
    $dados['ordenacao'] = (array) ['id_conta' => (bool) true];
    $dados['limite'] = (int) 0;
    
    echo json_encode(['dados' => (array) $objeto_contas->pesquisar_conta_todas($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});
?>