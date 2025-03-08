/** 
 * Função responsável por retornar a de acordo com o retorno da operação e redirecionar o usuário para outra tela caso for necessário.
 * 
 * Versão 0: Mensagem padrão do sistema
 * Versão 1: recebe um array na variável retorno como parâmetro.
 * 
 * @param array retorno da operação que vem do back convertido em forma json.
 * @param array contendo o endereço que a função deve redirecionar o usuário, caso não esteja ''
 */
function validar_retorno(retorno, endereco = '', versao = 0) {
	if (versao == 0) {
		if (retorno.status == true) {
			this.Swal.fire({ title: "SUCESSO NA OPERAÇÃO!", text: "Operação realizada com sucesso!", icon: "success" });
		} else {
			this.Swal.fire({ title: "FALHA NA OPERAÇÃO!", text: "Erro durante o processo, tente mais tarde!", icon: "error" });
		}
	}else{
		this.Swal.fire({title: retorno.titulo, text: retorno.mensagem, icon: retorno.icone});
	}

	if (endereco != '') {
		window.setTimeout(function () {
			window.location.href = sistema.url(endereco, {'rota':'index'});
		}, 2500);
	}
}

/** 
 * Função responsável por apresentar uma mensagem de erro caso o sistema apresente algum erro de validação de dados.
 * o mesmo ainda adiciona o focu para o compo que apresentou o erro.
 * @param string nome_componente_usuario nome que deve aparecer para o usuário do componente.
 * @param string identificador_componente #id do componente para adicionar o focus.
 */

function apresentar_mensagem_erro(nome_componente_usuario, identificador_componente = '') {
	this.Swal.fire({ title: "ERRO DE VALIDAÇÃO!", text: 'o campo ' + nome_componente_usuario + ' não pode ser vazio, ou a informação informada está incorreta!', icon: "error" });

	if (identificador_componente != '') {
		let campo = document.querySelector(identificador_componente);
		campo.classList.add('is-invalid');
	}
}