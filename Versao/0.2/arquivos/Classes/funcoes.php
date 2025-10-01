<?php
require_once 'Sistema/db.php';
function router_add($rota, $pagina) {
  $rota_atual = $_REQUEST['rota'] ?? 'index';
  if ($rota_atual == $rota) {
    call_user_func($pagina);
    exit;
  }
}

function model_parse($model, $data = []) {
  foreach ($data as $field => $value) {
    if (array_key_exists($field, $model) == true) {
      if($model[$field] === 'date'){
        $data[$field] = model_date($value);
      }else{

        $field_type = (string) gettype($model[$field]);
  
        if ($field_type == 'int' || $field_type == 'integer') {
          $data[$field] = (int) $value;
  
        } else if ($field_type == 'double') {
          $data[$field] = (float) $value;
        }else if($field_type == 'string'){
          $data[$field] = (string) $value;
        }
      }
    }
  }

  foreach ($model as $field => $value) {
    if ($value === (string) 'date') {
      $model[$field] = model_date();
    }
  }

  return array_merge($model, $data);
}
function model_insert($table, $data) {
  return (bool) DB::use($table)->insert($data);
}
function model_update($table, $condition, $data) {
  return DB::use($table)->update($condition, $data);
}
function model_delete($table, $condition) {
  return DB::use($table)->delete($condition);
}
function model_all($table, $condition = [], $order = [], $limit = 0) {
  return DB::use($table)->all($condition, $order, $limit);
}
function model_one($table, $condition = [], $order = []) {
  return DB::use($table)->one($condition, $order);
}
function model_check($table, $condition = []) {
  return (bool) DB::use($table)->one($condition);
}
function model_next($table, $field, $condition = []) {
  $next = (int) 1;
  $last = (array) DB::use($table)->one($condition, [ $field => false ]);

  if (empty($last) == false) {
    $next = (int) $last[$field] + 1;
  }

  return (int) intval($next, 10);
}
function model_columns($table, $key, $values = [], $condition = [], $order = []) {
  if (empty($values)) {
    $rows = DB::use($table)->all($condition, $order);

  } else {
    $rows = DB::use($table)->columns(array_merge($values, [$key]), $condition, $order);
  }
  return array_combine(array_column($rows, $key), array_values($rows));
}
function model_column($table, $key, $value=null, $condition = [], $order = []) {
  if ($value) {
    $rows = DB::use($table)->columns([$key, $value], $condition, $order);
    return array_column($rows, $value, $key);
  } else {
    $rows = DB::use($table)->columns([$key], $condition, $order);
    return array_column($rows, $key);
  }
}
function model_cell($table, $cell, $condition = [], $order = []) {
  $row = model_one($table, $condition, $order);

  if (empty($row) == false) {
    return $row[$cell];
  }

  return '';
}
function http_request($url, $data = []) {
  $curl = curl_init();

  curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $data
  ]);

  $retorno = curl_exec($curl);

  if (curl_errno($curl)) {
      $retorno = curl_error($curl);
  }

  curl_close($curl);

  return $retorno;
}
function array_get($array, $key, $default = '') {
  if (array_key_exists($key, $array) == true) {
    return $array[$key];
  }
  return $default;
}
function array_group($array, $key) {
  $grouped = (array) [];

  foreach ($array as $value) {
    $grouped[$value[$key]][] = $value;
  }

  return $grouped;
}

function model_validator($model) {
  $validator = (array) [];

  foreach ($model as $field => $value) {
    $field_type = (string) gettype($model[$field]);
    
    if ($field_type == 'integer') {
      $validator[$field] = (array) ['$type' => 'int'];
    } else if ($value === 'date') {
      $validator[$field] = (array) ['$type' => 'date'];
    } else $validator[$field] = (array) ['$type' => $field_type];
  }

  return $validator;
}

/**
 * Formata a data que é passada como parâmetro para o objeto de data do tipo que o mongo reconhece
 * @param string data no formato 1996-09-26
 * @param string time no formato 13:22:15
 * @return object de data do mongo
 */
function model_date($date = null, $time = null, $inverter = false){
  date_default_timezone_set('America/Sao_Paulo');
  if (is_object($date)){// caso chegue uma data que já está como objeto
    return $date;
  }

  if($inverter == true){
    $explode = explode("/", $date);
    $date = $explode[2].'-'.$explode[1].'-'.$explode[0];
  }
  
  if ($date == ('' || null)){
    // $date = (string) date('d/m/Y');
    $date = (string) date('Y-m-d');
  }
  if ($time == ('' || null)){
    $time = (string) date('H:i:s');
  }
  $date_format = date_create_from_format('Y-m-dH:i:s', $date.$time, new DateTimeZone('UTC'));
  $timestamp = date_timestamp_get($date_format);
  return new MongoDB\BSON\UTCDateTime($timestamp * 1000);

}

/**
 * Converte o objeto de data do mongo para o tipo de data que o sistema reconhece
 * @param object de formato de data do mongo
 * @param string formato de data que deseja que o sistema formate
 * @return string data formatada
 */
function convert_date($date_time, $format = 'Y-m-d'){
  if ($date_time == ('' || null)){
    $date_time = new MongoDB\BSON\UTCDateTime;
  }
  return (string) $date_time->toDateTime()->format($format);
}