<?php
require_once 'Classes/bancoDeDados.php';
class Contas{
    private $id_conta;
    private $id_tipo_conta;
    private $id_documento_devedor;
    private $id_documento_pagamento;
    private $data_vencimento;
    private $data_pagamento;
    private $nome_conta;
    private $descricao_conta;
    private $valor_cadastro;
    private $valor_pagamento;
    private $status;

    private function tabela(){
        return (string) 'contas';
    }

    private function modelo(){
        return (array) ['id_conta' => (int) 0, 'id_tipo_conta' => (int) 0, 'id_documento_devedor' => (int) 0, 'id_documento_pagamento' => (int) 0, 'data_vencimento' => 'date', 'data_pagamento' => 'date', 'nome_conta' => (string) '', 'descricao_conta' => (string) '', 'valor_cadastro' => (float) 0, 'valor_pagamento' => (float) 0, 'status' => (string) 'AGUARDANDO'];
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_conta', $dados) == true){
            $this->id_conta = (int) intval($dados['codigo_conta'], 10);
        }

        if(array_key_exists('codigo_tipo_conta', $dados) == true){
            $this->id_tipo_conta = (int) intval($dados['codigo_tipo_conta'], 10);
        }

        if(array_key_exists('codigo_documento_devedor', $dados) == true){
            $this->id_documento_devedor = (int) intval($dados['codigo_documento_devedor'], 10);
        }

        if(array_key_exists('codigo_documento_pagamento', $dados) == true){
            $this->id_documento_pagamento = (int) intval($dados['codigo_documento_pagamento'], 10);
        }

        if(array_key_exists('data_vencimento', $dados) == true){
            $this->data_vencimento = model_date($dados['data_vencimento']);
        }

        if(array_key_exists('data_pagamento', $dados) == true){
            $this->data_pagamento = model_date($dados['data_pagamento']);
        }

        if(array_key_exists('nome_conta', $dados) == true){
            $this->nome_conta = (string) strtoupper($dados['nome_conta']);
        }

        if(array_key_exists('descricao_conta', $dados) == true){
            $this->descricao_conta = (string) $dados['descricao_conta'];
        }

        if(array_key_exists('valor_cadastro', $dados) == true){
            $this->valor_cadastro = (float) floatval($dados['valor_cadastro']);
        }

        if(array_key_exists('valor_pagamento', $dados) == true){
            $this->valor_pagamento = (float) floatval($dados['valor_pagamento']);
        }

        if(array_key_exists('status', $dados) == true){
            $this->status = (string) $dados['status'];
        }
    }

    public function salvar($dados){
        $this->colocar_dados($dados);
        $checar_existencia = (bool) model_check($this->tabela(), ['id_conta', '===', (int) $this->id_conta]);

        if($checar_existencia == true){
            if($this->status == 'PAGA'){
                model_insert('lancamento_contabil', ['id_lancamento' => (int) model_next('lancamento_contabil', 'id_lancamento'), 'conta_debito' => (string) '2.'.$this->id_tipo_conta, 'conta_credito' => (string) '1.0', 'descricao' => (string) 'Pagamento da conta '.$this->nome_conta, 'data_lancamento' => model_date(), 'valor' => (float) $this->valor_pagamento]);
            }

            return (bool) model_update($this->tabela(), ['id_conta', '===', (int) $this->id_conta], model_parse($this->modelo(), ['id_conta' => (int) $this->id_conta, 'id_tipo_conta' => (int) $this->id_tipo_conta, 'id_documento_devedor' => (int) $this->id_documento_devedor, 'id_documento_pagamento' => (int) $this->id_documento_pagamento, 'data_vencimento' => $this->data_vencimento, 'data_pagamento' => $this->data_pagamento, 'nome_conta' => (string) $this->nome_conta, 'descricao_conta' => (string) $this->descricao_conta, 'valor_cadastro' => (float) $this->valor_cadastro, 'valor_pagamento' => (float) $this->valor_pagamento, 'status' => (string) $this->status]));
        }else{
            model_insert('lancamento_contabil', ['id_lancamento' => (int) model_next('lancamento_contabil', 'id_lancamento'), 'conta_debito' => (string) '', 'conta_credito' => (string) '2.'.$this->id_tipo_conta, 'descricao' => (string) 'Cadastro da conta '.$this->nome_conta, 'data_lancamento' => model_date(), 'valor' => (float) $this->valor_pagamento]);
            return (bool) model_insert($this->tabela(), model_parse($this->modelo(), ['id_conta' => (int) model_next($this->tabela(), 'id_conta'), 'id_tipo_conta' => (int) $this->id_tipo_conta, 'id_documento_devedor' => (int) $this->id_documento_devedor, 'id_documento_pagamento' => (int) $this->id_documento_pagamento, 'data_vencimento' => $this->data_vencimento, 'data_pagamento' => $this->data_pagamento, 'nome_conta' => (string) $this->nome_conta, 'descricao_conta' => (string) $this->descricao_conta, 'valor_cadastro' => (float) $this->valor_cadastro, 'valor_pagamento' => (float) $this->valor_pagamento, 'status' => (string) $this->status]));
        }        
    }

    public function pesquisar_conta($dados){
        return (array) model_one($this->tabela(), $dados['filtro']);
    }

    public function pesquisar_conta_todas($dados){
        return (array) model_all($this->tabela(), $dados['filtro'], $dados['ordenacao'], $dados['limite']);
    }

    public function validar_vencimento_contas(){
        $data_hoje = (string) date('Y-m-d');
        $retorno_final = (array) ['contas_atrasadas' => (int) 0, 'contas_aguardando' => (int) 0];
        $retorno = (array) model_all($this->tabela(), ['and' => [['status', '===', (string) 'AGUARDANDO'], ['data_vencimento', '<=', model_date
        ($data_hoje, '23:59:59')]]]);
        

        if(empty($retorno) == false){
            foreach($retorno as $contas){
                $contas['status'] = (string) 'ATRASADA';
                model_update($this->tabela(), ['id_conta', '===', (int) $contas['id_conta']], model_parse($this->modelo(), $contas));
            }
        }

        $retorno_contas_atrasadas = pesquisa_banco_aggregate($this->tabela(), (array) [['$match' => ['status' => 'ATRASADA']], ['$group' => ['_id' => [], 'COUNT(*)' => ['$sum' => 1]]], ['$project' => ['COUNT(*)' => '$COUNT(*)', '_id' => 0]]]);
        
        foreach($retorno_contas_atrasadas as $contas_atrasadas){
            foreach($contas_atrasadas as $quantidade){
                $retorno_final['contas_atrasadas'] = (int) intval($quantidade, 10);
            }
        }
        
        $retorno_contas_aguardando = pesquisa_banco_aggregate($this->tabela(), (array) [['$match' => ['status' => 'AGUARDANDO']], ['$group' => ['_id' => [], 'COUNT(*)' => ['$sum' => 1]]], ['$project' => ['COUNT(*)' => '$COUNT(*)', '_id' => 0]]]);
        
        foreach($retorno_contas_aguardando as $contas_aguardando){
            foreach($contas_aguardando as $quantidade){
                $retorno_final['contas_aguardando'] = (int) intval($quantidade, 10);
            }
        }

        return (array) $retorno_final;
    }
}
?>