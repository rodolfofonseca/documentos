<?php
require_once 'Classes/bancoDeDados.php';

class RelatoriosContabeis{

    /**
     * Função responsável por realizar a consulta na base de dados dos lançamentos contábeis para montar o relatório de movimentação das contas.
     * @param string $conta
     * @param string $data_inicial
     * @param string $data_final
     * @return array
     */
    public function relatorio_saldo_nas_contas($conta, $data_inicial, $data_final){
        // $retorno_pesquisas = (array) model_all('lancamento_contabil', ['and' => [['data_lancamento', '>=', model_date((string) $data_inicial)], ['data_lancamento', '<=', model_date((string)$data_final)]]]);
        //'2023-01-15'
        $retorno_pesquisas = (array) model_all('lancamento_contabil', ['and' => [['data_lancamento', '>=', model_date((string) $data_inicial)], ['data_lancamento', '<=', model_date((string) $data_final)]]]);
        $retorno = (array) [];
        
        if(empty($retorno_pesquisas) == false){
            foreach($retorno_pesquisas as $retorno_pesquisa){
                if($retorno_pesquisa['conta_debito'] == $conta || $retorno_pesquisa['conta_credito'] == $conta){
                    array_push($retorno, $retorno_pesquisa);
                }
            }
        }

        return (array) $retorno;
    }
}
?>