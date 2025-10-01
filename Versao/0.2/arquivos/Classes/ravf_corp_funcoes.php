<?php
require_once 'Sistema/database.php';

/**
 * Função responsável por realizar a inserção de um novo registro no bando de dados
 * @param string $table
 * @param array $data
 * @return bool
 */
function ravf_corp_model_insert($table, $data) {
  return (bool) Database::use($table)->insert($data);
}

/**
 * Função responsável por alterar o registro no banco de dados
 * @param string $table
 * @param array $condition
 * @param array $data
 * @return bool
 */
function ravf_corp_model_update($table, $condition, $data) {
  return Database::use($table)->update($condition, $data);
}

/**
 * Função responsável por deletar do banco de dados um registro
 * @param string $table
 * @param array $condition
 * @return bool
 */
function ravf_corp_model_delete($table, $condition) {
  return Database::use($table)->delete($condition);
}

/**
 * Função responsável por pesquisar todos os registros encontrados no banco de dados com os filtros passados
 * @param string $table
 * @param array $condition
 * @param array $order
 * @param int $limit
 * @return array
 */
function ravf_corp_model_all($table, $condition = [], $order = [], $limit = 0) {
  return Database::use($table)->all($condition, $order, $limit);
}

/**
 * Função responsável por pesquisar no banco de dados apenas um registro com os filtros passados
 * @param string $table
 * @param array $condition
 * @param array $order
 * @return array|object
 */
function ravf_corp_model_one($table, $condition = [], $order = []) {
  return Database::use($table)->one($condition, $order);
}

/**
 * função responsável por realizar a checagem de se um registro existe no banco de dados.
 * @param string $table
 * @param array $condition
 * @return bool
 */
function ravf_corp_model_check($table, $condition = []) {
  return (bool) Database::use($table)->one($condition);
}

/**
 * Função responsável por verificar o próximo número disponível no bando de dados e retornar esse número
 * @param string $table
 * @param string $field
 * @param array $condition
 * @return int
 */
function ravf_corp_model_next($table, $field, $condition = []) {
  $next = (int) 1;
  $last = (array) Database::use($table)->one($condition, [ $field => false ]);

  if (empty($last) == false) {
    $next = (int) $last[$field] + 1;
  }

  return $next;
}

/**
 * Função responsável por pesquisar no banco de ados apenas as colunas que o usuário deseja retornar.
 * @param string $table
 * @param string $key
 * @param array $values
 * @param array $condition
 * @param array $order
 * @return array
 */
function ravf_corp_model_columns($table, $key, $values = [], $condition = [], $order = []) {
  if (empty($values)) {
    $rows = Database::use($table)->all($condition, $order);

  } else {
    $rows = Database::use($table)->columns(array_merge($values, [$key]), $condition, $order);
  }
  return array_combine(array_column($rows, $key), array_values($rows));
}

/**
 * Função responsável por pequisar e retornar do banco de dados apenas as colunas do registro que o usuário deseja
 * @param string $table
 * @param string $key
 * @param string $value
 * @param array $condition
 * @param array $order
 * @return array
 */
function ravf_corp_model_column($table, $key, $value=null, $condition = [], $order = []) {
  if ($value) {
    $rows = Database::use($table)->columns([$key, $value], $condition, $order);
    return array_column($rows, $value, $key);
  } else {
    $rows = Database::use($table)->columns([$key], $condition, $order);
    return array_column($rows, $key);
  }
}