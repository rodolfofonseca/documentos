<?php

/*
 * Obtendo dados do HG Weather API
 *
 * Consulte nossa documentacao em http://hgbrasil.com/weather
 * Contato: hugodemiglio@hgbrasil.com
 * Copyright 2015 - Hugo Demiglio - @hugodemiglio
 *
*/

$cid = 'BRXX0198'; // CID da sua cidade, encontre a sua em http://hgbrasil.com/weather

$dados = json_decode(file_get_contents('http://api.hgbrasil.com/weather/?cid='.$cid.'&format=json'), true); // Recebe os dados da API

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Previsão do Tempo - HG Weather</title>
  </head>
  <body>
    <?php echo $dados['results']['city']; ?> <?php echo $dados['results']['temp']; ?> ºC<br>
    <?php echo $dados['results']['description']; ?><br>
    Nascer do Sol: <?php echo $dados['results']['sunrise']; ?> - Pôr do Sol: <?php echo $dados['results']['sunset']; ?><br>
    Velocidade do vento: <?php echo $dados['results']['wind_speedy']; ?><br>
    <img src="imagens/<?php echo $dados['results']['img_id']; ?>.png" class="imagem-do-tempo"><br>
  </body>
</html>