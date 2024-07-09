<?php
require_once 'Classes/bancoDedados.php';
require_once 'modelos/Usuario.php';

/**
 * Rota index, responsável por realizar o cadastro de novos usuários assim como alterar as suas informações.
 */
router_add('index', function(){
    require_once 'includes/head.php';
    ?>
    <script>
        const ID_EMPRESA = <?php echo $_SESSION['id_empresa']; ?>;

        /**
         * Função responsável por redirecionar o usuário para a rota de cadastro de novos usuários dentro do sistema.
         */
        function cadastrar_usuario(){
            window.location.href = sistema.url('/usuario.php', {'rota': 'cadastro_usuario'});
        }

        /**
         * Função responsável por montar o array de pesquisa e enviar para o back, para que o mesmo possa ser pesquisado no banco de dados.
         * E montar a tabela com o resultado das informações pesquisadas.
         */
        function pesquisar_usuario(){
            let id_usuario = parseInt(document.querySelector('#id_usuario').value, 10);
            let nome_usuario = document.querySelector('#nome_usuario').value;
            let login = document.querySelector('#login').value;
            let tipo = document.querySelector('#tipo').value;

            if(isNaN(id_usuario)){
                id_usuario = 0;
            }

            sistema.request.post('/usuario.php', {'rota': 'pesquisar_usuario', 'codigo_usuario': id_usuario, 'codigo_empresa': ID_EMPRESA, 'nome_usuario': nome_usuario, 'login': login, 'tipo': tipo}, function(retorno){
                let retorno_usuario = retorno.dados;
                let tamanho_retorno = retorno_usuario.length;
                let tabela = document.querySelector('#tabela_usuario tbody');
                let tamanho_tabela = tabela.rows.length;

                if(tamanho_tabela > 0){
                    tabela = sistema.remover_linha_tabela(tabela);
                }

                if(tamanho_retorno < 1){
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM USUÁRIO ENCONTRADO!', 'inner', true, 7));
                    tabela.appendChild(linha);
                }else{
                    sistema.each(retorno_usuario, function(index, usuario){
                        let linha = document.createElement('tr');

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.str_pad(usuario.id_usuario, 3, '0', 'STR_PAD_LEFT'), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], usuario.nome_usuario, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], usuario.login, 'inner'));

                        if(usuario.tipo == 'ADMINISTRADOR'){
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_tipo_usuario_'+usuario.id_usuario, 'ADMINISTRADOR', ['btn', 'btn-primary'], function visualizar(){}), 'append'));
                        }else{
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_tipo_usuario_'+usuario.id_usuario, 'COMUM', ['btn', 'btn-secondary'], function visualizar(){}), 'append'));
                        }
                        
                        if(usuario.status == 'ATIVO'){
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_tipo_usuario_'+usuario.id_usuario, 'ATIVO', ['btn', 'btn-success'], function visualizar(){}), 'append'));
                        }else{
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_tipo_usuario_'+usuario.id_usuario, 'INATIVO', ['btn', 'btn-danger'], function visualizar(){}), 'append'));
                        }

                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_Alterar_senha_'+usuario.id_usuario, 'ALTERAR SENHA', ['btn', 'btn-info'], function alterar_senha_usuario(){alterar_senha(usuario.id_usuario);}),'append'));
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_alterar_status_'+usuario.id_usuario, 'ALTERAR STATUS', ['btn', 'btn-info'], function alterar_status_usuario(){alterar_status(usuario.id_usuario, usuario.status);}), 'append'));

                        tabela.appendChild(linha);
                    });
                }
            });
        }

        /** Função responsável por alterar a senha do usuário
         * @param integer id_usuario identificador único do usuário
         */
        async function alterar_senha(id_usuario){
            const { value: senha_usuario } = await Swal.fire({title: "Digite uma nova senha", input: "password", inputLabel: "Senha para realizar login no sistema", inputPlaceholder: "Digite a senha", inputAttributes: {maxlength: "100", autocapitalize: "off", autocorrect: "off"}});
            
            if (senha_usuario) {
                sistema.request.post('/usuario.php', {'rota': 'alterar_senha_usuario', 'codigo_usuario':id_usuario, 'codigo_empresa': ID_EMPRESA, 'senha_usuario': senha_usuario}, function(retorno){
                    validar_retorno(retorno);
                });
            }
        }

        /** 
         * Função responsável por alterar o status do usuário de ATIVO para INATIVO e vice versa
         * @param integer id_usuario identificador do usuário.
         * 
        */
        function alterar_status(id_usuario, status){
            sistema.request.post('/usuario.php', {'rota': 'alterar_status_usuario', 'codigo_usuario': id_usuario, 'codigo_empresa':ID_EMPRESA, 'status':status}, function(retorno){
                validar_retorno(retorno);
                pesquisar_usuario();
            });
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Pesquisa de usuários</h4>
                        <br/>
                        <div class="row">
                            <div class="col-3 text-center">
                                <button class="btn btn-secondary btn-lg botao_grande custom-radius" onclick="cadastrar_usuario();">Cadastrar Usuário</button>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-1 text-center">
                                <label>Código</label>
                                <input type="text" class="form-control custom-radius text-center" id="id_usuario" placeholder="Código" sistema-mask="codigo"/>
                            </div>
                            <div class="col-3 text-center">
                                <label>Nome Usuário</label>
                                <input type="text" class="form-control custom-radius" id="nome_usuario" placeholder="Nome usuário"/>
                            </div>
                            <div class="col-3 text-center">
                                <label>Login usuário</label>
                                <input type="text" class="form-control custom-radius" id="login" placeholder="Login usuário"/>
                            </div>
                            <div class="col-3 text-center">
                                <label>Tipo Usuário</label>
                                <select class="form-control custom-radius" id="tipo">
                                    <option value="TODOS">TODOS</option>
                                    <option value="ADMINISTRADOR">ADMINISTRADOR</option>
                                    <option value="COMUM">COMUM</option>
                                </select>
                            </div>
                            <div class="col-2 text-center">
                                <button class="btn btn-info custom-radius botao_vertical_linha botao_grande" onclick="pesquisar_usuario();">Pesquisar</button>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="tabela_usuario">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">#</th>
                                                <th scope="col">Nome</th>
                                                <th scope="col">Login</th>
                                                <th scope="col">Tipo</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Alterar Senha</th>
                                                <th scope="col">Alterar Status</th>
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
    <script>
        window.onload = function(){
            pesquisar_usuario();
        }
    </script>
    <?php
    require_once 'includes/footer.php';
    exit;
});

/**
 * Rota responsável por apresentar o formulário de cadastro de novos usuários na base de dados.
 */
router_add('cadastro_usuario', function(){
    require_once 'includes/head.php';
    $id_empresa = (int) intval($_SESSION['id_empresa'], 10);
    ?>
     <script>
        const ID_EMPRESA = <?php echo $id_empresa; ?>;

        /** 
         * Função responsável por validar os dados do usuário e enviar para o back para salvar as informações no banco de dados.
         */
        function salvar_dados(){
            let codigo_usuario = parseInt(document.querySelector('#codigo_usuario').value, 10);
            let nome_usuario = document.querySelector('#nome_usuario').value;
            let login = document.querySelector('#login').value;
            let senha_usuario = document.querySelector('#senha_usuario').value;
            let tipo = document.querySelector('#tipo').value;
            let status = document.querySelector('#status').value;
            let retorno_validacao = true;

            if(isNaN(codigo_usuario)){
                codigo_usuario = 0;
            }

            if(nome_usuario == ''){
                apresentar_mensagem_erro('NOME USUÁRIO', '#nome_usuario');
                retorno_validacao = false;
            }

            if(login == ''){
                apresentar_mensagem_erro('LOGIN USUÁRIO', '#login');
                retorno_validacao = false;
            }

            if(senha_usuario == ''){
                apresentar_mensagem_erro('SENHA USUÁRIO', '#senha_usuario');
                retorno_validacao = false;
            }

            if(tipo == ''){
                apresentar_mensagem_erro('TIPO USUÁRIO', '#tipo');
                retorno_validacao = false;
            }

            if(status == ''){
                apresentar_mensagem_erro('STATUS USUÁRIO', '#status');
                retorno_validacao = false;
            }

            if(retorno_validacao == true){
                sistema.request.post('/usuario.php', {'rota': 'salvar_dados_usuario', 'codigo_usuario': codigo_usuario, 'codigo_empresa':ID_EMPRESA, 'nome_usuario': nome_usuario, 'login': login, 'senha_usuario': senha_usuario, 'tipo': tipo, 'status': status}, function(retorno){
                    validar_retorno(retorno, sistema.url('/usuario.php', {'rota':'index'}));
                });
            }
        }

        /** 
         * Função responsável por limpar o formulário de cadastro de usuários.
         */
        function excluir_dados(){
            document.querySelector('#codigo_usuario').value = "";
            document.querySelector('#nome_usuario').value = "";
            document.querySelector('#login').value = "";
            document.querySelector('#senha_usuario').value = "";
            document.querySelector('#tipo').value = "";
            document.querySelector('#status').value="";
        }

        /** 
         * Função responsável por retornar a rota index do módulo de usuários
         */
        function cancelar_dados(){
            window.location.href = sistema.url('/ususario.php', {'rota': 'index'});
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Cadastro de usuários</h4>
                        <br/>
                        <div class="row">
                            <input type="hidden" class="form-control custom-radius text-center" placeholder="Código" readonly="true" sistema-mask="codigo" id="codigo_usuario"/>
                            <div class="col-4 text-center">
                                <label>Nome Usuário</label>
                                <input type="text" class="form-control custom-radius text-uppercase" placeholder="Nome Usuário" id="nome_usuario"/>
                            </div>
                            <div class="col-2 text-center">
                                <label>Login Usuário</label>
                                <input type="text" class="form-control custom-radius" placeholder="Login Usuário" id="login"/>
                            </div>
                            <div class="col-2 text-center">
                                <label>Senha Usuário</label>
                                <input type="password" class="form-control custom-radius" id="senha_usuario" placeholder="Senha Usuário"/>
                            </div>
                            <div class="col-2 text-center">
                                <label>Tipo Usuário</label>
                                <select class="form-control custom-radius" id="tipo">
                                    <option value="">Selecione uma opção</option>
                                    <option value="ADMINISTRADOR">ADMINISTRADOR</option>
                                    <option value="COMUM">COMUM</option>
                                </select>
                            </div>
                            <div class="col-2 text-center">
                                <label>Status Usuário</label>
                                <select class="form-control custom-radius" id="status">
                                    <option value="">Selecione uma opção</option>
                                    <option value="ATIVO">ATIVO</option>
                                    <option value="INATIVO">INATIVO</option>
                                </select>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-4">
                                <button class="btn btn-primary custom-radius botao_vertical_linha botao_grande" onclick="salvar_dados();">Salvar</button>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-danger custom-radius botao_vertical_linha botao_grande" onclick="excluir_dados();">Excluir</button>
                            </div>
                            <dvi class="col-4">
                                <button class="btn btn-secondary custom-radius botao_vertical_linha botao_grande" onclick="cancelar_dados();">Cancelar</button>
                            </div>
                            </dvi>
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

/**
 * Rota responsável por alterar as informações do usuário comum
 */
router_add('alterar_informacoes_usuario_comum', function(){
    require_once 'includes/head.php';
    $id_empresa = (int) $_SESSION['id_empresa'];

    $read_only = (string) 'true';

    if($tipo_usuario == 'ADMINISTRADOR'){
        $read_only = (string) 'false';
    }

    ?>
    <script>
        const ID_EMPRESA = <?php echo $id_empresa; ?>;

        function pesquisar_informacoes_usuario(){
            let codigo_usuario = sistema.int(document.querySelector('#codigo_usuario').value);
            sistema.request.post('/usuario.php', {'rota': 'pesquisar_informacoes_usuario', 'codigo_usuario': codigo_usuario, 'codigo_empresa': ID_EMPRESA}, function(retorno){
                document.querySelector('#nome_usuario').value = retorno.dados.nome_usuario;
                document.querySelector('#login').value = retorno.dados.login;
            });
        }

        function salvar_dados(){
            let codigo_usuario = sistema.int(document.querySelector('#codigo_usuario').value);
            let nome_usuario = document.querySelector('#nome_usuario').value;
            let login = document.querySelector('#login').value;
            let senha = document.querySelector('#senha_usuario').value;

            if(senha == ''){
                Swal.fire('Erro', 'Para salvar as informações do usuário a senha não pode ser vazia!', 'error');
            }else{
                sistema.request.post('/usuario.php', {'rota': 'salvar_dados_usuario', 'codigo_usuario': codigo_usuario, 'codigo_empresa': ID_EMPRESA, 'nome_usuario': nome_usuario, 'login': login, 'senha_usuario': senha}, function(retorno){
                    sistema.verificar_status(retorno.status, sistema.url('/usuario.php', {'rota':'index'}));
                });
            }
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Informações do Usuário</h4>
                        <br/>
                        <input type="hidden" id="codigo_usuario" value = "<?php echo $_SESSION['id_usuario']; ?>"/>
                        <div class="row">
                            <div class="col-6 text-ceter">
                                <label class="text">Nome Usuário</label>
                                <input type="text" class="form-control custom-radius text-uppercase" id="nome_usuario" placeholder="Nome Usuário" <?php if($read_only == 'true'){echo "readonly = 'true'";} ?>/>
                            </div>
                            <div class="col-6 text-center">
                                <label class="text">Login</label>
                                <input type="text" class="form-control custom-radius" id="login" placeholder="Login Usuário" <?php if($read_only == 'true'){echo "readonly = 'true'";} ?>/>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-12 text-center">
                                <label class="text">Senha Usuário</label>
                                <input type="password" class="form-control custom-radius" id="senha_usuario" placeholder="Senha Usuário"/>
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
            pesquisar_informacoes_usuario();
        }
    </script>
    <?php
    require_once 'includes/footer.php';
    exit;
});

/**
 * Rota responsável por receber as informaçõs de vem do front e enviar para o back para que seja validada e alterada.
 */
router_add('alterar_status_usuario', function(){
    $objeto_usuario = new Usuario();

    echo json_encode(['status' => (bool) $objeto_usuario->alterar_status($_REQUEST)]);
    exit;
});

/**
 * Rota responsável por alterar a senha do usuário.
 * Esta rota recebe a nova senha e então envia para o back para que seja validada e alterada.
 */
router_add('alterar_senha_usuario', function(){
    $objeto_usuario = new Usuario();
    echo json_encode(['status' => (bool) $objeto_usuario->alterar_senha($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});

/**
 * Rota responsável por pesquisar as informações do usuário do sistema.
 * Esta rota recebe todos os filtros e monta o array de pesquisa e então envia para o back.
 */
router_add('pesquisar_usuario', function(){
    $objeto_usuario = new Usuario();

    $id_usuario = (int) (isset($_REQUEST['codigo_usuario']) ? (int) intval($_REQUEST['codigo_usuario'], 10):0);
    $id_empresa = (int) (isset($_REQUEST['codigo_empresa']) ? (int) intval($_REQUEST['codigo_empresa'], 10):0);
    $nome_usuario = (string) (isset($_REQUEST['nome_usuario']) ? (string) $_REQUEST['nome_usuario']:'');
    $login = (string) (isset($_REQUEST['login']) ? (string) $_REQUEST['login']:'');
    $tipo_usuario = (string) (isset($_REQUEST['tipo_usuario']) ? (string) $_REQUEST['tipo_usuario']:'');

    $dados = (array) ['filtro' => (array) [], 'ordenacao' => (array) ['nome_usuario' => (bool) true], 'limite' => (int) 0];
    $filtro = (array) [];

    if($id_usuario != 0){
        array_push($filtro, (array) ['id_usuario', '===', (int) $id_usuario]);
    }

    if($id_empresa != 0){
        array_push($filtro, (array) ['id_empresa', '===', (int) $id_empresa]);
    }

    if($nome_usuario != ''){
        array_push($filtro, ['nome_usuario', '=', (string) $nome_usuario]);
    }

    if($login != ''){
        array_push($filtro, ['login', '=', (string) $login]);
    }

    if($tipo_usuario != ''){
        array_push($filtro, ['tipo_usuario', '===', (string) $tipo_usuario]);
    }

    if(empty($filtro) == false){
        $dados['filtro'] = (array) ['and' => (array) $filtro];
    }

    echo json_encode((array) ['dados' => (array) $objeto_usuario->pesquisar_todos($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});

/**
 * Rora responsável por pesquisa a informaçõa de um usuário através do seu identificador único de dentro do sistema.
 */
router_add('pesquisar_informacoes_usuario', function(){
    $id_usuario = (int) (isset($_REQUEST['codigo_usuario']) ? (int) intval($_REQUEST['codigo_usuario'], 10):0);
    $id_empresa = (int) (isset($_REQUEST['codigo_empresa']) ? (int) intval($_REQUEST['codigo_empresa'], 10):0);
    $objeto_usuario = new Usuario();
    $filtro = (array) [];
    $dados = (array) [];

    array_push($filtro, ['id_usuario', '===', (int) $id_usuario]);
    array_push($filtro, ['id_empresa', '===', (int) $id_empresa]);

    $dados['filtro'] = (array) ['and' => (array) $filtro];

    echo json_encode(['dados' => (array) $objeto_usuario->pesquisar($dados)], JSON_UNESCAPED_UNICODE);
    exit;
});

/**
 * Rota responsável por receber do front das informações do usuário e enviar para o back para que as mesmas possam ser cadastradas na base de dados.
 */
router_add('salvar_dados_usuario', function(){
    $objeto_usuario = new Usuario();

    echo json_encode(['status' => (bool) $objeto_usuario->salvar_dados($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});
?>