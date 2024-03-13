<?php
interface ModelosInterface{ 
    /**
     * Método responsável por pesquisar apenas uma informação no banco de dados.
     * @param array $dados ['filtro', 'ordenacao']
     * @return array com as informações.
     */
    public function pesquisar($dados);

    /**
     * Método responsável por pesquisar apenas uma informação no banco de dados.
     * @param array $dados ['filtro', 'ordenacao']
     * @return array com as informações.
     */
    public function pesquisar_todos($dados);
    
    /**
     * Método responsável por colocar as informações e verificar se é para salvar ou alterar.
     * @param array $dados com as informações que devem ser colocadas na variável
     * @return bool com o retorno do banco de dados.
     */
    public function salvar($dados);
    
    /**
     * Método responsável por retornar o nome da tabela.
     * @return string nome da tabela.
     */
    public function tabela();
    
    /**
     * Método responsável por retornar o modelo do banco de dados.
     * @return array modelo do banco de dados.
     */
    public function modelo();

    /**
     * Método responsável por colocar as informações nas devidas variáveis
     * @param string dados
     */
    public function colocar_dados($dados);
}
?>