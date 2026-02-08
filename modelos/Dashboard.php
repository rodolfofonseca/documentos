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
        // $sql = [['$project' => ['_id' => 0, 'documentos' => '$$ROOT']], ['$lookup' => ['localField' => 'documentos.non_existing_field', 'from' => 'tipo_arquivo', 'foreignField' => 'non_existing_field', 'as' => 'tipo_arquivo']], ['$unwind' => ['path' => '$tipo_arquivo', 'preserveNullAndEmptyArrays' => FALSE]], ['$match' => ['$expr' => ['$eq' => ['$documentos.id_tipo_arquivo', '$tipo_arquivo.id_tipo_arquivo']]]], ['$group' => ['_id' => ['documentos᎐id_tipo_arquivo' => '$documentos.id_tipo_arquivo'], 'COUNT(*)' => ['$sum' => 1]]], ['$project' => ['documentos.id_tipo_arquivo' => '$_id.documentos᎐id_tipo_arquivo', 'COUNT(*)' => '$COUNT(*)', '_id' => 0]], ['$sort' => ['documentos.id_tipo_arquivo' => 1]]];


        $sql = [
            [
                '$lookup' => [
                    'from' => 'tipo_arquivo',
                    'localField' => 'tipo_arquivo',
                    'foreignField' => '_id',
                    'as' => 'tipo_arquivo_dados'
                ]
            ],
            [
                '$unwind' => '$tipo_arquivo_dados'
            ],
            [
                '$group' => [
                    '_id' => '$tipo_arquivo_dados.tipo_arquivo',
                    'quantidade' => ['$sum' => 1]
                ]
            ],
            [
                '$project' => [
                    '_id' => 0,
                    'tipo_arquivo' => '$_id',
                    'quantidade' => 1
                ]
            ],
            [
                '$sort' => ['tipo_arquivo' => 1]
            ]
        ];

        $retorno = pesquisa_banco_aggregate('documentos', $sql);

        $resultado_pesquisa = (array) [];

        foreach ($retorno as $documento) {
            $array_temporario = (array) [];

            $array_temporario['quantidade'] = (int) $documento['quantidade'];
            $array_temporario['tipo_arquivo'] = (string) $documento['tipo_arquivo'];


            array_push($resultado_pesquisa, $array_temporario);
        }

        return (array) $resultado_pesquisa;
    }

    /**
     * Função responsável por pesquisar no banco de dados o tamanho total de todos os arquivos cadastrados no banco de dados e retornar o valor dos mesmos
     * @return array
     */
    public function relatorio_tamanho_arquivos(){
        $sql = [['$group' => ['_id' => '$tipo_arquivo', 'total_tamanho' => ['$sum' => '$tamanho_arquivo']]], ['$lookup' => ['from' => 'tipo_arquivo', 'localField' => '_id', 'foreignField' => '_id', 'as' => 'tipo']], ['$unwind' => '$tipo'], ['$project' => ['_id' => 0, 'tipo_arquivo_id' => '$_id', 'nome_arquivo' => '$tipo.tipo_arquivo', 'total_tamanho' => 1]]];
        $retorno = pesquisa_banco_aggregate('documentos', $sql);

        $resultado_pesquisa = (array) [];

        foreach($retorno as $documento){
            $array_temporario = (array) [];
            
            $array_temporario['tamanho_total'] = (float) $documento['total_tamanho'];
            $array_temporario['nome_extensao'] = (string) $documento['nome_arquivo'];

            array_push($resultado_pesquisa, $array_temporario);
        }

        return $resultado_pesquisa;
    }
}
?>