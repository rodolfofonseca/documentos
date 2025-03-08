<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Prateleira.php';
require_once 'Modelos/Armario.php';
require_once 'Modelos/Preferencia.php';

/**
 * Função responsável por montar o array de retorno da forma como o front espera
 * @param array $retorno_pesquisa retorno com a pesquisa do banco de dados da tabela prateleira com os filtros passados pelo usuário
 * @param array $modelo Modelo que o front espera com as informações importantes
 * @param int $id_empresa identitificador da empresa no banco de dados
 * @param array $retorno Array de retorno que está sendo montando, normalmente este array já contém dados
 * @return array $retorno Array de retorno após a execução da função.
 */
function validar_dados_pesquisa_prateleira($retorno_pesquisa, $modelo, $id_empresa, $retorno)
{
    if (empty($retorno_pesquisa) == false) {
        foreach ($retorno_pesquisa as $retorno_pesquisa_prateleira) {
            $retorno_temporario = (array) model_parse($modelo, $retorno_pesquisa_prateleira);

            $objeto_armario = new Armario();
            $dados_armario = (array) $objeto_armario->pesquisar((array) ['filtro' => (array) ['and' => (array) [(array) ['id_empresa', '===', $id_empresa], (array) ['id_armario', '===', (int) $retorno_pesquisa_prateleira['id_armario']]]]]);

            if (empty($dados_armario) == false) {
                if (array_key_exists('nome_armario', $dados_armario) == true) {
                    $retorno_temporario['nome_armario'] = (string) $dados_armario['nome_armario'];
                }
            }

            array_push($retorno, $retorno_temporario);
        }
    }

    return (array) $retorno;
}

/**
 * Rota index, primeira rota que é executada dentro do sistema.
 */
router_add('index', function () {
    require_once 'includes/head.php';

    $objeto_preferencia = new Preferencia();
    $usuario_preferencia_nome_prateleira = (String) 'CHECKED';
    $usuario_preferencia_pesquisar_prateleira_automaticamente = (string) 'CHECKED';
    $usuario_preferencia_quantidade_prateleira = (int) intval(25, 10);

    //MONTANDO FILTRO DE PESQUISA, PARA SABER SE O USUÁRIO QUE ESTÁ LOGADO NO SISTEMA, PREFERE VER O NOME COMPLETO DA PRATELEIRA OU NÃO.
    $filtro_pesquisa = (array) ['and' => (array) [['id_sistema', '===', (int) intval($_SESSION['id_sistema'], 10)], ['id_usuario', '===', (int) intval($_SESSION['id_usuario'], 10)], ['nome_preferencia', '===', (string) 'NOME_COMPLETO_PRATELEIRA']]];
    $retorno_pesquisa_preferencia = (array) $objeto_preferencia->pesquisar((array) ['filtro' => (array) $filtro_pesquisa]);

    if(empty($retorno_pesquisa_preferencia) == true){
        $usuario_preferencia_nome_prateleira = (string) '';
    }

    //MONTANDO FILTRO DE PESQUISA PARA SABER SE O USUÁRIO LOGADO NO SISTEMA PREFERE PESQUISAR AS PRATELEIRAS AUTOMÁTICAMENTE AO ABRIR A PÁGINA.
    $filtro_pesquisa = (array) ['and' => (array) [['id_sistema', '===', (int) intval($_SESSION['id_sistema'], 10)], ['id_usuario', '===', (int) intval($_SESSION['id_usuario'], 10)], ['nome_preferencia', '===', (string) 'PESQUISAR_PRATELEIRA_AUTOMATICAMENTE']]];
    $retorno_pesquisa_preferencia = (array) $objeto_preferencia->pesquisar((array) ['filtro' => (array) $filtro_pesquisa]);

    if(empty($retorno_pesquisa_preferencia) == true){
        $usuario_preferencia_pesquisar_prateleira_automaticamente = (string) '';
    }

    //MONTANDO FILTRO DE PESQUISAR PARA SABER A QUANTIDADE DE PRATELEIRAS QUE O USUÁRIO QUE ESTÁ LOGADO NO SISTEMA PREFERE QUE O SISTEMA RETORNE DURANTE AS PESQUISAS.
    $filtro_pesquisa = (array) ['and' => (array) [['id_sistema', '===', (int) intval($_SESSION['id_sistema'], 10)], ['id_usuario', '===', (int) intval($_SESSION['id_usuario'], 10)], ['nome_preferencia', '===', (string) 'QUANTIDADE_LIMITE_PRATELEIRA']]];
    $retorno_pesquisa_preferencia = (array) $objeto_preferencia->pesquisar((array) ['filtro' => (array) $filtro_pesquisa]);

    if(empty($retorno_pesquisa_preferencia) == true){
        $retorno_salvar_dados = (bool) $objeto_preferencia->salvar_dados((array) ['codigo_usuario' => (int) intval($_SESSION['id_usuario'], 10), 'codigo_sistema' => (int) intval($_SESSION['id_sistema'], 10), 'nome_preferencia' => (string) 'QUANTIDADE_LIMITE_PRATELEIRA', 'preferencia' => (string) $usuario_preferencia_quantidade_prateleira]);
    }else{
        if(array_key_exists('preferencia', $retorno_pesquisa_preferencia) == true){
            $usuario_preferencia_quantidade_prateleira = (int) intval($retorno_pesquisa_preferencia['preferencia'], 10);
        }
    }    
    ?>
    <script>
        const CODIGO_EMPRESA = <?php echo $_SESSION['id_empresa']; ?>;
        const CODIGO_USUARIO = <?php echo $_SESSION['id_usuario']; ?>;
        const CODIGO_SISTEMA = <?php echo $_SESSION['id_sistema']; ?>;
        const PREFERENCIA_QUANTIDADE_PRATELEIRA = <?php echo intval($usuario_preferencia_quantidade_prateleira, 10); ?>;
        const PRESQUISAR_PRATELEIRA_AUTOMATICAMENTE = "<?php echo $usuario_preferencia_pesquisar_prateleira_automaticamente; ?>";

        let CODIGO_ORGANIZACAO = 0;
        let CODIGO_ARMARIO = 0;

        function cadastro_prateleira(codigo_prateleria) {
            window.location.href = sistema.url('/prateleira.php', {
                'rota': 'salvar_alterar_dados',
                'codigo_prateleira': codigo_prateleria
            });
        }

        function pesquisar_prateleiras() {
            let codigo_prateleira = parseInt(document.querySelector('#codigo_prateleira').value, 10);
            let codigo_barras = document.querySelector('#codigo_barras').value;
            let nome_prateleira = document.querySelector('#nome_prateleira').value;
            let descricao = document.querySelector('#descricao').value;
            let forma_visualizacao = document.querySelector('#forma_visualizacao').value;
            let limite_retorno = sistema.int(document.querySelector('#limite_retorno').value);
            
            if (isNaN(codigo_armario)) {
                codigo_armario = 0;
            }

            if (isNaN(codigo_prateleira)) {
                codigo_prateleira = 0;
            }

            sistema.request.post('/prateleira.php', {
                'rota': 'pesquisar_prateleira_todas',
                'codigo_prateleira': codigo_prateleira,
                'codigo_armario': CODIGO_ARMARIO,
                'codigo_empresa': CODIGO_EMPRESA,
                'codigo_usuario': CODIGO_USUARIO,
                'codigo_sistema': CODIGO_SISTEMA,
                'nome_prateleira': nome_prateleira,
                'codigo_barras': codigo_barras,
                'descricao': descricao,
                'forma_visualizacao': forma_visualizacao,
                'limite_retorno':limite_retorno,
                'preferencia_usuario_limite_retorno': PREFERENCIA_QUANTIDADE_PRATELEIRA
            }, function (retorno) {
                let retorno_prateleira = retorno.dados;
                let tamanho_retorno = retorno_prateleira.length;
                let tabela = document.querySelector('#tabela_prateleira tbody');

                tabela = sistema.remover_linha_tabela(tabela);

                if (tamanho_retorno < 1) {
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA PRATELEIRA ENCONTRADA COM OS FILTROS INFORMADOS', 'inner', true, 7));
                    tabela.appendChild(linha);
                } else {
                    sistema.each(retorno_prateleira, function (contador, prateleiras) {
                        let linha = document.createElement('tr');

                        linha.appendChild(sistema.gerar_td(['text-center'], prateleiras.id_prateleira, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], prateleiras.nome_prateleira), 'inner');
                        linha.appendChild(sistema.gerar_td(['text-center'], prateleiras.descricao, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], prateleiras.nome_armario, 'inner'));

                        if (prateleiras.forma_visualizacao == 'PUBLICO') {
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_informacao_' + prateleiras.id_prateleira, prateleiras.forma_visualizacao, ['btn', 'btn-primary'], function visualizar() { }), 'append'));
                        } else {
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_informacao_' + prateleiras.id_prateleira, prateleiras.forma_visualizacao, ['btn', 'btn-secondary'], function visualizar() { }), 'append'));
                        }

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('imprimir_codigo_barras_prateleira_' + prateleiras.id_prateleira, prateleiras.codigo_barras, ['btn', 'btn-success'], function impressao() {
                            imprimir_codigo_barras_prateleiras(prateleiras.id_prateleira)
                        }), 'append'));

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('excluir_prateleira_sistema_' + prateleiras.id_prateleira, 'EXCLUIR', ['btn', 'btn-danger'],
                            function excluir_prateleira_sistema() {
                                excluir_prateleira(prateleiras.id_prateleira);
                            }), 'append'));

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('visualizar_prateleira_' + prateleiras.id_prateleira, 'VISUALIZAR', ['btn', 'btn-info'], function visualizar() {
                            cadastro_prateleira(prateleiras.id_prateleira);
                        }), 'append'));


                        tabela.appendChild(linha);
                    });
                }
            }, false);
        }

        function imprimir_codigo_barras_prateleiras(codigo_prateleira) {
            window.open(sistema.url('/prateleira.php', {
                'rota': 'impressao_codigo_barras_prateleira',
                'codigo_prateleira': codigo_prateleira
            }), 'Impressão de código de barras', 'width=740px, height=500px, scrollbars=yes');
        }

        /**
         * Função responsável por chamar a rota de excluir prateleira do sistema e depois realizar a validação.
         */
        function excluir_prateleira(codigo_prateleira) {
            sistema.request.post('/prateleira.php', { 'rota': 'excluir_prateleira', 'codigo_prateleira': codigo_prateleira }, function (retorno) {
                validar_retorno(retorno, '', 1);
                pesquisar_prateleiras();
            });
        }

        /** 
         * Função responsável por alterar no banco de dados a preferência do usuário sobre ver o nome completo das prateleiras ou não.
        */
        function alterar_preferencia_nome_completo_prateleira(){
            let check_visualizar_nome_prateleira = document.querySelector('#visualizar_nome_prateleira_completo');
            let preferencia = '';

            if(check_visualizar_nome_prateleira.checked == true){
                preferencia = 'CHECKED';
            }else{
                preferencia = '';
            }

            sistema.request.post('/preferencia.php', {'rota': 'salvar_preferencia_usuario', 'codigo_sistema': CODIGO_SISTEMA, 'codigo_usuario': CODIGO_USUARIO, 'nome_preferencia':'NOME_COMPLETO_PRATELEIRA', 'preferencia': preferencia}, function(retorno){}, false);
        }
        
        /** 
         * Função responsável por salvar na base de dados a quantidade de organizações que o usuário prefere pesquisar.
         */
        function colocar_preferencia_quantidade_organizacao(){
            let objeto_limte_prateleira = document.querySelector('#limite_retorno');
            objeto_limte_prateleira.value =PREFERENCIA_QUANTIDADE_PRATELEIRA;
        }
        
        /** 
         * Função responsável por alterar a preferência do usuário onde, o sistema pesquisa automáticamente as prateleiras ao abrir a página
         */
        function alterar_preferencia_pesquisar_prateleira_automaticamente(){
            let check_pesquisar_prateleira_automaticamente = document.querySelector('#pesquisar_prateleira_automaticamente');
            let preferencia = '';

            if(check_pesquisar_prateleira_automaticamente.checked == true){
                preferencia = 'CHECKED';
            }else{
                preferencia = '';
            }
            
            sistema.request.post('/preferencia.php', {'rota': 'salvar_preferencia_usuario', 'codigo_sistema': CODIGO_SISTEMA, 'codigo_usuario': CODIGO_USUARIO, 'nome_preferencia': 'PESQUISAR_PRATELEIRA_AUTOMATICAMENTE', 'preferencia': preferencia}, function(retorno){}, false);
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Pesquisa de Prateleiras</h4>
                        <div class="row">
                            <div class="col-3">
                                <button class="btn btn-secondary custom-radius btn-lg"
                                    onclick="cadastro_prateleira(0);">Cadastrar Prateleira</button>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-2 text-center">
                                <label class="text">Código</label>
                                <input type="text" class="form-control custom-radius" id="codigo_prateleira"
                                    placeholder="Código" sistema-mask="codigo" />
                            </div>
                            <div class="col-6 text-center">
                                <label class="text">Nome Prateleira</label>
                                <input type="text" class="form-control custom-radius text-uppercase text-uppercase"
                                    id="nome_prateleira" placeholder="Nome Prateleira" onkeyup="pesquisar_prateleiras();" />
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Código Barras</label>
                                <input type="text" id="codigo_barras" class="form-control custom-radius"
                                    sistema-mask="codigo" placeholder="Código Barras" maxlength="13"
                                    onkeyup="pesquisar_prateleiras();" />
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Forma de visualização</label>
                                <select class="form-control custom-radius" id="forma_visualizacao">
                                    <option value="TODOS">TODOS</option>
                                    <option value="PUBLICO">PÚBLICO</option>
                                    <option value="PRIVADO">PRIVADO</option>
                                </select>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-12">
                                <textarea class="form-control custom-radius" id="descricao"
                                    placeholder="Descrição"></textarea>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <?php
                            $abrir_modal_armario = 'prateleira';
                            $abrir_modal_organizacao = 'prateleira';
                            require_once 'includes/modal_organizacao.php';
                            require_once 'includes/modal_armario.php';
                            ?>

                        </div>
                        <br />
                        <div class="row">
                            <div class="col-2">
                                <select class="form-control custom-radius" id="limite_retorno">
                                    <option value="25">25 PRATELEIRAS</option>
                                    <option value="50">50 PRATELEIRAS</option>
                                    <option value="75">75 PRATELEIRAS</option>
                                    <option value="100">100 PRATELEIRAS</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="push-9 col-3 text-center">
                                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                    <button type="button" class="btn btn-info custom-radius botao_grande btn-lg"
                                        onclick="pesquisar_prateleiras();">PESQUISAR PRATELEIRA</button>
                                    <div class="btn-group" role="group">
                                        <button type="button"
                                            class="btn btn-secondary dropdown-toggle custom-radius botao_grande btn-lg"
                                            data-toggle="dropdown" aria-expanded="false"> PREFERÊNCIAS </button>
                                        <div class="dropdown-menu">
                                            <div class="dropdown-item">
                                                <input class="form-check-item" type="checkbox" role="switch"
                                                    id="visualizar_nome_prateleira_completo" <?php echo $usuario_preferencia_nome_prateleira; ?>
                                                    onclick="alterar_preferencia_nome_completo_prateleira();" />
                                                <label class="form-check-label"
                                                    for="visualizar_nome_prateleira_completo">Ver nome/descrição
                                                    completos</label>
                                            </div>
                                            <div class="dropdown-item">
                                                <input class="form-check-item" type="checkbox" role="switch"
                                                    id="pesquisar_prateleira_automaticamente" <?php echo $usuario_preferencia_pesquisar_prateleira_automaticamente; ?>
                                                    onclick="alterar_preferencia_pesquisar_prateleira_automaticamente();" />
                                                <label class="form-check-label"
                                                    for="pesquisar_prateleira_automaticamente">Pesquisar prateleira
                                                    Automáticamente</label>
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
                                    <table class="table table-hover table-striped" id="tabela_prateleira">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">#</th>
                                                <th scope="col">Nome</th>
                                                <th scope="col">Descrição</th>
                                                <th scope="col">Armário</th>
                                                <th scope="col">Forma Visualização</th>
                                                <th scope="col">Código Barras</th>
                                                <th scope="col">Excluir</th>
                                                <th scope="col">Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="8" class="text-center">UTILIZE OS FILTROS PARA FACILITAR
                                                    SUA PESQUISA</td>
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
            validar_acesso_administrador('<?php echo $_SESSION['tipo_usuario']; ?>');

            if(PRESQUISAR_PRATELEIRA_AUTOMATICAMENTE == 'CHECKED'){
                pesquisar_prateleiras();
            }
        }
    </script>
    <?php
    require_once 'includes/footer.php';
    exit;
});

//@note salvar_alterar_dados
/**
 * View Contendo o formulário de cadastro de prateleiras.
 */
router_add('salvar_alterar_dados', function () {
    $codigo_prateleira = (int) (isset($_REQUEST['codigo_prateleira']) ? (int) intval($_REQUEST['codigo_prateleira'], 10) : 0);
    require_once 'includes/head.php';
    ?>
    <script>
        let CODIGO_PRATELEIRA = <?php echo $codigo_prateleira; ?>;
        const CODIGO_EMPRESA = <?php echo $_SESSION['id_empresa']; ?>;
        const CODIGO_USUARIO = <?php echo $_SESSION['id_usuario']; ?>;

        /** 
         * Função responsável por pesquisar os todos os armários públicos cadastrados no banco de dados
         */
        function pesquisar_armarios() {
            sistema.request.post('/armario.php', {
                'rota': 'pesquisar_armario_todos',
                'codigo_usuario': CODIGO_USUARIO,
                'codigo_empresa': CODIGO_EMPRESA,
                'forma_visualizacao': 'TODOS'
            }, function (retorno) {
                let armario = document.querySelector('#codigo_armario');

                sistema.each(retorno.dados, function (index, armarios) {
                    armario.options[armario.options.length] = new Option(armarios.nome_armario, armarios.id_armario);
                });
            });
        }

        /** 
         * Função responsável por enviar as informações preenxidas pelo usuário para o back.
         */
        function salvar_dados() {
            let codigo_prateleira = parseInt(document.querySelector('#codigo_prateleira').value, 10);
            let nome_prateleira = document.querySelector('#nome_prateleira').value;
            let descricao = document.querySelector('#descricao').value;
            let codigo_barras = document.querySelector('#codigo_barras').value;
            let forma_visualizacao = document.querySelector('#forma_visualizacao').value;

            if (isNaN(codigo_prateleira)) {
                codigo_prateleira = 0;
            }

            if (CODIGO_ARMARIO != 0 && forma_visualizacao != '') {
                sistema.request.post('/prateleira.php', {
                    'rota': 'salvar_dados',
                    'codigo_prateleira': codigo_prateleira,
                    'codigo_empresa': CODIGO_EMPRESA,
                    'codigo_usuario': CODIGO_USUARIO,
                    'codigo_armario': CODIGO_ARMARIO,
                    'nome_prateleira': nome_prateleira,
                    'descricao': descricao,
                    'codigo_barras': codigo_barras,
                    'forma_visualizacao': forma_visualizacao
                }, function (retorno) {
                    validar_retorno(retorno);
                    limpar_campos();
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "O campo PRATELEIRA e FORMA VISUALIZAÇÃO não pode ser vazio!"
                });
            }

        }

        function limpar_campos() {
            document.querySelector('#codigo_prateleira').value = '';
            document.querySelector('#nome_prateleira').value = '';
            document.querySelector('#descricao').value = '';
            document.querySelector('#forma_visualizacao').value = '';
            document.querySelector('#codigo_modal_armario').value = '0';

            sistema.request.post('/index.php', {
                'rota': 'buscar_codigo_barras'
            }, function (retorno) {
                document.querySelector('#codigo_barras').value = retorno.codigo_barras;
            }, false);
        }

        function voltar() {
            window.location.href = sistema.url('/prateleira.php', {
                'rota': 'index'
            });
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Cadastro de Prateleiras</h4>
                        <br />
                        <div class="row">
                            <div class="col-1 text-center">
                                <label class="text">Código</label>
                                <input type="text" class="form-control custom-radius text-center" id="codigo_prateleira"
                                    disabled="true" placeholder="Código" />
                            </div>
                            <div class="col-7 text-center">
                                <label class="text">Nome Prateleira</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="nome_prateleira"
                                    placeholder="Nome Prateleira" />
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Forma Visualização</label>
                                <select class="form-control custom-radius" id="forma_visualizacao">
                                    <option value="">Selecione uma opção</option>
                                    <option value="PUBLICO">PÚBLICO</option>
                                    <option value="PRIVADO">PRIVADO</option>
                                </select>
                            </div>
                            <div class="col-2 text-center">
                                <label class="text">Código Barras</label>
                                <input type="text" class="form-control custom-radius text-center" id="codigo_barras"
                                    value="<?php echo codigo_barras(); ?>" disabled="true" />
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-12">
                                <textarea class="form-control custom-radius" id="descricao"
                                    placeholder="Descrição da prateleira"></textarea>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <?php
                                $abrir_modal_armario = 'prateleira';
                                $abrir_modal_organizacao = 'prateleira';
                                require_once 'includes/modal_organizacao.php';
                                require_once 'includes/modal_armario.php';
                            ?>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-4">
                                <button class="btn btn-info custom-radius btn-lg botao_grande"
                                    onclick="salvar_dados();">Salvar Dados</button>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-danger custom-radius btn-lg botao_grande"
                                    onclick="limpar_campos();">Limpar Campos</button>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-secondary custom-radius btn-lg botao_grande"
                                    onclick="voltar();">Voltar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    require_once 'includes/footer.php';
    ?>
    <script>
        window.onload = async function () {
            validar_acesso_administrador('<?php echo $_SESSION['tipo_usuario']; ?>');

            await pesquisar_armarios();

            window.setTimeout(function () {
                if (CODIGO_PRATELEIRA != 0) {
                    sistema.request.post('/prateleira.php', {
                        'rota': 'pesquisar_prateleira',
                        'codigo_prateleira': CODIGO_PRATELEIRA
                    }, function (retorno) {
                        document.querySelector('#codigo_prateleira').value = retorno.dados.id_prateleira;
                        document.querySelector('#nome_prateleira').value = retorno.dados.nome_prateleira;
                        document.querySelector('#descricao').value = retorno.dados.descricao;
                        document.querySelector('#codigo_barras').value = retorno.dados.codigo_barras;
                        document.querySelector('#forma_visualizacao').value = retorno.dados.forma_visualizacao;
                        document.querySelector('#codigo_modal_armario').value = retorno.dados.id_armario;
                    });
                }
            }, 500);

        }
    </script>
    <?php
    exit;
});

//@audit salvar_dados
router_add('salvar_dados', function () {
    $objeto_prateleria = new Prateleira();

    echo json_encode(['status' => (bool) $objeto_prateleria->salvar_dados($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@audit pesquisar_prateleira
router_add('pesquisar_prateleira', function () {
    $objeto_prateleira = new Prateleira();
    $codigo_prateleira = (int) (isset($_REQUEST['codigo_prateleira']) ? (int) intval($_REQUEST['codigo_prateleira'], 10) : 0);
    $filtro = (array) [];
    $dados = (array) [];

    if ($codigo_prateleira != 0) {
        array_push($filtro, ['id_prateleira', '===', (int) $codigo_prateleira]);
    }

    $dados['filtro'] = (array) ['and' => (array) $filtro];

    echo json_encode(['dados' => (array) $objeto_prateleira->pesquisar($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});

/**
 * Rota responsável por montar o filtro de pesquisa e realizar a pesquisa retornando as informações para a rota que fez a solicitação.
 */
router_add('pesquisar_prateleira_todas', function () {
    $objeto_prateleira = new Prateleira();

    $id_prateleira = (int) (isset($_REQUEST['codigo_prateleira']) ? (int) intval($_REQUEST['codigo_prateleira'], 10) : 0);
    $id_empresa = (int) (isset($_REQUEST['codigo_empresa']) ? (int) intval($_REQUEST['codigo_empresa'], 10) : 0);
    $id_armario = (int) (isset($_REQUEST['codigo_armario']) ? (int) intval($_REQUEST['codigo_armario'], 10) : 0);
    $id_usuario = (int) (isset($_REQUEST['codigo_usuario']) ? (int) intval($_REQUEST['codigo_usuario'], 10) : 0);
    $id_sistema = (int) (isset($_REQUEST['codigo_sistema']) ? (int) intval($_REQUEST['codigo_sistema'], 10):0);


    $nome_prateleira = (string) (isset($_REQUEST['nome_prateleira']) ? (string) strtoupper($_REQUEST['nome_prateleira']) : '');
    $descricao = (string) (isset($_REQUEST['descricao']) ? (string) $_REQUEST['descricao'] : '');
    $codigo_barras = (string) (isset($_REQUEST['codigo_barras']) ? (string) $_REQUEST['codigo_barras'] : '');
    $forma_visualizacao = (string) (isset($_REQUEST['forma_visualizacao']) ? (string) $_REQUEST['forma_visualizacao'] : 'TODOS');

    $limite_retorno = (int) (isset($_REQUEST['limite_retorno']) ? (int) intval($_REQUEST['limite_retorno'], 10):0);
    $usuario_preferencia_quantidade_prateleira = (int) (isset($_REQUEST['preferencia_usuario_limite_retorno']) ? (int) intval($_REQUEST['preferencia_usuario_limite_retorno'], 10):0);

    $filtro = (array) [];
    $filtro_todos_publico = (array) [];
    $filtro_todos_privado = (array) [];

    $dados = (array) ['filtro' => (array) [], 'ordenacao' => (array) ['nome_prateleira' => (bool) true], 'limite' => (int) $limite_retorno];
    $modelo = (array) ['id_prateleria' => (int) 0, 'id_armario' => (int) 0, 'id_empresa' => (int) 0, 'id_usuario' => (int) 0, 'nome_prateleira' => (string) '', 'nome_armario' => (string) '', 'descricao' => (string) '', 'codigo_barras' => (string) '', 'forma_visualizacao' => (string) ''];

    $retorno = (array) [];

    if ($id_prateleira != 0) {
        array_push($filtro, (array) ['id_prateleira', '===', (int) $id_prateleira]);
        array_push($filtro_todos_publico, (array) ['id_prateleira', '===', (int) $id_prateleira]);
        array_push($filtro_todos_privado, (array) ['id_prateleira', '===', (int) $id_prateleira]);
    }

    if ($id_empresa != 0) {
        array_push($filtro, (array) ['id_empresa', '===', (int) $id_empresa]);
        array_push($filtro_todos_publico, (array) ['id_empresa', '===', (int) $id_empresa]);
        array_push($filtro_todos_privado, (array) ['id_empresa', '===', (int) $id_empresa]);

    }

    if ($id_armario != 0) {
        array_push($filtro, (array) ['id_armario', '===', (int) $id_armario]);
        array_push($filtro_todos_publico, (array) ['id_armario', '===', (int) $id_armario]);
        array_push($filtro_todos_privado, (array) ['id_armario', '===', (int) $id_armario]);
        
    }

    if ($nome_prateleira != '') {
        array_push($filtro, (array) ['nome_prateleria', '=', (string) $nome_prateleira]);
        array_push($filtro_todos_publico, (array) ['nome_prateleria', '=', (string) $nome_prateleira]);
        array_push($filtro_todos_privado, (array) ['nome_prateleria', '=', (string) $nome_prateleira]);
    }

    if ($descricao != '') {
        array_push($filtro, (array) ['descricao', '=', (string) $descricao]);
        array_push($filtro_todos_publico, (array) ['descricao', '=', (string) $descricao]);
        array_push($filtro_todos_privado, (array) ['descricao', '=', (string) $descricao]);
    }

    if ($codigo_barras != '') {
        array_push($filtro, (array) ['codigo_barras', '=', (string) $codigo_barras]);
        array_push($filtro_todos_publico, (array) ['codigo_barras', '=', (string) $codigo_barras]);
        array_push($filtro_todos_privado, (array) ['codigo_barras', '=', (string) $codigo_barras]);
    }

    if ($forma_visualizacao == 'PRIVADO') {
        array_push($filtro, (array) ['id_usuario', '===', (int) $id_usuario]);
    }

    if ($forma_visualizacao == 'PRIVADO' || $forma_visualizacao == 'PUBLICO') {
        array_push($filtro, ['forma_visualizacao', '===', (string) $forma_visualizacao]);
        $dados['filtro'] = (array) ['and' => (array) $filtro];
        $retorno = (array) validar_dados_pesquisa_prateleira((array) $objeto_prateleira->pesquisar_todos($dados), (array) $modelo, (int) $id_empresa, (array) $retorno);
    } else {
        array_push($filtro_todos_privado, (array) ['id_usuario', '===', (int) $id_usuario]);
        array_push($filtro_todos_privado, (array) ['forma_visualizacao', '===', (string) 'PRIVADO']);
        array_push($filtro_todos_publico, (array) ['forma_visualizacao', '===', (string) 'PUBLICO']);

        $dados['filtro'] = (array) ['and' => (array) $filtro_todos_privado];
        $retorno_pesquisa_privado = (array) $objeto_prateleira->pesquisar_todos((array) $dados);

        $dados['filtro'] = (array) ['and' => (array) $filtro_todos_publico];
        $retorno_pesquisa_publico = (array) $objeto_prateleira->pesquisar_todos((array) $dados);

        $retorno = (array) validar_dados_pesquisa_prateleira($retorno_pesquisa_privado, $modelo, $id_empresa, $retorno);
        $retorno = (array) validar_dados_pesquisa_prateleira($retorno_pesquisa_publico, $modelo, $id_empresa, $retorno);
    }

    $objeto_preferencia = new Preferencia();
    $objeto_preferencia->alterar_quantidade_retorno($usuario_preferencia_quantidade_prateleira, $limite_retorno, 'QUANTIDADE_LIMITE_PRATELEIRA', $id_usuario, $id_sistema);

    echo json_encode(['dados' => (array) $retorno], JSON_UNESCAPED_UNICODE);
    exit;
});

/**
 * Rota reponsável pela impressão de código de barras
 * TODO imprime apenas um código de barras por vez.
 */

router_add('impressao_codigo_barras_prateleira', function () {
    $codigo_prateleira = (int) (isset($_REQUEST['codigo_prateleira']) ? intval($_REQUEST['codigo_prateleira'], 10) : 0);
    $filtro = ['filtro' => (array) ['id_prateleira', '===', (int) $codigo_prateleira]];

    $objeto_prateleira = new Prateleira();
    $retorno_pesquisa_prateleira = (array) $objeto_prateleira->pesquisar($filtro);

    $nome_prateleira = (string) '';
    $codigo_barras = (string) '';

    if (array_key_exists('nome_prateleira', $retorno_pesquisa_prateleira) == true) {
        $nome_prateleira = (string) $retorno_pesquisa_prateleira['nome_prateleira'];
    }

    if (array_key_exists('codigo_barras', $retorno_pesquisa_prateleira) == true) {
        $codigo_barras = (string) $retorno_pesquisa_prateleira['codigo_barras'];
    }

    require_once 'includes/head_relatorio.php';
    ?>
    <script>
        const CODIGO_BARRAS = "<?php echo $codigo_barras; ?>";

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

        function fechar_janela() {
            window.close();
        }

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
    </script>
    <div class="row">
        <div class="col-8" id="card_impressao">
            <div class="card text-center">
                <div class="card-header">
                    <?php echo $nome_prateleira; ?>
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
            gerar_codigo_barras();
        }
    </script>
    <?php
    require_once 'includes/footer_relatorio.php';
});

/**
 * Rota responsável por pegar as informações  vindas da model e converter para passar a modelo e então realizar a exclusão da prateleira.
 */
router_add('excluir_prateleira', function () {
    $objeto_prateleira = new Prateleira();

    echo json_encode((array) $objeto_prateleira->excluir($_REQUEST), JSON_UNESCAPED_UNICODE);
    exit;
});
?>