<script>
    /**
     * Função responsável por pesquisar os armários no sistema
     */
    function pesquisar_armario() {
        let nome_armario = document.querySelector('#nome_modal_armario').value;
        let descricao = document.querySelector('#descricao_modal_armario').value;
        let forma_visualizacao = document.querySelector('#visualizacao_modal_armario').value;

        sistema.request.post('/armario.php', {
            'rota': 'pesquisar_armario_todos',
            'usuario': CODIGO_USUARIO,
            'empresa': CODIGO_EMPRESA,
            'organizacao': CODIGO_ORGANIZACAO,
            'nome_armario': nome_armario,
            'descricao': descricao,
            'tipo': forma_visualizacao
        }, function (retorno) {
            let tabela = document.querySelector('#tabela_modal_armario tbody');

            tabela = sistema.remover_linha_tabela(tabela);

            let armarios = retorno.dados;
            let tamanho_retorno = armarios.length;

            if (tamanho_retorno < 1) {
                let linha = document.createElement('tr');
                linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM ARMÁRIO ENCONTRADO!', 'inner', true, 2));
                tabela.appendChild(linha);
            } else {
                sistema.each(armarios, function (index, armario) {
                    let linha = document.createElement('tr');

                    linha.appendChild(sistema.gerar_td(['text-left'], armario.nome_armario, 'inner'));
                    linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_armario_' + armario._id.$oid, 'SELECIONAR', ['btn', 'btn-success'], function selecionar_armario() {
                        selecionar_informacao_armario(armario._id.$oid);
                    }), 'append'));

                    tabela.appendChild(linha);
                });
            }
        }, false);
    }

    function selecionar_informacao_armario(valor){
        let codigo_organizacao = document.querySelector('#codigo_organizacao').value;

            if (codigo_organizacao == '') {
                Swal.fire({
                    title: "Erro de validação",
                    text: "É necessário primeiro selecionar uma organização",
                    icon: "error"
                });
            } else {
                document.querySelector('#codigo_armario').value = valor;
                let botao_fechar = document.querySelector('#botao_fechar_modal_armario');

                CODIGO_ARMARIO = valor;

                botao_fechar.click();
            }
    }
</script>

<div class="col-3 text-center">
        <label class="text">Armário</label>
        <div class="row">
            <div class="col-6">
                <input type="text" class="form-control custom-radius text-center" id="codigo_armario"
                    name="codigo_armario" readonly="true" />
            </div>
            <div class="col-6">
                <button class="btn btn-info  custom-radius botao_grande btn-lg" data-toggle="modal"
                    data-target="#modal_pesquisar_armario" onclick="abri_modal(event, false, '<?php echo $abrir_modal_armario; ?>');">Pesquisar</button>
            </div>
        </div>
    </div>

<!-- Modal de pesquisar armários -->
<div class="modal fade" id="modal_pesquisar_armario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pesquisa de Ármario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-4 text-center">
                        <label class="text" for="nome_modal_armario">Nome</label>
                        <input type="text" class="form-control custom-radius text-center" id="nome_modal_armario" placeholder="Nome" onkeyup="pesquisar_armario();"/>
                    </div>
                    <div class="col-4 text-center">
                        <label class="text" for="descricao_modal_armario">Descrição</label>
                        <input type="text" class="form-control custom-radius" id="descricao_modal_armario"
                        placeholder="Descrição" onkeyup="pesquisar_armario();"/>
                    </div>
                    <div class="col-2 text-center">
                        <label class="text" for="visualizacao_modal_armario">Visualizacao</label>
                        <select id="visualizacao_modal_armario" class="form-control custom-radius">
                            <option value="TODOS">TODOS</option>
                            <option value="PÚBLICO">PÚBLICO</option>
                            <option value="PRIVADO">PRIVADO</option>
                        </select>
                    </div>
                    <div class="col-2 text-center">
                        <button class="btn btn-info botao_vertical_linha custom-radius"
                            onclick="pesquisar_armario();">Pesquisar</button>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="tabela_modal_armario">
                                <thead class="bg-info text-white">
                                    <tr class="text-center">
                                        <th scope="col">Nome</th>
                                        <th scope="col">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="2" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA
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
                    id="botao_fechar_modal_armario">Fechar</button>
                <button type="button" class="btn btn-primary  custom-radius">Salvar</button>
            </div>
        </div>
    </div>
</div>