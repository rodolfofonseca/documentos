<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Documentos.php';
require_once 'Modelos/Organizacao.php';
require_once 'Modelos/Armario.php';
require_once 'Modelos/Caixa.php';
require_once 'Modelos/Prateleira.php';
require_once 'Modelos/Preferencia.php';

/**
 * Rota index, primeira rota do sistema,
 */
router_add('index', function () {
    require_once 'includes/head.php';
    $objeto_preferencia = new Preferencia();

    $usuario_preferencia_nome_documento = (string) 'CHECKED';
    $usuario_preferencia_quantidade_documentos = (int) intval(25, 10);

    $cadastro_documento = (string) (isset($_REQUEST['cadastro_documento']) ? (string) $_REQUEST['cadastro_documento'] : 'false');
    $mensagem = (string) (isset($_REQUEST['retorno']) ? (string) $_REQUEST['retorno'] : 'false');

    //MONTANDO O FILTRO E PESQUISANDO PARA SABER SE O USUÁRIO QUE ESTÁ LOGADO PREFERE VER O NOME COMPLETO DOS DOCUMENTOS OU NÃO.
    $filtro_pesquisa = (array) ['and' => (array) [['id_sistema', '===', (int) intval($_SESSION['id_sistema'], 10)], ['id_usuario', '===', (int) intval($_SESSION['id_usuario'], 10)], ['nome_preferencia', '===', (string) 'NOME_COMPLETO_DOCUMENTO']]];
    $retorno_pesquisa_preferencia = (array) $objeto_preferencia->pesquisar((array) ['filtro' => (array) $filtro_pesquisa]);

    if (empty($retorno_pesquisa_preferencia) == true) {
        $usuario_preferencia_nome_documento = (string) '';
    }

    //MONTANDO O FILTRO E PESQUISANDO PARA SABER A QUANTIDADE DE DOCUMENTOS QUE O USUÁRIO QUE ESTÁ LOGADO NO SISTEMA PREFERE VER.
    $filtro_pesquisa = (array) ['filtro' => (array) ['and' => (array) [['id_sistema', '===', (int) intval($_SESSION['id_sistema'], 10)], ['id_usuario', '===', (int) intval($_SESSION['id_usuario'], 10)], ['nome_preferencia', '===', (string) 'QUANTIDADE_LIMITE_DOCUMENTO']]]];
    $retorno_pesquisa_preferencia = (array) $objeto_preferencia->pesquisar((array)$filtro_pesquisa);

    if(empty($retorno_pesquisa_preferencia) == true){
        $retorno_salvar_dados = (bool) $objeto_preferencia->salvar_dados((array) ['codigo_usuario' => (int) intval($_SESSION['id_usuario'], 10), 'codigo_sistema' => (int) intval($_SESSION['id_sistema'], 10), 'nome_preferencia' => (string) 'QUANTIDADE_LIMITE_DOCUMENTO', 'preferencia' => (string) $usuario_preferencia_quantidade_documentos]);
    }else{
        $usuario_preferencia_quantidade_documentos = (int) intval($retorno_pesquisa_preferencia['preferencia'], 10);
    }


    ?>
    <script>
        let CADASTRO_DOCUMENTO = "<?php echo $cadastro_documento; ?>";
        let MENSAGEM = "<?php echo $mensagem ?>";
        const CODIGO_EMPRESA = <?php echo $_SESSION['id_empresa']; ?>;
        const CODIGO_SISTEMA = <?php echo $_SESSION['id_sistema'] ?>;
        const CODIGO_USUARIO = <?php echo $_SESSION['id_usuario'] ?>;
        const PREFERENCIA_QUANTIDADE_DOCUMENTOS = <?php echo $usuario_preferencia_quantidade_documentos;  ?>;

        function cadastro_documentos(codigo_documento) {
            window.location.href = sistema.url('/documentos.php', {
                'rota': 'salvar_dados_documentos',
                'codigo_documento': codigo_documento
            });
        }

        function baixar_documento(endereco, codigo_documento) {
            sistema.download('/documentos.php', {
                rota: 'baixar_documento',
                'endereco': endereco,
                'codigo_documento': codigo_documento
            });
        }

        /**
         * Funçao responsável por realizar a pergunta se o usuário desejar realmente deletar o documento do banco de dados, caso o mesmo responda que sim, o sistema realiza a comunicação com a rota, deletando o documento, caso a resposta seja que não, o sistema não faz nada.
         * @param integer codigo_documento.
         */
        function excluir_documentos(codigo_documento) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger"
                },
                buttonsStyling: false
            });
            swalWithBootstrapButtons.fire({
                title: "Você tem certeza?",
                text: "A operação de exclusão é irreversível! \n Os arquivos na pasta serão excluídos permanentemente e nenhuma cópia será mantida.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sim, deletar documento!",
                cancelButtonText: "Não, cancelar!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    sistema.request.post('/documentos.php', {
                        'rota': 'excluir_documento',
                        'codigo_documento': codigo_documento
                    }, function (retorno) { }, false);


                    swalWithBootstrapButtons.fire({
                        title: "Deletado!",
                        text: "O documento foi excluído com sucesso.",
                        icon: "success"
                    });
                } else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire({
                        title: "Cancelado",
                        text: "Não foi deletado nenhum documento do sistema :)",
                        icon: "error"
                    });
                }

                pesquisar_documento();
            });
        }

        function pesquisar_documento() {
            let codigo_documento = sistema.int(document.querySelector('#codigo_documento').value);
            let nome_documento = sistema.string(document.querySelector('#nome_documento').value);
            let descricao = sistema.string(document.querySelector('#descricao').value);
            let codigo_barras = sistema.string(document.querySelector('#codigo_barras').value);
            let limite_retorno = sistema.int(document.querySelector('#limite_retorno').value);

            let flexSwitchCheckChecked = document.querySelector('#flexSwitchCheckChecked');

            sistema.request.post('/documentos.php', {
                'rota': 'pesquisar_documentos_todos',
                'codigo_documento': codigo_documento,
                'codigo_empresa': CODIGO_EMPRESA,
                'codigo_sistema':CODIGO_SISTEMA,
                'codigo_usuario':CODIGO_USUARIO,
                'nome_documento': nome_documento,
                'descricao': descricao,
                'codigo_barras': codigo_barras,
                'limite_retorno':limite_retorno,
                'preferencia_usuario_retorno':PREFERENCIA_QUANTIDADE_DOCUMENTOS
            }, function (retorno) {
                let documentos = retorno.dados;
                let tamanho_retorno = documentos.length;
                let tabela = document.querySelector('#tabela_documentos tbody');

                tabela = sistema.remover_linha_tabela(tabela);

                if (tamanho_retorno == 0) {
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM DOCUMENTO ENCONTRADO COM OS FILTROS PASSADOS!', 'inner', true, 11));
                    tabela.appendChild(linha);
                } else {
                    sistema.each(documentos, function (index, documento) {
                        let linha = document.createElement('tr');

                        if (flexSwitchCheckChecked.checked == true) {
                            linha.appendChild(sistema.gerar_td(['text-left'], documento.nome_documento, 'inner'));
                            linha.appendChild(sistema.gerar_td(['text-left'], documento.descricao, 'inner'));
                        } else {
                            linha.appendChild(sistema.gerar_td(['text-left'], documento.nome_documento.substring(0, 15), 'inner'));
                            linha.appendChild(sistema.gerar_td(['text-left'], documento.descricao.substring(0, 40), 'inner'));
                        }

                        linha.appendChild(sistema.gerar_td(['text-left'], documento.nome_armario, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], documento.nome_prateleira, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], documento.nome_caixa, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], documento.quantidade_downloads, 'inner'));


                        if (documento.tipo == 'PUBLICO') {
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_tipo_documento_' + documento.codigo_barras, 'PÚBLICO', ['btn', 'btn-info'], function tipo_documento_tipo() { }), 'append'));

                        } else {
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_tipo_documento_' + documento.codigo_barras, 'PRIVADO', ['btn', 'btn-secondary'], function tipo_documento_tipo() { }), 'append'));
                        }

                        linha.appendChild(sistema.gerar_td(['text-left'], sistema.gerar_botao('botao_baixar_codigo_barras' + documento.codigo_barras, documento.codigo_barras, ['btn', 'btn-warning'], function codigo_barra_baixar() {
                            baixar_codigo_barras(documento.codigo_barras);
                        }), 'append'));

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_' + documento.id_documento, 'EXCLUIR', ['btn', 'btn-danger'], function alterar() {
                            excluir_documentos(documento.id_documento);
                        }), 'append'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_' + documento.id_documento, 'ALTERAR', ['btn', 'btn-info'], function alterar() {
                            cadastro_documentos(documento.id_documento);
                        }), 'append'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_' + documento.id_documento, 'BAIXAR', ['btn', 'btn-success'], function alterar() {
                            baixar_documento(documento.endereco, documento.id_documento);
                        }), 'append'));

                        tabela.appendChild(linha);
                    });
                }
            }, false);
        }

        /**
         * Função responsável por abrir o modal para a impressão do código de barras do documento.
         */
        function baixar_codigo_barras(codigo_barras) {
            window.open(sistema.url('/documentos.php', {
                'rota': 'imprimir_codigo_barra_documento',
                'codigo_barra': codigo_barras
            }), 'Impressão de código de barras', 'width=740px, height=500px, scrollbars=yes');
        }

        function cadastrar_preferencia_usuario() {
            let objeto_check = document.querySelector('#flexSwitchCheckChecked');
            let preferencia = '';

            if (objeto_check.checked == true) {
                preferencia = 'CHECKED';
            } else {
                preferencia = '';
            }

            sistema.request.post('/documentos.php', { 'rota': 'salvar_preferencia_usuario_documento', 'codigo_sistema': CODIGO_SISTEMA, 'codigo_usuario': CODIGO_USUARIO, 'nome_preferencia': 'NOME_COMPLETO_DOCUMENTO', 'preferencia': preferencia }, function (retorno) {
            }, false);
        }

        function colocar_preferencia_quantidade_documentos(){
            let objeto_limite_retorno =document.querySelector('#limite_retorno');
            objeto_limite_retorno.value = PREFERENCIA_QUANTIDADE_DOCUMENTOS;
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Pesquisa de Documentos</h4>
                        <br />
                        <div class="row">
                            <div class="col-3">
                                <button class="btn btn-secondary btn-lg botao_grande custom-radius"
                                    onclick="cadastro_documentos(0);">Cadastro de Documentos</button>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-1 text-center">
                                <input type="text" class="form-control custom-radius" id="codigo_documento"
                                    sistema-mask="codigo" placeholder="Código" onkeyup="pesquisar_documento();"/>
                            </div>
                            <div class="col-7 text-center">
                                <input type="text" class="form-control custom-radius text-uppercase" id="nome_documento" placeholder="Nome Documento" onkeyup="pesquisar_documento();"/>
                            </div>
                            <div class="col-2 text-center">
                                <input type="text" class="form-control custom-radius" id="codigo_barras" sistema-mask="codigo" placeholder="Código Barras" maxlength="13" onkeyup="pesquisar_documento();"/>
                            </div>
                            <div class="col-2 text-center">
                                <select class="form-control custom-radius" id="forma_visualizacao">
                                    <option value="TODOS">TODOS</option>
                                    <option value="PUBLICO">PÚBLICO</option>
                                    <option value="PRIVADO">PRIVADO</option>
                                </select>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-12 text-center">
                                <input type="text" class="form-control custom-radius" id="descricao"
                                placeholder="Descrição Documento" onkeyup="pesquisar_documento();"/>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-2 text-center">
                                <select class="form-control custom-radius" id="limite_retorno">
                                    <option value="25">25 DOCUMENTOS</option>
                                    <option value="50">50 DOCUMENTOS</option>
                                    <option value="75">75 DOCUMENTOS</option>
                                    <option value="100">100 DOCUMENTOS</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="push-9 col-3 text-center">
                                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                    <button type="button" class="btn btn-info custom-radius botao_grande btn-lg"
                                        onclick="pesquisar_documento();">PESQUISAR
                                        DOCUMENTO</button>

                                    <div class="btn-group" role="group">
                                        <button type="button"
                                            class="btn btn-secondary dropdown-toggle custom-radius botao_grande btn-lg"
                                            data-toggle="dropdown" aria-expanded="false">
                                            PREFERÊNCIAS
                                        </button>
                                        <div class="dropdown-menu">
                                            <div class="dropdown-item">
                                                <input class="form-check-input" type="checkbox"
                                                    role="switch" id="flexSwitchCheckChecked" <?php echo $usuario_preferencia_nome_documento; ?> onclick="cadastrar_preferencia_usuario();" />
                                                <label class="form-check-label" for="flexSwitchCheckChecked">Ver nome/descrição completos</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="tabela_documentos">
                                <thead class="bg-info text-white">
                                    <tr class="text-center">
                                        <th scope="col">Nome</th>
                                        <th scope="col">Descrição</th>
                                        <th scope="col">Armário</th>
                                        <th scope="col">Prateleira</th>
                                        <th scope="col">Caixa</th>
                                        <th scope="col">Dow.</th>
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Cód.</th>
                                        <th scope="col">excluir</th>
                                        <th scope="col">Alterar</th>
                                        <th scope="col">Baixar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="11" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA
                                            PESQUISA</td>
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
        window.onload = function () {
            if (CADASTRO_DOCUMENTO == 'true') {
                if (MENSAGEM == 'true') {
                    Swal.fire('Sucesso!', 'Operação realizada com sucesso!', 'success');

                    setTimeout(pesquisar_documento(), 5000);
                } else {
                    Swal.fire('Erro', 'Erro durante a operação!', 'error');
                }
            } else {
                colocar_preferencia_quantidade_documentos();
                pesquisar_documento();
            }
        }
    </script>
    <?php
    require_once 'includes/footer.php';
});

/**
 * Rota responsável por realizar o cadastro e alteração de novos documentos no sistema.
 */
router_add('salvar_dados_documentos', function () {
    require_once 'includes/head.php';

    $codigo_documento = (int) (isset($_REQUEST['codigo_documento']) ? (int) intval($_REQUEST['codigo_documento'], 10) : 0);
    $codigo_empresa = (int) 0;
    $codigo_usuario = (int) 0;

    if (isset($_SESSION['id_empresa'])) {
        $codigo_empresa = (int) intval($_SESSION['id_empresa'], 10);
    }

    if (isset($_SESSION['id_usuario'])) {
        $codigo_usuario = (int) intval($_SESSION['id_usuario'], 10);
    }

    ?>
    <script>
        let CODIGO_DOCUMENTO = <?php echo $codigo_documento; ?>;
        let CODIGO_USUARIO = <?php echo $codigo_usuario; ?>;
        let CODIGO_EMPRESA = <?php echo $codigo_empresa; ?>;

        function valor(parametro, sair) {
            parametro.preventDefault();

            if (sair == true) {
                window.location.href = sistema.url('/documentos.php', {
                    'rota': 'index'
                });
            }
        }

        function selecionar_informacao(tipo, valor) {
            if (tipo == 'ARMARIO') {
                let codigo_organizacao = sistema.int(document.querySelector('#codigo_organizacao').value);

                if (codigo_organizacao == 0) {
                    Swal.fire({
                        title: "Erro de validação",
                        text: "É necessário primeiro selecionar uma organização",
                        icon: "error"
                    });
                } else {
                    document.querySelector('#codigo_armario').value = valor;
                    let botao_fechar = document.querySelector('#botao_fechar_modal_armario');
                    botao_fechar.click();
                }

            } else if (tipo == 'PRATELEIRA') {
                let codigo_armario = sistema.int(document.querySelector('#codigo_armario').value);

                if (codigo_armario == 0) {
                    Swal.fire({
                        title: "Erro de validação",
                        text: "É necessário primeiro selecionar um armário",
                        icon: "error"
                    });
                    botao_fechar.click();
                } else {
                    document.querySelector('#codigo_prateleira').value = valor;
                    let botao_fechar = document.querySelector('#botao_fechar_modal_prateleira');
                    botao_fechar.click();
                }


            } else if (tipo == 'CAIXA') {
                let codigo_prateleira = sistema.int(document.querySelector('#codigo_prateleira').value);

                if (codigo_prateleira == 0) {
                    Swal.fire({
                        title: "Erro de validação",
                        text: "É necessário primeiro selecionar uma prateleira",
                        icon: "error"
                    });
                } else {
                    document.querySelector('#codigo_caixa').value = valor;
                    let botao_fechar = document.querySelector('#botao_fechar_modal_caixa');
                    botao_fechar.click();
                }

            } else if (tipo == 'ORGANIZACAO') {
                document.querySelector('#codigo_organizacao').value = valor;
                let botao_fechar = document.querySelector('#botao_fechar_modal_organizacao');
                botao_fechar.click();
            }
        }

        function pesquisar_armario() {
            let codigo_armario = sistema.int(document.querySelector('#codigo_modal_armario').value);
            let nome_armario = document.querySelector('#descricao_modal_armario').value;
            sistema.request.post('/armario.php', {
                'rota': 'pesquisar_armario_todos',
                'codigo_armario': codigo_armario,
                'nome_armario': nome_armario,
                'codigo_usuario': CODIGO_USUARIO,
                'codigo_empresa': CODIGO_EMPRESA
            }, function (retorno) {
                let tabela = document.querySelector('#tabela_modal_armario tbody');

                tabela = sistema.remover_linha_tabela(tabela);

                let armarios = retorno.dados;
                let tamanho_retorno = armarios.length;

                if (tamanho_retorno < 1) {
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM ARMÁRIO ENCONTRADO!', 'inner', true, 3));
                    tabela.appendChild(linha);
                } else {
                    sistema.each(armarios, function (index, armario) {
                        let linha = document.createElement('tr');

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.str_pad(armario.id_armario, 3, '0'), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], armario.nome_armario, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_armario_' + armario.id_armario, 'SELECIONAR', ['btn', 'btn-success'], function selecionar_armario() {
                            selecionar_informacao('ARMARIO', armario.id_armario);
                        }), 'append'));

                        tabela.appendChild(linha);
                    });
                }
            }, false);
        }

        function pesquisar_organizacao() {
            sistema.request.post('/organizacao.php', {
                'rota': 'pesquisar_todos',
                'codigo_usuario': CODIGO_USUARIO,
                'codigo_empresa': CODIGO_EMPRESA
            }, function (retorno) {
                let organizacoes = retorno.dados;
                let tamanho_retorno = organizacoes.length;
                let tabela = document.querySelector('#tabela_modal_organizacao tbody');

                tabela = sistema.remover_linha_tabela(tabela);

                if (tamanho_retorno < 1) {
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA ORGANIZAÇÃO ENCONTRADA!', 'inner', true, 3));
                    tabela.appendChild(linha);
                } else {
                    sistema.each(organizacoes, function (index, organizacao) {
                        let linha = document.createElement('tr');

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.str_pad(organizacao.id_organizacao, 3, '0'), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], organizacao.nome_organizacao, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_organizacao_' + organizacao.id_organizacao, 'SELECIONAR', ['btn', 'btn-success'], function selecionar_organizacao() {
                            selecionar_informacao('ORGANIZACAO', organizacao.id_organizacao);
                        }), 'append'));

                        tabela.appendChild(linha);
                    });
                }
            }, false);
        }

        function pesquisar_prateleira() {
            let codigo_armario = sistema.int(document.querySelector('#codigo_armario').value);
            let codigo_prateleira = sistema.int(document.querySelector('#codigo_modal_prateleira').value);
            let nome_prateleira = document.querySelector('#descricao_modal_prateleira').value;

            sistema.request.post('/prateleira.php', {
                'rota': 'pesquisar_prateleira_todas',
                'codigo_armario': codigo_armario,
                'codigo_prateleira': codigo_prateleira,
                'nome_prateleira': nome_prateleira,
                'codigo_usuario': CODIGO_USUARIO,
                'codigo_empresa': CODIGO_EMPRESA
            }, function (retorno) {
                let tabela = document.querySelector('#tabela_modal_prateleira tbody');
                let prateleiras = retorno.dados;
                let tamanho_retorno = prateleiras.length;

                tabela = sistema.remover_linha_tabela(tabela);

                if (tamanho_retorno < 1) {
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA PRATELEIRA ENCONTRADO!', 'inner', true, 3));
                    tabela.appendChild(linha);
                } else {
                    sistema.each(prateleiras, function (index, prateleira) {
                        let linha = document.createElement('tr');

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.str_pad(prateleira.id_prateleira, 3, '0'), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], prateleira.nome_prateleira, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_prateleira_' + prateleira.id_prateleira, 'SELECIONAR', ['btn', 'btn-success'], function selecionar_prateleira() {
                            selecionar_informacao('PRATELEIRA', prateleira.id_prateleira);
                        }), 'append'));

                        tabela.appendChild(linha);
                    });
                }
            }, false);
        }

        function pesquisar_caixa() {
            let codigo_prateleira = sistema.int(document.querySelector('#codigo_prateleira').value);
            let codigo_caixa = sistema.int(document.querySelector('#codigo_modal_caixa').value);
            let nome_caixa = document.querySelector('#descricao_modal_caixa').value;

            sistema.request.post('/caixa.php', {
                'rota': 'pesquisar_caixa_todas',
                'codigo_prateleira': codigo_prateleira,
                'codigo_caixa': codigo_caixa,
                'nome_caixa': nome_caixa,
                'codigo_usuario': CODIGO_USUARIO,
                'codigo_empresa': CODIGO_EMPRESA
            }, function (retorno) {
                let tabela = document.querySelector('#tabela_modal_caixa tbody');
                tabela = sistema.remover_linha_tabela(tabela);

                let caixas = retorno.dados;
                let tamanho_retorno = caixas.length;

                if (tamanho_retorno < 1) {
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA CAIXA ENCONTRADO!', 'inner', true, 3));
                    tabela.appendChild(linha);
                } else {
                    sistema.each(caixas, function (index, caixa) {
                        let linha = document.createElement('tr');

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.str_pad(caixa.id_caixa, 3, '0'), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], caixa.nome_caixa, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_caixa_' + caixa.id_caixa, 'SELECIONAR', ['btn', 'btn-success'], function selecionar_caixa() {
                            selecionar_informacao('CAIXA', caixa.id_caixa);
                        }), 'append'));

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
                        <h4 class="card-title text-center">Cadastro de Documentos</h4>
                        <br />
                        <!-- onsubmit="valor(0); return false;" -->
                        <form method="POST" accept="documentos.php" enctype="multipart/form-data">
                            <input type="hidden" name="login_usuario" id="login_usuario"
                                value="<?php echo $nome_usuario; ?>" />
                            <input type="hidden" name="rota" id="rota" value="salvar_dados" />
                            <input type="hidden" name="codigo_empresa" id="codigo_empresa"
                                value="<?php echo $codigo_empresa; ?>" />
                            <input type="hidden" name="codigo_usuario" id="codigo_usuario"
                                value="<?php echo $codigo_usuario; ?>" />
                            <input type="hidden" name="quantidade_downloads" id="quantidade_downloads" />
                            <div class="row">
                                <div class="col-1 text-center">
                                    <label class="text">Código</label>
                                    <input type="text" class="form-control custom-radius text-center" id="codigo_documento"
                                        sistema-mask="codigo" placeholder="Código" readonly="true"
                                        name="codigo_documento" />
                                </div>
                                <div class="col-7 text-center">
                                    <label class="text">Nome</label>
                                    <input type="text" class="form-control custom-radius text-uppercase" id="nome_documento"
                                        placeholder="Nome Documento" name="nome_documento" />
                                </div>
                                <div class="col-2 text-center">
                                    <label class="text">Forma Visulização</label>
                                    <select class="form-control custom-radius" id="forma_visualizacao"
                                        name="forma_visualizacao">
                                        <option value="">Selecione uma opção</option>
                                        <option value="PUBLICO">PÚBLICO</option>
                                        <option value="PRIVADO">PRIVADO</option>
                                    </select>
                                </div>
                                <div class="col-2 text-center">
                                    <label class="text">Código Barras</label>
                                    <input type="text" class="form-control custom-radius text-center " id="codigo_barras"
                                        sistema-mask="codigo" placeholder="Código Barras" maxlength="13" readonly="true"
                                        value="<?php echo codigo_barras(); ?>" name="codigo_barras" />
                                </div>
                            </div>
                            <br />
                            <div class="row">
                                <div class="col-12 text-center">
                                    <label class="text">Descrição</label>
                                    <textarea class="form-control custom-radius  custom-radius botao_grande btn-lg"
                                        id="descricao" placeholder="Descrição Documento" name="descricao"></textarea>
                                </div>
                            </div>
                            <br />
                            <div class="row">
                                <div class="col-3 text-center">
                                    <label class="text">Organização</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="text" class="form-control custom-radius text-center"
                                                id="codigo_organizacao" name="codigo_organizacao" value="0"
                                                readonly="true" />
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-info  custom-radius botao_grande btn-lg"
                                                data-toggle="modal" data-target="#modal_pesquisar_organizacao"
                                                onclick="valor(event, false);">Pesquisar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3 text-center">
                                    <label class="text">Armário</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="text" class="form-control custom-radius text-center"
                                                id="codigo_armario" name="codigo_armario" value="0" readonly="true" />
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-info  custom-radius botao_grande btn-lg"
                                                data-toggle="modal" data-target="#modal_pesquisar_armario"
                                                onclick="valor(event, false);">Pesquisar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3 tex-center">
                                    <label class="text">Prateleira</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="text" class="form-control custom-radius text-center"
                                                id="codigo_prateleira" name="codigo_prateleira" value="0" readonly="true" />
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-info  custom-radius botao_grande btn-lg"
                                                data-toggle="modal" data-target="#modal_pesquisar_prateleira"
                                                onclick="valor(event, false);">Pesquisar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3 text-center">
                                    <label class="text">Caixa</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="text" class="form-control custom-radius text-center"
                                                id="codigo_caixa" name="codigo_caixa" value="0" readonly="true" />
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-info  custom-radius botao_grande btn-lg"
                                                data-toggle="modal" data-target="#modal_pesquisar_caixa"
                                                onclick="valor(event, false);">Pesquisar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br />
                            <div class="row">
                                <div class="col-8 text-center">
                                    <label class="text">arquivo</label>
                                    <input type="file" class="form-control custom-radius text-center" id="arquivo"
                                        placeholder="arquivo" name="arquivo" />
                                </div>
                                <div class="col-4 text-center">
                                    <label class="text">Tipo Alteração</label>
                                    <select class="form-control custom-radius" name="tipo_alteracao" id="tipo_alteracao">
                                        <option value="TODOS">TODOS</option>
                                        <option value="INFORMACOES">INFORMAÇÕES</option>
                                        <!-- <option value="ARQUIVOS">Arquivos</option> -->
                                    </select>
                                </div>
                            </div>
                            <br />
                            <div class="row">
                                <div class="col-4">
                                    <input type="submit" class="btn btn-info custom-radius btn-lg botao_grande"
                                        value="Salvar Dados" />
                                </div>
                                <div class="col-4">
                                    <input type="reset" class="btn btn-danger custom-radius btn-lg botao_grande" />
                                </div>
                                <div class="col-4">
                                    <button class="btn btn-secondary custom-radius botao_grande btn-lg"
                                        onclick="valor(event, true);">Retornar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de pesquisar armários -->
    <div class="modal fade" id="modal_pesquisar_armario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
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
                            <input type="text" class="form-control custom-radius text-center" id="codigo_modal_armario"
                                placeholder="Código" sistema-mask="codigo" />
                        </div>
                        <div class="col-7 text-center">
                            <label class="text" for="descricao_modal_armario">Descrição</label>
                            <input type="text" class="form-control custom-radius" id="descricao_modal_armario"
                                placeholder="Descrição" />
                        </div>
                        <div class="col-2 text-center">
                            <button class="btn btn-info botao_vertical_linha custom-radius"
                                onclick="pesquisar_armario();">Pesquisar</button>
                        </div>
                    </div>
                    <br />
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
                                    <tbody>
                                        <tr>
                                            <td colspan="3" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA
                                                PESQUISA</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger  custom-radius" data-dismiss="modal"
                        id="botao_fechar_modal_armario">Fechar</button>
                    <button type="button" class="btn btn-primary  custom-radius">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de pesquisar prateleiras -->
    <div class="modal fade" id="modal_pesquisar_prateleira" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
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
                            <input type="text" class="form-control custom-radius text-center" id="codigo_modal_prateleira"
                                placeholder="Código" sistema-mask="codigo" />
                        </div>
                        <div class="col-7 text-center">
                            <label class="text" for="descricao_modal_prateleira">Descrição</label>
                            <input type="text" class="form-control custom-radius" id="descricao_modal_prateleira"
                                placeholder="Descrição" />
                        </div>
                        <div class="col-2 text-center">
                            <button class="btn btn-info botao_vertical_linha  custom-radius"
                                onclick="pesquisar_prateleira();">Pesquisar</button>
                        </div>
                    </div>
                    <br />
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
                                    <tbody>
                                        <tr>
                                            <td colspan="3" class="text-center">NENHUMA ORGANIZAÇÃO ENCONTRADA</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger  custom-radius" data-dismiss="modal"
                        id="botao_fechar_modal_prateleira">Fechar</button>
                    <button type="button" class="btn btn-primary  custom-radius">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para pesquisar Caixas -->
    <div class="modal fade" id="modal_pesquisar_caixa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
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
                            <input type="text" class="form-control custom-radius text-center" id="codigo_modal_caixa"
                                placeholder="Código" sistema-mask="codigo" />
                        </div>
                        <div class="col-7 text-center">
                            <label class="text" for="descricao_modal_caixa">Descrição</label>
                            <input type="text" class="form-control custom-radius" id="descricao_modal_caixa"
                                placeholder="Descrição" />
                        </div>
                        <div class="col-2 text-center">
                            <button class="btn btn-info botao_vertical_linha  custom-radius"
                                onclick="pesquisar_caixa();">Pesquisar</button>
                        </div>
                    </div>
                    <br />
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
                                    <tbody>
                                        <tr>
                                            <td colspan="3" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA
                                                PESQUISA</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger  custom-radius" data-dismiss="modal"
                        id="botao_fechar_modal_caixa">Fechar</button>
                    <button type="button" class="btn btn-primary  custom-radius">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de pesquisa de organização -->
    <div class="modal fade" id="modal_pesquisar_organizacao" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
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
                                    <tbody>
                                        <tr>
                                            <td colspan="3" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA
                                                PESQUISA</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger  custom-radius" data-dismiss="modal"
                        id="botao_fechar_modal_organizacao">Fechar</button>
                    <button type="button" class="btn btn-primary  custom-radius">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = async function () {
            await pesquisar_organizacao();

            window.setTimeout(function () {
                if (CODIGO_DOCUMENTO != 0) {
                    sistema.request.post('/documentos.php', {
                        'rota': 'pesquisar_documentos',
                        'codigo_documento': CODIGO_DOCUMENTO
                    }, function (retorno) {
                        document.querySelector('#codigo_documento').value = retorno.dados.id_documento;
                        document.querySelector('#codigo_prateleira').value = retorno.dados.id_prateleira;
                        document.querySelector('#codigo_organizacao').value = retorno.dados.id_organizacao;
                        document.querySelector('#codigo_caixa').value = retorno.dados.id_caixa;
                        document.querySelector('#codigo_armario').value = retorno.dados.id_armario;

                        document.querySelector('#nome_documento').value = retorno.dados.nome_documento;
                        document.querySelector('#descricao').value = retorno.dados.descricao;
                        document.querySelector('#codigo_barras').value = retorno.dados.codigo_barras;
                        document.querySelector('#quantidade_downloads').value = retorno.dados.quantidade_downloads;
                        document.querySelector('#forma_visualizacao').value = retorno.dados.forma_visualizacao;
                    }), false;
                }
            }, 500);

        }
    </script>
    <?php
    require_once 'includes/footer.php';
    exit;
});

/**
 * Rota responsável por realizar a impressão do código de barras do documento.
 */
router_add('imprimir_codigo_barra_documento', function () {
    $codigo_barras = (string) (isset($_REQUEST['codigo_barra']) ? (string) $_REQUEST['codigo_barra'] : '');

    $nome_documento = (string) '';
    $nome_organizacao = (string) '';
    $nome_armario = (string) '';
    $nome_prateleira = (string) '';
    $nome_caixa = (string) '';

    $objeto_documento = new Documentos();
    $retorno_documento = (array) $objeto_documento->pesquisar_documento((array) ['filtro' => (array) ['codigo_barras', '===', (string) $codigo_barras]]);

    if (empty($retorno_documento) == false) {
        $nome_documento = (string) $retorno_documento['nome_documento'];

        $objeto_organizacao = new Organizacao();
        $retorno_organizacao = (array) $objeto_organizacao->pesquisar((array) ['filtro' => (array) ['and' => [(array) ['id_organizacao', '===', $retorno_documento['id_organizacao']], (array) ['id_empresa', '===', (int) $retorno_documento['id_empresa']]]]]);

        if (empty($retorno_organizacao) == false) {
            $nome_organizacao = (string) $retorno_organizacao['nome_organizacao'];
        }

        $objeto_armario = new Armario();
        $retorno_armario = (array) $objeto_armario->pesquisar((array) ['filtro' => (array) ['and' => (array) [(array) ['id_armario', '===', (int) $retorno_documento['id_armario']], (array) ['id_empresa', '===', (int) $retorno_documento['id_empresa']]]]]);

        if (empty($retorno_armario) == false) {
            $nome_armario = (string) $retorno_armario['nome_armario'];
        }

        $objeto_prateleira = new Prateleira();
        $retorno_prateleira = (array) $objeto_prateleira->pesquisar((array) ['filtro' => (array) ['and' => (array) [(array) ['id_prateleira', '===', (int) $retorno_documento['id_prateleira']], (array) ['id_empresa', '===', (int) $retorno_documento['id_empresa']]]]]);

        if (empty($retorno_prateleira) == false) {
            $nome_prateleira = (string) $retorno_prateleira['nome_prateleira'];
        }

        $objeto_caixa = new Caixa();
        $retorno_caixa = (array) $objeto_caixa->pesquisar((array) ['filtro' => (array) ['and' => (array) [(array) ['id_caixa', '===', (int) $retorno_documento['id_caixa']], (array) ['id_empresa', '===', (int) $retorno_documento['id_empresa']]]]]);

        if (empty($retorno_caixa) == false) {
            $nome_caixa = (string) $retorno_caixa['nome_caixa'];
        }
    }

    require_once 'includes/head_relatorio.php';
    ?>
    <script>
        const CODIGO_BARRAS = "<?php echo $codigo_barras; ?>";

        /**
         * Função responsável por fechar a janela
         */
        function fechar_janela() {
            window.close();
        }

        /**
         * Função responsável por imprimir o conteúdo que está em apresentação na tela.
        */
        function imprimir_conteudo() {
            document.querySelector('#botao_fechar').style.display = 'none';
            document.querySelector('#botao_imprimir').style.display = 'none';

            let card_impressao = document.querySelector('#card_impressao');

            card_impressao.classList.remove();
            card_impressao.classList.add('col-12');

            window.print();

            window.setTimeout(function () {
                document.querySelector('#botao_fechar').style.display = 'block';
                document.querySelector('#botao_imprimir').style.display = 'block';

                card_impressao.classList.remove();
                card_impressao.classList.add('col-8');
            }, 500);
        }

        /**
         * Função responsável por gerar o código de barras e apresentar na tela.
        */
        function gerar_codigo_barras() {
            JsBarcode("#barcode")
                .options({
                    font: "OCR-B"
                })
                .CODE128(CODIGO_BARRAS, {
                    fontSize: 25,
                    textMargin: 0
                })
                .blank(20)
                .render();
        }
    </script>
    <div class="row">
        <div class="col-8" id="card_impressao">
            <div class="card text-center">
                <div class="card-header">
                    <strong>NOME DOCUMENTO:</strong> <?php echo $nome_documento; ?><br />
                    <strong>ORGANIZAÇÃO:</strong> <?php echo $nome_organizacao; ?><br />
                    <strong>ARMÁRIO:</strong> <?php echo $nome_armario; ?><br />
                    <strong>PRATELEIRA:</strong> <?php echo $nome_prateleira; ?><br />
                    <strong>CAIXA:</strong> <?php echo $nome_caixa; ?><br />
                </div>
                <div class="card-body">
                    <svg id="barcode"></svg>
                </div>
            </div>
        </div>
        <div class="col-2" id="botao_imprimir">
            <br />
            <button class="btn btn-info btn-lg" onclick="imprimir_conteudo();">IMPRIMIR</button>
        </div>
        <div class="col-2" id="botao_fechar">
            <br />
            <button class="btn btn-danger btn-lg" onclick="fechar_janela();">FECHAR</button>
        </div>
    </div>
    <script>
        window.onload = function () {
            if (CODIGO_BARRAS == '') {
                fechar_janela();
            } else {
                gerar_codigo_barras();
            }
        }
    </script>
    <?php

    require_once 'includes/footer_relatorio.php';
    exit;
});
/**
 * Rota responsável por pesquisar todos os documentos do banco de dados e retornar ao front.
 */
router_add('pesquisar_documentos_todos', function () {
    $id_empresa = (int) (isset($_REQUEST['codigo_empresa']) ? (int) intval($_REQUEST['codigo_empresa'], 10) : 0);
    $id_sistema = (int) (isset($_REQUEST['codigo_sistema']) ? (int) intval($_REQUEST['codigo_sistema'], 10):0);
    $id_usuario = (int) (isset($_REQUEST['codigo_usuario']) ? (int) intval($_REQUEST['codigo_usuario'], 10):0);
    $codigo_documento = (int) (isset($_REQUEST['codigo_documento']) ? (int) intval($_REQUEST['codigo_documento'], 10) : 0);


    $nome_documento = (string) (isset($_REQUEST['nome_documento']) ? (string) $_REQUEST['nome_documento'] : '');
    $descricao = (string) (isset($_REQUEST['descricao']) ? (string) $_REQUEST['descricao'] : '');
    $codigo_barras = (string) (isset($_REQUEST['codigo_barras']) ? (string) $_REQUEST['codigo_barras'] : '');
    
    $limite_retorno = (int) (isset($_REQUEST['limite_retorno']) ? (int) intval($_REQUEST['limite_retorno'], 10):25);
    $preferencia_usuario_retorno = (int) (isset($_REQUEST['preferencia_usuario_retorno'])?(int) intval($_REQUEST['preferencia_usuario_retorno'], 10):25);

    $filtro_pesquisa = (array) ['filtro' => (array) [], 'ordenacao' => (array) ['quantidade_downloads' => (bool) false], 'limite' => (int) $limite_retorno];
    $retorno = (array) [];
    $filtro = (array) [];

    $objeto_documento = new Documentos();

    if ($id_empresa != 0) {
        array_push($filtro, (array) ['id_empresa', '===', (int) $id_empresa]);
    }

    if ($codigo_documento != 0) {
        array_push($filtro, (array) ['id_documento', '===', (int) $codigo_documento]);
    }

    if ($nome_documento != '') {
        array_push($filtro, (array) ['nome_documento', '=', (string) $nome_documento]);
    }

    if ($descricao != '') {
        array_push($filtro, (array) ['descricao', '=', (string) $descricao]);
    }

    if ($codigo_barras != '') {
        array_push($filtro, (array) ['codigo_barras', '=', (string) $codigo_barras]);
    }

    $filtro_pesquisa['filtro'] = (array) ['and' => (array) $filtro];


    $retorno_documentos_banco = (array) $objeto_documento->pesquisar_documentos_todos($filtro_pesquisa);

    if (empty($retorno_documentos_banco) == false) {
        foreach ($retorno_documentos_banco as $documento) {
            $modelo = (array) ['id_documento' => (int) 0, 'nome_documento' => (string) '', 'descricao' => (string) '', 'tipo' => (string) '', 'nome_armario' => (string) '', 'nome_prateleira' => (string) '', 'nome_caixa' => (string) '', 'quantidade_downloads' => (int) 0, 'codigo_barras' => (string) ''];

            $modelo['id_documento'] = (int) $documento['id_documento'];
            $modelo['nome_documento'] = (string) $documento['nome_documento'];
            $modelo['descricao'] = (string) $documento['descricao'];
            $modelo['tipo'] = (string) $documento['forma_visualizacao'];
            $modelo['quantidade_downloads'] = (int) $documento['quantidade_downloads'];
            $modelo['codigo_barras'] = (string) $documento['codigo_barras'];
            $modelo['endereco'] = (string) $documento['endereco'];

            $objeto_armario = new Armario();
            $retorno_armario = (array) $objeto_armario->pesquisar((array) ['filtro' => (array) ['and' => (array) [(array) ['id_armario', '===', (int) $documento['id_armario']], (array) ['id_empresa', '===', (int) $id_empresa]]]]);

            if (empty($retorno_armario) == false) {
                $modelo['nome_armario'] = (string) $retorno_armario['nome_armario'];
            }

            $objeto_prateleira = new Prateleira();
            $retorno_prateleira = (array) $objeto_prateleira->pesquisar((array) ['filtro' => (array) ['and' => (array) [(array) ['id_prateleira', '===', (int) $documento['id_prateleira']], (array) ['id_empresa', '===', (int) $id_empresa]]]]);

            if (empty($retorno_prateleira) == false) {
                $modelo['nome_prateleira'] = (string) $retorno_prateleira['nome_prateleira'];
            }

            $objeto_caixa = new Caixa();
            $retorno_caixa = (array) $objeto_caixa->pesquisar((array) ['filtro' => (array) ['and' => (array) [(array) ['id_caixa', '===', (int) $documento['id_caixa']], (array) ['id_empresa', '===', (int) $id_empresa]]]]);

            if (empty($retorno_caixa) == false) {
                $modelo['nome_caixa'] = (string) $retorno_caixa['nome_caixa'];
            }

            array_push($retorno, $modelo);
        }
    }

    if($preferencia_usuario_retorno != $limite_retorno){
        $objeto_preferencia = new Preferencia();
        $retorno_prefenrencia = (bool) $objeto_preferencia->excluir((array) ['nome_preferencia' => (string) 'QUANTIDADE_LIMITE_DOCUMENTO', 'codigo_usuario' => (int) $id_usuario, 'codigo_sistema' => (int) $id_sistema]);
        $retorno_prefenrencia = (bool) $objeto_preferencia->salvar_dados((array) ['codigo_usuario' => (int) $id_usuario, 'codigo_sistema' => (int) $id_sistema, 'nome_preferencia' => (string) 'QUANTIDADE_LIMITE_DOCUMENTO', 'preferencia' => (string) $limite_retorno]);
    }

    echo json_encode(['dados' => $retorno], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit pesquisar_documentos
router_add('pesquisar_documentos', function () {
    $objeto_documento = new Documentos();
    $id_documento = (int) (isset($_REQUEST['codigo_documento']) ? intval($_REQUEST['codigo_documento'], 10) : 0);

    $dados['filtro'] = (array) ['and' => [['id_documento', '===', (int) $id_documento]]];

    echo json_encode(['dados' => (array) $objeto_documento->pesquisar_documento($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit baixar_documento
router_add('baixar_documento', function () {
    $documento_objeto = new Documentos();
    $informacoes_documento = (array) $documento_objeto->update_download($_REQUEST);
    $endereco = (string) (isset($_REQUEST['endereco']) ? (string) $_REQUEST['endereco'] : '');
    $arquivo = (string) file_get_contents($endereco);
    $nome_documento = (string) 'nome_padrao';

    if (array_key_exists('nome_documento', $informacoes_documento) == true) {
        $nome_documento = (string) $informacoes_documento['nome_documento'];
    }

    if (array_key_exists('id_tipo_arquivo', $informacoes_documento) == true) {
        $extensao = (array) model_one('tipo_arquivo', ['id_tipo_arquivo', '===', (int) intval($informacoes_documento['id_tipo_arquivo'], 10)]);

        if (empty($extensao) == false) {
            if (array_key_exists('tipo_arquivo', $extensao) == true) {
                $nome_documento = (string) $nome_documento . strtolower($extensao['tipo_arquivo']);
            }
        }
    }

    header('Content-Disposition: attachment; filename="' . $nome_documento . '"');
    header('Content-Type: application/octet-stream');
    header('Content-Type: application/download');
    header('Content-Length: ' . strlen($arquivo));
    echo $arquivo;
    exit;
});

router_add('excluir_documento', function () {
    $documento_objeto = new Documentos();

    echo json_encode((array) ['status' => (bool) $documento_objeto->excluir_documento($_REQUEST)], JSON_UNESCAPED_UNICODE);

    exit;
});

router_add('salvar_preferencia_usuario_documento', function () {
    $codigo_sistema = (int) (isset($_REQUEST['codigo_sistema']) ? (int) intval($_REQUEST['codigo_sistema'], 10) : 0);
    $codigo_usuario = (int) (isset($_REQUEST['codigo_usuario']) ? (int) intval($_REQUEST['codigo_usuario'], 10) : 0);
    $nome_preferencia = (string) (isset($_REQUEST['nome_preferencia']) ? (string) $_REQUEST['nome_preferencia'] : '');
    $preferencia = (string) (isset($_REQUEST['preferencia']) ? (string) $_REQUEST['preferencia'] : '');

    $objeto_preferencia = new Preferencia();
    $filtro_pesquisa = (array) ['and' => (array) [['id_sistema', '===', (int) $codigo_sistema], ['id_usuario', '===', (int) $codigo_usuario], ['nome_preferencia', '===', (string) $nome_preferencia]]];

    $retorno = (bool) false;

    $filtro = (array) ['filtro' => (array) $filtro_pesquisa];
    $retorno_pesquisa = (array) $objeto_preferencia->pesquisar((array) $filtro);

    $dados = (array) ['codigo_sistema' => (int) $codigo_sistema, 'codigo_usuario' => (int) $codigo_usuario, 'nome_preferencia' => (string) $nome_preferencia, 'preferencia' => (string) $preferencia];

    if (empty($retorno_pesquisa) == true) {
        $retorno = (bool) $objeto_preferencia->salvar_dados((array) $dados);
    } else {
        $retorno = (bool) $objeto_preferencia->excluir((array) $dados);
    }

    echo json_encode((array) ['retorno' => (bool) $retorno], JSON_UNESCAPED_UNICODE);

    exit;
});

//@audit salvar_documento
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST)) {
        if (array_key_exists('rota', $_POST)) {
            if ($_POST['rota'] == 'salvar_dados') {
                $objeto_documento = new Documentos();
                $retorno = (bool) $objeto_documento->salvar_dados($_POST, $_FILES);
                if ($retorno == true) {
                    header('Location:documentos.php?cadastro_documento=true&retorno=true');
                } else {
                    header('Location:documentos.php?cadastro_documento=true&retorno=false');
                }
            }
        }
    }
}
?>