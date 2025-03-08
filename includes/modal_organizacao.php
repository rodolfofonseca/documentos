<script>
    function pesquisar_organizacao() {
        let codigo_organizacao = document.querySelector('#codigo_modal_organizacao').value;
        let descricao_organizacao = document.querySelector('#descricao_modal_organizacao').value;
        let nome_organizacao = document.querySelector('#nome_modal_organizacao').value;
        let forma_visualizacao = document.querySelector('#visualizacao_modal_organizacao').value;

        let objeto_pesquisa = {
            'rota': 'pesquisar_todos',
            'codigo_usuario': CODIGO_USUARIO,
            'codigo_empresa': CODIGO_EMPRESA,
            'codigo_organizacao': codigo_organizacao,
            'nome_organizacao': nome_organizacao,
            'descricao': descricao_organizacao,
            'forma_visualizacao': forma_visualizacao
        };

        sistema.request.post('/organizacao.php', objeto_pesquisa, function (retorno) {
            let organizacoes = retorno.dados;
            let tamanho_retorno = organizacoes.length;
            let tabela = document.querySelector('#tabela_modal_organizacao tbody');

            tabela = sistema.remover_linha_tabela(tabela);

            if (tamanho_retorno < 1) {
                let linha = document.createElement('tr');
                linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA ORGANIZAÇÃO ENCONTRADA!', 'inner', true, 3));
                tabela.appendChild(linha);
            } else {
                sistema.each(organizacoes, function (index, organizacao) {
                    let linha = document.createElement('tr');

                    linha.appendChild(sistema.gerar_td(['text-center'], sistema.str_pad(organizacao.id_organizacao, 3, '0'), 'inner'));
                    linha.appendChild(sistema.gerar_td(['text-left'], organizacao.nome_organizacao, 'inner'));
                    linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_organizacao_' + organizacao.id_organizacao, 'SELECIONAR', ['btn', 'btn-success'], function selecionar_organizacao() {
                        selecionar_informacao_organizacao(organizacao.id_organizacao);
                    }), 'append'));

                    tabela.appendChild(linha);
                });
            }
        }, false);
    }

    function selecionar_informacao_organizacao(valor){
        document.querySelector('#codigo_organizacao').value = valor;
        CODIGO_ORGANIZACAO = valor;
        
        let botao_fechar = document.querySelector('#botao_fechar_modal_organizacao');
        botao_fechar.click();
    }

</script>

<div class="col-3 text-center">
    <label class="text">Organização</label>
    <div class="row">
        <div class="col-6">
            <input type="text" class="form-control custom-radius tex-center" id="codigo_organizacao" value="0"
                readonly="true" />
        </div>
        <div class="col-6">
            <button class="btn btn-info custom-radius botao_grande btn-lg" data-toggle="modal" data-target="#modal_pesquisar_organizacao" onclick="abri_modal(event, false, '<?php echo $abrir_modal_organizacao; ?>');">Pesquisar</button>
        </div>
    </div>
</div>

<!-- Modal de pesquisa de organização -->
<div class="modal fade" id="modal_pesquisar_organizacao" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pesquisa de Organização</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-2 text-center">
                        <label class="text" for="codigo_modal_caixa">Código</label>
                        <input type="text" class="form-control custom-radius text-center" id="codigo_modal_organizacao"
                            placeholder="Código" sistema-mask="codigo" onkeyup="pesquisar_organizacao();" />
                    </div>
                    <div class="col-2 text-center">
                        <label class="text" for="nome_modal_organizacao">Nome</label>
                        <input type="text" class="form-control custom-radius" id="nome_modal_organizacao"
                            placeholder="Nome" onkeyup="pesquisar_organizacao();" />
                    </div>
                    <div class="col-4 text-center">
                        <label class="text" for="descricao_modal_organizacao">Descrição</label>
                        <input type="text" class="form-control custom-radius" id="descricao_modal_organizacao"
                            placeholder="Descrição" onkeyup="pesquisar_organizacao();" />
                    </div>
                    <div class="col-2 text-center">
                        <label class="text" for="visualizacao_modal_organizacao">Visualizacao</label>
                        <select id="visualizacao_modal_organizacao" class="form-control custom-radius">
                            <option value="TODOS">TODOS</option>
                            <option value="PRIVADO">PRIVADO</option>
                            <option value="PUBLICO">PÚBLICO</option>
                        </select>
                    </div>
                    <div class="col-2 text-center">
                        <button class="btn btn-info botao_vertical_linha  custom-radius"
                            onclick="pesquisar_organizacao();">Pesquisar</button>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="tabela_modal_organizacao">
                                <thead class="bg-info text-white">
                                    <tr class="text-center">
                                        <th scope="col">#</th>
                                        <th scope="col">Nome</th>
                                        <th scope="col">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="3" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA PESQUISA</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger  custom-radius" data-dismiss="modal"
                    id="botao_fechar_modal_organizacao">Fechar</button>
                <button type="button" class="btn btn-primary  custom-radius">Salvar</button>
            </div>
        </div>
    </div>
</div>