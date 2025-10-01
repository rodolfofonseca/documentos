<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Empresa.php';

//@audit index
/**
 * Rota que apresenta ao usuário a chave de sistema para que o mesmo possa alterar o cadastrar uma nova chave caso o suporte acabe
 */
router_add('index', function(){
    require_once 'includes/head.php';
    ?>
    <script>
        const CODIGO_EMPRESA = <?php echo CODIGO_EMPRESA; ?>;
        
        /**
         * Função responável por chamar a rota que salvar as informações da empresa no banco de dados
         */
        function salvar_dados(){
            let chave_sistema = document.querySelector('#chave_sistema').value;
            let data_hora_ativacao = document.querySelector('#data_hora_ativacao');

            if(chave_sistema != '' && CODIGO_EMPRESA != 0){
                sistema.request.post('/empresa.php', {'rota': 'salvar_dados_empresa', 'chave_sistema': chave_sistema, 'codigo_empresa': CODIGO_EMPRESA}, function(retorno){
                    validar_retorno(retorno, '/empresa.php');
                });
            }else{
                Swal.fire({'icon':'warning', 'text':'Sistema já ativado. Se deseja renovar sua assinatura de suporte entre em contato com os desenvolvedores', 'title':'SISTEMA JÁ ATIVADO'});
            }
        }

        /** 
         * Função responsável por pesquisar no banco de dados as informações da empresa e colcoar as informações nos componentes corretos
        */
        function pesquisar_empresa(){
            sistema.request.post('/empresa.php', {'rota':'pesquisar_dados_empresa', 'codigo_empresa': CODIGO_EMPRESA}, function(retorno){

                document.querySelector('#chave_sistema').value = retorno.dados.chave_sistema;
                document.querySelector("#data_hora_ativacao").value = retornar_data(retorno.dados.data_hora_ativacao);
                document.querySelector("#data_hora_bloqueio").value = retornar_data(retorno.dados.data_hora_bloqueio);

            }, false);
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Cadastro de Chave de sistema</h4>
                        <br/>
                        <div class="row">
                            <div class="col-12">
                                <p>
                                    Quando se possui uma chave de sistema ativada, os usuários do sistema podem abrir ticket de suporte, para relatar aos desenvolvedores do sistema sobre possíveis "bugs" que o projeto venha a ter, além de poder solicitar novas funcionalidades dentro do mesmo.
                                </p>
                                <p>
                                    Todo cliente que compra uma chave tem acesso vitalício ao sistema, mas para que a funcionalidade de suporte venha a ser ativado é necessário adquirir essa chave separada.
                                    <br/>
                                    Mas pode ficar tranquilo que não é uma obrigatoriedade, e mesmo que não se tenha comprado uma chave você continua recebendo as atualizações de sistema sempre que for lançada.
                                </p>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-6 text-center">
                                <label class="text">CHAVE SISTEMA</label>
                                <input type="text" id="chave_sistema" class="custom-radius form-control" maxlength="29">
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">DATA DE ATIVAÇÃO</label>
                                <input type="text" class="form-control custom-radius" id="data_hora_ativacao" readonly="true"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">DATA BLOQUEIO</label>
                                <input type="text" class="form-control custom-radius" id="data_hora_bloqueio" readonly="true"/>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-3">
                                <button class="btn btn-info custom-radius" onclick="salvar_dados();">SALVAR CHAVE</button>
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

            pesquisar_empresa();
        }
    </script>
    <?php
    require_once 'includes/footer.php';
    exit;
});

//@note salvar_dados_empresa
/**
 * Rota resposável por salvar as informações no banco de dados
 */
router_add('salvar_dados_empresa', function(){
    $objeto_empresa = new Empresa();
    
    echo json_encode((array) ['status' => (bool) $objeto_empresa->salvar_dados($_REQUEST)], JSON_UNESCAPED_UNICODE);
    exit;
});

//@note pesquisar_dados_empresa
/**
 * Rota responsável por pesquisar as informações da empresa e colocar nos devidos componentes
 */
router_add('pesquisar_dados_empresa', function(){
    $objeto_empresa = new Empresa();

    $codigo_empresa = (isset($_REQUEST['codigo_empresa']) ? (int) intval($_REQUEST['codigo_empresa'], 10): (int) 0);
    $filtro_de_pesquisa = (array) ['filtro'];
   
    if($codigo_empresa != 0){
        $filtro_de_pesquisa['filtro'] = (array) ['id_empresa', '===', (int) $codigo_empresa];
    }

    echo json_encode((array) ['dados' => (array) $objeto_empresa->pesquisar($filtro_de_pesquisa)], JSON_UNESCAPED_UNICODE);
    exit;
});
?>