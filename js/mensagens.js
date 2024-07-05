
function validar_retorno(retorno, endereco = ''){
	if(retorno.status == true){
		this.Swal.fire({title: "SUCESSO NA OPERAÇÃO!", text: "Operação realizada com sucesso!", icon: "success"});

	}else{
		this.Swal.fire({title: "FALHA NA OPERAÇÃO!", text: "Erro durante o processo, tente mais tarde!", icon: "error"});
	}
	
	if(endereco != ''){
		window.setTimeout(function(){
			window.location.href = endereco;
		}, 2500);
	}
}