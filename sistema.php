<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Sistema.php';

router_add('index', function(){
    require_once 'includes/head.php';
    ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-title text-center">
                        <div class="row">
                            <div class="col-12">
                                <h1>Configuração do Sistema</h1>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="id_sistema" id="id_sistema" value=""/>
                        <div class="row">
                            <div class="col-2">
                                <input type="text" class="form-control custom-radius text-center" name="versao_sistema" id="versao_sistema" value="1.0" readonly/>
                            </div>
                            <div class="col-5">
                                <input type="text" class="form-control custom-radius" name="chave_api" id="chave_api" placeholder="CHAVE DE API"/>
                            </div>
                            <div class="col-5">
                                <input type="text" class="form-control custom-radius" name="cidade" id="cidade" placeholder="CIDADE"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <button class="btn btn-info botao_vertical_linha">Salvar Dados</button>
                            </div>
                            <div class="col-4">
                                <button class="btn btn-secondary botao_vertical_linha">Voltar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    require_once 'includes/footer.php';
});
?>