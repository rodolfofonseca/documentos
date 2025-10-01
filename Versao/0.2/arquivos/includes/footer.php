<?php
//Empresa aberta em 06/10/2024
//Sistema lançado em 22/01/2025
$ano_lancamento_sistema = 2025;
$ano_atual = date('Y');
?>

<footer class="footer text-center text-muted">
    <div class="row">
        <div class="col-8">
            <?php
            if($ano_atual == 2025){
                ?>
                    Copyright©️ <?php echo $ano_atual; ?> RAVF Desenvolvimento de Sistemas LTDA.
                <?php
            }else{
                ?>
                    Copyright©️ <?php echo $ano_lancamento_sistema.'-'.$ano_atual; ?> RAVF Desenvolvimeno de Sistema LTDA.
                <?php
            }
            ?>
            CNPJ nª: 57.575.467/0001-87 / Rua José da Ponte. 96. Big Plazza, Jales-SP CEP: 15700-856 Uma empresa do grupo RAVF
        </div>
        <div class="col-4">
            Sistema Gerenciador de Documentos Versão: <?php echo VERSAO_SISTEMA; ?>
        </div>
    </div>
</footer>
</div>
</div>
<script src="assets/libs/jquery/dist/jquery.min.js"></script>
<script src="assets/libs/popper.js/dist/umd/popper.min.js"></script>
<script src="assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="dist/js/app-style-switcher.js"></script>
<script src="dist/js/feather.min.js"></script>
<script src="assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
<script src="dist/js/sidebarmenu.js"></script>
<script src="dist/js/custom.min.js"></script>
<script src="assets/extra-libs/c3/d3.min.js"></script>
<script src="assets/extra-libs/c3/c3.min.js"></script>
<script src="assets/libs/chartist/dist/chartist.min.js"></script>
<!--<script src="assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>-->
<script src="assets/extra-libs/jvector/jquery-jvectormap-2.0.2.min.js"></script>
<script src="assets/extra-libs/jvector/jquery-jvectormap-world-mill-en.js"></script>
<script src="dist/js/pages/dashboards/dashboard1.min.js"></script>
<script src="https://cdn.jsdelivr.net/jsbarcode/3.6.0/JsBarcode.all.min.js"></script>
</body>
</html>
