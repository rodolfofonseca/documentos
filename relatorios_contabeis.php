<?php
require_once('Classes/bancoDeDados.php');
require_once('modelos/ContasBancarias.php');
require_once('modelos/RelatoriosContabeis.php');

//@note index
router_add('index', function () {
    require_once('includes/head.php');
    
    $objeto_contas_bancarias = new ContasBancarias();

    $dados_contas_bancarias['filtro'] = (array) [];
    $dados_contas_bancarias['ordenacao'] = (array) ['nome_conta' => (bool) true];
    $dados_contas_bancarias['limite'] = (int) 0;

    $retorno_pesquisa_contas_bancarias = (array) $objeto_contas_bancarias->pesquisar_todos($dados_contas_bancarias);

    $data_hoje = date('Y-m-d');
    ?>
    <script>
        function gerar_relatorio_movimento_contas(){
            let conta_contabil = document.querySelector('#conta_contabil').value;
            let data_inicial = document.querySelector('#data_inicial').value;
            let data_final = document.querySelector('#data_final').value;

            var jan = window.open("relatorios_contabeis.php?rota=pesquisar_informacoes_movimento_contas&conta_contabil="+conta_contabil+"&data_inicial="+data_inicial+"&data_final="+data_final, "Relatório Movimentação de Contas", "toolbar=no,location=center,directories=no,status=no, menubar=no,scrollbars=yes,resizable=no,width=1500, height=800");
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center">Saldo nas contas</h4>
                        <div class="row">
                            <div class="col-3 text-center">
                                <label class="text">Conta</label>
                                <select id="conta_contabil" class="form-control custom-radius">
                                    <?php
                                    if(empty($retorno_pesquisa_contas_bancarias) == false){
                                        foreach($retorno_pesquisa_contas_bancarias as $contas_bancarias){
                                            echo '<option value="1.'.$contas_bancarias['id_conta_bancaria'].'">'.$contas_bancarias['nome_conta'].'</option>';
                                        }
                                    }else{
                                        echo '<option value="">NENHUMA CONTA ENCONTRADA</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Data Inicial</label>
                                <input type="date" class="form-control custom-radius" id="data_inicial" value="<?php echo $data_hoje; ?>"/>
                            </div>
                            <div class="col-3 text-center">
                                <label class="text">Data Final</label>
                                <input type="date" class="form-control custom-radius" id="data_final" value="<?php echo $data_hoje; ?>"/>
                            </div>
                            <div class="col-3 text-center">
                                <button class="btn btn-secondary botao_vertical_linha" onclick="gerar_relatorio_movimento_contas();">Gerar Relatório</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    require_once('includes/footer.php');
});

//@audit pesquisar_informacoes_movimento_contas
router_add('pesquisar_informacoes_movimento_contas', function () {
    require_once('includes/head_relatorio.php');
    $objeto_relatorio_contabil = new RelatoriosContabeis();

    $conta_contabil = (string) (isset($_REQUEST['conta_contabil']) ? (string) $_REQUEST['conta_contabil'] : '');
    $data_inicial = (string) (isset($_REQUEST['data_inicial']) ? (string) $_REQUEST['data_inicial'] : '');
    $data_final = (string) (isset($_REQUEST['data_final']) ? (string) $_REQUEST['data_final'] : '');

    ?>
    <br/>
    <table border="1px solid">
        <tr>
            <td>ID</td>
            <td>Descrição</td>
            <td>Data</td>
            <td>Tipo</td>
            <td>Valor</td>
        </tr>
    </table>
    <?php

    require_once('includes/footer.php');
    exit;
});
?>