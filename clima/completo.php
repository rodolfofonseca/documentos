<?php
$chave = 'cd082846';
 $dados = hg_request(array(
   'cid' => '457398',
 ), $chave);

function hg_request($parametros, $chave = null, $endpoint = 'weather'){
  $url = 'https://api.hgbrasil.com/weather?woeid=457398&format=json';
  
  if(is_array($parametros)){
    // Insere a chave nos parametros
    if(!empty($chave)) $parametros = array_merge($parametros, array('key' => $chave));
    
    // Transforma os parametros em URL
    foreach($parametros as $key => $value){
      if(empty($value)) continue;
      $url .= $key.'='.urlencode($value).'&';
    }
    
    // Obtem os dados da API
    $resposta = file_get_contents(substr($url, 0, -1));
    
    return json_decode($resposta, true);
  } else {
    return false;
  }
}
var_dump($dados);
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