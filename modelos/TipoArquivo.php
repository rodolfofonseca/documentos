<?php
require_once 'Classes/bancoDeDados.php';

class TipoArquivo
{

    private $id_tipo_arquivo;
    private $descricao;
    private $tipo_arquivo;
    private $usar;
    private $endereco_documento;

    private function tabela(){

    }

    private function modelo(){

    }

    private function colocar_dados(){

    }

    public function salvar_dados(){

    }

    public function pesquisar(){

    }

    public function pesquisar_todos(){

    }

    /**
     * Função responsável por pesquisar e montar o componente de visualização, onde o usuário pode configurar os tipos de documentos aceitos pela empresa e onde o mesmo deseja que seja salvo.
     */
    public function montar_array_tipo_arquivo()
    {
        $array_padrao_tipo_arquivo = (array) [0 => (array) ['tipo' => (string) '.jpg', 'descricao' => (string) 'IMAGEM'], 1 => (array) ['tipo' => (string) '.doc', 'descricao' => (string) 'WORD'], 2 => (array) ['tipo' => (string) '.odt', 'descricao' => (string) 'WORD'], 3 => (array) ['tipo' => (string) '.rar', 'descricao' => (string) 'WINRAR'], 4 => (array) ['tipo' => (string) '.bin', 'descricao' => (string) 'BIN'], 5 => (array) ['tipo' => (string) '.asta', 'descricao' => (string) 'ASTAH'], 6 => (array) ['tipo' => (string) '.jpeg', 'descricao' => (string) 'IMAGEM'], 7 => (array) ['tipo' => (string) '.docx', 'descricao' => (string) 'WORD'], 8 => (array) ['tipo' => (string) '.pdf', 'descricao' => (string) 'PDF'], 9 => (array) ['tipo' => (string) '.zip', 'descricao' => (string) 'WINRAR'], 10 => (array) ['tipo' => (string) '.png', 'descricao' => (string) 'IMAGEM'], 11 => (array) ['tipo' => (string) '.psd', 'descricao' => (string) 'PHOTOSHOP']];

        return (array) $array_padrao_tipo_arquivo;
    }
}
