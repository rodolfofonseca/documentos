<?php
require_once 'Classes/bancoDedados.php';

class Empresa{
    private $id_empresa;
    private $nome_empresa;
    private $representante;
    private $informacao_contato;
    private $chave_sistema;
    private $data_hora_ativacao;
    private $data_hora_bloqueio;

    private function tabela(){
        return (string) 'empresa';
    }

    private function modelo(){
        return (array) ['id_empresa' => (int) 0, 'nome_empresa' => (string) '', 'representante' => (string) '', 'informacao_contato' => (string) '', 'chave_sistema' => (string) '', 'data_hora_ativacao' => 'date', 'data_hora_bloqueio' => 'date'];
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_empresa', $dados) == true){
            $this->id_empresa = (int) intval($dados['codigo_empresa'], 10);
        }else{
            $this->id_empresa = (int) intval(0, 10);
        }

        if(array_key_exists('nome_empresa', $dados) == true){
            $this->nome_empresa = (string) strtoupper($dados['nome_empresa']);
        }else{
            $this->nome_empresa = (string) '';
        }

        if(array_key_exists('representante', $dados) == true){
            $this->representante = (string) strtoupper($dados['representante']);
        }else{
            $this->representante = (string) '';
        }

        if(array_key_exists('informacao_contato', $dados) == true){
            $this->informacao_contato = (string) $dados['informacao_contato'];
        }else{
            $this->informacao_contato = (string) '';
        }

        if(array_key_exists('chave_sistema', $dados) == true){
            $this->chave_sistema = (string) $dados['chave_sistema'];
        }else{
            $this->chave_sistema = '';
        }

        if(array_key_exists('data_hora_ativacao', $dados) == true){
            $this->data_hora_ativacao = model_date($dados['data_hora_ativacao']);
        }else{
            $this->data_hora_ativacao = model_date();
        }

        if(array_key_exists('data_hora_bloqueio', $dados) == true){
            $this->data_hora_bloqueio = model_date($dados['data_hora_bloqueio']);
        }else{
            date_default_timezone_set('America/Sao_Paulo');
            $date = (string) date('Y-m-d');

            $date_time = new DateTime($date);
            $data = $date_time->modify('+1 month');
            $this->data_hora_bloqueio = model_date($data->format('Y-m-d'));
        }
    }

    public function salvar_dados($dados){
        $this->colocar_dados($dados);

        $retorno_pesquisa = (bool) ravf_corp_model_check($this->tabela(), ['id_empresa', '===', (int) $this->id_empresa]);
        $retorno_operacao = (bool) false;

        $modelo_insercao = (array) model_parse((array) $this->modelo(), (array) ['id_empresa' => (int) $this->id_empresa, 'nome_empresa' => (string) $this->nome_empresa, 'representante' => (string) $this->representante, 'informacao_contato' => (string) $this->informacao_contato, 'chave_sistema' => (string) $this->chave_sistema, 'data_hora_ativacao' => $this->data_hora_ativacao, 'data_hora_bloqueio' => $this->data_hora_bloqueio]);

        if($retorno_pesquisa == true){
            $retorno_empresa = (array) $this->pesquisar((array) ['filtro' => (array) ['id_empresa', '===', (int) $this->id_empresa]]);

            if(empty($retorno_empresa) == false){
                $modelo_insercao = (array) model_parse((array) $this->modelo(), $retorno_empresa);

                if($this->chave_sistema == $retorno_empresa['chave_sistema']){
                    $retorno_operacao = (bool) model_update($this->tabela(), ['id_empresa', '===', (int) $this->id_empresa], (array) $modelo_insercao);
                    $retorno_ravf_corp = (bool) ravf_corp_model_update($this->tabela(), ['id_empresa', '===', (int) $this->id_empresa], $modelo_insercao);
                }else{
                    $modelo_insercao['chave_sistema'] = (string) $this->chave_sistema;
                    $modelo_insercao['data_hora_ativacao'] = $this->data_hora_ativacao;
                    $modelo_insercao['data_hora_bloqueio'] = $this->data_hora_bloqueio;
                    
                    $retorno_operacao = (bool) model_update($this->tabela(), ['id_empresa', '===', (int) $this->id_empresa], (array) $modelo_insercao);
                    $retorno_ravf_corp = (bool) ravf_corp_model_update($this->tabela(), ['id_empresa', '===', (int) $this->id_empresa], $modelo_insercao);
                }
            }else{
                return (bool) false;
            }
        }else{
            $retorno_operacao = (bool) model_check($this->tabela(), ['nome_empresa', '===', (string) $this->nome_empresa]);

            if($retorno_operacao == false){
                $this->id_empresa = (int) intval(ravf_corp_model_next($this->tabela(), 'id_empresa'), 10);

                $modelo_insercao['id_empresa'] = (int) $this->id_empresa;

                $retorno_checage_chave = (bool) ravf_corp_model_check((string) 'chave_sistema', (array) ['and' => (array) [(array) ['chave', '===', (string) $this->chave_sistema], (array) ['status', '===', (string) 'AGUARDANDO']]]);

                if($retorno_checage_chave == true){
                    $retorno_operacao = (bool) model_insert($this->tabela(), (array) $modelo_insercao);
                    $retorno_ravf_corp = (bool) ravf_corp_model_insert($this->tabela(), (array) $modelo_insercao);
                }else{
                    return (bool) false;
                }

            }else{
                return (bool) false;
            }
        }

        $retorno_chave_ravf_corp = (array) ravf_corp_model_one('chave_sistema', ['chave', '===', (string) $this->chave_sistema]);
        $retorno_chave_ravf_corp['data_utilizacao'] = $this->data_hora_ativacao;

        $retorno_ravf_corp = (bool) ravf_corp_model_update('chave_sistema', ['chave', '===', (string) $this->chave_sistema], (array) $retorno_chave_ravf_corp);

        return (bool) $retorno_operacao;
    }

    public function pesquisar($filtro){
        return (array) model_one($this->tabela(), $filtro['filtro']);
    }

    public function pesquisar_todos($filtro){
        return (array) model_all($this->tabela(), $filtro['filtro'], $filtro['ordenacao'], $filtro['limite']);
    }

    /**
     * Função responsável por excluir uma empresa cadastrada no banco de dados.
     * @param array $dados array contendo o "codigo_empresa"
     * @return boolean TRUE ou FALSE de acordo com o retorno da função.
     */
    public function excluir($dados){
        $this->colocar_dados($dados);

        return (bool) model_delete($this->tabela(), ['id_empresa', '===', (int) $this->id_empresa]);
    }

    /**
     * Verifica se o sistema está dentro do prazo de validade da chave, lembranbdo que as funcionalidades do sistema é vitalícia, o que é pago apenas as funcionalidades de suporte.
     * @param array $dados
     * @return bool
     */
    public function verificar_ativacao($dados){
        $this->colocar_dados($dados);

        $retorno_verificacao = (array) $this->pesquisar((array) ['filtro' => (array) ['and' => (array) [(array) ['id_empresa', '===', (int) intval($this->id_empresa, 10)], (array) ['data_hora_bloqueio', '<=', model_date()]]]]);

        if(empty($retorno_verificacao) == true){
            return (bool) true;
        }else{
            if(array_key_exists('chave_sistema', $retorno_verificacao) == true){
                $retorno_update = (bool) ravf_corp_model_update('chave_sistema', ['chave', '===', $retorno_verificacao['chave_sistema']], ['status' => (string) 'INATIVO']);
            }
            return (bool) false;
        }
    }
}
?>