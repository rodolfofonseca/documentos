<?php
require_once 'Classes/bancoDeDados.php';

$retorno = model_one('tipo_arquivo', (array) [(array) ['and' => (array) [(array) ['id_empresa', '===', (int) 1], (array) ['tipo_arquivo', '===', (string) '.PDF'], (array) ['usar', '===', (string) 'S']]]]);

var_dump($retorno);
?>