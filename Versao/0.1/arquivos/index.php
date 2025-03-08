<?php
require_once 'Classes/bancoDeDados.php';
require_once 'modelos/Usuario.php';
require_once 'modelos/Sistema.php';

/**
 * Rota responsável por capturar as informações de login do usuário, e caso o banco de dados do usuário não possui nenhuma empresa cadastrada redirecionar o usuário para o formulário onde o mesmo irá realizar o cadastro de sua empresa.
 */
router_add('index', function(){
  verificar_conexao_internet();
  $retorno_login = (string) (isset($_REQUEST['retorno']) ? (string) $_REQUEST['retorno']: 'true');
  $retorno_dados = model_one('sistema');

  if(empty($retorno_dados) == false){
    $retorno_dados = (bool) true;
  }

  ?>
    <link rel="icon" type="image/png" sizes="16x16" href="imagens/icone_sistema.ico">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link href="css/alerta_css.css" rel="stylesheet"/>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="js/alerta.js"></script>
    <style>
      html {background-color: #56baed;}body {font-family: "Poppins", sans-serif;height: 100vh;}a {color: #92badd;display:inline-block;text-decoration: none;font-weight: 400;}h2 {text-align: center;font-size: 16px;font-weight: 600;text-transform: uppercase;display:inline-block;margin: 40px 8px 10px 8px;color: #cccccc;}.wrapper {display: flex;align-items: center;flex-direction: column;justify-content: center;width: 100%;min-height: 100%;padding: 20px;}#formContent {-webkit-border-radius: 10px 10px 10px 10px;border-radius: 10px 10px 10px 10px;background: #fff;padding: 30px;width: 90%;max-width: 450px;position: relative;padding: 0px;-webkit-box-shadow: 0 30px 60px 0 rgba(0,0,0,0.3);box-shadow: 0 30px 60px 0 rgba(0,0,0,0.3);text-align: center;}#formFooter {background-color: #f6f6f6;border-top: 1px solid #dce8f1;padding: 25px;text-align: center;-webkit-border-radius: 0 0 10px 10px;border-radius: 0 0 10px 10px;}h2.inactive {color: #cccccc;}h2.active {color: #0d0d0d;border-bottom: 2px solid #5fbae9;}input[type=button], input[type=submit], input[type=reset] {background-color: #56baed;border: none;color: white;padding: 15px 80px;text-align: center;text-decoration: none;display: inline-block;text-transform: uppercase;font-size: 13px;-webkit-box-shadow: 0 10px 30px 0 rgba(95,186,233,0.4);box-shadow: 0 10px 30px 0 rgba(95,186,233,0.4);-webkit-border-radius: 5px 5px 5px 5px;border-radius: 5px 5px 5px 5px;margin: 5px 20px 40px 20px;-webkit-transition: all 0.3s ease-in-out;-moz-transition: all 0.3s ease-in-out;-ms-transition: all 0.3s ease-in-out;-o-transition: all 0.3s ease-in-out;transition: all 0.3s ease-in-out;}input[type=button]:hover, input[type=submit]:hover, input[type=reset]:hover {background-color: #39ace7;}input[type=button]:active, input[type=submit]:active, input[type=reset]:active {-moz-transform: scale(0.95);-webkit-transform: scale(0.95);-o-transform: scale(0.95);-ms-transform: scale(0.95);transform: scale(0.95);}input[type=text] {background-color: #f6f6f6;border: none;color: #0d0d0d;padding: 15px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 5px;width: 85%;border: 2px solid #f6f6f6;-webkit-transition: all 0.5s ease-in-out;-moz-transition: all 0.5s ease-in-out;-ms-transition: all 0.5s ease-in-out;-o-transition: all 0.5s ease-in-out;transition: all 0.5s ease-in-out;-webkit-border-radius: 5px 5px 5px 5px;border-radius: 5px 5px 5px 5px;}input[type=text]:focus {background-color: #fff;border-bottom: 2px solid #5fbae9;}input[type=text]:placeholder {color: #cccccc;}input[type=password] {background-color: #f6f6f6;border: none;color: #0d0d0d;padding: 15px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;margin: 5px;width: 85%;border: 2px solid #f6f6f6;-webkit-transition: all 0.5s ease-in-out;-moz-transition: all 0.5s ease-in-out;-ms-transition: all 0.5s ease-in-out;-o-transition: all 0.5s ease-in-out;transition: all 0.5s ease-in-out;-webkit-border-radius: 5px 5px 5px 5px;border-radius: 5px 5px 5px 5px;}input[type=password]:focus {background-color: #fff;border-bottom: 2px solid #5fbae9;}input[type=password]:placeholder {color: #cccccc;}.fadeInDown {-webkit-animation-name: fadeInDown;animation-name: fadeInDown;-webkit-animation-duration: 1s;animation-duration: 1s;-webkit-animation-fill-mode: both;animation-fill-mode: both;}@-webkit-keyframes fadeInDown {0% {opacity: 0;-webkit-transform: translate3d(0, -100%, 0);transform: translate3d(0, -100%, 0);}100% {opacity: 1;-webkit-transform: none;transform: none;}}@keyframes fadeInDown {0% {opacity: 0;-webkit-transform: translate3d(0, -100%, 0);transform: translate3d(0, -100%, 0);}100% {opacity: 1;-webkit-transform: none;transform: none;}}@-webkit-keyframes fadeIn {from {opacity:0;}to {opacity:1;}}@-moz-keyframes fadeIn {from {opacity:0;}to {opacity:1;}}@keyframes fadeIn {from {opacity:0;}to {opacity:1;}}.fadeIn {opacity:0;-webkit-animation:fadeIn ease-in 1;-moz-animation:fadeIn ease-in 1;animation:fadeIn ease-in 1;-webkit-animation-fill-mode:forwards;-moz-animation-fill-mode:forwards;animation-fill-mode:forwards;-webkit-animation-duration:1s;-moz-animation-duration:1s;animation-duration:1s;}.fadeIn.first {-webkit-animation-delay: 0.4s;-moz-animation-delay: 0.4s;animation-delay: 0.4s;}.fadeIn.second {-webkit-animation-delay: 0.6s;-moz-animation-delay: 0.6s;animation-delay: 0.6s;}.fadeIn.third {-webkit-animation-delay: 0.8s;-moz-animation-delay: 0.8s;animation-delay: 0.8s;}.fadeIn.fourth {-webkit-animation-delay: 1s;-moz-animation-delay: 1s;animation-delay: 1s;}.underlineHover:after {display: block;left: 0;bottom: -10px;width: 0;height: 2px;background-color: #56baed;content: "";transition: width 0.2s;}.underlineHover:hover {color: #0d0d0d;}.underlineHover:hover:after{width: 100%;}*:focus {outline: none;}#icon {width:60%;}
    </style>
    <script>
      let retorno_login = <?php echo $retorno_login; ?>;
    </script>
    <div class="wrapper fadeInDown">
      <div id="formContent">
        <form method="POST" action="index.php">
          <input type="hidden" id="rota" name="rota" value="validar_login"/>
          <input type="text" id="login" class="fadeIn second" name="login" placeholder="login"/>
          <input type="password" id="password" class="fadeIn third" name="senha_usuario" placeholder="senha"/>
          <input type="submit" class="fadeIn fourth" value="Entrar no Sistema">
        </form>

        <?php
          if($retorno_dados == false){
            ?>
              <div id="formFooter">
                <a class="underlineHover" href="cadastro_empresa.php">Primeiro Acesso!</a>
              </div>
            <?php
          }
        ?>
      </div>
    </div>
    <script>
      window.onload = function(){
        if(retorno_login == false){
          Swal.fire({title: "ERRO NO LOGIN", text: "Login ou senha incorretos",icon: "error"});
        }
      }
    </script>
    <?php
    exit;
});

/**
 * Rota responsável por validar as informações do usuário e redirecionar ele para o local correto caso todas as informações estejam corretas.
 */
router_add('validar_login', function(){
  $objeto_usuario = new Usuario();
  $usuario = (array) $objeto_usuario->login_sistema($_REQUEST);

  if($usuario != []){
    session_start();
    $_SESSION['id_usuario'] = (int) $usuario['id_usuario'];
    $_SESSION['login'] = (string) $usuario['login'];
    $_SESSION['senha'] = (string) $usuario['senha_usuario'];
    $_SESSION['tipo_usuario'] = (string) $usuario['tipo'];
    $_SESSION['versao_sistema'] = (string) 'v0.0';
    $_SESSION['id_empresa'] = (int) $usuario['id_empresa'];
    $_SESSION['id_sistema'] = (int) 0;
    $_SESSION['versao_sistema'] = (string) '0.00';

    $objeto_sistema = new Sistema();
    $filtro_sistema['filtro'] = (array) ['id_empresa', '===', (int) $usuario['id_empresa']];
    $retorno_sistema = (array) $objeto_sistema->pesquisar($filtro_sistema);

    if(empty($retorno_sistema) == false){
      $_SESSION['id_sistema'] = (int) $retorno_sistema['id_sistema'];
      $_SESSION['versao_sistema'] = (string) $retorno_sistema['versao_sistema'];
      
      header('location:dashboard.php');
    }else{
      header('location:index.php?retorno=false');
    }
  }else{
    header('location:index.php?retorno=false');
  }
});

/**
 * Rota responsável por criar um novo código de barras e retornar para a rota que está realizando a requisição.
 */
router_add('buscar_codigo_barras', function(){
  echo json_encode((array) ['codigo_barras' => (string) codigo_barras()], JSON_UNESCAPED_UNICODE);
  exit;
});

?>