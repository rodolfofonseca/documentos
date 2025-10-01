<?php
interface Template{
    /**
     * Função responsável por retornar a tabela
     * @return string
     */
    public function tabela();

    /**
     * Função responsável por retornar o modelo do banco de dados
     * @return array
     */
    public function modelo();
    
    /**
     * Função responsável por colocar os dados vindos do front para as variávels de manipulação.
     * @param array $dados
     * @return void
     */
    public function colocar_dados($dados);
    
    /**
     * Função responsável por salvar os dados no banco de dados.
     * @param array $dados
     * @return array
     */
    public function salvar_dados($dados);
    
    /**
     * Função responsável por pesquisar as informações no banco de dados e retornar apenas uma opção
     * @param array $dados
     * @return array
     */
    public function pesquisar($dados);
    
    /**
     * Função responsável por pesquisar as informações no banco de dados e retornar todas que foram encontradas com os filtros.
     * @param array $dados
     * @return array
     */
    public function pesquisar_todos($dados);
    
    /**
     * Função responsável por deletar do banco de dados os dados que foram encontradas com os filtros que foram passados
     * @param array $dados
     * @return array
     */
    public function deletar($dados);
}
?>