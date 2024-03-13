<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Documentos.php';

//@note index
router_add('index', function(){
    require_once 'includes/head.php';
    $cadastro_documento = (string) (isset($_REQUEST['cadastro_documento']) ? (string) $_REQUEST['cadastro_documento']:'false');
    $mensagem = (string) (isset($_REQUEST['retorno']) ? (string) $_REQUEST['retorno']:'false');
    ?>
    <script>
        let CADASTRO_DOCUMENTO = "<?php echo $cadastro_documento;?>";
        let MENSAGEM = "<?php echo $mensagem ?>";
        function cadastro_documentos(codigo_documento){
            window.location.href = sistema.url('/documentos.php', {'rota': 'salvar_dados_documentos', 'codigo_documento': codigo_documento});
        }

        function baixar_documento(endereco ,codigo_documento){
            sistema.download('/documentos.php', {rota:'baixar_documento', 'endereco':endereco, 'codigo_documento': codigo_documento});
        }

        function pesquisar_documento(){
            let codigo_documento = sistema.int(document.querySelector('#codigo_documento').value);
            let nome_documento = sistema.string(document.querySelector('#nome_documento').value);
            let descricao = sistema.string(document.querySelector('#descricao').value);
            let codigo_barras = sistema.string(document.querySelector('#codigo_barras').value);

            sistema.request.post('/documentos.php', {'rota': 'pesquisar_documentos_todos', 'codigo_documento': codigo_documento, 'nome_documento': nome_documento, 'descricao': descricao, 'codigo_barras': codigo_barras}, function(retorno){
                let documentos = retorno.dados;
                let tamanho_retorno = documentos.length;
                let tabela = document.querySelector('#tabela_documentos tbody');

                tabela = sistema.remover_linha_tabela(tabela);

                if(tamanho_retorno == 0){
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM DOCUMENTO ENCONTRADO COM OS FILTROS PASSADOS!', 'inner', true, 5));
                    tabela.appendChild(linha);
                }else{
                    sistema.each(documentos, function(index, documento){
                        let linha = document.createElement('tr');

                        linha.appendChild(sistema.gerar_td(['text-center'], documento.id_documento, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], documento.nome_documento, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], documento.descricao, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], documento.quantidade_downloads, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_'+documento.id_documento, 'ALTERAR', ['btn', 'btn-info'], function alterar(){cadastro_documentos(documento.id_documento);}), 'append'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_'+documento.id_documento, 'BAIXAR', ['btn', 'btn-success'], function alterar(){baixar_documento(documento.endereco, documento.id_documento);}), 'append'));

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
                        <h4 class="card-title text-center">Pesquisa de Documentos</h4>
                        <br/>
                        <div class="row">
                            <div class="col-3">
                                <button class="btn btn-secondary" onclick="cadastro_documentos(0);">Cadastro de Documentos</button>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-2 text-center">
                                <label class="text">Código</label>
                                <input type="text" class="form-control custom-radius" id="codigo_documento" sistema-mask="codigo" placeholder="Código Documento"/>
                            </div>
                            <div class="col-7 text-center">
                                <label class="text">Nome</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="nome_documento" placeholder="Nome Documento"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Código Barras</label>
                                <input type="text" class="form-control custom-radius" id="codigo_barras" sistema-mask="codigo" placeholder="Código Barras" maxlength="13"/>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-9 text-center">
                            <label class="text">Descrição</label>
                                <input type="text" class="form-control custom-radius" id="descricao" placeholder="Descrição Documento" onkeyup="pesquisar_documento();"/>
                            </div>
                            <div class="col-3 text-center">
                                <button class="btn btn-info botao_vertical_linha" onclick="pesquisar_documento();"> Pesquisar Documento</button>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="tabela_documentos">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">#</th>
                                                <th scope="col">Nome</th>
                                                <th scope="col">Descrição</th>
                                                <th scope="col">Dow.</th>
                                                <th scope="col">Ação</th>
                                                <th scope="col">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody><tr><td colspan="5" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA PESQUISA</td></tr></tbody>
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
            if(CADASTRO_DOCUMENTO == 'true'){
                if(MENSAGEM == 'true'){
                    Swal.fire('Sucesso!', 'Operação realizada com sucesso!', 'success');
                }else{
                    Swal.fire('Erro', 'Erro durante a operação!', 'error');
                }
            }

            pesquisar_documento();
        }
    </script>
    <?php
    require_once 'includes/footer.php';
});

//@note salvar_dados_documentos
router_add('salvar_dados_documentos', function(){
    $codigo_documento = (int) (isset($_REQUEST['codigo_documento']) ? (int) intval($_REQUEST['codigo_documento'], 10):0);
    require_once 'includes/head.php';
    ?>
    <script>
        let CODIGO_DOCUMENTO = <?php echo $codigo_documento; ?>;

        function pesquisar_armario(){
            sistema.request.post('/armario.php', {'rota': 'pesquisar_armario_todos'}, function(retorno){
                let select = document.querySelector('#codigo_armario');

                select = sistema.remover_option(select);

                sistema.each(retorno.dados, function(index, armario){
                    select.appendChild(sistema.gerar_option(armario.id_armario, armario.nome_armario));
                });
            });
        }

        function pesquisar_organizacao(){
            sistema.request.post('/organizacao.php', {'rota': 'pesquisar_todos'}, function(retorno){
                let select = document.querySelector('#codigo_organizacao');

                select = sistema.remover_option(select);

                sistema.each(retorno.dados, function(index, organizacao){
                    select.appendChild(sistema.gerar_option(organizacao.id_organizacao, organizacao.nome_organizacao));
                });
            });
        }

        function pesquisar_prateleira(){
            let codigo_armario = sistema.int(document.querySelector('#codigo_armario').value);

            sistema.request.post('/prateleira.php', {'rota': 'pesquisar_prateleira_todas', 'codigo_armario': codigo_armario}, function(retorno){
                let select = document.querySelector('#codigo_prateleira');

                select = sistema.remover_option(select);

                sistema.each(retorno.dados, function(index, prateleira){
                    select.appendChild(sistema.gerar_option(prateleira.id_prateleira, prateleira.nome_prateleira));
                });
            });
        }

        function pesquisar_caixa(){
            let codigo_prateleira = sistema.int(document.querySelector('#codigo_prateleira').value);

            sistema.request.post('/caixa.php', {'rota': 'pesquisar_caixa_todas', 'codigo_prateleira': codigo_prateleira}, function(retorno){
                let select = document.querySelector('#codigo_caixa');

                select = sistema.remover_option(select);

                sistema.each(retorno.dados, function(index, caixa){
                    select.appendChild(sistema.gerar_option(caixa.id_caixa, caixa.nome_caixa));
                });
            });
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Cadastro de Documentos</h4>
                        <br/>
                        <form method="POST" accept="documentos.php" enctype="multipart/form-data">
                            <input type="hidden" name="rota" id="rota" value="salvar_dados"/> 
                            <input type="hidden" name="quantidade_downloads" id="quantidade_downloads"/> 
                            <div class="row">
                                <div class="col-2 text-center">
                                    <label class="text">Código</label>
                                    <input type="text" class="form-control custom-radius text-center" id="codigo_documento" sistema-mask="codigo" placeholder="Código" readonly="true" name="codigo_documento"/>
                                </div>
                                <div class="col-7 text-center">
                                    <label class="text">Nome</label>
                                    <input type="text" class="form-control custom-radius text-uppercase" id="nome_documento" placeholder="Nome Documento" name="nome_documento"/>
                                </div>
                                <div class="col-3 text-center">
                                    <label class="text">Código Barras</label>
                                    <input type="text" class="form-control custom-radius text-center" id="codigo_barras" sistema-mask="codigo" placeholder="Código Barras" maxlength="13" readonly="true" value="<?php echo codigo_barras(); ?>" name="codigo_barras"/>
                                </div>
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <label class="text">Descrição</label>
                                    <textarea class="form-control custom-radius" id="descricao" placeholder="Descrição Documento" name="descricao"></textarea>
                                </div>
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col-4 text-center">
                                    <label class="text">Armário</label>
                                    <select id="codigo_armario" class="form-control custom-radius" name="codigo_armario" onblur="pesquisar_prateleira();"><option value="0">Selecione uma Armário</option></select>
                                </div>
                                <div class="col-4 text-center">
                                    <label class="text">Prateleira</label>
                                    <select id="codigo_prateleira" class="form-control custom-radius" name="codigo_prateleira" onblur="pesquisar_caixa();"><option value="0">Selecione uma Prateleira</option></select>
                                </div>
                                <div class="col-4 text-center">
                                    <label class="text">Caixa</label>
                                    <select id="codigo_caixa" class="form-control custom-radius" name="codigo_caixa"><option value="0">Selecione uma Caixa</option></select>
                                </div>
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col-4 text-center">
                                    <label class="text">organização</label>
                                    <select id="codigo_organizacao" class="form-control custom-radius" name="codigo_organizacao"><option value="0">Selecione uma Organizacao</option></select>
                                </div>
                                <div class="col-8 text-center">
                                    <label class="text">arquivo</label>
                                    <input type="file" class="form-control custom-radius text-center" id="arquivo" placeholder="arquivo" name="arquivo"/>
                                </div>
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col-3">
                                    <input type="submit" class="btn btn-info" value="Salvar Dados"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.onload = function(){
            pesquisar_armario();
            pesquisar_organizacao();

            if(CODIGO_DOCUMENTO != 0){
                sistema.request.post('/documentos.php', {'rota': 'pesquisar_documentos', 'codigo_documento': CODIGO_DOCUMENTO}, function(retorno){
                    document.querySelector('#codigo_documento').value = retorno.dados.id_documento;
                    document.querySelector('#nome_documento').value = retorno.dados.nome_documento;
                    document.querySelector('#codigo_barras').value = retorno.dados.codigo_barras;
                    document.querySelector('#descricao').value = retorno.dados.descricao;
                    document.querySelector('#quantidade_downloads').value = retorno.dados.quantidade_downloads;
                    // document.querySelector('#codigo_prateleira').value = retorno.dados.id_prateleira;
                    // document.querySelector('#codigo_codigo_caixa').value = retorno.dados.id_caixa;
                    // document.querySelector('#codigo_organizacao').value = retorno.dados.id_organizacao;
                });
            }
        }
    </script>
    <?php
    require_once 'includes/footer.php';
    exit;
});

//@audit pesquisar_documentos_todos
router_add('pesquisar_documentos_todos', function(){
    $id_documento = (int) (isset($_REQUEST['codigo_documento']) ? (int) intval($_REQUEST['codigo_documento'], 10): 0);
    $nome_documento = (string) (isset($_REQUEST['nome_documento']) ? (string) strtoupper($_REQUEST['nome_documento']):'');
    $codigo_barras = (string) (isset($_REQUEST['codigo_barras']) ? (string) $_REQUEST['codigo_barras']:'');
    $descricao = (string) (isset($_REQUEST['descricao']) ? (string) $_REQUEST['descricao']:'');
    $objeto_documento = new Documentos();
    $filtro = (array) [];
    $dados = (array) ['filtro' => (array) [], 'ordenacao' => ['quantidade_downloads' => (bool) false], 'limite' => (int) 10];

    array_push($filtro, ['nome_documento', '=', (string) $nome_documento]);

    if($id_documento != 0){
        array_push($filtro, ['id_documento', '===', (int) $id_documento]);
    }

    if($codigo_barras != ''){
        array_push($filtro, ['codigo_barras', '===', (string) $codigo_barras]);
    }

    if($descricao != ''){
        array_push($filtro, ['descricao', '=', (string) $descricao]);
    }

    if(empty($filtro) == false){
        $dados['filtro'] = (array) ['and' => (array) $filtro];
    }

    echo json_encode(['dados' => (array) $objeto_documento->pesquisar_documentos_todos($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit pesquisar_documentos
router_add('pesquisar_documentos', function(){
    $objeto_documento = new Documentos();
    $id_documento = (int) (isset($_REQUEST['codigo_documento']) ? intval($_REQUEST['codigo_documento'], 10):0);

    $dados['filtro'] = (array) ['and' => [['id_documento', '===', (int) $id_documento]]];

    echo json_encode(['dados' => (array) $objeto_documento->pesquisar_documento($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit baixar_documento
router_add('baixar_documento', function(){
    $documento_objeto = new Documentos();
    $documento_objeto->update_download($_REQUEST);
	$endereco = (string) (isset($_REQUEST['endereco']) ? (string) $_REQUEST['endereco']:'');
	$arquivo = (string) file_get_contents($endereco);
    header('Content-Disposition: attachment; filename="'.$endereco.'"');
    header('Content-Type: application/octet-stream');
    header('Content-Type: application/download');
    header('Content-Length: ' . strlen($arquivo));
    echo $arquivo;
    exit;
});

//@audit salvar_documento
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(!empty($_POST)){
        if(array_key_exists('rota', $_POST)){
            if($_POST['rota'] == 'salvar_dados'){
                $objeto_documento = new Documentos();
                $retorno = (bool) $objeto_documento->salvar($_POST, $_FILES);
                if($retorno == true){
                    header('Location:documentos.php?cadastro_documento=true&retorno=true');
                }else{
                    header('Location:documentos.php?cadastro_documento=true&retorno=false');
                }
            }
        }
    }
}
?>