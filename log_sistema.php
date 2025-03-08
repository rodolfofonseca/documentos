<?php 
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/LogSistema.php';
require_once 'Modelos/Preferencia.php';

//@audit index
/**
 * Rota que contém as informações para pesquisa de logs no sistema
 */
router_add('index', function(){
    require_once 'includes/head.php';

    $objeto_preferencia = new Preferencia();

    $usuario_preferencia_pesquisar_log_automaticamente = (string) 'CHECKED';

    //MONTANDO O FILTRO DE PESQUISA PARA SABER SE O USUÁRIO QUE ESTÁ LOGADO NO SISTEMA PREFERE PESQUISAR OS LOGS AUTOMATICAMENTE AO ABRIR A PÁGINA
    $filtro_pesquisa = (array) ['and' => (array) [['id_sistema', '===', (int) intval(CODIGO_SISTEMA, 10)], ['id_usuario', '===', (int) intval(CODIGO_USUARIO, 10)], ['nome_preferencia', '===', (string) 'PESQUISAR_LOG_AUTOMATICAMENTE']]];
    $retorno_pesquisa_preferencia = (array) $objeto_preferencia->pesquisar((array) ['filtro' => (array) $filtro_pesquisa]);

    if(empty($retorno_pesquisa_preferencia) == true){
        $usuario_preferencia_pesquisar_log_automaticamente = (string) '';
    }

    ?>
    <script>
        const CODIGO_EMPRESA = <?php echo CODIGO_EMPRESA; ?>;
        const CODIGO_USUARIO = <?php echo CODIGO_USUARIO; ?>;
        const NOME_USUARIO = "<?php echo NOME_USUARIO; ?>";
        const CODIGO_SISTEMA = "<?php echo CODIGO_SISTEMA; ?>";
        const PESQUISAR_LOG_AUTOMATICAMENTE = "<?php echo $usuario_preferencia_pesquisar_log_automaticamente; ?>";

        /** 
         * Função responsável por pesquisar os usuários cadastrados no sistema
        */
        function pesquisar_usuario(){
            sistema.request.post('/usuario.php', {'rota': 'pesquisar_usuario'}, function(retorno){
                let tamanho_retorno = sistema.int(retorno.dados.length);
                let select_usuario = document.querySelector('#usuario');
                let retorno_usuario = retorno.dados;

                for(let contador = 0; contador < tamanho_retorno; contador++){
                    select_usuario.options[select_usuario.options.length] = new Option(retorno_usuario[contador]['login'], retorno_usuario[contador]['login']);
                }                
            });
        }

        /** 
         * Função responsável por pesquisar os logs que estão cadastrados no sistema
        */
        function pesquisar_log(){
            let codigo_barras = sistema.string(document.querySelector('#codigo_barras').value);
            let modulo = sistema.string(document.querySelector('#modulo').value);
            let usuario = sistema.string(document.querySelector('#usuario').value);
            let data_inicial = sistema.string(document.querySelector('#data_inicial').value);
            let data_final = sistema.string(document.querySelector('#data_final').value);

            sistema.request.post('/log_sistema.php', {'rota': 'pesquisar_log', 'codigo_empresa': CODIGO_EMPRESA, 'codigo_barras': codigo_barras, 'modulo': modulo, 'usuario': usuario, 'data_inicial': data_inicial, 'data_final': data_final}, function(retorno){
                let retorno_log = retorno.dados;
                let tamanho_retorno = retorno_log.length;
                let tabela = document.querySelector('#tabela_logs tbody');
                let tamanho_tabela = tabela.rows.length;

                if(tamanho_tabela > 0){
                    tabela = sistema.remover_linha_tabela(tabela);
                }

                if(tamanho_retorno < 1){
                    let linha = document.querySelector('tr');
                    linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM LOG ENCONTRADO!', 'inner', true, 4));
                }else{
                    sistema.each(retorno_log, function(contador, log){
                        
                        let linha = document.createElement('tr');
                        
                        linha.appendChild(sistema.gerar_td(['text-center'], log.usuario, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], log.modulo, 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-center'], retornar_data(log.data_log), 'inner'));
                        linha.appendChild(sistema.gerar_td(['text-left'], log.descricao, 'inner'));

                        tabela.appendChild(linha);
                    });
                }
            }, false);
        }

        /** 
         * Função responsável por chamar a rota que altera no banco de dados a preferência do usuário quanto a pesquisar os logs de forma automática ou não.
        */
        function alterar_preferencia_pesquisar_log_automaticamente(){
            let check_pesquisar_log_automaticamente = document.querySelector('#pesquisar_log_automaticamente');
            let preferencia = '';

            if(check_pesquisar_log_automaticamente.checked == true){
                preferencia = 'CHECKED';
            }else{
                preferencia = '';
            }

            sistema.request.post('/preferencia.php', {'rota': 'salvar_preferencia_usuario', 'codigo_sistema': CODIGO_SISTEMA, 'codigo_usuario': CODIGO_USUARIO, 'nome_preferencia': 'PESQUISAR_LOG_AUTOMATICAMENTE', 'preferencia': preferencia}, function(retorno){}, false);
        }
        
        /** 
         * Função responsável por chamar a rota que gera o arquivo .xlsx cadastra no banco de dados e retorna para o usuário escolher se deseja realizar o download ou não.
        */
        function salvar_log_xlsx(){
            let codigo_barras = sistema.string(document.querySelector('#codigo_barras').value);
            let modulo = sistema.string(document.querySelector('#modulo').value);
            let usuario = sistema.string(document.querySelector('#usuario').value);
            let data_inicial = sistema.string(document.querySelector('#data_inicial').value);
            let data_final = sistema.string(document.querySelector('#data_final').value);

            sistema.request.post('/log_sistema.php', {'rota': 'log_xlsx', 'codigo_empresa': CODIGO_EMPRESA, 'codigo_usuario': CODIGO_USUARIO, 'codigo_barras': codigo_barras, 'modulo': modulo, 'usuario': usuario, 'data_inicial': data_inicial, 'data_final': data_final}, function(retorno){
                let retorno_operacao = retorno.status;
                let endereco_configuracao = '';

                if(retorno_operacao == false){
                    endereco_configuracao = sistema.url('/sistema.php', {'rota': 'index'});

                    if(retorno.tipo_erro == 'ERRO_CONFIGURACAO'){
                        Swal.fire({title: retorno.titulo, message: retorno.mensagem, icon: retorno.icone, footer: '<a href="'+endereco_configuracao+'">Confgurar agora!</a>'});
                    }else if(retorno.tipo_erro == 'ERRO_PESQUISA' || retorno.tipo_erro == 'ERRO_GERAR_DOCUMENTO'){
                        Swal.fire({title: retorno.titulo, message: retorno.mensagem, icon: retorno.icone});
                    }

                }else{
                    if(retorno.tipo_erro == 'SUCESSO_REGISTRO_LOG'){
                        endereco_configuracao = sistema.url('/documentos.php', {'rota': 'baixar_documento', 'endereco': retorno.endereco_documento, 'codigo_documento':retorno.codigo_documento});
                        Swal.fire({title: retorno.titulo, message: retorno.mensagem, icon: retorno.icone, footer: '<a href="'+endereco_configuracao+'">Baixar Arquivo Agora!</a>'});
                    }
                }
            });
        }
        
        /** 
         * Função responsável por chamar a rota que gera o arquivo de log no formato .PDF e retorna o arquivo para o usuário escolher se deseja realizar o download do arquivo ou não.
        */
        function salvar_log_pdf(){
            let codigo_barras = sistema.string(document.querySelector('#codigo_barras').value);
            let modulo = sistema.string(document.querySelector('#modulo').value);
            let usuario = sistema.string(document.querySelector('#usuario').value);
            let data_inicial = sistema.string(document.querySelector('#data_inicial').value);
            let data_final = sistema.string(document.querySelector('#data_final').value);

            sistema.request.post('/log_sistema.php', {'rota': 'log_pdf', 'codigo_empresa': CODIGO_EMPRESA, 'codigo_usuario': CODIGO_USUARIO, 'codigo_barras': codigo_barras, 'modulo': modulo, 'usuario': usuario, 'data_inicial': data_inicial, 'data_final': data_final}, function(retorno){
                let retorno_operacao = retorno.status;
                let endereco_configuracao = '';

                if(retorno_operacao == false){
                    endereco_configuracao = sistema.url('/sistema.php', {'rota': 'index'});

                    if(retorno.tipo_erro == 'ERRO_CONFIGURACAO'){
                        Swal.fire({title: retorno.titulo, message: retorno.mensagem, icon: retorno.icone, footer: '<a href="'+endereco_configuracao+'">Confgurar agora!</a>'});
                    }else if(retorno.tipo_erro == 'ERRO_PESQUISA' || retorno.tipo_erro == 'ERRO_GERAR_DOCUMENTO'){
                        Swal.fire({title: retorno.titulo, message: retorno.mensagem, icon: retorno.icone});
                    }

                }else{
                    if(retorno.tipo_erro == 'SUCESSO_REGISTRO_LOG'){
                        endereco_configuracao = sistema.url('/documentos.php', {'rota': 'baixar_documento', 'endereco': retorno.endereco_documento, 'codigo_documento':retorno.codigo_documento});
                        Swal.fire({title: retorno.titulo, message: retorno.mensagem, icon: retorno.icone, footer: '<a href="'+endereco_configuracao+'">Baixar Arquivo Agora!</a>'});
                    }
                }
            });
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Log Do Sistema</h4>
                        <br/>
                        <div class="row">
                            <div class="col-2 text-center">
                                <label for="codigo_barras">Código de Barras</label>
                                <input type="text" class="form-control custom-radius" id="codigo_barras" sistema-mask="codigo" placeholder="Código Barras" maxlength="13" onkeyup="pesquisar_log();" />
                            </div>
                            <div class="col-2 text-center">
                                <label for="modulo">Módulo</label>
                                <select class="form-control custom-radius" id="modulo">
                                    <option value="TODOS">TODOS</option>
                                    <option value="ORGANIZACAO">ORGANIZACAO</option>
                                    <option value="ARMARIO">ARMÁRIO</option>
                                    <option value="PRATELEIRA">PRATELEIRA</option>
                                    <option value="CAIXA">CAIXA</option>
                                    <option value="DOCUMENTO">DOCUMENTO</option>
                                </select>
                            </div>
                            <div class="col-2 text-center">
                                <label for="usuario">Usuário</label>
                                <select class="form-control custom-radius" id="usuario">
                                    <option value="TODOS">TODOS</option>
                                </select>
                            </div>
                            <div class="col-3 text-center">
                                <label for="data_inicial">Data Inicial</label>
                                <input type="date" class="form-control custom-radius" id="data_inicial"/>
                            </div>
                            <div class="col-3 text-center">
                                <label for="data_final">Data Final</label>
                                <input type="date" class="form-control custom-radius" id="data_final"/>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-2 text-center">
                                <select class="form-control custom-radius" id="limite_retorno">
                                    <option value="25">25 LOGS</option>
                                    <option value="50">50 LOGS</option>
                                    <option value="75">75 LOGS</option>
                                    <option value="100">100 LOGS</option>
                                </select>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="push-7 col-5 text-center">
                                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                                    <button type="button" class="btn btn-info custom-radius botao_grande btn-lg" onclick="pesquisar_log();">PESQUISAR LOG</button>
                                    <button type="button" class="btn btn-outline-success custom-radius botao_grande btn-lg" onclick="salvar_log_pdf();">SALVAR (.PDF)</button>
                                    <button type="button" class="btn btn-outline-success custom-radius botao_grande btn-lg" onclick="salvar_log_xlsx();">SALVAR (.XLSX)</button>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-secondary dropdown-toggle custom-radius botao_grande btn-lg" data-toggle="dropdown" aria-expanded="false"> PREFERÊNCIAS </button>
                                        <div class="dropdown-menu">
                                            <div class="dropdown-item">
                                                <input class="form-check-item" type="checkbox" role="switch" id="pesquisar_log_automaticamente" <?php echo $usuario_preferencia_pesquisar_log_automaticamente; ?> onclick="alterar_preferencia_pesquisar_log_automaticamente();"/>
                                                <label class="form-check-label" for="pesquisar_log_automaticamente">Pesquisar log Automáticamente</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped"  id="tabela_logs">
                                        <thead class="bg-info text-white">
                                            <tr class="text-center">
                                                <th scope="col">USUÁRIO</th>
                                                <th scope="col">MODULO</th>
                                                <th scope="col">DATA</th>
                                                <th scope="col">DESCRIÇÃO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="4" class="text-center">UTILIZE OS FILTROS E FACILITE SUA PESQUISA!</td>
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
        window.onload = function(){
            validar_acesso_administrador('<?php echo TIPO_USUARIO; ?>');

            pesquisar_usuario();

            if(PESQUISAR_LOG_AUTOMATICAMENTE == 'CHECKED'){
                pesquisar_log();
            }
        }
    </script>
    <?php
    require_once 'includes/footer.php';
    exit;
});

//@note pesqusar_log
/**
 * Rota responsável por montar o filtro de pesquisa de log e retornar para o front.
 */
router_add('pesquisar_log', function(){
    $objeto_log = new LogSistema();

    $id_empresa = (int) (isset($_REQUEST['codigo_empresa']) ? (int) intval($_REQUEST['codigo_empresa'], 10):0);
    $codigo_barras = (string) (isset($_REQUEST['codigo_barras']) ? (string) $_REQUEST['codigo_barras']:'');
    $modulo = (string) (isset($_REQUEST['modulo']) ? (string) $_REQUEST['modulo']:'TODOS');
    $usuario = (string) (isset($_REQUEST['usuario']) ? (string) $_REQUEST['usuario']:'');
    $data_inicial = (string) (isset($_REQUEST['data_inicial']) ? (string) $_REQUEST['data_inicial']: '');
    $data_final = (string) (isset($_REQUEST['data_final']) ? (string) $_REQUEST['data_final']:'');

    $filtro_pesquisa = (array) [];
    $retorno_pesquisa = (array) [];
    $retorno = (array) [];

    if($id_empresa != 0){
        array_push($filtro_pesquisa, (array) ['id_empresa', '===', (int) $id_empresa]);
    }

    if($codigo_barras != ''){
        array_push($filtro_pesquisa, (array) ['codigo_barras', '===', (string) $codigo_barras]);
    }

    if($modulo != 'TODOS'){
        array_push($filtro_pesquisa, (array) ['modulo', '===', (string) $modulo]);
    }

    if($usuario != 'TODOS'){
        array_push($filtro_pesquisa, (array) ['usuario', '===', (string) $usuario]);
    }

    if($data_inicial != ''){
        array_push($filtro_pesquisa, (array) ['data_log', '>=', model_date($data_inicial, '00:00:00')]);
    }

    if($data_final != ''){
        array_push($filtro_pesquisa, (array) ['data_log', '<=', model_date($data_final, '23:59:59')]);
    }

    $retorno_pesquisa = (array) $objeto_log->pesquisar_todos((array) ['filtro' => (array) ['and' => (array) $filtro_pesquisa], 'ordenacao' => (array) [], 'limite' => (int) 10]);

    echo json_encode((array) ['dados' => (array) $retorno_pesquisa], JSON_UNESCAPED_UNICODE);
    exit;
});

//@note log_xlsx
/**
 * Rota responsável por pesquisar e gerar o arquivo .xlsx e retorna o arquivo para o front end do sistema para realizar o download
 */
router_add('log_xlsx', function(){
    $objeto_log = new LogSistema();

    echo json_encode((array) $objeto_log->gerar_arquivo_xlsx((array) $_REQUEST), JSON_UNESCAPED_UNICODE);
    exit;
});

//@note log_pdf
/**
 * Rota responsável por pesquisar e gerar o arquivo .pdf e retorna o arquivo para o front end do sistema para realizar o download.
 */
router_add('log_pdf', function(){
    $objeto_log = new LogSistema();

    echo json_encode((array) $objeto_log->gerar_arquivo_pdf((array) $_REQUEST), JSON_UNESCAPED_UNICODE);
    exit;
});
?>