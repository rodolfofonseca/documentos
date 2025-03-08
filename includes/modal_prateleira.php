<script>
     /** 
     * Função responsável por pesquisar as organizações que estão cadastradas no sistema.
    */
    function pesquisar_prateleira() {
        let codigo_armario = sistema.int(document.querySelector('#codigo_armario').value);
        let codigo_prateleira = sistema.int(document.querySelector('#codigo_modal_prateleira').value);
        let nome_prateleira = document.querySelector('#nome_modal_prateleira').value;
        let descricao_prateleira = document.querySelector('#descricao_modal_prateleira').value;
        let visualizacao = document.querySelector('#visualizacao_modal_prateleira').value;

        sistema.request.post('/prateleira.php', {
            'rota': 'pesquisar_prateleira_todas',
            'codigo_armario': codigo_armario,
            'codigo_prateleira': codigo_prateleira,
            'nome_prateleira': nome_prateleira,
            'descricao': descricao_prateleira,
            'forma_visualizacao': visualizacao,
            'codigo_usuario': CODIGO_USUARIO,
            'codigo_empresa': CODIGO_EMPRESA
        }, function (retorno) {
            let tabela = document.querySelector('#tabela_modal_prateleira tbody');
            let prateleiras = retorno.dados;
            let tamanho_retorno = prateleiras.length;

            tabela = sistema.remover_linha_tabela(tabela);

            if (tamanho_retorno < 1) {
                let linha = document.createElement('tr');
                linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA PRATELEIRA ENCONTRADO!', 'inner', true, 3));
                tabela.appendChild(linha);
            } else {
                sistema.each(prateleiras, function (index, prateleira) {
                    let linha = document.createElement('tr');

                    linha.appendChild(sistema.gerar_td(['text-center'], sistema.str_pad(prateleira.id_prateleira, 3, '0'), 'inner'));
                    linha.appendChild(sistema.gerar_td(['text-left'], prateleira.nome_prateleira, 'inner'));
                    linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_prateleira_' + prateleira.id_prateleira, 'SELECIONAR', ['btn', 'btn-success'], function selecionar_prateleira() {
                        selecionar_informacao_prateleira(prateleira.id_prateleira);
                    }), 'append'));

                    tabela.appendChild(linha);
                });
            }
        }, false);
    }

    function selecionar_informacao_prateleira(valor){
        document.querySelector('#codigo_prateleira').value = valor;
        CODIGO_ORGANIZACAO = valor;
        
        let botao_fechar = document.querySelector('#botao_fechar_modal_prateleira');
        botao_fechar.click();
    }
</script>

<div class="col-3 tex-center">
    <label class="text">Prateleira</label>
    <div class="row">
        <div class="col-6">
            <input type="text" class="form-control custom-radius text-center" id="codigo_prateleira"
                name="codigo_prateleira" value="0" readonly="true" />
        </div>
        <div class="col-6">
            <button class="btn btn-info  custom-radius botao_grande btn-lg" data-toggle="modal"
                data-target="#modal_pesquisar_prateleira" onclick="abri_modal(event, false, '<?php echo $abrir_modal_prateleira; ?>');">Pesquisar</button>
        </div>
    </div>
</div>

<!-- Modal de pesquisar prateleiras -->
<div class="modal fade" id="modal_pesquisar_prateleira" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pesquisa de Prateleira</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-2 text-center">
                        <label class="text" for="codigo_modal_prateleira">Código</label>
                        <input type="text" class="form-control custom-radius text-center" id="codigo_modal_prateleira"
                            placeholder="Código" sistema-mask="codigo" onkeyup="pesquisar_prateleira();" />
                    </div>
                    <div class="col-2 tex-center">
                        <label class="text" for="nome_modal_prateleira">Nome</label>
                        <input type="text" class="form-control custom-radius" id="nome_modal_prateleira"
                            placeholder="Nome" onkeyup="pesquisar_prateleira();" />
                    </div>
                    <div class="col-4 text-center">
                        <label class="text" for="descricao_modal_prateleira">Descrição</label>
                        <input type="text" class="form-control custom-radius" id="descricao_modal_prateleira"
                            placeholder="Descrição" onkeyup="pesquisar_prateleira();" />
                    </div>
                    <div class="col-2 text-center">
                        <label class="text" for="visualizacao_modal_prateleira">Visualizacao</label>
                        <select class="form-control custom-radius" id="visualizacao_modal_prateleira">
                            <option value="TODOS">TODOS</option>
                            <option value="PUBLICO">PÚBLICO</option>
                            <option value="PRIVADO">PRIVADO</option>
                        </select>
                    </div>
                    <div class="col-2 text-center">
                        <button class="btn btn-info botao_vertical_linha  custom-radius"
                            onclick="pesquisar_prateleira();">Pesquisar</button>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="tabela_modal_prateleira">
                                <thead class="bg-info text-white">
                                    <tr class="text-center">
                                        <th scope="col">#</th>
                                        <th scope="col">Nome</th>
                                        <th scope="col">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="3" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA
                                            PESQUISA</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger  custom-radius" data-dismiss="modal"
                    id="botao_fechar_modal_prateleira">Fechar</button>
                <button type="button" class="btn btn-primary  custom-radius">Salvar</button>
            </div>
        </div>
    </div>
</div>