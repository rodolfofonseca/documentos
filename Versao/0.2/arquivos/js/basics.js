/**
 * Função responsável por gerar a validação dos campos.
 * @param {string}} variable 
 * @param {string} name 
 * @returns 
 */
function validate(variable, name) {
  let return_validate = true;
  if (variable == 0 || variable == '' || variable == '--' || variable == '---') {
    return_validate = false;
    alerta_campo_vazio(name);
  }
  return return_validate;
}

/**
 * Função responsável por colocar campos conforme o usuário deseja para preenxer o tamanho dos campos.
 * @param {string} string 
 * @param {int} quantidade 
 * @param {string} campo_preenximento 
 * @returns {string} valor formatado
 */
function str_pad(string , quantidade, campo_preenximento) {
  string = string + '';
  return string.padStart(quantidade, campo_preenximento);
}


/**
 * Função responsável por apresentar ao usuário uma mensagem informando que o campo é obrigatório.
 * @param {string} campo 
 */
function alerta_campo_vazio(campo) {
  Swal.fire('Erro', 'O campo ' + campo + ' não pode ser vazio!', 'error');
}

/**
 * Função responsável por retornar para a página principal de cada arquivo.
 * @param {string*} nome_arquivo 
 */
function retornar(url) {
  window.location.href = url;
}

function imprimir(div_botao, class_remover, div_trocar, class_voltar) {
  div_botao.style.display = 'none';

  div_trocar.classList.remove(class_remover);
  div_trocar.classList.add('col-12');

  window.print();

  window.setTimeout(function () {
    div_trocar.classList.remove('col-12');
    div_trocar.classList.add(class_voltar);
    div_botao.style.display = 'block';
  }, 500);

}

function fechar() {
  window.close();
}

/**
* Função responsável por verificar se a variável tempo é < 10 caso for adiciona um zero na frente para melhor visualizarção do usuário.
*/
function adicionar_zero(tempo) {
  if (tempo < 10) {
    tempo = '0' + tempo;
  }
  return tempo;
}

function mascara_conta(campo) {
  var conta = campo.value;

  if (existe(campo)) {
    conta = strReplace(".", "", conta);

    if (conta.length == 0) {
      conta = "";
    } else if (conta.length == 1) {
      if (conta == 0) {
        conta = '0';
        return false;
      } else {
        conta = conta + "0000000000000000";
      }

    } else if (conta.length == 2) {
      conta = conta + "000000000000000";

    } else if (conta.length == 3) {
      conta = conta + "00000000000000";

    } else if (conta.length == 4) {
      conta = conta + "0000000000000";

    } else if (conta.length == 5) {
      conta = conta + "000000000000";

    } else if (conta.length == 6) {
      conta = conta + "00000000000";

    } else if (conta.length == 7) {
      conta = conta + "0000000000";

    } else if (conta.length == 8) {
      conta = conta + "000000000";

    } else if (conta.length == 9) {
      conta = conta + "00000000";

    } else if (conta.length == 10) {
      conta = conta + "0000000";

    } else if (conta.length == 11) {
      conta = conta + "000000";

    } else if (conta.length == 12) {
      conta = conta + "00000";

    } else if (conta.length == 13) {
      conta = conta + "0000";

    } else if (conta.length == 14) {
      conta = conta + "000";

    } else if (conta.length == 15) {
      conta = conta + "00";

    } else if (conta.length == 16) {
      conta = conta + "0";

    } else if (conta.length == 17) {
      conta = conta + "";
    }
  }

  if ((campo.value).length !== 0) {
    conta = conta.substr(0, 1) + '.' +
      conta.substr(1, 1) + '.' +
      conta.substr(2, 2) + '.' +
      conta.substr(4, 3) + '.' +
      conta.substr(7, 4) + '.' +
      conta.substr(11, 6);
  }

  campo.value = conta;
}

/**
 * Função responsável por limpar todos os componentes que é passado dentro do paêmetro
 * @param {arrray} campos 
 */
function limpar_campo_text(campos) {
  for (let contador = 0; contador < campos.length; contador++) {
    document.querySelector('#' + campos[contador]).value = '';
  }
}


function loader_sistema(boolean) {
  if (boolean == true) {
    document.querySelector('#loader').style.display = 'block';
  } else {
    document.querySelector('#loader').style.display = 'none';
  }
}

function gerar_input(tipo, id, texto, classes = [], mascara = '', disabled = '', onblur = 'N') {
  let input = document.createElement('input');
  input.type = tipo;
  input.id = id;

  input.classList.add('form-control');
  input.classList.add('custom-radius');

  let tamanho_classe = classes.length;

  if (tamanho_classe > 0) {
    for (let contador = 0; contador < tamanho_classe; contador++) {
      input.classList.add(classes[contador]);
    }
  }

  if (mascara != '') {
    if (mascara == 'moeda') {
      input.setAttribute('sistema-mask', 'moeda');
    } else if (mascara == 'quantidade') {
      input.setAttribute('sistema-mask', 'codigo');
    }
  }

  if (disabled != '') {
    input.setAttribute('disabled', 'true');
  }

  if (onblur != 'N') {
    input.addEventListener('blur', onblur);
  }

  input.value = texto;

  return input;
}

/**
 * Método responsável por validar se o usuário atual é do tipo administrador, caso o usuário não for redireciona o mesmo para a dashboard do sistema+
 * @param {integer} tipo_usuario 
 */
function validar_acesso_administrador(tipo_usuario) {

  if (tipo_usuario == 'COMUM') {
    window.location.href = sistema.url('/dashboard.php', { 'rota': 'index' });
  }
}

/**
 * Função responsável por verificar se é para abrir ou fechar o modal.
 * @param {object} parametro 
 * @param {boolean} sair 
 * @param {string} arquivo 
 */
function abri_modal(parametro, sair, arquivo) {
  parametro.preventDefault();

  if (sair == true) {
      window.location.href = sistema.url('/'+arquivo+'.php', {
          'rota': 'index'
      });
  }
}


/**
 * Função responsável por pegar a data em formato timestamp que vem do mongo e converter em string da forma que o usuário entende.
 * @param {*} time_stamp 
 * @returns 
 */
function retornar_data(time_stamp){
  const data = new Date(time_stamp.$date.$numberLong.substring(0,10) * 1000);

  // return new Intl.DateTimeFormat("pt-BR", {dateStyle: "short", timeStyle: "short", timeZone: "America/Sao_Paulo"}).format(data);
  return new Intl.DateTimeFormat("pt-BR", {dateStyle: "short", timeZone: "America/Sao_Paulo"}).format(data);
}