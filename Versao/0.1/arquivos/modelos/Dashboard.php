<?php
require_once 'Classes/bancoDeDados.php';
require_once 'modelos/TipoArquivo.php';

class Dashboard
{
    /**
     * Relatório responsável por contar a quantidade de documentos que está cadastrado no banco de dados pelo tipo de arquivo configurado no sistema.
     * @return array
     */
    public function relatorio_quantidade_documentos_por_tipo_arquivo()
    {
        $sql = [['$project' => ['_id' => 0, 'documentos' => '$$ROOT']], ['$lookup' => ['localField' => 'documentos.non_existing_field', 'from' => 'tipo_arquivo', 'foreignField' => 'non_existing_field', 'as' => 'tipo_arquivo']], ['$unwind' => ['path' => '$tipo_arquivo', 'preserveNullAndEmptyArrays' => FALSE]], ['$match' => ['$expr' => ['$eq' => ['$documentos.id_tipo_arquivo', '$tipo_arquivo.id_tipo_arquivo']]]], ['$group' => ['_id' => ['documentos᎐id_tipo_arquivo' => '$documentos.id_tipo_arquivo'], 'COUNT(*)' => ['$sum' => 1]]], ['$project' => ['documentos.id_tipo_arquivo' => '$_id.documentos᎐id_tipo_arquivo', 'COUNT(*)' => '$COUNT(*)', '_id' => 0]], ['$sort' => ['documentos.id_tipo_arquivo' => 1]]];

        $retorno = pesquisa_banco_aggregate('documentos', $sql);

        $resultado_pesquisa = (array) [];

        foreach ($retorno as $document) {
            $array_temporario = (array) [];

            $array_temporario['id_tipo_arquivo'] = (int) intval($document['documentos']['id_tipo_arquivo'], 10);
            $array_temporario['quantidade_documentos'] = (int) intval($document['COUNT(*)'], 10);

            $objeto_tipo_arquivo = new TipoArquivo();
            $retorno_tipo_arquivo = $objeto_tipo_arquivo->pesquisar((array) ['filtro' => (array) ['id_tipo_arquivo', '===', (int) intval($array_temporario['id_tipo_arquivo'], 10)]]);

            if (empty($retorno_tipo_arquivo) == false) {
                $array_temporario['tipo_arquivo'] = (string) $retorno_tipo_arquivo['tipo_arquivo'];
            }

            array_push($resultado_pesquisa, $array_temporario);
        }

        return (array) $resultado_pesquisa;
    }

    /**
     * Função responsável por pesquisar no banco de dados o tamanho total de todos os arquivos cadastrados no banco de dados e retornar o valor dos mesmos
     * @return array
     */
    public function relatorio_tamanho_arquivos(){
        $sql = [['$group' => ['_id' => ['id_tipo_arquivo' => '$id_tipo_arquivo'], 'SUM(tamanho_arquivo)' => ['$sum' => '$tamanho_arquivo']]], ['$project' => ['id_tipo_arquivo' => '$_id.id_tipo_arquivo', 'SUM(tamanho_arquivo)' => '$SUM(tamanho_arquivo)', '_id' => 0]]];
        $retorno = pesquisa_banco_aggregate('documentos', $sql);

        $resultado_pesquisa = (array) [];

        foreach($retorno as $documento){
            $array_temporario = (array) [];
            $array_temporario['id_tipo_arquivo'] = (int) intval($documento['id_tipo_arquivo'], 10);
            $array_temporario['tamanho_arquivo'] = (float) floatval($documento['SUM(tamanho_arquivo)']);
            $array_temporario['tamanho_convertido'] = (float) round(converter_tamanho_arquivo($array_temporario['tamanho_arquivo'], true), 2, PHP_ROUND_HALF_UP);

            $objeto_tipo_arquivo = new TipoArquivo();
            $retorno_tipo_arquivo = $objeto_tipo_arquivo->pesquisar((array) ['filtro' => (array) ['id_tipo_arquivo', '===', (int) intval($array_temporario['id_tipo_arquivo'], 10)]]);

            if(empty($retorno_tipo_arquivo) == false){
                $array_temporario['tipo_arquivo'] = (string) $retorno_tipo_arquivo['tipo_arquivo'];
            }

            array_push($resultado_pesquisa, $array_temporario);
        }

        return $resultado_pesquisa;
    }
}
?>