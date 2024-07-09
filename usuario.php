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
            sistema.request.post('/usuario.php', {'rota': 'pesquisar_usuario'}, function(retorno){
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
                        linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_alterar_status_'+usuario.id_usuario, 'ALTERAR STATUS', ['btn', 'btn-info'], function alterar_status_usuario(){alterar_status(usuario.id_usuario);}), 'append'));

                        tabela.appendChild(linha);
                    });
                }
            });
        }

        /** Função responsável por alterar a senha do usuário
         * @param integer id_usuario identificador único do usuário
         */
        function alterar_senha(id_usuario){

        }

        /** 
         * Função responsável por alterar o status do usuário de ATIVO para INATIVO e vice versa
         * @param integer id_usuario identificador do usuário.
         * 
        */
        function alterar_status(id_usuario){

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
                                <input type="text" class="form-control custom-radius text-center" id="codigo_usuario" placeholder="Código" sistema-mask="codigo"/>
                            </div>
                            <div class="col-3 text-center">
                                <label>Nome Usuário</label>
                                <input type="text" class="form-control custom-radius" id="nome_usuario" placeholder="Nome usuário"/>
                            </div>
                            <div class="col-3 text-center">
                                <label>Login usuário</label>
                                <input type="text" class="form-control custom-radius" id="login_usuario" placeholder="Login usuário"/>
                            </div>
                            <div class="col-3 text-center">
                                <label>Tipo Usuário</label>
                                <select class="form-control custom-radius">
                                    <option value="TODOS">TODOS</option>
                                    <option value="ADMINISTRADOR">ADMINISTRADOR</option>
                                    <option value="COMUM">COMUM</option>
                                </select>
                            </div>
                            <div class="col-2 text-center">
                                <button class="btn btn-info custom-radius botao_vertical_linha botao_grande">Pesquisar</button>
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

router_add('cadastro_usuario', function(){
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
 * Rota responsável por pesquisar as informações do usuário do sistema.
 * Esta rota recebe todos os filtros e monta o array de pesquisa e então envia para o back.
 */
router_add('pesquisar_usuario', function(){
    $objeto_usuario = new Usuario();
    $dados = (array) ['filtro' => (array) [], 'ordenacao' => (array) [], 'limite' => (int) 0];

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

router_add('salvar_dados_usuario', function(){
    $objeto_usuario = new Usuario();

    echo json_encode(['status' => (bool) $objeto_usuario->salvar_dados($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});
?>