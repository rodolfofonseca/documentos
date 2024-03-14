<?php
require_once 'Classes/bancoDedados.php';
require_once 'modelos/Usuario.php';

//@note index
router_add('index', function(){
    require_once 'includes/head.php';

    $read_only = (string) 'true';

    if($tipo_usuario == 'ADMINISTRADOR'){
        $read_only = (string) 'false';
    }

    ?>
    <script>
        function pesquisar_informacoes_usuario(){
            let codigo_usuario = sistema.int(document.querySelector('#codigo_usuario').value);
            sistema.request.post('/usuario.php', {'rota': 'pesquisar_informacoes_usuario', 'codigo_usuario': codigo_usuario}, function(retorno){
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
                sistema.request.post('/usuario.php', {'rota': 'salvar_dados_usuario', 'codigo_usuario': codigo_usuario, 'nome_usuario': nome_usuario, 'login': login, 'senha_usuario': senha}, function(retorno){
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

//@audit pesquisar_informacoes_usuario
router_add('pesquisar_informacoes_usuario', function(){
    $id_usuario = (int) (isset($_REQUEST['codigo_usuario']) ? (int) intval($_REQUEST['codigo_usuario'], 10):0);
    $objeto_usuario = new Usuario();
    $filtro = (array) [];
    $dados = (array) [];

    array_push($filtro, ['id_usuario', '===', (int) $id_usuario]);

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