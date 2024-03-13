<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');
if((!isset ($_SESSION['login']) == true) && (!isset ($_SESSION['senha']) == true)){
    header('location:index.php');
}else{
    $vari = (string) 'tem sessão';
}

//Rua joão inacio de santos, 52 em frente com a magia eventos
// $quantidade_notificacao = (int) contar_notificaoes();
$quantidade_notificacao = (int) 50001;
$versao_sistema = (string) $_SESSION['versao_sistema'];
$retorno = (bool) false;
$nome_usuario = (string) strtoupper($_SESSION['login']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistema de sex shop">
    <meta name="author" content="Rodolfo Fonseca">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <title>Admin Smart</title>
    <link href="assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <link href="assets/extra-libs/jvector/jquery-jvectormap-2.0.2.css" rel="stylesheet" />
    <link href="dist/css/style.min.css" rel="stylesheet">
	<link href="css/alerta_css.css" rel="stylesheet">
	<script type="text/javascript" src="js/alerta.js"></script>
    <script type="text/javascript" src="dist/js/sistema.js?v=<?php echo filemtime('dist/js/sistema.js'); ?>"></script>
    <script type="text/javascript" src="dist/js/padrao.js"></script>
    <script type="text/javascript" src="js/basics.js?v=<?php echo filemtime('js/basics.js'); ?>"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <link href="css/estilo.css?v=<?php echo filemtime('css/estilo.css'); ?>" rel="stylesheet">
</head>

<body>
    <div class="preloader" id="loader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
        <header class="topbar" data-navbarbg="skin6">
            <nav class="navbar top-navbar navbar-expand-md">
                <div class="navbar-header" data-logobg="skin6">
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
                    <div class="navbar-brand">
                        <a href="index.php">
                            <b class="logo-icon">
                                <img src="assets/images/logo-icon.png" alt="homepage" class="dark-logo" />
                                <img src="assets/images/logo-icon.png" alt="homepage" class="light-logo" />
                            </b>
                            <span class="logo-text">
                                <img src="assets/images/logo-text.png" alt="homepage" class="dark-logo" />
                                <img src="assets/images/logo-light-text.png" class="light-logo" alt="homepage" />
                            </span>
                        </a>
                    </div>
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
                </div>
                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <?php
                    //$quantidade = (int) count($retorno);
                    ?>
                    <ul class="navbar-nav float-left mr-auto ml-3 pl-1">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle pl-md-3 position-relative" href="javascript:void(0)" id="bell" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span><i data-feather="bell" class="svg-icon"></i></span>
                                <span class="badge badge-primary notify-no rounded-circle">0</span>
                            </a>
                            <?php
                             if ($quantidade_notificacao > 50000) {
                            ?>
                                <div class="dropdown-menu dropdown-menu-left mailbox animated bounceInDown">
                                    <ul class="list-style-none">
                                        <li>
                                            <div class="message-center notifications position-relative">
                                                    <a href="notificacao.php?rota=Visualizar&id_notificacao=1" class="message-item d-flex align-items-center border-bottom px-3 py-2">
                                                        <div class="btn btn-danger rounded-circle btn-circle"><i data-feather="activity" class="text-white"></i></div>
                                                        <div class="w-75 d-inline-block v-middle pl-2">
                                                            <h6 class="message-title mb-0 mt-1">Atualização de Sistema</h6>
                                                            <span class="font-12 text-nowrap d-block text-muted">Atualização realizada para a versão 1.0</span>
                                                            <span class="font-12 text-nowrap d-block text-muted">10:25</span>
                                                        </div>
                                                    </a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            <?php
                            }
                            ?>
                        </li>
                    </ul>
                    <ul class="navbar-nav float-right">
                        <!-- <li class="nav-item d-none d-md-block">
                            <a class="nav-link" href="javascript:void(0)">
                                <form method="POST" action="alunoPesq.php">
                                    <div class="customize-input">
                                        <input class="form-control custom-shadow custom-radius border-0 bg-white" type="text" placeholder="Pesquisar" aria-label="Pesquisar" name="nomealuno">
                                        <input type="hidden" name="status" value="ATIVO" />
                                        <input type="submit" class="form-control-icon" data-feather="search">
                                    </div>
                                </form>
                            </a>
                        </li> -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="assets/images/users/1.jpg" alt="user" class="rounded-circle" width="40">
                                <span class="ml-2 d-none d-lg-inline-block">
                                    <span class="text-dark"><?php echo $nome_usuario; ?></span>
                                    <i data-feather="chevron-down" class="svg-icon"></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                                <a class="dropdown-item" href="usuario.php"> <i data-feather="user" class="svg-icon mr-2 ml-1"></i>Meu Perfil</a>
                                <a class="dropdown-item" href="#"> <i data-feather="credit-card" class="svg-icon mr-2 ml-1"> </i>caixa</a>
                                <a class="dropdown-item" href="#"><i data-feather="mail" class="svg-icon mr-2 ml-1"></i>Mensagens</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="sistema.php"><i data-feather="settings" class="svg-icon mr-2 ml-1"></i>Configurações da Empresa</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="index.php"><i data-feather="power" class="svg-icon mr-2 ml-1"></i>Sair</a>
                                <div class="dropdown-divider"></div>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <aside class="left-sidebar" data-sidebarbg="skin6">
            <div class="scroll-sidebar" data-sidebarbg="skin6">
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="sidebar-item"> <a class="sidebar-link sidebar-link" href="dashboard.php" aria-expanded="false"><i data-feather="home" class="feather-icon"></i><span class="hide-menu">Dashboard</span></a></li>
                        <li class="list-divider"></li>
                        <li class="sidebar-item"> <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false"><i data-feather="sidebar" class="feather-icon"></i><span class="hide-menu">Documentos</span></a>
                            <ul aria-expanded="false" class="collapse  first-level base-level-line">
                                <li class="sidebar-item"><a href="documentos.php" class="sidebar-link"><span class="hide-menu">Visualizar Documentos</span></a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item"> <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false"><i data-feather="file-text" class="feather-icon"></i><span class="hide-menu">Cadastros</span></a>
                            <ul aria-expanded="false" class="collapse  first-level base-level-line">
                                <li class="sidebar-item"><a href="organizacao.php" class="sidebar-link"><span class="hide-menu">Organização</span></a></li>
                                <li class="sidebar-item"><a href="armario.php" class="sidebar-link"><span class="hide-menu">Armário</span></a></li>
                                <li class="sidebar-item"><a href="prateleira.php" class="sidebar-link"><span class="hide-menu">Prateleira</span></a></li>
                                <li class="sidebar-item"><a href="caixa.php" class="sidebar-link"><span class="hide-menu">caixa</span></a></li>
                                <li class="sidebar-item"><a href="contas_bancarias.php" class="sidebar-link"><span class="hide-menu">Contas Bancárias</span></a></li>
                                <li class="sidebar-item"><a href="tipo_despesa.php" class="sidebar-link"><span class="hide-menu">Tipo Despesa / tipo Contas</span></a></li>
                            </ul>
                        </li>
                        <!-- <li class="sidebar-item"><a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false"><i data-feather="truck" class="feather-icon"></i><span class="hide-menu">Pedidos</span></a>
                            <ul aria-expanded="false" class="collapse first-level base-level-line">
                                <li class="sidebar-item"><a href="pedido.php" class="sidebar-link"><span class="hide-menu">Pedido</span></a></li>
                            </ul>
                        </li> -->
                        <li class="sidebar-item"><a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false"><i data-feather="book-open" class="feather-icon"></i><span class="hide-menu">Contas</span></a>
                            <ul aria-expanded="false" class="collapse first-level base-level-line">
                                <li class="sidebar-item"><a href="contas.php" class="sidebar-link"><span class="hide-menu">Contas</span></a></li>
                            </ul>
                        </li>
                        <li class="sidebar-item"><a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false"><i data-feather="airplay" class="feather-icon"></i><span class="hide-menu">Lançamentos</span></a>
                        <ul aria-expanded="false" class="collapse first-level base-level-line">
                            <li class="sidebar-item"><a href="lancamentos.php" class="sidebar-link"><span class="hide-menu">Corrigir Lançamentos</span></a></li>
                        </ul>
                        <li class="sidebar-item"><a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false"><i data-feather="book-open" class="feather-icon"></i><span class="hide-menu">Relatórios</span></a>
                            <ul aria-expanded="false" class="collapse first-level base-level-line">
                                <li class="sidebar-item"><a href="relatorios_contabeis.php" class="sidebar-link"><span class="hide-menu">Relatórios Contábeis</span></a></li>
                            </ul>
                        </li>
                    </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <div class="page-wrapper">