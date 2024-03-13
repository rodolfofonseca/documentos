<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/TipoDespesa.php';
require_once 'Modelos/TiposContas.php';

//@note index
router_add('index', function(){
    require_once 'includes/head.php';
    ?>
    <script>
        function cadastro_tipo_despesa(){
            window.location.href = sistema.url('/tipo_despesa.php', {'rota': 'cadastro_tipo_despesa'});
        }

        function cadastro_tipo_conta(){
            window.location.href = sistema.url('/tipo_despesa.php', {'rota': 'cadastro_tipo_conta'});
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Cadastro de Tipo Despesa e Despesa</h4>
                        <div class="row">
                            <div class="col-3">
                                <button class="btn btn-secondary" onclick="cadastro_tipo_despesa();">Tipo de Despesa</button>
                            </div>
                            <div class="col-3">
                                <button class="btn btn-secondary" onclick="cadastro_tipo_conta();">Tipo de Contas</button>
                            </div>
                        </div>
                        <br/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    require_once 'includes/footer.php';
    exit;
});

//@note cadastro_tipo_despesa
router_add('cadastro_tipo_despesa', function(){
    require_once 'includes/head.php';
    $retorno_tipo_despesa = (array) pesquisa_tipo_despesa();
    ?>
    <script>
        /**
         * Função responsável por salvar os dados do tipo de despesa no banco de dados
         */
        function salvar_dados(){
            let codigo_despesa = sistema.int(document.querySelector('#codigo_despesa').value);
            let descricao_despesa = document.querySelector('#descricao_despesa').value;

            sistema.request.post('/tipo_despesa.php', {'rota': 'salvar_dados_tipo_despesa', 'codigo_despesa': codigo_despesa, 'descricao_tipo_despesa': descricao_despesa}, function(retorno){
                sistema.verificar_status(retorno.status, sistema.url('/tipo_despesa.php', {'rota': 'cadastro_tipo_despesa'}));
            });
        }

        /**
         * Função responsável por preenxer as informações para realizar alteração do tipo de despensa na base de dados
         */
        function preenxer_informacoes(codigo_despesa){
            sistema.request.post('/tipo_despesa.php', {'rota': 'pesquisar_despesa', 'codigo_despesa': codigo_despesa}, function(retorno){
                let dados = retorno.dados;

                document.querySelector('#codigo_despesa').value = dados.id_tipo_despesa;
                document.querySelector('#descricao_despesa').value = dados.descricao_tipo_despesa;
            });
        }
        
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Cadastro de Despesa</h4>
                        <div class="row">
                            <div class="col-3">
                                <button class="btn btn-secondary" data-toggle="modal" data-target="#modal_cadastro_despesa">Cadastro de Despesa</button>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="tabela_despesa">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">#</th>
                                                <th scope="col">Nome</th>
                                                <th scope="col">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if(empty($retorno_tipo_despesa) == false){
                                                foreach($retorno_tipo_despesa as $despesa){
                                                    echo '<tr>';
                                                    echo '<td class="text-center">'.$despesa['id_tipo_despesa'].'</td>';
                                                    echo '<td>'.$despesa['descricao_tipo_despesa'].'</td>';
                                                    echo '<td class="text-center"><button class="btn btn-info" data-toggle="modal" data-target="#modal_cadastro_despesa" onclick="preenxer_informacoes('.$despesa['id_tipo_despesa'].');">Visualizar</button></td>';
                                                    echo '</tr>';
                                                }
                                            }else{
                                                echo '<tr><td colspan="3" class="text-center">SEM DESPESAS CADASTRADAS!</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    
    <!-- MODAL COM O FORMULÁRIO DE CADASTRO DAS DESPESAS NO BANCO DE DADOS  -->
    <div class="modal fade" id="modal_cadastro_despesa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cadastro de Despesa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-3 text-center">
                            <label class="text" for="codigo_despesa">Código</label>
                            <input type="text" class="form-control custom-radius text-center" id="codigo_despesa" placeholder="Código" readonly="true"/>
                        </div>
                        <div class="col-9 text-center">
                            <label class="text" for="descricao_despesa">Descrição despesa</label>
                            <input type="text" class="form-control custom-radius" id="descricao_despesa" placeholder="Descrição Despesa"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" onclick="salvar_dados();">Salvar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL COM O FORMULÁRIO DE CADASTRO DAS DESPESAS NO BANCO DE DADOS  -->

    <?php
    require_once 'includes/footer.php';
    exit;
});

//@note cadastro_tipo_conta
router_add('cadastro_tipo_conta', function () {
    require_once('includes/head.php');
    $retorno_tipo_despesa = (array) pesquisa_tipo_despesa();
    ?>
    <script>
         /**
         * Função responsável por salvar os dados do tipo de conta na base de dados.
         */
        function salvar_dados_tipo_conta(){
            let codigo_tipo_despesa = sistema.int(document.querySelector('#codigo_tipo_despesa_conta').value);
            let codigo_tipo_conta = sistema.int(document.querySelector('#codigo_tipo_conta').value);
            let descricao_tipo_conta = document.querySelector('#descricao_tipo_conta').value;

            sistema.request.post('/tipo_despesa.php', {'rota': 'salvar_dados_tipo_conta', 'codigo_tipo_conta': codigo_tipo_conta, 'codigo_tipo_despesa': codigo_tipo_despesa, 'descricao_tipo_conta': descricao_tipo_conta}, function(retorno){
                sistema.verificar_status(retorno.status, sistema.url('/tipo_despesa.php', {'rota': 'cadastro_tipo_conta'}));
                document.querySelector('#codigo_tipo_conta').value = "";
                document.querySelector('#descricao_tipo_conta').value = "";
            });
        }

        function preenxer_informacoes_tipo_conta(codigo_tipo_conta){
            sistema.request.post('/tipo_despesa.php', {'rota': 'pesquisar_tipo_conta', 'codigo_tipo_conta': codigo_tipo_conta}, function(retorno){
                let dados = retorno.dados;

                document.querySelector('#codigo_tipo_conta').value = dados.id_tipo_conta;
                document.querySelector('#codigo_tipo_despesa_conta').value = dados.id_tipo_despesa;
                document.querySelector('#descricao_tipo_conta').value = dados.descricao_tipo_conta;
            });
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Cadastro de Tipos de contas</h4>
                        <div class="row">
                            <div class="col-3">
                                <button class="btn btn-secondary" data-toggle="modal" data-target="#modal_cadastro_tipo_conta">Cadastro Tipo Conta</button>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="tabela_tipo_conta">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">#</th>
                                                <th scope="col">Nome</th>
                                                <th scope="col">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $dados_tipo_conta['filtro'] = (array) [];
                                            $dados_tipo_conta['ordenacao'] = (array) ['descricao_tipo_conta' => (bool) true];
                                            $dados_tipo_conta['limite'] = (int) 0;

                                            $objeto_tipo_conta = new TiposContas();

                                            $retorno_tipo_conta = $objeto_tipo_conta->pesquisar_todos($dados_tipo_conta);

                                            if(empty($retorno_tipo_conta) == false){
                                                foreach($retorno_tipo_conta as $tipo_conta){
                                                    echo '<tr>';
                                                    echo '<td class="text-center">'.$tipo_conta['id_tipo_conta'].'</td>';
                                                    echo '<td>'.$tipo_conta['descricao_tipo_conta'].'</td>';
                                                    echo '<td class="text-center"><button class="btn btn-info" data-toggle="modal" data-target="#modal_cadastro_tipo_conta" onclick="preenxer_informacoes_tipo_conta('.$tipo_conta['id_tipo_conta'].');">Visualizar</button></td>';
                                                    echo '</tr>';
                                                }
                                            }else{
                                                echo '<tr><td class="text-center" colspan="3">NENHUM TIPO DE CONTA ENCONTRADO!</td></tr>';
                                            }
                                            ?>
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

    
    <!--    modal com o FORMULÁRIO de cadastro dos tipos de contas no banco de dados -->
    <div class="modal fade" id="modal_cadastro_tipo_conta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cadastro de Tipo Conta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-3 text-center">
                            <label class="text" for="codigo_tipo_conta">Código</label>
                            <input type="text" class="form-control custom-radius text-center" id="codigo_tipo_conta" placeholder="Código" readonly="true"/>
                        </div>
                        <div class="col-3 text-center">
                            <label class="text" for="codigo_tipo_despesa_conta">Tipo Despesa</label>
                            <select class="form-control custom-radius" id="codigo_tipo_despesa_conta">
                                <?php
                                if(empty($retorno_tipo_despesa) == false){
                                    foreach($retorno_tipo_despesa as $despesa){
                                        echo "<option value='".$despesa['id_tipo_despesa']."'>".$despesa['descricao_tipo_despesa']."</option>";
                                    }
                                }else{
                                    echo "<option value='0'>TIPO DESPESA NÃO ENCONTRADO</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-6 text-center">
                            <label class="text" for="descricao_tipo_conta">Descrição Tipo Conta</label>
                            <input type="text" class="form-control custom-radius" id="descricao_tipo_conta" placeholder="Descrição Tipo Conta"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" onclick="salvar_dados_tipo_conta();">Salvar</button>
                </div>
            </div>
        </div>
    </div>
    <!--   modal com o FORMULÁRIO de cadastro dos tipos de contas no banco de dados -->
    <?php
    require_once('includes/footer.php');
    exit;
});

//@audit salvar_dados_tipo_despesa
router_add('salvar_dados_tipo_despesa', function(){
    $objeto_tipo_despesa = new TipoDespesa();
    echo json_encode(['status' => (bool) $objeto_tipo_despesa->salvar($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit salvar_dados_tipo_conta
router_add('salvar_dados_tipo_conta', function () {
    $objeto_tipo_conta = new TiposContas();
    echo json_encode(['status' => (bool) $objeto_tipo_conta->salvar($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit pesquisar_despesa
router_add('pesquisar_despesa', function(){
    $objeto_tipo_despesa = new TipoDespesa();
    $id_tipo_despesa = (int) (isset($_REQUEST['codigo_despesa']) ? (int) intval($_REQUEST['codigo_despesa'], 10):0);

    echo json_encode(['dados' => (array) $objeto_tipo_despesa->pesquisar(['filtro' => (array) ['and' => (array) ['id_tipo_despesa', '===', (int) $id_tipo_despesa]]])], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit pesquisar_tipo_conta
router_add('pesquisar_tipo_conta', function () {
    $objeto_tipo_conta = new TiposContas();
    $id_tipo_conta = (int) (isset($_REQUEST['codigo_tipo_conta']) ? (int) intval($_REQUEST['codigo_tipo_conta'], 10):0);

    echo json_encode(['dados' => (array) $objeto_tipo_conta->pesquisar(['filtro' => (array) ['and' => (array) [['id_tipo_conta', '===', (int) $id_tipo_conta]]]])], JSON_UNESCAPED_UNICODE);
});

//@audit-issue pesquisa_tipo_despesa()
function pesquisa_tipo_despesa(){
    $dados['filtro'] = (array) [];
    $dados['ordenacao'] = (array) ['descricao_tipo_despesa' => (bool) true];
    $dados['limite'] = (int) 0;
    $objeto_tipo_despesa = new TipoDespesa();

    return (array) $objeto_tipo_despesa->pesquisar_todos($dados);
}
?>