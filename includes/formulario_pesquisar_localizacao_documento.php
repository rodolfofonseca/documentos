<script>
    function selecionar_informacao(tipo, valor) {
        if (tipo == 'ARMARIO') {
            let codigo_organizacao = document.querySelector('#codigo_organizacao').value;

            if (codigo_organizacao == '') {
                Swal.fire({title: "Erro de validação", text: "É necessário primeiro selecionar uma organização", icon: "error" });
            } else {
                document.querySelector('#codigo_armario').value = valor;
                let botao_fechar = document.querySelector('#botao_fechar_modal_armario');

                CODIGO_ARMAARIO = valor;

                botao_fechar.click();
            }

        } else if (tipo == 'PRATELEIRA') {
            let codigo_armario = document.querySelector('#codigo_armario').value;

            if (codigo_armario == '') {
                Swal.fire({title: "Erro de validação", text: "É necessário primeiro selecionar um armário", icon: "error" });
                botao_fechar.click();
            } else {
                document.querySelector('#codigo_prateleira').value = valor;
                let botao_fechar = document.querySelector('#botao_fechar_modal_prateleira');

                CODIGO_PRATELEIRA = valor;

                botao_fechar.click();
            }


        } else if (tipo == 'CAIXA') {
            let codigo_prateleira = document.querySelector('#codigo_prateleira').value;

            if (codigo_prateleira == '') {
                Swal.fire({title: "Erro de validação", text: "É necessário primeiro selecionar uma prateleira", icon: "error"});
            } else {
                document.querySelector('#codigo_caixa').value = valor;
                let botao_fechar = document.querySelector('#botao_fechar_modal_caixa');

                CODIGO_CAIXA = valor;

                botao_fechar.click();
            }

        } else if (tipo == 'ORGANIZACAO') {
            document.querySelector('#codigo_organizacao').value = valor;
            let botao_fechar = document.querySelector('#botao_fechar_modal_organizacao');

            CODIGO_ORGANIZACAO = valor;

            botao_fechar.click();
        }
    }

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
                    linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_armario_' + armario._id.$id, 'SELECIONAR', ['btn', 'btn-success'], function selecionar_armario() {
                        selecionar_informacao('ARMARIO', armario._id.$oid);
                    }), 'append'));

                    tabela.appendChild(linha);
                });
            }
        }, false);
    }
    /** 
     * Função responsável por pesquisar as organizações do sistema
    */
    function pesquisar_organizacao() {
        let descricao_organizacao = document.querySelector('#descricao_modal_organizacao').value;
        let nome_organizacao = document.querySelector('#nome_modal_organizacao').value;
        let forma_visualizacao = document.querySelector('#visualizacao_modal_organizacao').value;

        let objeto_pesquisa = {
            'rota': 'pesquisar_todos',
            'codigo_usuario': CODIGO_USUARIO,
            'codigo_empresa': CODIGO_EMPRESA,
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
                linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA ORGANIZAÇÃO ENCONTRADA!', 'inner', true, 2));
                tabela.appendChild(linha);
            } else {
                sistema.each(organizacoes, function (index, organizacao) {
                    let linha = document.createElement('tr');

                    linha.appendChild(sistema.gerar_td(['text-left'], organizacao.nome_organizacao, 'inner'));
                    linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_organizacao_' + organizacao._id.$oid, 'SELECIONAR', ['btn', 'btn-success'], function selecionar_organizacao() {
                        selecionar_informacao('ORGANIZACAO', organizacao._id.$oid);
                    }), 'append'));

                    tabela.appendChild(linha);
                });
            }
        }, false);
    }

    /** 
     * Função responsável por pesquisar as organizações que estão cadastradas no sistema.
    */
    function pesquisar_prateleira() {
        let codigo_armario = document.querySelector('#codigo_armario').value;
        let nome_prateleira = document.querySelector('#nome_modal_prateleira').value;
        let descricao_prateleira = document.querySelector('#descricao_modal_prateleira').value;
        let visualizacao = document.querySelector('#visualizacao_modal_prateleira').value;

        sistema.request.post('/prateleira.php', {
            'rota': 'pesquisar_prateleira_todas',
            'armario': codigo_armario,
            'nome_prateleira': nome_prateleira,
            'descricao': descricao_prateleira,
            'tipo': visualizacao,
            'usuario': CODIGO_USUARIO,
            'empresa': CODIGO_EMPRESA
        }, function (retorno) {
            let tabela = document.querySelector('#tabela_modal_prateleira tbody');
            let prateleiras = retorno.dados;
            let tamanho_retorno = prateleiras.length;

            tabela = sistema.remover_linha_tabela(tabela);

            if (tamanho_retorno < 1) {
                let linha = document.createElement('tr');
                linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA PRATELEIRA ENCONTRADO!', 'inner', true, 2));
                tabela.appendChild(linha);
            } else {
                sistema.each(prateleiras, function (index, prateleira) {
                    let linha = document.createElement('tr');

                    linha.appendChild(sistema.gerar_td(['text-left'], prateleira.nome_prateleira, 'inner'));
                    linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_prateleira_' + prateleira._id.$oid, 'SELECIONAR', ['btn', 'btn-success'], function selecionar_prateleira() {
                        selecionar_informacao('PRATELEIRA', prateleira._id.$oid);
                    }), 'append'));

                    tabela.appendChild(linha);
                });
            }
        }, false);
    }

    /** 
     * Função responsável por montar o filtro de pesquisa que será utilizado para pesquisar as caixas no banco de dados
    */
    function pesquisar_caixa() {
        let codigo_prateleira = document.querySelector('#codigo_prateleira').value;
        let nome_caixa = document.querySelector('#nome_modal_caixa').value;
        let descricao = document.querySelector('#descricao_modal_caixa').value;
        let visualizacao = document.querySelector('#visualizacao_modal_caixa').value;

        sistema.request.post('/caixa.php', {
            'rota': 'pesquisar_caixa_todas',
            'prateleira': codigo_prateleira,
            'nome_caixa': nome_caixa,
            'descricao': descricao,
            'tipo': visualizacao,
            'usuario': CODIGO_USUARIO,
            'empresa': CODIGO_EMPRESA
        }, function (retorno) {
            let tabela = document.querySelector('#tabela_modal_caixa tbody');
            tabela = sistema.remover_linha_tabela(tabela);

            let caixas = retorno.dados;
            let tamanho_retorno = caixas.length;

            if (tamanho_retorno < 1) {
                let linha = document.createElement('tr');
                linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA CAIXA ENCONTRADO!', 'inner', true, 2));
                tabela.appendChild(linha);
            } else {
                sistema.each(caixas, function (index, caixa) {
                    let linha = document.createElement('tr');

                    linha.appendChild(sistema.gerar_td(['text-left'], caixa.nome_caixa, 'inner'));
                    linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_caixa_' + caixa._id.$oid, 'SELECIONAR', ['btn', 'btn-success'], function selecionar_caixa() {
                        selecionar_informacao('CAIXA', caixa._id.$oid);
                    }), 'append'));

                    tabela.appendChild(linha);
                });
            }
        }, false);
    }
</script>
<div class="row">
    <div class="col-3 text-center">
        <label class="text">Organização</label>
        <div class="row">
            <div class="col-6">
                <input type="text" class="form-control custom-radius text-center" id="codigo_organizacao" name="codigo_organizacao" readonly="true" />
            </div>
            <div class="col-6">
                <button class="btn btn-info custom-radius botao_grande btn-lg" data-toggle="modal" data-target="#modal_pesquisar_organizacao" onclick="valor(event, false);">Pesquisar</button>
            </div>
        </div>
    </div>
    <div class="col-3 text-center">
        <label class="text">Armário</label>
        <div class="row">
            <div class="col-6">
                <input type="text" class="form-control custom-radius text-center" id="codigo_armario" name="codigo_armario" readonly="true" />
            </div>
            <div class="col-6">
                <button class="btn btn-info  custom-radius botao_grande btn-lg" data-toggle="modal" data-target="#modal_pesquisar_armario" onclick="valor(event, false);">Pesquisar</button>
            </div>
        </div>
    </div>
    <div class="col-3 tex-center">
        <label class="text">Prateleira</label>
        <div class="row">
            <div class="col-6">
                <input type="text" class="form-control custom-radius text-center" id="codigo_prateleira" name="codigo_prateleira" readonly="true" />
            </div>
            <div class="col-6">
                <button class="btn btn-info  custom-radius botao_grande btn-lg" data-toggle="modal" data-target="#modal_pesquisar_prateleira" onclick="valor(event, false);">Pesquisar</button>
            </div>
        </div>
    </div>
    <div class="col-3 text-center">
        <label class="text">Caixa</label>
        <div class="row">
            <div class="col-6">
                <input type="text" class="form-control custom-radius text-center" id="codigo_caixa" name="codigo_caixa"  readonly="true" />
            </div>
            <div class="col-6">
                <button class="btn btn-info  custom-radius botao_grande btn-lg" data-toggle="modal" data-target="#modal_pesquisar_caixa" onclick="valor(event, false);">Pesquisar</button>
            </div>
        </div>
    </div>
</div>