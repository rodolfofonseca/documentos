<?php
require_once 'includes/head_relatorio.php';
?>
<script>
    function acessar_sistema(){
        window.location.href = sistema.url('/index.php', {'rota':'index'});
    }
</script>
<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">SEM CONEXÃO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Você não está conectado a internet, por favor tenta mais tarde acessar esta funcionalidade do sistema.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
        <button type="button" class="btn btn-primary" onclick="acessar_sistema();">Continuar</button>
      </div>
    </div>
  </div>
</div>
<script>
    window.onload = function(){
        $('#exampleModalCenter').modal('show');
    }
</script>
<?php
require_once 'includes/footer_relatorio.php';
?>