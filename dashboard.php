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
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script>
		google.charts.load('current', {'packages':['corechart']});

		function desenharPizza (){
			var tabela = new google.visualization.DataTable();

			let contas_pagas = parseInt("<?php echo $retorno_quantidade['contas_pagas']; ?>", 10);
			let contas_atrasadas = parseInt("<?php echo $retorno_quantidade['contas_atrasadas']; ?>", 10);
			let contas_aguardando = parseInt("<?php echo $retorno_quantidade['contas_aguardando']; ?>", 10);

			tabela.addColumn('string','categorias');
			tabela.addColumn('number','valores');

			var opcoes = {'height': 200, 'width': 450};

			if(contas_pagas != 0){
				tabela.addRow(['Paga', contas_pagas]);
			}
			
			if(contas_atrasadas != 0){
				tabela.addRow(['Atrasada', contas_atrasadas]);
			}
			
			if(contas_aguardando != 0){
				tabela.addRow(['Aguardando', contas_aguardando]);
			}

			var grafico = new google.visualization.PieChart(document.getElementById('graficoPizza'));
			grafico.draw(tabela, opcoes);
		}

		google.charts.setOnLoadCallback(desenharPizza);
	</script>
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
		</div>
		<div class="row">
			<div class="col-2">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title text-center">Atrasadas</h4>
						<br/>
						<div class="row">
							<div class="col-12 text-center">
								<h4><?php echo $retorno_quantidade['contas_atrasadas']; ?></h4>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-2">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title text-center">Aguar...</h4>
						<br/>
						<div class="row">
							<div class="col-12 text-center">
								<h4><?php echo $retorno_quantidade['contas_aguardando']; ?></h4>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-2">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title text-center">Pagas</h4>
						<br/>
						<div class="row">
							<div class="col-12 text-center">
								<h4><?php echo $retorno_quantidade['contas_pagas']; ?></h4>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-6">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title text-center">Status Contas</h4>
						<br/>
						<div class="row">
							<div id="graficoPizza"></div>
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