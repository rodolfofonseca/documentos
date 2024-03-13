<?php
include_once __DIR__ . '/../trata-banco.php';
@session_start();
@session_cache_limiter("none");
@session_cache_expire(0);

ignore_user_abort(1);
set_time_limit(0);
header('P3P: CP="IDC DSP COR CURa ADM ADMa DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT PHY ONL COM STA"');

$dm['data_emissao']         = (string) ($_GET['data_emissao']);
$dm['valor']                = (float) ($_GET['valor']);
$dm['num_origem']           = (string) ($_GET['num_origem']);
$dm['num_nf']               = (integer) ($_GET['num_nf']);
$dm['data_vencimento']      = (string) ($_GET['data_vencimento']);
$dm['nome_devedor']         = (string) ($_GET['nome_devedor']);
$dm['endereco_devedor']     = (string) ($_GET['endereco_devedor']);
$dm['cep']                  = (string) ($_GET['cep']);
$dm['municipio_devedor']    = (string) ($_GET['municipio_devedor']);
$dm['estado_devedor']       = (string) ($_GET['estado_devedor']);
$dm['cpf_cnpj_devedor']     = (string) ($_GET['cpf_cnpj_devedor']);
$dm['insc_est_devedor']     = (string) ($_GET['insc_est_devedor']);
$dm['praca_pagamento']      = (string) ($_GET['praca_pagamento']);
$dm['valor_extenso']        = (string) ($_GET['valor_extenso']);
$dm['nome_credor']          = (string) ($_GET['nome_credor']);
$dm['cnpj']                 = (string) ($_GET['cnpj_credor']);
$dm['endereco_credor']      = (string) ($_GET['endereco_credor']);
$dm['cidade_credor']        = (string) ($_GET['cidade_credor']);
$dm['estado_credor']        = (string) ($_GET['estado_credor']);
$dm['sigla_moeda']          = (string) ($_GET['sigla_moeda']);
$dm['num_pedido']           = (string) ($_GET['num_pedido']);
$dm['numero_boleto']        = (string) ($_GET['numero_boleto']);

?>
<html>
	<head >
		<link rel="stylesheet" type="text/css" href="../Publico/ddo.css" />
		<title>Duplicata Mercantil</title>
		<style type="text/css">
			body {font: 13px Verdana; position: relative; color: #000;}
			#tbNumConteudoDM {
				width: 700px;
				height: 250px;
				border-collapse: collapse;
				font: 11px Verdana;
				border: 2px solid #000000;
			}
			#tbNumConteudoDM tr {
				border: 1px solid #000000;
			}
			#tbNumConteudoDM td {
				border: 1px solid #000000;
			}
			#dm {position: relative; width: 860px; height: 500px; border: 2px solid; margin: 50px 0 0 0;background: #FAFAFA;}
				#dvCredor {position: absolute; top: 0; left: -1px; height: 90px; width: 331px; border: 1px solid;}
				#dvDadosDM {position: absolute; top: 0; left: 330px; height: 90px; width: 331px; border: 1px solid;}
				#dvDadosDMDireita {position: absolute; top: 0; left: 661px; height: 90px; width: 197px; border: 1px solid;}
				#dvCorpoDM {position: absolute; top: 95; left: 0; height: 150px; width: 800px; padding: 10px;}
					#dvConteudoDM {position: absolute; top: 5px; left: 115px; height: 248px; width: 640px; border: 0px solid; padding: 0px; margin: 10px;}
					#tbRodapeDM {position: absolute; top: 280px; left: 0px; height: 100px; width: 640px; font: 11px Verdana; border: 0px none; padding: 0px; margin: 0px;}

			.Margin {margin: 15px;}
			#acao_popup {position: relative; display: none; float: right; margin: 5px;}
		</style>
	</head>
	<body>
		<div id="acao_popup">
			<input type="button" class="btn" onclick="imprimir()" value="Imprimir (Ctrl+P)">
		</div>
		<div id="dm">
			<div id="dvCredor">
				<div class="Margin">
					<b><?php echo $dm['nome_credor'];?></b><br>
					<b><?php echo $dm['endereco_credor'];?></b><br>
				</div>
			</div>
			<div id="dvDadosDM">
				<div class="Margin">
					<b>CNPJ:</b> <?php echo $dm['cnpj']; ?><br>
					<b>Data da Emissão:</b> <?php echo $dm['data_emissao']; ?><br>
				</div>
			</div>
			<div id="dvDadosDMDireita">
				<div class="Margin">
					<br>
					&nbsp;&nbsp;&nbsp;<b>DUPLICATA</b>
				</div>
			</div>
			<div id="dvCorpoDM">
				<img src="../Imagens/assinatura_vertical.png" width="104" height="259" alt="assinatura_vertical" ></img>
				<div id="dvConteudoDM">
					<table id="tbNumConteudoDM">
						<tr>
							<td><b>NF Fatura Nº</b></td>
							<td><b>NF FAT/Duplicata - Valor</b></td>
							<td><b>Duplicata <br> nº de Origem</b></td>
							<td><b>Vencimento</b></td>
							<td rowspan="3" valign=top ><b>PARA USO DA INSTITUIÇÃO FINANCEIRA</b></td>
						</tr>
						<tr>
							<td style="height: 45px;">
								<?php if (strlen($dm['num_pedido']) > 0) { ?>
									Pedido: <?php echo $dm['num_pedido']; ?><br>
								<?php } ?>

								<?php if ($dm['num_nf'] > 0) { ?>
									NF: <?php echo $dm['num_nf']; ?><br>
								<?php } ?>

								<?php if (strlen($dm['numero_boleto']) > 0) { ?>
									Boleto:<?php echo $dm['numero_boleto']; ?>
								<?php } ?>
							</td>
							<td><?php echo $dm['sigla_moeda'] . ' ' . number_format($dm['valor'], 2, ',', ''); ?></td>
							<td><?php echo str_pad($dm['num_origem'], 10, '0', STR_PAD_LEFT); ?></td>
							<td><?php echo $dm['data_vencimento']; ?></td>
						</tr>
						<tr style="height: 40px;">
							<td style=" border: 0px none;" valign="top">Descontos de Condições Especiais</td>
							<td style=" border: 0px none; text-align: center;" valign="top">% sobre</td>
							<td style=" border: 0px none; text-align: center;" valign="top">Até</td>
						</tr>
						<tr>
							<td style="text-align: left;" colspan="4" style="height: 80px;">
								<b>NOME DO SACADO:</b> <?php echo $dm['nome_devedor']; ?><br>
								<b>ENDEREÇO:</b> <?php echo $dm['endereco_devedor']; ?><br>
								<b>CEP:</b> <?php echo $dm['cep']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<b>MUNICÍPIO:</b> <?php echo $dm['municipio_devedor']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<b>ESTADO:</b> <?php echo $dm['estado_devedor']; ?><br>
								<b>PRAÇA DE PAGAMENTO:</b>&nbsp;<?php echo $dm['cidade_credor']; ?><br>
								<b>CPF/CNPJ (MF) Nº:</b> <?php echo $dm['cpf_cnpj_devedor']; ?><br>
								<b>Insc. Est. Nº:</b> <?php echo $dm['insc_est_devedor']; ?>
							</td>
							<td>
								<br><br>
							</td>
						</tr>
						<tr>
							<td style="height: 40px;"><b>Valor&nbsp;por&nbsp;extenso</b></td>
							<td colspan="4" style="height: 40px; border: 0px none; font: 11px Monospace; font-family: monospace;">
								<?php echo str_pad('(' . $dm['valor_extenso'] . ')', (190 - strlen(@$dm['valor_extenso'])), '--#', STR_PAD_RIGHT); ?>
							</td>
						</tr>
					</table>
					<table id="tbRodapeDM">
						<tr>
							<td colspan="3">
								Reconhecemos a exatidão desta <b>Duplicata de Serviços</b> na importância que pagaremos
								à <b><?php echo $dm['nome_credor']; ?></b> ou à sua ordem na praça e vencimentos indicados.
							</td>
						</tr>
						<tr>
							<td style="width: 120px;">Em ___/___ /_____<br>(Data do aceite)</td>
							<td style="font: 7px Verdana; width: 150px; border: 1px solid; text-align: justify;">
								<b>
									NÃO SENDO PAGO NO VENCIMENTO COBRAR JUROS DE MORA E DESPESAS FINANCEIRAS.
									NÃO CONCEDER DESCONTOS MESMO CONDICIONALMENTE.
								</b>
							</td>
							<td style="text-align: center;">________________________________________________<br>Assinatura</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			function imprimir() {
				document.querySelector('#acao_popup').style.display = 'none';
				print();
				window.setTimeout(function () {
					document.querySelector('#acao_popup').style.display = 'block';
				}, 500);
			}

			window.onload = function () {
				imprimir();
			}
		</script>
	</body>
</html>
