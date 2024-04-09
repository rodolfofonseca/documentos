<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Documentos.php';

/**
 * Rota index, primeira rota do sistema, 
 */
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
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM DOCUMENTO ENCONTRADO COM OS FILTROS PASSADOS!', 'inner', true, 6));
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
            }, false);
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
                                <input type="text" class="form-control custom-radius" id="codigo_documento" sistema-mask="codigo" placeholder="Código Documento" onkeyup="pesquisar_documento();"/>
                            </div>
                            <div class="col-7 text-center">
                                <label class="text">Nome</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="nome_documento" placeholder="Nome Documento" onkeyup="pesquisar_documento();"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Código Barras</label>
                                <input type="text" class="form-control custom-radius" id="codigo_barras" sistema-mask="codigo" placeholder="Código Barras" maxlength="13" onkeyup="pesquisar_documento();"/>
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

/**
 * Rota responsável por realizar o cadastro e alteração de novos documentos no sistema.
 */
router_add('salvar_dados_documentos', function(){
    $codigo_documento = (int) (isset($_REQUEST['codigo_documento']) ? (int) intval($_REQUEST['codigo_documento'], 10):0);
    require_once 'includes/head.php';
    ?>
    <script>
        let CODIGO_DOCUMENTO = <?php echo $codigo_documento; ?>;

        function valor(parametro, sair){
            parametro.preventDefault();

            if(sair == true){
                window.location.href = sistema.url('/documentos.php', {'rota':'index'});
            }
        }

        function selecionar_informacao(tipo, valor){
            if(tipo == 'ARMARIO'){
                document.querySelector('#codigo_armario').value = valor;
                let botao_fechar = document.querySelector('#botao_fechar_modal_armario');
                botao_fechar.click();
            }else if(tipo == 'PRATELEIRA'){
                document.querySelector('#codigo_prateleira').value = valor;
                let botao_fechar = document.querySelector('#botao_fechar_modal_prateleira');
                botao_fechar.click();
            }else if(tipo == 'CAIXA'){
                document.querySelector('#codigo_caixa').value = valor;
                let botao_fechar = document.querySelector('#botao_fechar_modal_caixa');
                botao_fechar.click();
            }else if(tipo == 'ORGANIZACAO'){
                document.querySelector('#codigo_organizacao').value = valor;
                let botao_fechar = document.querySelector('#botao_fechar_modal_organizacao');
                botao_fechar.click();
            }
        }

        function pesquisar_armario(){
            let codigo_armario = sistema.int(document.querySelector('#codigo_modal_armario').value);
            let nome_armario = document.querySelector('#descricao_modal_armario').value;
            sistema.request.post('/armario.php', {'rota': 'pesquisar_armario_todos', 'codigo_armario': codigo_armario, 'nome_armario': nome_armario}, function(retorno){
                let tabela = document.querySelector('#tabela_modal_armario tbody');

                tabela = sistema.remover_linha_tabela(tabela);

                let armarios = retorno.dados;
                let tamanho_retorno = armarios.length;

                if(tamanho_retorno < 1){
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM ARMÁRIO ENCONTRADO!', 'inner', true, 3));
                    tabela.appendChild(linha);
                }else{
                    sistema.each(armarios, function(index, armario){
                        let linha = document.createElement('tr');

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.str_pad(armario.id_armario, 3, '0'), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], armario.nome_armario, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_armario_'+armario.id_armario, 'SELECIONAR', ['btn', 'btn-success'], function selecionar_armario(){selecionar_informacao('ARMARIO', armario.id_armario);}),'append'));

                        tabela.appendChild(linha);
                    });
                }
            });
        }

        function pesquisar_organizacao(){
            sistema.request.post('/organizacao.php', {'rota': 'pesquisar_todos'}, function(retorno){
                let organizacoes = retorno.dados;
                let tamanho_retorno = organizacoes.length;
                let tabela = document.querySelector('#tabela_modal_organizacao tbody');

                tabela = sistema.remover_linha_tabela(tabela);

                if(tamanho_retorno < 1){
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA ORGANIZAÇÃO ENCONTRADA!', 'inner', true, 3));
                    tabela.appendChild(linha);
                }else{
                    sistema.each(organizacoes, function(index, organizacao){
                        let linha = document.createElement('tr');

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.str_pad(organizacao.id_organizacao, 3, '0'), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], organizacao.nome_organizacao, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_organizacao_'+organizacao.id_organizacao, 'SELECIONAR', ['btn', 'btn-success'], function selecionar_organizacao(){selecionar_informacao('ORGANIZACAO', organizacao.id_organizacao);}),'append'));

                        tabela.appendChild(linha);
                    });
                }
            });
        }

        function pesquisar_prateleira(){
            let codigo_armario = sistema.int(document.querySelector('#codigo_armario').value);
            let codigo_prateleira = sistema.int(document.querySelector('#codigo_modal_prateleira').value);
            let nome_prateleira = document.querySelector('#descricao_modal_prateleira').value;

            sistema.request.post('/prateleira.php', {'rota': 'pesquisar_prateleira_todas', 'codigo_armario': codigo_armario, 'codigo_prateleira': codigo_prateleira, 'nome_prateleira': nome_prateleira}, function(retorno){
                let tabela = document.querySelector('#tabela_modal_prateleira tbody');
                let prateleiras = retorno.dados;
                let tamanho_retorno = prateleiras.length;

                tabela = sistema.remover_linha_tabela(tabela);

                if(tamanho_retorno < 1){
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA PRATELEIRA ENCONTRADO!', 'inner', true, 3));
                    tabela.appendChild(linha);
                }else{
                    sistema.each(prateleiras, function(index, prateleira){
                        let linha = document.createElement('tr');

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.str_pad(prateleira.id_prateleira, 3, '0'), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], prateleira.nome_prateleira, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_prateleira_'+prateleira.id_prateleira, 'SELECIONAR', ['btn', 'btn-success'], function selecionar_prateleira(){selecionar_informacao('PRATELEIRA', prateleira.id_prateleira);}),'append'));

                        tabela.appendChild(linha);
                    });
                }
            });
        }

        function pesquisar_caixa(){
            let codigo_prateleira = sistema.int(document.querySelector('#codigo_prateleira').value);
            let codigo_caixa = sistema.int(document.querySelector('#codigo_modal_caixa').value);
            let nome_caixa = document.querySelector('#descricao_modal_caixa').value;

            sistema.request.post('/caixa.php', {'rota': 'pesquisar_caixa_todas', 'codigo_prateleira': codigo_prateleira, 'codigo_caixa': codigo_caixa, 'nome_caixa': nome_caixa}, function(retorno){
                let tabela = document.querySelector('#tabela_modal_caixa tbody');
                tabela = sistema.remover_linha_tabela(tabela);

                let caixas = retorno.dados;
                let tamanho_retorno = caixas.length;

                if(tamanho_retorno < 1){
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA CAIXA ENCONTRADO!', 'inner', true, 3));
                    tabela.appendChild(linha);
                }else{
                    sistema.each(caixas, function(index, caixa){
                        let linha = document.createElement('tr');

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.str_pad(caixa.id_caixa, 3, '0'), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], caixa.nome_caixa, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_caixa_'+caixa.id_caixa, 'SELECIONAR', ['btn', 'btn-success'], function selecionar_caixa(){selecionar_informacao('CAIXA', caixa.id_caixa);}),'append'));

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
                        <h4 class="card-title text-center">Cadastro de Documentos</h4>
                        <br/>
                        <!-- onsubmit="valor(0); return false;" -->
                        <form method="POST" accept="documentos.php" enctype="multipart/form-data" >
                            <input type="hidden" name="login_usuario" id="login_usuario" value="<?php echo $nome_usuario; ?>"/>
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
                                <div class="col-3 text-center">
                                    <label class="text">Armário</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="text" class="form-control custom-radius text-center" id="codigo_armario" name="codigo_armario" value="0" readonly="true"/>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-info" data-toggle="modal" data-target="#modal_pesquisar_armario" onclick="valor(event, false);">Pesquisar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3 tex-center">
                                    <label class="text">Prateleira</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="text" class="form-control custom-radius text-center" id="codigo_prateleira" name="codigo_prateleira" value="0" readonly="true"/>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-info" data-toggle="modal" data-target="#modal_pesquisar_prateleira" onclick="valor(event, false);">Pesquisar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3 text-center">
                                    <label class="text">Caixa</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="text" class="form-control custom-radius text-center" id="codigo_caixa" name="codigo_caixa" value="0" readonly="true"/>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-info" data-toggle="modal" data-target="#modal_pesquisar_caixa" onclick="valor(event, false);">Pesquisar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3 text-center">
                                    <label class="text">Organização</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="text" class="form-control custom-radius text-center" id="codigo_organizacao" name="codigo_organizacao" value="0" readonly="true"/>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-info" data-toggle="modal" data-target="#modal_pesquisar_organizacao" onclick="valor(event, false);">Pesquisar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col-8 text-center">
                                    <label class="text">arquivo</label>
                                    <input type="file" class="form-control custom-radius text-center" id="arquivo" placeholder="arquivo" name="arquivo"/>
                                </div>
                                <div class="col-4 text-center">
                                    <label class="text">Tipo Alteração</label>
                                    <select class="form-control custom-radius" name="tipo_alteracao" id="tipo_alteracao">
                                        <option value="TODOS">Todos</option>
                                        <option value="INFORMACOES">Informações</option>
                                        <option value="ARQUIVOS">Arquivos</option>
                                    </select>
                                </div>
                            </div>
                            <br/> 
                            <div class="row">
                                <div class="col-3">
                                    <input type="submit" class="btn btn-info" value="Salvar Dados"/>
                                </div>
                                <div class="col-3">
                                    <button class="btn btn-secondary" onclick="valor(event, true);">Retornar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de pesquisar armários -->
    <div class="modal fade" id="modal_pesquisar_armario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pesquisa de Ármario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-3 text-center">
                            <label class="text" for="codigo_modal_armario">Código</label>
                            <input type="text" class="form-control custom-radius text-center" id="codigo_modal_armario" placeholder="Código" sistema-mask="codigo"/>
                        </div>
                        <div class="col-7 text-center">
                            <label class="text" for="descricao_modal_armario">Descrição</label>
                            <input type="text" class="form-control custom-radius" id="descricao_modal_armario" placeholder="Descrição"/>
                        </div>
                        <div class="col-2 text-center">
                            <button class="btn btn-info botao_vertical_linha" onclick="pesquisar_armario();">Pesquisar</button>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped" id="tabela_modal_armario">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="botao_fechar_modal_armario">Fechar</button>
                    <button type="button" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de pesquisar prateleiras -->
    <div class="modal fade" id="modal_pesquisar_prateleira" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pesquisa de Prateleira</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <div class="row">
                        <div class="col-3 text-center">
                            <label class="text" for="codigo_modal_prateleira">Código</label>
                            <input type="text" class="form-control custom-radius text-center" id="codigo_modal_prateleira" placeholder="Código" sistema-mask="codigo"/>
                        </div>
                        <div class="col-7 text-center">
                            <label class="text" for="descricao_modal_prateleira">Descrição</label>
                            <input type="text" class="form-control custom-radius" id="descricao_modal_prateleira" placeholder="Descrição"/>
                        </div>
                        <div class="col-2 text-center">
                            <button class="btn btn-info botao_vertical_linha" onclick="pesquisar_prateleira();">Pesquisar</button>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped" id="tabela_modal_prateleira">
                                    <thead class="bg-info text-white">
                                        <tr class="text-center">
                                            <th scope="col">#</th>
                                            <th scope="col">Nome</th>
                                            <th scope="col">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody><tr><td colspan="3" class="text-center">NENHUMA ORGANIZAÇÃO ENCONTRADA</td></tr></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="botao_fechar_modal_prateleira">Fechar</button>
                    <button type="button" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para pesquisar Caixas -->
    <div class="modal fade" id="modal_pesquisar_caixa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pesquisa de Caixa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-3 text-center">
                            <label class="text" for="codigo_modal_caixa">Código</label>
                            <input type="text" class="form-control custom-radius text-center" id="codigo_modal_caixa" placeholder="Código" sistema-mask="codigo"/>
                        </div>
                        <div class="col-7 text-center">
                            <label class="text" for="descricao_modal_caixa">Descrição</label>
                            <input type="text" class="form-control custom-radius" id="descricao_modal_caixa" placeholder="Descrição"/>
                        </div>
                        <div class="col-2 text-center">
                            <button class="btn btn-info botao_vertical_linha" onclick="pesquisar_caixa();">Pesquisar</button>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped" id="tabela_modal_caixa">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="botao_fechar_modal_caixa">Fechar</button>
                    <button type="button" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de pesquisa de organização -->
    <div class="modal fade" id="modal_pesquisar_organizacao" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pesquisa de Organização</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped" id="tabela_modal_organizacao">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="botao_fechar_modal_organizacao">Fechar</button>
                    <button type="button" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        window.onload = function(){
            pesquisar_organizacao();

            if(CODIGO_DOCUMENTO != 0){
                sistema.request.post('/documentos.php', {'rota': 'pesquisar_documentos', 'codigo_documento': CODIGO_DOCUMENTO}, function(retorno){
                    document.querySelector('#codigo_documento').value = retorno.dados.id_documento;
                    document.querySelector('#nome_documento').value = retorno.dados.nome_documento;
                    document.querySelector('#codigo_barras').value = retorno.dados.codigo_barras;
                    document.querySelector('#descricao').value = retorno.dados.descricao;
                    document.querySelector('#quantidade_downloads').value = retorno.dados.quantidade_downloads;
                    // document.querySelector('#codigo_prateleira').value = retorno.dados.id_prateleira;
                    document.querySelector('#codigo_caixa').value = retorno.dados.id_caixa;
                    document.querySelector('#codigo_organizacao').value = retorno.dados.id_organizacao;
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
    $informacoes_documento = (array) $documento_objeto->update_download($_REQUEST);
	$endereco = (string) (isset($_REQUEST['endereco']) ? (string) $_REQUEST['endereco']:'');
	$arquivo = (string) file_get_contents($endereco);
    $nome_documento = (string) 'nome_padrao';

    if(array_key_exists('nome_documento', $informacoes_documento) == true){
        $nome_documento = (string) $informacoes_documento['nome_documento'];
    }

    if(array_key_exists('id_tipo_arquivo', $informacoes_documento) == true){
        $extensao = (array) model_one('tipo_arquivo', ['id_tipo_arquivo', '===', (int) intval($informacoes_documento['id_tipo_arquivo'], 10)]);

        if(empty($extensao) == false){
            if(array_key_exists('tipo_arquivo', $extensao) == true){
                $nome_documento = (string) $nome_documento.$extensao['tipo_arquivo'];
            }
        }
    }

    header('Content-Disposition: attachment; filename="'.$nome_documento.'"');
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