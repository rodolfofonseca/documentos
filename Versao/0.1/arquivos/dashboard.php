<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Modelos/Documentos.php'; 
require_once 'Modelos/Clima.php';
require_once 'Modelos/Contas.php';

router_add('index', function(){
	require_once 'includes/head.php';
	
	$objeto_documento = new Documentos();
	$objeto_clima = new Clima();
	$objeto_contas = new Contas();

	$cidade = (string) '';
	$temperatura = (string) '';
	$descricao = (string) '';
	$retorno_previsao = (array) $objeto_clima->pesquisar_previsao();

	if(array_key_exists('results', $retorno_previsao) == true){
		if(array_key_exists('city', $retorno_previsao['results']) == true){
			$cidade = (string) $retorno_previsao['results']['city'];
		}
	
		if(array_key_exists('temp', $retorno_previsao['results']) == true){
			$temperatura = (string) $retorno_previsao['results']['temp'];
		}
	
		if(array_key_exists('description', $retorno_previsao['results']) == true){
			$descricao = (string) $retorno_previsao['results']['description'];
		}
	}

	$retorno_quantidade = (array) $objeto_contas->validar_vencimento_contas();

	?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-4">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title text-center">Documentos Cadastrados</h4>
						<br/>
						<div class="row">
							<div class="col-12 text-center">
								<h4><?php echo $objeto_documento->contar_quantidade_documentos(); ?></h4>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-4">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title text-center">Previsão do Tempo</h4>
						<br/>
						<div class="row">
							<div class="col-12 text-center">
								<h4><?php echo $cidade.' '.$temperatura.' ºC '.$descricao ;  ?></h4>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-4">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title text-center">Contas Atrasadas</h4>
						<br/>
						<div class="row">
							<div class="col-12 text-center">
								<h4><?php echo $retorno_quantidade['contas_atrasadas']; ?></h4>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-4">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title text-center">Contas Aguardando</h4>
						<br/>
						<div class="row">
							<div class="col-12 text-center">
								<h4><?php echo $retorno_quantidade['contas_aguardando']; ?></h4>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	require_once 'includes/footer.php';
	exit;
});
?>