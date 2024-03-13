<?php
require_once 'Classes/bancoDeDados.php';

class Foto{
    private $id_foto;
    private $id_produto;
    private $endereco;

    private function tabela(){
        return (string) 'foto';
    }

    private function modelo(){
        return (array) ['id_foto' => (int) 0, 'id_produto' => (int) 0, 'endereco' => (string) ''];
    }

    public function salvar_dados($dados, $arquivo){
        $this->colocar_dados($dados);
        $retorno_dados = (bool) false;
        $checar = (bool) model_check($this->tabela(), ['id_foto', '===', (int) $this->id_foto]);
        
        $this->salvar_documento($arquivo);

        if($checar == true){
            $retorno_dados = (bool) $this->alterar();
        }else{
            $retorno_dados = (bool) $this->cadastrar();
        }


        return $retorno_dados;
    }

    private function salvar_documento($arquivo){
        if(isset($arquivo['arquivo']) && $arquivo['arquivo']['error'] == 0){
            $nome_atual = (string) $arquivo['arquivo']['name'];
            $nome_temporario = (string) $arquivo['arquivo']['tmp_name'];
            $extensao = strchr($nome_atual, '.');
            $extensao = strtolower($extensao);
            $endereco_documento = (string) '';
            $diretorio = (string) str_replace('\\', '/', __DIR__);
            $diretorio = (string) str_replace('modelos', '', $diretorio);
            $endereco_documento = $diretorio.'imagens/produtos/';

            if(is_dir($endereco_documento)){

            }else{
                mkdir($endereco_documento, 0777);
            }

            $nome_documento = $endereco_documento.str_pad($this->id_foto, 12, '0', STR_PAD_LEFT).$extensao;

            $this->endereco = (string) $nome_documento;

            chmod($endereco_documento, 0777);

            if(@move_uploaded_file($nome_temporario, $nome_documento)){
                chmod($nome_documento, 0777);
            }
        }
    }

    private function alterar(){
        return model_update($this->tabela(), ['id_foto', '===', (int) $this->id_foto], model_parse($this->modelo(), ['id_foto' => (int) $this->id_foto, 'id_produto' => (int) $this->id_produto, 'endereco' => (string) $this->endereco]));
    }

    private function cadastrar(){
        return model_insert($this->tabela(), model_parse($this->modelo(), ['id_foto' => (int) $this->id_foto, 'id_produto' => (int) $this->id_produto, 'endereco' => (string) $this->endereco]));
    }

    private function colocar_dados($dados){
        if(array_key_exists('codigo_foto', $dados) == true){
            $this->id_foto = (int) intval($dados['codigo_foto'], 10);
        }else{
            $this->id_foto = (int) model_next($this->tabela(), 'id_foto');
        }

        if(array_key_exists('codigo_produto', $dados) == true){
            $this->id_produto  = (int) intval($dados['codigo_produto'], 10);
        }

        if(array_key_exists('endereco', $dados) == true){
            $this->endereco = (string) intval($dados['endereco'], 10);
        }
    }

    public function pesquisar_fotos($codigo_produto){
        return (array) model_all($this->tabela(), ['id_produto', '===', (int) $codigo_produto]);
    }

    public function remover_imagem($codigo){
        $retorno_pesquisa_foto = (array) model_one($this->tabela(), ['id_foto', '===', (int) $codigo]);

        if(empty($retorno_pesquisa_foto) == false){
            if(array_key_exists('endereco', $retorno_pesquisa_foto) == true){
                unlink($retorno_pesquisa_foto['endereco']);      
            }
        }

        return (bool) model_delete($this->tabela(), ['id_foto', '===', (int) $codigo]);
    }
}
?>