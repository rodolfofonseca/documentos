## VERSÃO 0.1

# UserFuncions.php
    Criação de classe padrão para realizar pesquisas no banco de dados utilizando o aggregate do mongo

# modelos/Usuario.php
    Desenvolvimento da modelos de usuário contendo informações a respeito de logins e demais informações pertinentes.

# modelos/Contas.php
    Criação de função que faz a validação automática das contas que devem ser trocado o status para atrasado de acordo com a data, está mesma função retorna a quantidade de contas atrasadas e aguardando pagamento

# modelos/ContasBancarias.php
    Desenvolvimento de interface para que o usuário do sistema possa cadastrar e alterar as contas que desejar.

# modelo/ModelosInterface.php
    Alteração na parâmetrização das funções para todos ficarem publicos.

# index.php
    Criação do arquivo index com sistema de login
# dashboard.php
    Desenvolvimento do contador com a quantidade de documentos cadastrados no banco de dados e sistema de previsão do tempo.
    Desenvolvimento de contatos de contas atrasadas e aguardando pagamento

# documentos.php
    Correção para apresentação de mensagem de sucesso ou de erro quando cadastrar e alterar documentos.

# usuario.php
    Criação do arquivo usuario.php onde o usuário que está logado no sistema pode realizar a alteração de suas informações.

# contas_bancarias.php
    Criação do arquivo contas_bancarias.php onde o usuário que está logando no sistema pode realizar a alteração das informações das contas bancárias que desejar.


## VERSÃO 0.1.1

# modelos/Documentos.php
    Alteração das classes para ter mais opções de alterações

# documentos.php
    Alteração para ter mais opções de alteração
    Quando realizar o download do arquivo o nome dele vem como nome do arquivo e não o endereço
    Quando for alterar tem como escolher o tipo de alteração que se deseja, se quer alterar tudo, só informações ou o documento completo.
    Alteração da forma de pesquisa para abrir modal de pesquisa ao invés de ser selects.

# dist/js/sistema.js
    Adicionado o padrão str_pad na classe.


## VERSÃO 1.0

# modelos/TipoDespesa.php
    Modelo responsável por realizar o cadastrar dos tipos de despesa na base de dados.

# modelos/tipoContas.php
    Modelo responsável por realizar o cadastro dos tipos de contas na base de dados.

# dist/js/sistema.js
    Correção do formato de data para retorna o mês de forma correta

# includes/head.php
    Inserção de novo menu no sistema

# modelos/Contas.php
    Alteração na função de contas a quantidade de contas para retornar as contas pagas.

# contas.php
    Alteração para enviar o modelo correto de data e assim visualizar da forma padão brasileira

# dashboard.php
    Desenvolvimento de novo relatório o de contas em forma de pizza.

## VERSÂO 1.1

# modelos/Clima.php
    utilização do try, para evitar warning quando falhar a busca do clima
# modelos/TiposContas.php
    Alteração na função de cadastro onde estava referênciado o modelo de forma errada
# includes/head.php
    Alteração para colocar a nova funcionalidade no menu correspondente.
    Adição do menu para poder visualizar os novos relatórios do sistema.
# tipo_despesa.php
    Desenvolvimento de nova funcionalidade tipo_contas para poder cadastrar os tipos de contas que deseja.
# contas.php
    Alteração para receber informações da nova funcionalidade, de forma que se possa utilizar agora.
# lancamentos.php
    Alteração para poder realizar o cadastro no novos lançamentos no sistema.
# relatorios_contabeis.php
    Desenvolvimento da função de relatórios contábeis