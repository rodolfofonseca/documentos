<?php

require_once 'Sistema/db.php';
/**
 * Função responsável por realizar o arredondamento de cálculos
 * @param float $valor valor em reais
 * @param string $operacao que deseja realizar dentro da função! EX '*'
 * @param float $quantidade a quantidade do item
 * @param int $casas_decimais a quantidade de casas decimais que deseja que a função retorne 
 */
function arredondar($valor, $operacao = '', $quantidade = null, $casas_decimais = 2)
{
  //  converte a quantidade que foi recebida pelo parâmetro em float para garantir que estaja no tipo correto
  $valor = (float) floatval($valor);
  $quantidade = (float) floatval($quantidade);

  //Se a quantidade for maior que zero realiza o cálculo de acordo com a operação que foi recebida por parâmetro.
  if ($quantidade != null && $operacao != '') {
    //Realiza o cálculo de acordo com o parâmetro que foi recebido.
    if ($operacao == '*') {
      $valor = $quantidade * $valor;
    } else if ($operacao == '+') {
      $valor = $valor + $quantidade;
    } else if ($operacao == '-') {
      $valor = $valor - $quantidade;
    } else if ($operacao == '/') {
      $valor = $valor / $quantidade;
    }

    //O valor precisa ser formatado para calcular corretamente quando for por ex: 15.549888888880;
    $valor = (float) formatar_numero($valor, 4, '.', '');
  }
  // verifica se o valor será um inteiro, para retorná-lo de forma direta, sem tratamento
  if (mb_strpos(strval($valor), '.')) {
    $auxPrecisao = (int) 3;
    $auxComparacao = (float) 5 * pow(10, $auxPrecisao - 1);

    $ultimoPonto = strripos($valor, '.') + 1;
    $valor = substr($valor, 0, $ultimoPonto) . substr($valor, $ultimoPonto, 4); // <- deixo o número com 4 casas decimais, caso vier a mais ou a menos
    $numeroInteiro = str_pad($valor, ($casas_decimais + $auxPrecisao + $ultimoPonto), "0", STR_PAD_RIGHT);
    $numeroInteiro = (int) intval(str_replace('.', '', $numeroInteiro));

    $sobra = intval(substr(($numeroInteiro), -$auxPrecisao), 10);
    $numero = intval(substr(($numeroInteiro), 0, (strlen($numeroInteiro) - $auxPrecisao)), 10);

    if ($numero % 2 == 0) {
      if ($sobra > $auxComparacao) {
        $numero++;
      }
    } else {
      if ($sobra >= $auxComparacao) {
        $numero++;
      }
    }

    $numero = (float) round(($numero / pow(10, $casas_decimais)), intval($casas_decimais, 10));
    return floatval($numero);
  }
  return $valor;
}


/** FORMATAR NÚMERO
 * - Responsável por alterar a formatação do valor passado
 * @param $numero Deve ser informado o número a ser formatado
 * @param $decimais Pode ser informado a quantidade de números decimais
 * @param $decimal Pode ser informado o separador do decimal
 * @param $milhar Pode ser informado o separador dos milhares
 */
function formatar_numero($numero, $decimais = 2, $decimal = ',', $milhar = '')
{
  $numero = (float) arredondar($numero, '', 0, $decimais);
  return (string) number_format($numero, $decimais, $decimal, $milhar);
}

/**
 * Função responsável por ler as informações presentes no arquivo de configuração e retornar as informações
 * @return array informações do arquivo
 */
function ler_arquivo_configuracao($tipo = 'BANCO')
{
  //NOME DO ARQUIVO DE CONFIGURAÇÃO
  $arquivo_configuracao = (string) 'configuracao.ini';

  if ($tipo == 'BANCO') {
    //CRIO UM ARRAY COM AS CHAVES COM INFORMAÇÕES GENÉRICAS
    $retorno = (array) ['nome_banco' => (string) 'sex_shop', 'dns' => (string) 'mongodb://localhost:27017'];
    //VERIFICO SE EXISTE O ARQUIVO DE CONFIGURACAO
    if (file_exists($arquivo_configuracao) == true) {
      //CONVERTO O CONTEÚDO DO ARQUIVO DE CONFIGURAÇÃO EM UM ARRAY
      $configuracao = (array) parse_ini_file($arquivo_configuracao, true);
      //VERIFICO SE TEM A CHAVE QUE RECEBE O NOME DO BANCO DE DADOS, POIS POR ALGUM MOTIVO DESCONHECIDO PODE ACONTECER DE NÃO TER
      if (isset($configuracao['DB']['db']) == true) {
        $retorno['nome_banco'] = (string) $configuracao['DB']['db'];
      }
      //FAÇO A MESMA VERIFICAÇÃO AQUI TAMBÉM
      if (isset($configuracao['DB']['dns']) == true) {
        $retorno['dns'] = (string) $configuracao['DB']['dns'];
      }
    }

    return (array) $retorno;
  } else {
    if (file_exists($arquivo_configuracao) == true) {
      $configuracao = (array) parse_ini_file($arquivo_configuracao, true);

      return (array) $configuracao;
    }
  }

  return (array) [];
}

/**
 * Função responsável por retornar o código de barras para cadastro ou alteração de componentes.
 */
function codigo_barras()
{
  $codigo = (string) microtime(true);
  $codigo = (string) str_replace('.', '', $codigo);
  $codigo = (string) str_replace(' ', '', $codigo);
  return (string) substr($codigo, 0, 13);
}

/**
 * Função responsável por se conectar ao banco de dados e realizar as pesquisas de forma mais direta.
 * $pipeline (SQL) da forma que o mongo entende
 * @param string $tabela tabela a ser consultada
 * @param array $pipeline (SQL) da forma como o mongo entende
 * @return object com o retorno
 */
function pesquisa_banco_aggregate($tabela, $pipeline)
{
  $classe = new DB();
  $retorno = $classe->connect($tabela);
  $connection = $classe->connection;

  return $connection->aggregate($pipeline, ['allowDiskUse' => TRUE]);
}

/**
 * Função responsável por verificar a conexão do usuário com a internet, caso o mesmo não esteja conectado a uma rede, redireciona o mesmo para uma página de erro.
 * Caso esteja continua seguindo o fluxo.
 * @return void
 */
function verificar_conexao_internet()
{
  exec('ping 8.8.8.8', $saida, $retorno);
  $quantidade = (int) intval(count($saida), 10);
  if ($quantidade <= 10) {
    header('location:sem_internet.php');
  }
}

/**
 * Função responsável por montar o array de retorno e retornar o mesmo formatado, sempre que for solicitado
 * @param mixed $titulo
 * @param mixed $mensagem
 * @param mixed $icone
 * @return array
 */
function mensagem_retorno($titulo, $mensagem, $icone){
  return (array) ['titulo' => (string) $titulo, 'mensagem' => (string) $mensagem, 'icone' => (string) $icone];
}

/**
 * Função responsável por realizar a conversão da unidade de medida computacional bytes para kilobytes
 * 
 * em formatação passando FALSE o sistema converte de bytes para kilobytes
 * em formatação passando TRUE o sistema converte de bytes para Megabytes
 * 
 * @param float|int $tamanho
 * @param boolean $formatacao
 * @return float
 */
function converter_tamanho_arquivo($tamanho, $formatacao){
  if($formatacao == false){
    return (float) $tamanho / (1024 * 1024);
  }else{
    return (float) floatval(($tamanho / 1000));
  }
}
?>