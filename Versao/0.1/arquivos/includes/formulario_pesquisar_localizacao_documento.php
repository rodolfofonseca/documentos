<script>
    function selecionar_informacao(tipo, valor) {
        if (tipo == 'ARMARIO') {
            let codigo_organizacao = sistema.int(document.querySelector('#codigo_organizacao').value);

            if (codigo_organizacao == 0) {
                Swal.fire({
                    title: "Erro de validação",
                    text: "É necessário primeiro selecionar uma organização",
                    icon: "error"
                });
            } else {
                document.querySelector('#codigo_armario').value = valor;
                let botao_fechar = document.querySelector('#botao_fechar_modal_armario');

                CODIGO_ARMAARIO = parseInt(valor);

                botao_fechar.click();
            }

        } else if (tipo == 'PRATELEIRA') {
            let codigo_armario = sistema.int(document.querySelector('#codigo_armario').value);

            if (codigo_armario == 0) {
                Swal.fire({
                    title: "Erro de validação",
                    text: "É necessário primeiro selecionar um armário",
                    icon: "error"
                });
                botao_fechar.click();
            } else {
                document.querySelector('#codigo_prateleira').value = valor;
                let botao_fechar = document.querySelector('#botao_fechar_modal_prateleira');

                CODIGO_PRATELEIRA = parseInt(valor);

                botao_fechar.click();
            }


        } else if (tipo == 'CAIXA') {
            let codigo_prateleira = sistema.int(document.querySelector('#codigo_prateleira').value);

            if (codigo_prateleira == 0) {
                Swal.fire({
                    title: "Erro de validação",
                    text: "É necessário primeiro selecionar uma prateleira",
                    icon: "error"
                });
            } else {
                document.querySelector('#codigo_caixa').value = valor;
                let botao_fechar = document.querySelector('#botao_fechar_modal_caixa');

                CODIGO_CAIXA = parseInt(valor);

                botao_fechar.click();
            }

        } else if (tipo == 'ORGANIZACAO') {
            document.querySelector('#codigo_organizacao').value = valor;
            let botao_fechar = document.querySelector('#botao_fechar_modal_organizacao');

            CODIGO_ORGANIZACAO = parseInt(valor);

            botao_fechar.click();
        }
    }

    /**
     * Função responsável por pesquisar os armários no sistema
     */
    function pesquisar_armario() {
        let codigo_armario = sistema.int(document.querySelector('#codigo_modal_armario').value);
        let nome_armario = document.querySelector('#nome_modal_armario').value;
        let descricao = document.querySelector('#descricao_modal_armario').value;
        let forma_visualizacao = document.querySelector('#visualizacao_modal_armario').value;

        sistema.request.post('/armario.php', {
            'rota': 'pesquisar_armario_todos',
            'codigo_usuario': CODIGO_USUARIO,
            'codigo_empresa': CODIGO_EMPRESA,
            'codigo_organizacao': CODIGO_ORGANIZACAO,
            'codigo_armario': codigo_armario,
            'nome_armario': nome_armario,
            'descricao': descricao,
            'forma_visualizacao': forma_visualizacao
        }, function (retorno) {
            let tabela = document.querySelector('#tabela_modal_armario tbody');

            tabela = sistema.remover_linha_tabela(tabela);

            let armarios = retorno.dados;
            let tamanho_retorno = armarios.length;

            if (tamanho_retorno < 1) {
                let linha = document.createElement('tr');
                linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUM ARMÁRIO ENCONTRADO!', 'inner', true, 3));
                tabela.appendChild(linha);
            } else {
                sistema.each(armarios, function (index, armario) {
                    let linha = document.createElement('tr');

                    linha.appendChild(sistema.gerar_td(['text-center'], sistema.str_pad(armario.id_armario, 3, '0'), 'inner'));
                    linha.appendChild(sistema.gerar_td(['text-left'], armario.nome_armario, 'inner'));
                    linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_armario_' + armario.id_armario, 'SELECIONAR', ['btn', 'btn-success'], function selecionar_armario() {
                        selecionar_informacao('ARMARIO', armario.id_armario);
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
                        selecionar_informacao('ORGANIZACAO', organizacao.id_organizacao);
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
                        selecionar_informacao('PRATELEIRA', prateleira.id_prateleira);
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
        let codigo_prateleira = sistema.int(document.querySelector('#codigo_prateleira').value);
        let codigo_caixa = sistema.int(document.querySelector('#codigo_modal_caixa').value);
        let nome_caixa = document.querySelector('#nome_modal_caixa').value;
        let descricao = document.querySelector('#descricao_modal_caixa').value;
        let visualizacao = document.querySelector('#visualizacao_modal_caixa').value;

        sistema.request.post('/caixa.php', {
            'rota': 'pesquisar_caixa_todas',
            'codigo_prateleira': codigo_prateleira,
            'codigo_caixa': codigo_caixa,
            'nome_caixa': nome_caixa,
            'descricao': descricao,
            'forma_visualizacao': visualizacao,
            'codigo_usuario': CODIGO_USUARIO,
            'codigo_empresa': CODIGO_EMPRESA
        }, function (retorno) {
            let tabela = document.querySelector('#tabela_modal_caixa tbody');
            tabela = sistema.remover_linha_tabela(tabela);

            let caixas = retorno.dados;
            let tamanho_retorno = caixas.length;

            if (tamanho_retorno < 1) {
                let linha = document.createElement('tr');
                linha.appendChild(sistema.gerar_td(['text-center'], 'NENHUMA CAIXA ENCONTRADO!', 'inner', true, 3));
                tabela.appendChild(linha);
            } else {
                sistema.each(caixas, function (index, caixa) {
                    let linha = document.createElement('tr');

                    linha.appendChild(sistema.gerar_td(['text-center'], sistema.str_pad(caixa.id_caixa, 3, '0'), 'inner'));
                    linha.appendChild(sistema.gerar_td(['text-left'], caixa.nome_caixa, 'inner'));
                    linha.appendChild(sistema.gerar_td(['text-center'], sistema.gerar_botao('botao_selecionar_caixa_' + caixa.id_caixa, 'SELECIONAR', ['btn', 'btn-success'], function selecionar_caixa() {
                        selecionar_informacao('CAIXA', caixa.id_caixa);
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
                <input type="text" class="form-control custom-radius text-center" id="codigo_organizacao"
                    name="codigo_organizacao" value="0" readonly="true" />
            </div>
            <div class="col-6">
                <button class="btn btn-info custom-radius botao_grande btn-lg" data-toggle="modal"
                    data-target="#modal_pesquisar_organizacao" onclick="valor(event, false);">Pesquisar</button>
            </div>
        </div>
    </div>
    <div class="col-3 text-center">
        <label class="text">Armário</label>
        <div class="row">
            <div class="col-6">
                <input type="text" class="form-control custom-radius text-center" id="codigo_armario"
                    name="codigo_armario" value="0" readonly="true" />
            </div>
            <div class="col-6">
                <button class="btn btn-info  custom-radius botao_grande btn-lg" data-toggle="modal"
                    data-target="#modal_pesquisar_armario" onclick="valor(event, false);">Pesquisar</button>
            </div>
        </div>
    </div>
    <div class="col-3 tex-center">
        <label class="text">Prateleira</label>
        <div class="row">
            <div class="col-6">
                <input type="text" class="form-control custom-radius text-center" id="codigo_prateleira"
                    name="codigo_prateleira" value="0" readonly="true" />
            </div>
            <div class="col-6">
                <button class="btn btn-info  custom-radius botao_grande btn-lg" data-toggle="modal"
                    data-target="#modal_pesquisar_prateleira" onclick="valor(event, false);">Pesquisar</button>
            </div>
        </div>
    </div>
    <div class="col-3 text-center">
        <label class="text">Caixa</label>
        <div class="row">
            <div class="col-6">
                <input type="text" class="form-control custom-radius text-center" id="codigo_caixa" name="codigo_caixa"
                    value="0" readonly="true" />
            </div>
            <div class="col-6">
                <button class="btn btn-info  custom-radius botao_grande btn-lg" data-toggle="modal"
                    data-target="#modal_pesquisar_caixa" onclick="valor(event, false);">Pesquisar</button>
            </div>
        </div>
    </div>
</div>