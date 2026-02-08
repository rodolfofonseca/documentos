<?php
require_once 'Classes/bancoDedados.php';
require_once 'modelos/Empresa.php';
require_once 'modelos/Usuario.php';
require_once 'modelos/Sistema.php';

router_add('index', function () {
    require_once 'includes/head_relatorio.php';
    ?>
    <script>
        /**
         * Função responsável por realizar a pesquisa de todas as empresas cadastradas no sistema e apresentar ao usuário.
         */
        function pesquisar_empresas() {
            sistema.request.post('/cadastro_empresa.php', {
                'rota': 'pesquisar_empresas'
            }, function(retorno) {
                let tabela = document.querySelector('#tabela_empresa tbody');
                tabela = sistema.remover_linha_tabela(tabela);

                let empresa = retorno.dados;
                let tamanho_retorno = empresa.length;

                if (tamanho_retorno < 1) {
                    let linha = document.createElement('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA EMPRESA ENCONTRADA!', 'inner', true, 5));
                    tabela.appendChild(linha);
                } else {
                    sistema.each(empresa, function(index, empresa) {
                        let linha = document.createElement('tr');

                        linha.appendChild(sistema.gerar_td(['text-left'], empresa.nome_empresa, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], empresa.status, 'inner'));
                        linha.appendChild(sistema.gerar_td(['center-center'], sistema.gerar_botao('botao_cadastrar_usuario_' + empresa._id.$oid, 'CADASTRO USUÁRIO', ['btn', 'btn-success'], function abrir_cadastro_usuario() {
                            abril_modal_cadastro_usuario(empresa._id.$oid, empresa.nome_empresa, empresa.status);
                        }), 'append'));

                        tabela.appendChild(linha);
                    });
                }
            }, false);
        }

        /** 
         * Função responsável por abrir o modal de cadatro de usuários.
         */
        function abril_modal_cadastro_usuario(id_empresa, nome_empresa, status) {
            window.location.href = sistema.url('/cadastro_empresa.php', {
                'rota': 'cadastro_usuario',
                'id_empresa': id_empresa,
                'nome_empresa': nome_empresa,
                'status': status,
            });
        }

        /** 
         * Função responsável por capturar as informações preenxidas pelo usuário no formulário e enviar para a dao, para que a mesma possa realizar a validação das informações e fazer a pensistência na base de dados.
         */
        function salvar_dados_empresa() {
            let nome_empresa = document.querySelector('#nome_empresa').value;
            let cnpj = document.querySelector('#cnpj').value;
            let status = document.querySelector('#status').value;
            
            sistema.request.post('/cadastro_empresa.php', {'rota': 'salvar_dados_empresa', 'nome_empresa': nome_empresa, 'cnpj': cnpj, 'status': status}, function(retorno) {
                validar_retorno(retorno, '/cadastro_empresa.php');
            });
        }

        /** 
         * Função responsável por retornar a página de login de usuário
         */
        function retornar(){
            window.location.href = sistema.url('/index.php', {'rota': 'index'});
        }

        function validar_cnpj() {
            validarCpfCnpj(document.getElementById('cnpj'), true);
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-title text-center">
                        <div class="row">
                            <div class="col-12">
                                <h1>cadastro de empresa</h1>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3 text-center">
                                <button class="btn btn-info botao_vertical_linha custom-radius" data-toggle="modal" data-target="#modal_cadastro_empresa">CADASTRO DE EMPRESA</button>
                            </div>
                            <div class="col-3 text-center">
                                <button class="btn btn-secondary botao_vertical_linha custom-radius" onclick="retornar();">RETORNAR</button>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="tabela_empresa">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">NOME EMPRESA</th>
                                                <th scope="col">STATUS</th>
                                                <th scope="col">CADASTRO USUÁRIO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="3" class="text-center">NENHUMA EMPRESA ENCONTRADA!</td>
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

    <!-- Modal de cadastro de empresa -->
    <div class="modal fade" id="modal_cadastro_empresa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cadastro de Empresa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="status" name="status" value="ATIVO" />
                        <div class="col-6 text-center">
                            <label class="text" for="nome_empresa">Nome Empresa</label>
                            <input type="text" class="form-control custom-radius text-uppercase" id="nome_empresa" name="nome_empresa" placeholder="Nome Empresa" />
                        </div>
                        <div class="col-6 text-center">
                            <label class="text" for="cnpj">CNPJ</label>
                            <input type="text" class="form-control custom-radius text-uppercase" id="cnpj" name="cnpj" placeholder="CNPJ" onblur="validar_cnpj()" />
                        </div>
                    </div>
                    <br />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger custom-radius" data-dismiss="modal" id="botao_fechar_modal_empresa">Fechar</button>
                    <button type="button" class="btn btn-primary custom-radius" onclick="salvar_dados_empresa();">Salvar</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.onload = function() {
            pesquisar_empresas();
        }
    </script>
    <div class="card-body">
    <?php

    require_once 'includes/footer_relatorio.php';

    exit;
});

/**
 * Rota responsável por listar e cadastrar usuários para as empresas cadastradas no sistema.
 */
router_add('cadastro_usuario', function () {
    $id_empresa = (isset($_REQUEST['id_empresa']) ? $_REQUEST['id_empresa'] : '');
    $nome_empresa = (isset($_REQUEST['nome_empresa']) ? (string) $_REQUEST['nome_empresa'] : '');
    $status = (isset($_REQUEST['status']) ? (string) $_REQUEST['status'] : '');

    require_once 'includes/head_relatorio.php';
    ?>
        <script>
            /**
             * Função responsável por pesquisar os usuários cadastrados para a empresa e colocar a informação na tabela
             */
            function pesquisar_usuario() {
                let codigo_empresa = document.querySelector('#id_empresa').value;

                sistema.request.post('/cadastro_empresa.php', {
                    'rota': 'pesquisar_usuario',
                    'codigo_empresa': codigo_empresa
                }, function(retorno) {

                    console.log(retorno);

                    let tabela = document.querySelector('#tabela_dados_usuario tbody');
                    tabela = sistema.remover_linha_tabela(tabela);

                    let usuarios = retorno.dados;
                    let tamanho_retorno = sistema.int(usuarios.length);

                    if (tamanho_retorno < 1) {
                        let linha = document.createElement('tr');
                        linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM USUÁRIO ENCONTRADO!', 'inner', true, 5));
                        tabela.appendChild(linha);
                    } else {
                        sistema.each(retorno.dados, function(index, usuario) {
                            let linha = document.createElement('tr');

                            linha.appendChild(sistema.gerar_td(['text-left'], usuario.nome_usuario, 'inner'));
                            linha.appendChild(sistema.gerar_td(['text-left'], usuario.login_usuario, 'inner'));
                            linha.appendChild(sistema.gerar_td(['text-left'], usuario.tipo_usuario, 'inner'));
                            linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('alterar_informacao_usuario_' + usuario._id.$oid, 'ALTERAR', ['btn', 'btn-success'], function retornar_dados() {
                                console.log(usuario._id.$oid);
                                colocar_dados_input(usuario._id.$oid, usuario.nome_usuario, usuario.login_usuario, usuario.tipo_usuario);
                            }), 'append'));

                            tabela.appendChild(linha);
                        });
                    }
                }, false);
            }

            /**
             * Função responsável por colocar as informações do usuário nos inputs para alteração
             */
            function colocar_dados_input(id_usuario, nome_usuario, login, tipo) {
                document.querySelector('#id_usuario').value = id_usuario;
                document.querySelector('#nome_usuario').value = nome_usuario;
                document.querySelector('#login').value = login;
                document.querySelector('#tipo_usuario').value = tipo;
            }

            /**
             * Função responsável por enviar os dados para a modelos para a mesma realizar o cadatro ou alteração das informações do usuário.
             */
            function enviar_dados() {
                let codigo_usuario = document.querySelector('#id_usuario').value;
                let codigo_empresa = document.querySelector('#id_empresa').value;
                let nome_usuario = document.querySelector('#nome_usuario').value;
                let login = document.querySelector('#login').value;
                let senha_usuario = document.querySelector('#senha_usuario').value;
                let tipo = document.querySelector('#tipo_usuario').value;

                sistema.request.post('/cadastro_empresa.php', {'rota': 'salvar_dados_usuario', 'codigo_usuario': codigo_usuario, 'codigo_empresa': codigo_empresa, 'nome_usuario': nome_usuario, 'login': login, 'senha_usuario': senha_usuario, 'tipo': tipo}, function(retorno) {
                    sistema.verificar_status(retorno.status);

                    pesquisar_usuario();
                });
            }

            function retornar(){
                window.location.href = sistema.url('/cadastro_empresa.php', {'rota':'index'});
            }
        </script>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-title text-center">
                            <div class="row">
                                <div class="col-12">
                                    <h1>DADOS EMPRESA</h1>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-4 text-center">
                                    <label>CÓDIGO</label>
                                    <input type="text" class="form-control custom-radius text-center" name="id_empresa" id="id_empresa" value="<?php echo $id_empresa; ?>" readonly />
                                </div>
                                <div class="col-4 text-center">
                                    <label>NOME EMPRESA</label>
                                    <input type="text" class="form-control custom-radius text-center" name="nome_empresa" id="nome_empresa" value="<?php echo $nome_empresa; ?>" readonly />
                                </div>
                                <div class="col-4 text-center">
                                    <label>STATUS</label>
                                    <input type="text" class="form-control custom-radius text-center" name="status_empresa" id="status_empresa" value="<?php echo $status; ?>" readonly />
                                </div>
                            </div>
                            <br />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-title text-center">
                            <div class="row">
                                <div class="col-12">
                                    <h1>CADASTRO USUÁRIOS</h1>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-1 text-center">
                                    <label>ID USUÁRIO</label>
                                    <input type="text" class="form-control custom-radius text-center" name="id_usuario" id="id_usuario" value="" readonly />
                                </div>
                                <div class="col-5 text-center">
                                    <label>NOME USUÁRIO</label>
                                    <input type="text" class="form-control custom-radius text-uppercase" name="nome_usuario" id="nome_usuario" placeholder="NOME USUÁRIO" />
                                </div>
                                <div class="col-2 text-center">
                                    <label>LOGIN</label>
                                    <input type="text" class="form-control custom-radius" name="login" id="login" placeholder="LOGIN" />
                                </div>
                                <div class="col-2 text-center">
                                    <label>SENHA</label>
                                    <input type="password" class="form-control custom-radius" name="senha_usuario" id="senha_usuario" placeholder="SENHA USUÁRIO" />
                                </div>
                                <div class="col-2 text-center">
                                    <label>TIPO USUÁRIO</label>
                                    <select class="form-control custom-radius" id="tipo_usuario" name="tipo_usuario">
                                        <option value="ADMINISTRADOR">ADMINISTRADOR</option>
                                        <option value="COMUM">COMUM</option>
                                    </select>
                                </div>
                            </div>
                            <br />
                            <div class="row">
                                <div class="col-3">
                                    <button class="btn btn-info custom-radius" onclick="enviar_dados();">SALVAR</button>
                                </div>
                                <div class="col-3">
                                    <button class="btn btn-secondary custom-radius" onclick="retornar();">VOLTAR</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="tabela_dados_usuario">
                            <thead class="bg-info text-white">
                                <tr class="text-center">
                                    <th scope="col">NOME</th>
                                    <th scope="col">LOGIN</th>
                                    <th scope="col">TIPO</th>
                                    <th scope="col">ALTERAR</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center">NENHUM USUÁRIO ENCONTRADO!</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.onload = function() {
            pesquisar_usuario();
        }
    </script>
<?php
    require_once 'includes/footer_relatorio.php';
    exit;
});

/**
 * Rota responsável por criar o objeto Empresa, montar o filtro de pesquisa e realizar a pesquisa no banco de dados.
 */
router_add('pesquisar_empresas', function () {
    $objeto_empresa = new Empresa();
    $filtro = (array) ['filtro' => (array) [], 'ordenacao' => (array) ['nome_empresa' => (bool) false], 'limite' => (int) 0];

    echo json_encode((array) ['dados' => (array) $objeto_empresa->pesquisar_todos($filtro)], JSON_UNESCAPED_UNICODE);
    exit;
});

/**
 * Rota responsável por realizar o cadastro de empresas no banco de dados juntamente com o cadastro de novos sistemas.
 */
router_add('salvar_dados_empresa', function () {
    $objeto_empresa = new Empresa();

    $retorno_cadastro_empresa = (bool) $objeto_empresa->salvar_dados($_REQUEST);

    echo json_encode((array) ['status' => (bool) $retorno_cadastro_empresa], JSON_UNESCAPED_UNICODE);
    exit;
});

/**
 * Rota responsável por realizar a pesquisa do usuário pelo código da empresa.
 */
router_add('pesquisar_usuario', function () {
    $objeto_usuario = new Usuario();
    $id_empresa = (string) (isset($_REQUEST['codigo_empresa']) ? $_REQUEST['codigo_empresa'] : '');

    $filtro['filtro'] = (array) ['empresa', '===', convert_id($id_empresa)];
    $filtro['ordenacao'] = (array) ['login' => false];
    $filtro['limite'] = (int) 0;

    echo json_encode((array) ['dados' => (array) $objeto_usuario->pesquisar_todos($filtro)], JSON_UNESCAPED_UNICODE);
    exit;
});

/**
 * Rota responsável por enviar os dados para a modal para realizar o cadastro de novos usuários
 */
router_add('salvar_dados_usuario', function () {
    $objeto_usuario = new Usuario();
    echo json_encode((array) ['status' => (bool) $objeto_usuario->salvar_dados($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});

/**
 * Rota responsável por pesquisar o usuário através do seu identificador 
 */
router_add('pesquisar_usuario_id', function () {
    $id_usuario = (isset($_REQUEST['codigo_usuario']) ? (string) $_REQUEST['codigo_usuario'] : '');
    $objeto_usuario = new Usuario();
    $filtro = (array) ['filtro' => (array) ['id_usuario', '===', convert_id($id_usuario)]];

    echo json_encode((array) $objeto_usuario->pesquisar($filtro), JSON_UNESCAPED_UNICODE);
    exit;
});
?>