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
                    <div class="col-3 text-center">
                        <label class="text" for="nome_modal_armario">Nome</label>
                        <input type="text" class="form-control custom-radius text-center" id="nome_modal_armario" placeholder="Nome" onkeyup="pesquisar_armario();"/>
                    </div>
                    <div class="col-4 text-center">
                        <label class="text" for="descricao_modal_armario">Descrição</label>
                        <input type="text" class="form-control custom-radius" id="descricao_modal_armario"
                        placeholder="Descrição" onkeyup="pesquisar_armario();"/>
                    </div>
                    <div class="col-3 text-center">
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
                                        <td colspan="2" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA PESQUISA</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger custom-radius" data-dismiss="modal" id="botao_fechar_modal_armario">Fechar</button>
                <button type="button" class="btn btn-primary custom-radius">Salvar</button>
            </div>
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
                    <div class="col-3 tex-center">
                        <label class="text" for="nome_modal_prateleira">Nome</label>
                        <input type="text" class="form-control custom-radius" id="nome_modal_prateleira" placeholder="Nome" onkeyup="pesquisar_prateleira();"/>
                    </div>
                    <div class="col-4 text-center">
                        <label class="text" for="descricao_modal_prateleira">Descrição</label>
                        <input type="text" class="form-control custom-radius" id="descricao_modal_prateleira"
                            placeholder="Descrição" onkeyup="pesquisar_prateleira();"/>
                    </div>
                    <div class="col-3 text-center">
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
                                        <th scope="col">Nome</th>
                                        <th scope="col">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="2" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA PESQUISA</td>
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

<!-- Modal para pesquisar Caixas -->
<div class="modal fade" id="modal_pesquisar_caixa" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pesquisa de Caixa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-3 text-center">
                        <label for="nome_modal_caixa" class="text">Nome</label>
                        <input type="text" class="form-control custom-radius" id="nome_modal_caixa" placeholder="Nome" onkeydown="pesquisar_caixa();"/>
                    </div>
                    <div class="col-4 text-center">
                        <label class="text" for="descricao_modal_caixa">Descrição</label>
                        <input type="text" class="form-control custom-radius" id="descricao_modal_caixa" placeholder="Descrição" onkeydown="pesquisar_caixa();"/>
                    </div>
                    <div class="col-3 text-center">
                        <label class="text" for="visualizacao_modal_caixa">visualização</label>
                        <select class="form-control custom-radius" id="visualizacao_modal_caixa">
                            <option value="TODOS">TODOS</option>
                            <option value="PUBLICO">PÚBLICO</option>
                            <option value="PRIVADO">PRIVADO</option>
                            </select>
                        </div>
                    <div class="col-2 text-center">
                        <button class="btn btn-info botao_vertical_linha  custom-radius"
                            onclick="pesquisar_caixa();">Pesquisar</button>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="tabela_modal_caixa">
                                <thead class="bg-info text-white">
                                    <tr class="text-center">
                                        <th scope="col">Nome</th>
                                        <th scope="col">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="2" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA PESQUISA</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger  custom-radius" data-dismiss="modal"
                    id="botao_fechar_modal_caixa">Fechar</button>
                <button type="button" class="btn btn-primary  custom-radius">Salvar</button>
            </div>
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
                    <div class="col-3 text-center">
                        <label class="text" for="nome_modal_organizacao">Nome</label>
                        <input type="text" class="form-control custom-radius" id="nome_modal_organizacao" placeholder="Nome" onkeyup="pesquisar_organizacao();"/>
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
                    <div class="col-3 text-center">
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
                                        <th scope="col">Nome</th>
                                        <th scope="col">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="2" class="text-center">UTILIZE OS FILTROS PARA FACILITAR SUA PESQUISA</td>
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