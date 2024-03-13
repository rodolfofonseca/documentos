<?php
require_once 'Classes/bancoDeDados.php';
require_once('Modelos/TiposContas.php');

//@note index
router_add('index', function(){
    require_once 'includes/head.php';
    ?>
    <script>
        function pesquisar_lancamento(){
            let data_lancamento = document.querySelector('#data_lancamento').value;

            sistema.request.post('/lancamentos.php', {'rota': 'pesquisar_lancamento', 'data_lancamento': data_lancamento}, function(retorno){
                let lancamentos = retorno.dados;
                let tamanho_retorno = lancamentos.length;
                let tabela = document.querySelector('#tabela_lancamentos tbody');

                tabela = sistema.remover_linha_tabela(tabela);
                
                if(tamanho_retorno == 0){
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM LANÇAMENTO ENCONTRADO NESTA DATA', 'inner', true, 7));
                    tabela.appendChild(linha);
                }else{
                    sistema.each(lancamentos, function(index, lancamento){
                        let linha = document.createElement('tr');

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.left(lancamento.id_lancamento, 4, '0'), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], lancamento.descricao, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.date_create(lancamento.data_lancamento, 'd/m/Y'), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], lancamento.conta_debito, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], lancamento.conta_credito, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.number_format(lancamento.valor, 2, ',', '.'), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_visualizar_lancamento_'+lancamento.id_lancamento, 'ALTERAR', ['btn', 'btn-info'], function alterarndo_lancamento(){alterar_lancamento(lancamento.id_lancamento);}), 'append'));
                        
                        tabela.appendChild(linha);
                    });
                }
            });
        }

        function alterar_lancamento(codigo_lancamento){
            window.location.href = sistema.url('/lancamentos.php', {'rota': 'alterar_lancamento', 'codigo_lancamento': codigo_lancamento});
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Pesquisa de Lançamentos</h4>
                        <div class="row">
                            <div class="col-3 text-center">
                                <label class="text">Data Lançamento</label>
                                <input type="date" class="form-control custom-radius" value="<?php echo date('Y-m-d'); ?>" id="data_lancamento"/>
                            </div>
                            <div class="col-2 text-center">
                                <button class="btn btn-info botao_vertical_linha" onclick="pesquisar_lancamento();">Pesquisar</button>
                            </div>
                            <div class="col-3 text-center">
                                <button class="btn btn-secondary botao_vertical_linha" onclick="alterar_lancamento(0);">Cadastrar Lançamento</button>
                            </div>
                        </div>
                        </br>
                        <br/>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="tabela_lancamentos">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">#</th>
                                                <th scope="col">Descrição</th>
                                                <th scope="col">Data</th>
                                                <th scope="col">Conta Crédito</th>
                                                <th scope="col">Conta Débito</th>
                                                <th scope="col">Valor</th>
                                                <th scope="col">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody><tr><td colspan="7" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA PESQUISA</td></tr></tbody>
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

//@note alterar_lancamaento
router_add('alterar_lancamento', function(){
    $codigo_lancamento = (int) (isset($_REQUEST['codigo_lancamento']) ? (int) intval($_REQUEST['codigo_lancamento'], 10):0);
    $conta_credito = (string) '';
    $conta_debito = (string) '';
    $valor = (float) 0;
    $data_lancamento = (string) '';
    $descricao_lancamento = (string) '';
    
    $retorno_lancamento = (array) model_one('lancamento_contabil', ['id_lancamento', '===', (int) $codigo_lancamento]);
    $retorno_contas_bancarias = (array) model_all('contas_bancarias', [], ['nome_conta' => (bool) true]);

    if(array_key_exists('descricao', $retorno_lancamento) == true){
        $descricao_lancamento = (string) $retorno_lancamento['descricao'];
    }

    if(array_key_exists('conta_credito', $retorno_lancamento) == true){
        $conta_credito = (string) $retorno_lancamento['conta_credito'];
    }

    if(array_key_exists('conta_debito', $retorno_lancamento) == true){
        $conta_debito = (string) $retorno_lancamento['conta_debito'];
    }

    if(array_key_exists('valor', $retorno_lancamento) == true){
        $valor = (float) floatval($retorno_lancamento['valor']);
    }

    if(array_key_exists('data_lancamento', $retorno_lancamento) == true){
        $data_lancamento = (string) convert_date($retorno_lancamento['data_lancamento']);
    }

    $dados_tipo_conta['filtro'] = (array) [];
    $dados_tipo_conta['ordenacao'] = (array) ['descricao_tipo_conta' => (bool) true];
    $dados_tipo_conta['limite'] = (int) 0;

    $objeto_tipo_conta = new TiposContas();

    $retorno_tipo_conta = $objeto_tipo_conta->pesquisar_todos($dados_tipo_conta);

    require_once 'includes/head.php';
    ?>
    <script>
        function salvar_dados(){
            let codigo_lancamento = sistema.int(document.querySelector('#codigo_lancamento').value);
            let conta_debito = document.querySelector('#conta_debito').value;
            let conta_credito = document.querySelector('#conta_credito').value;
            let data = document.querySelector('#data_lancamento').value;
            let valor = sistema.float(document.querySelector('#valor_lancamento').value);
            let descricao = document.querySelector('#descricao_lancamento').value;

            sistema.request.post('/lancamentos.php', {'rota': 'salvar_dados', 'codigo_lancamento': codigo_lancamento, 'conta_debito': conta_debito, 'conta_credito': conta_credito, 'data_lancamento': data, 'valor_lancamento': valor, 'descricao_lancamento': descricao}, function(retorno){
                sistema.verificar_status(retorno.status, sistema.url('/lancamentos.php', {'rota': 'index'}));
            });
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Alteração de Lançamentos</h4>
                        <br/>
                        <div class="row">
                            <div class="col-2 text-center">
                                <label class="text">Código</label>
                                <input type="text" class="form-control custom-radius text-center" id="codigo_lancamento" value="<?php echo str_pad($codigo_lancamento, 4, '0', STR_PAD_LEFT); ?>" disabled="true"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text"> Conta Débito</label>
                                <select id="conta_debito" class="form-control custom-radius">
                                    <option value="">Selecione uma Conta</option>
                                    <option value="1.0">Conta Bancária Genérica</option>
                                    <?php
                                    foreach($retorno_contas_bancarias as $contas_bancarias){
                                        echo '<option value="1.'.$contas_bancarias['id_conta_bancaria'].'">'.$contas_bancarias['nome_conta'].'</option>';
                                    }

                                    echo '<option value="2.0">Conta Genérica</option>';

                                    foreach($retorno_tipo_conta as $tipo_conta){
                                        echo '<option value = "2.'.$tipo_conta['id_tipo_conta'].'">'.$tipo_conta['descricao_tipo_conta'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text"> Conta Crédito</label>
                                <select id="conta_credito" class="form-control custom-radius">
                                    <option value="">Selecione uma Conta</option>
                                    <option value="1.0">Conta Bancária Genérica</option>
                                    <?php
                                    foreach($retorno_contas_bancarias as $contas_bancarias){
                                        echo '<option value="1.'.$contas_bancarias['id_conta_bancaria'].'">'.$contas_bancarias['nome_conta'].'</option>';
                                    }

                                    echo '<option value="2.0">Conta Genérica</option>';

                                    foreach($retorno_tipo_conta as $tipo_conta){
                                        echo '<option value = "2.'.$tipo_conta['id_tipo_conta'].'">'.$tipo_conta['descricao_tipo_conta'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Data</label>
                                <input type="date" class="form-control custom-radius" id="data_lancamento" value="<?php echo $data_lancamento; ?>"/>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Valor</label>
                                <input type="text" class="form-control custom-radius" id="valor_lancamento" value="<?php echo formatar_numero($valor, 2, ',', ''); ?>"/>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-12 text-center">
                                <label class="text">Descrição Lançamento</label>
                                <textarea class="form-control custom-radius" id="descricao_lancamento" placeholder="Descrição Lançamento"><?php echo $descricao_lancamento; ?></textarea>
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
            document.querySelector('#conta_credito').value = "<?php echo $conta_credito; ?>";
            document.querySelector('#conta_debito').value = "<?php echo $conta_debito; ?>";
        }
    </script>
    <?php
    require_once 'includes/footer.php';
    exit;
});

//@audit pesquisar_lancamento
router_add('pesquisar_lancamento', function(){
    $data_lancamento = (string) (isset($_REQUEST['data_lancamento']) ? (string) $_REQUEST['data_lancamento']: date('Y-m-d'));
    $retorno_lancamento = (array) model_all('lancamento_contabil', ['and' => [['data_lancamento', '>=', model_date($data_lancamento, '00:00:00')], ['data_lancamento', '<=', model_date($data_lancamento, '23:59:59')]]]);

    echo json_encode(['dados' => (array) $retorno_lancamento], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit salvar_dados
router_add('salvar_dados', function(){
    $id_lancamento = (int) (isset($_REQUEST['codigo_lancamento']) ? (int) intval($_REQUEST['codigo_lancamento'], 10):0);
    $conta_debito = (string) (isset($_REQUEST['conta_debito']) ? (string) $_REQUEST['conta_debito']:'');
    $conta_credito = (string) (isset($_REQUEST['conta_credito']) ? (string) $_REQUEST['conta_credito']:'');
    $descricao = (string) (isset($_REQUEST['descricao_lancamento']) ? (string) $_REQUEST['descricao_lancamento']:'');
    $data_lancamento = (isset($_REQUEST['data_lancamento']) ? model_date($_REQUEST['data_lancamento']): model_date());
    $valor = (float) (isset($_REQUEST['valor_lancamento']) ? (float) floatval(formatar_numero($_REQUEST['valor_lancamento'], 2, '.', '')):0);
    $retorno = (bool) false;
    
    if($id_lancamento == 0){
        $retorno = (bool) model_insert('lancamento_contabil', ['id_lancamento' => (int) model_next('lancamento_contabil', 'id_lancamento'), 'conta_debito' => (string) $conta_debito, 'conta_credito' => (string) $conta_credito, 'descricao' => (string) $descricao, 'data_lancamento' => $data_lancamento, 'valor' => (float) $valor]);
    }else{
        $retorno = (bool) model_update('lancamento_contabil', ['id_lancamento', '===', (int) $id_lancamento], ['id_lancamento' => (int) $id_lancamento, 'conta_debito' => (string) $conta_debito, 'conta_credito' => (string) $conta_credito, 'descricao' => (string) $descricao, 'data_lancamento' => $data_lancamento, 'valor' => (float) $valor]);
    }    

    echo json_encode(['status' => (bool) $retorno], JSON_UNESCAPED_UNICODE);
    exit;
});
?>