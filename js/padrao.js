var indexOf = [].indexOf || function (item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

// :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// :::::::::::::::::::::::::::::::::::::::::::::::::::::::: Core :::::::::::::::::::::::::::::::::::::::::::::::::::::::
// :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// Carregar o conteúdo de uma variável
// @param {String} nomeVar Nome da variável a ter seu valor carregado
function get(nomeVar) {
  return window[nomeVar];
}
// Verifica se o elemento de entrada existe
// @param {Mixed} elemento Elemento a ser verificado
function existe(elemento) {
  return (elemento !== null) && (elemento !== void 0) && (typeof elemento !== "undefined");
}
// Converte um valor para o tipo code (somente números)
// @param {Mixed} valor Valor para conversão
function toCode(valor) {
  var codigo, i, len, ref, ref1, resultado;
  resultado = [];
  ref = valor.split('');
  for (i = 0, len = ref.length; i < len; i++) {
    codigo = ref[i];
    if (ref1 = parseInt(codigo, 10), indexOf.call([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10], ref1) >= 0) {
      resultado.push(codigo);
    }
  }
  return resultado.join('');
}
// Verifica se a variável foi criada
// @param {String} variavel - A variável a ser testada em formato string
function isSet(variavel) {
  return eval("typeof(" + variavel + ") != 'undefined'");
};
// Verifica se um elemento é do tipo integer
// @param {Mixed} elemento - O elemento a ser testado
function isInteger(elemento) {
  return ((+elemento === elemento) && (isFinite(elemento) == true) && ((elemento % 1) == false));
}
// Verifica se um elemento é do tipo float
// @param {Mixed}   elemento  - O elemento a ser testado
function isFloat(elemento) {
  return (+elemento === elemento) && ((isFinite(elemento) == false) || !!(elemento % 1));
}
// Verifica se um elemento é do tipo string
// @param {Mixed} elemento - O elemento a ser testado
function isString(elemento) {
  return (typeof elemento === "string");
}
// Verifica se a data é válida
// @param {String} strData a data a ser testada
function isDate(strData) {
  var data = toString(strData);

  if (data.length == 10) {
    if ((data[2] === '/') && (data[5] === '/')) {

      data = trim(data, ["/", "-"]);

      var dia = toInteger(data.substr(0, 2));
      var mes = toInteger(data.substr(2, 2));
      var ano = toInteger(data.substr(4));

      if ((data.length === 8) && (dia > 0) && (dia <= 31) && (mes > 0) && (mes <= 12) && (ano >= 1990) && (ano <= 2100)) {
        if ((((ano % 4) === 0) && (mes === 2) && (dia <= 29)) || (((ano % 4) !== 0) && (mes === 2) && (dia <= 28)) || (mes !== 2)) {
          return true;
        }
      }
    }
  }
  return false;
}
// Converte um valor para o tipo boolean
// @param {Mixed} elemento - O elemento a ser convertido
function toBoolean(elemento) {
  elemento = toString(elemento).toUpperCase();
  if ((elemento == "TRUE") || (elemento == "S") || (elemento == "SIM") || (elemento == "1")) {
    return true;
  } else if ((elemento == "FALSE") || (elemento == "N") || (elemento == "NAO") || (elemento == "NÃO") || (elemento == "0") || (elemento == "")) {
    return false;
  } else {
    return Boolean(elemento);
  }
}
// Converte um valor para o tipo integer
// @param {Mixed} elemento - O elemento a ser convertido
function toInteger(elemento) {
  var temporaria;

  if ((elemento === true) || (elemento === false)) {
    return +elemento;
  } else if (isString(elemento) === true) {
    temporaria = parseInt(elemento, 10);
    return ((isNaN(temporaria) == true) || (isFinite(temporaria) == false)) ? 0 : temporaria;
  } else if ((isInteger(elemento) == true) || (isFloat(elemento) == true)) {
    return parseInt(elemento, 10);
  } else {
    return 0;
  }
}
// Converte um valor para o tipo float
// @param {Mixed}   elemento  Elemento a ser convertido
// @param {String}  separador Indica qual o separador das casas decimais será convertido para "." (padrão: ",")
// @param {Integer} precisao  Quantidade de casas decimais a serem consideradas (parâmetro não implementado até momento)
function toFloat(elemento, separador, precisao) {
  separador = (existe(separador) === true) ? separador : ",";
  precisao = (existe(precisao) === true) ? toInteger(precisao) : 2;
  elemento = toString(elemento);
  if ((elemento.indexOf('.') >= 0) && (elemento.indexOf(',') >= 0)) {
    elemento = strReplace('.', '', elemento);
  }
  elemento = elemento.replace(separador, ".");
  return (parseFloat(elemento) || 0);
}
// Converte um valor para o tipo string
// @param {Mixed} elemento - O elemento a ser convertido
function toString(elemento) {
  switch (typeof elemento) {
    case "boolean":
      if (elemento === true) {
        return "true";
      }
      return "false";
    case "array":
      return "Array";
    case "object":
      return "Objeto";
    case "null":
      return "null";
    default:
      return (existe(elemento) === true) ? (elemento + "").toString() : "";
  }
}
// Converte um valor para o tipo percentual
// Ex: console.log(toPercentage(50));
// >>> 0.5
// @param {Float} elemento - O elemento a ser convertido
function toPercentage(elemento) {
  elemento = toFloat(elemento);
  if (elemento > 0) {
    return elemento / 100;
  }
  return 0;
}
// Executa a uma rotina para cada elemento de um array
// @param {Array|Object}  array   Array a ser utilizado
// @param {Function}    funcao  Função a ser executada a cada posição do array
function each(arr, funcao) {
  if (funcao.length == 2) {
    for (var i in arr) {
      funcao(i, arr[i]);
    }
  } else {
    for (var i in arr) {
      funcao(arr[i]);
    }
  }
}
// Conta a quantidade de elementos em um array
// @param {Array|Object}  elemento  Array a ser utilizado
// @param {String}    modo    Função a ser executada a cada posição do array
function count(elemento, modo) {
  var contagem = 0;
  var recursivo = (modo === "COUNT_RECURSIVE") ? true : false;

  if (existe(elemento) === false) {
    return 0;
  } else if ((elemento.constructor !== Array) && (elemento.constructor !== Object)) {
    return 1;
  }

  for (var chave in elemento) {
    if (elemento.hasOwnProperty(chave)) {
      contagem = contagem + 1;
      if ((recursivo === true) && (elemento[chave]) && ((elemento.constructor === Array) || (elemento.constructor === Object))) {
        contagem = contagem + this.count(elemento[chave], 1);
      }
    }
  }

  return contagem;
}
// Converte um valor para o tipo float
// @param {float} valor Elemento a ser utilizado
// @param {integer} precisao  Quantidade de casas decimais a serem exibidas
function round(elemento, precisao) {
  if (precisao == null) {
    precisao = 0;
  }
  precisao = toInteger(precisao);
  elemento = toFloat(elemento).toFixed(precisao);
  return toFloat(elemento);
}
// :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// :::::::::::::::::::::::::::::::::::::::::::::::::::::::: Util :::::::::::::::::::::::::::::::::::::::::::::::::::::::
// :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// Retorna uma string com todas as ocorrências de $procurar em $str substituídas com o valor dado para $substituir
// @param   {String|Array}  procurar    Parte que será substituída
// @param   {String}    substituir    Novo texto
// @param   {String}    sujeito     Texto que receberá a alteração
// @param   {Boolean}     sensivel    Diferenciar maiúsculas e minúsculas (padrão: false)
function strReplace(procurar, substituir, sujeito, sensivel) {
  var procurar = [toString(procurar)];
  var substituir = [toString(substituir)];
  var sujeito = toString(sujeito);
  var sensivel = (existe(sensivel) == true) ? sensivel : false;

  if (sensivel === true) {
    sujeito = sujeito.split(procurar).join(substituir);

  } else {
    var escapeRegex = function (str) {
      return str.replace(/([\\\^\$*+\[\]?{}.=!:(|)])/g, '\\$1');
    };

    while (procurar.length > substituir.length) {
      substituir[substituir.length] = '';
    }

    for (var i = 0; i < toInteger(procurar.length); i++) {
      sujeito = sujeito.replace(new RegExp(escapeRegex(procurar[i]), 'gi'), substituir[i]);
    }
  }
  return sujeito;
}
// Remove o caractere espaço em branco, ou uma lista de caracteres passada como parâmetro
// @param {String} str    O texto a ser convertido
// @param {String} [lista]  Lista de caracteres a serem removidos
function trim(str, lista) {
  var str = toString(str);
  var substituir = (count(lista) > 0) ? "" : " ";
  var lista = (count(lista) > 0) ? lista : [];
  var indice = 0;

  each(lista, function (chave, procurar) {
    str = strReplace(procurar, substituir, str);
  });

  if ((count(lista) === 0) && (str.length > 0)) {
    novaStr = str;
    carac = str.substr(indice, 1);

    while ((carac === " ") || (carac === "\t") || (carac === "\n") || (carac === "\r") || (carac === "\0") || (carac === "\x0B")) {
      novaStr = str.substr(indice + 1);
      indice = indice + 1;
      carac = str.substr(indice, 1);
    }

    str = novaStr;
    indice = str.length - 1;
    carac = str.substr(indice, 1);
    while ((carac === " ") || (carac === "\t") || (carac === "\n") || (carac === "\r") || (carac === "\0") || (carac === "\x0B")) {
      novaStr = str.substr(0, indice);
      indice = indice - 1;
      carac = str.substr(indice, 1);
    }

    str = novaStr;
  }

  return str;
}
// Transforma o número passado como parâmetro em sua versão positiva
// @param {String} numero O número a ser convertido
function abs(numero) {
  return Math.abs(numero);
}
// Retorna a url principal do projeto excluindo o caractere "/" do final
function exibirUrl() {
  url = strReplace("\\", "/", window.location.href);
  url = (url.indexOf("?") > 0) ? url.substr(0, url.indexOf("?") + 1) : url;
  return url.substr(0, url.lastIndexOf("/"));
}
// Retorna o valor de um parâmetro da url ($_GET)
// @param {String} nome O nome do parâmetro a ser carregado
function getParam(nome) {
  var nome = nome + "=";
  var url = window.location.href;
  var url = url.slice(url.indexOf("?") + 1);

  for (var i = 0; i < (arr = url.split("&")).length; i = i + 1) {
    if (arr[i].substr(0, nome.length) == nome) {
      return arr[i].slice(nome.length);
      // return decodeURIComponent(arr[i].slice(nome.length));
    }
  }
  return null;
}
// Carrega um elemento do DOM através de seu identificador
// @param {InputHtml} elemento - Elemento do DOM a ser carregado
function id(elemento) {
  try {
    return window.document.getElementById(elemento);
  } catch (e) {
    return null;
  }
}
// Carrega uma lista de elementos do DOM através da classe selecionada
// @param {String} nomeClasse - O nome da classe a ser pesquisada
function classe(nomeClasse) {
  var lista = [];

  each(window.document.querySelectorAll("." + nomeClasse), function (chave, classe) {
    if (existe(classe.id) === true) {
      lista.push(classe.id);
    }
  });

  return lista;
}
// Carrega um frame do DOM através de seu identificador
// @param {IFrameHtml} elemento - O iframe do DOM a ser carregado
function frame(elemento) {
  return window.frames[elemento];
}
// Retorna as posições do objeto de acordo com o parâmetro (propriedade)
// @param {String} elemento   - O identificador do elemento a ser carregada a propriedade
// @param {String} propriedade  - A propriedade a ser carregada ("top", "left")
// @param {String} relacao    - Indica se deve ser exibida a posição em relação ao elemento anterior ou a toda janela (padrão elemento anterior)
function getPosition(elemento, propriedade, relacao) {
  var propriedade = ((propriedade === "top") || (propriedade === "left")) ? propriedade : "top";
  var relacao = (isSet(relacao) === true) ? relacao : false;

  if (relacao === false) {
    var posicao = id(elemento).getBoundingClientRect()[propriedade];
  } else {
    var posicao = id(elemento).getBoundingClientRect()[propriedade] - id(elemento).offsetParent.getBoundingClientRect()[propriedade];
  }

  return toInteger(posicao);
}
// Carrega uma página com seu nome passado por parâmetro
// @param {String} pagina - Nome da página a ser carregada
function carregarPagina(pagina) {
  window.location.href = exibirUrl() + "/" + pagina;
}


// Converte um número Decimal em um valor do tipo Moeda.
// @param {Float}   numero      - Número decimal que será convertido
// @param {Integer} decimais      - Quantidade de casas decimais
// @param {String}  separadorDecimal  - Indica qual será a pontuação decimal
// @param {String}  separadorMilhar   - Indica qual será a pontuação para as casas de milhar, milhão etc...
function numberFormat(numero, decimais, separadorDecimal, separadorMilhar) {
  var numero = toFloat(numero);
  var decimais = toInteger(decimais);
  var separadorDecimal = (existe(separadorDecimal) == true) ? separadorDecimal : ",";
  var separadorMilhar = (existe(separadorMilhar) == true) ? separadorMilhar : "";

  var fixarDecimaisIE = function (num, decimais) {
    var k = Math.pow(10, decimais);
    return toString(Math.round(num * k) / k);
  };

  var separarMilhar = function (num, milhar) {
    var resultado = [];
    while (num.length > 0) {
      inicio = ((num.length - 3) > 0) ? num.length - 3 : 0;
      resultado.push(num.substr(inicio, 3));
      num = num.substr(0, inicio);
    }
    resultado.reverse();
    return resultado.join(milhar);
  }

  var arr = ((decimais) ? fixarDecimaisIE(toFloat(numero), decimais) : toString(Math.round(toFloat(numero)))).split(".");
  if (arr[0].length > 3) {
    arr[0] = (numero >= 0) ? separarMilhar(arr[0], separadorMilhar) : '-' + separarMilhar(arr[0].substr(1), separadorMilhar);
  }
  arr[1] = ((toString(arr[1])).length < decimais) ? strPad(arr[1], decimais, "0", "STR_PAD_RIGHT") : arr[1];

  return arr.join(separadorDecimal);
}
// Atrasa a execução do script (nada é inserido na CPU ao carregar este script, NÃO usar valores altos)
// @param   {Integer}   espera  O tempo de execução desta função em milissegundos
function sleep(espera) {
  var espera = (existe(espera) == true) ? toInteger(espera) : 5;
  var inicio = new Date().getTime();
  while (new Date().getTime() < inicio + espera) { };
}
// Exibir uma descrição das chaves e valores de um array ou objeto
// @param   {Mixed}   elemento  Elemento a ser utilizado
// @param   {Boolean}   recursivo   Indica se deve exibir os sub-elementos
// @param   {String}  espacamento Número de espaços antes de cada elemento
function varDump(elemento, recursivo, espacamento) {
  var espacamento = (existe(espacamento) == true) ? espacamento + "  " : "  ";
  var recursivo = (existe(recursivo) == false) ? true : recursivo;

  each(elemento, function (i, atributo) {
    if ((recursivo == true) && (typeof (atributo) == "object")) {
      console.log(espacamento + "[" + i + "] : " + " (" + typeof (atributo) + ") ");
      varDump(atributo, true, espacamento);
    } else {
      console.log(espacamento + "[" + i + "] : " + " (" + typeof (atributo) + ") " + atributo);
    }
  });
}
// Remover acentos dos textos
// @param   {String}  texto   Texto a ser convertido
function stripAccents(texto) {
  var listaComAcento = ['à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý'];
  var listaSemAcento = ['a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y'];

  each(listaComAcento, function (chave, letra) {
    texto = strReplace(letra, listaSemAcento[chave], texto);
  });

  return texto;
}
// Retorna a data atual no formato passado por parâmetro
// @param   {String}  padrao  O padrão da data a ser retornada
function gerarData(padrao) {
  var data = new Date();

  dataConvertida = strReplace("d", strPad(data.getDate(), 2, "0", "STR_PAD_LEFT"), padrao);
  dataConvertida = strReplace("m", strPad(data.getMonth() + 1, 2, "0", "STR_PAD_LEFT"), dataConvertida);
  dataConvertida = strReplace("Y", data.getFullYear(), dataConvertida);
  dataConvertida = strReplace("H", strPad(data.getHours(), 2, "0", "STR_PAD_LEFT"), dataConvertida);
  dataConvertida = strReplace("i", strPad(data.getMinutes(), 2, "0", "STR_PAD_LEFT"), dataConvertida);
  dataConvertida = strReplace("s", strPad(data.getSeconds(), 2, "0", "STR_PAD_LEFT"), dataConvertida);
  dataConvertida = strReplace("u", strPad(data.getMilliseconds(), 3, "0", "STR_PAD_LEFT"), dataConvertida);

  return toString(dataConvertida);
}
// POG que retorna o frame na escala mais alta do sistema (index)
// até o 4º grau
function topo() {
  var elFrame = null;
  if (window.frames['FrameCorpo']) {
    elFrame = window;
  } else if (window.parent.frames['FrameCorpo']) {
    elFrame = window.parent;
  } else if (window.parent.parent.frames['FrameCorpo']) {
    elFrame = window.parent.parent;
  } else if (window.parent.parent.parent.frames['FrameCorpo']) {
    elFrame = window.parent.parent.parent;
  } else if (window.parent.parent.parent.parent.frames['FrameCorpo']) {
    elFrame = window.parent.parent.parent.parent;
  }
  return elFrame;
}
// :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// ::::::::::::::::::::::::::::::::::::::::::::::::::::: Validation ::::::::::::::::::::::::::::::::::::::::::::::::::::
// :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// Valida os caracteres digitados
// @param   {String}  tipo      Tipo de dado a ser validado
// @param   {String}  evento      Evento de pressionar a tecla
// @param   {Object}  elemento    Objeto a ser validado
// @param   {Array}   extraPermitir   Array com caracteres válidos a serem acrescentados (extraNegar é prioridade)
// @param   {Array}   extraNegar    Array com caracteres inválidos a serem acrescentados
function validarElemento(tipo, evento, elemento, extraPermitir, extraNegar) {
  var retorno = false;
  var codigo = (window.document.all) ? evento.keyCode : evento.which;
  var extraPermitir = (existe(extraPermitir) == true) ? extraPermitir : [];
  var extraNegar = (existe(extraNegar) == true) ? extraNegar : [];
  var caractere = String.fromCharCode(codigo);
  var tipo = (toString(tipo)).toUpperCase();
  var codigos = {
    "CEP": [45, 46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57],
    "CPF_CNPJ": [45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57],
    "DATE": [47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57],
    "DATA": [47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57],
    "DD/MM/AAAA": [47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57],
    "D/M/Y": [47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57],
    "ESPECIAL": [0, 8, 13],
    "HH:MM:SS": [48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58],
    "H:I:S": [48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58],
    "INTEGER": [48, 49, 50, 51, 52, 53, 54, 55, 56, 57],
    "WEIGHT": [44, 46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57],
    "PESO": [44, 46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57],
    "STRING": [32, 33, 36, 37, 38, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 124, 125, 126, 170, 176, 180, 186, 192, 193, 194, 195, 199, 200, 201, 202, 204, 205, 210, 211, 212, 213, 217, 218, 220, 224, 225, 226, 227, 231, 232, 233, 234, 236, 237, 242, 243, 244, 245, 249, 250, 252],
    "TELEFONE": [32, 40, 41, 45, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57],
    "TIME": [48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58],
    "VALOR": [44, 46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57],
    "MOEDA": [44, 46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57],
    "PRICE": [44, 46, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57]
  }

  try { topo().RenovaSessao(); } catch (e) { }

  each(extraPermitir, function (chave, valor) {
    codigos["ESPECIAL"].push(valor.charCodeAt(0));
  });

  each(codigos["ESPECIAL"], function (chave, valor) {
    retorno = (valor === codigo) ? true : retorno;
  });

  if (retorno === false) {

    each(codigos[tipo], function (chave, valor) {
      retorno = (valor === codigo) ? true : retorno;
    });

    if (retorno === true) {

      switch (tipo) {
        case "DATE":
        case "DATA":
        case "DD/MM/AAAA":
        case "D/M/Y":
          var tamanhoData = toInteger((elemento.value).length);

          if (tamanhoData == 2) {
            if (caractere != "/") {
              elemento.value = toString(elemento.value + "/");
            }
          } else if (tamanhoData == 5) {
            if (caractere != "/") {
              elemento.value = toString(elemento.value + "/");
            }
          } else if (tamanhoData == 10) {
            returno = false;
          }
          break;

        case "TIME":
          if (retorno == true) {
            var tamanhoData = toInteger((elemento.value).length);

            if (tamanhoData == 2) {
              if (caractere != ":") {
                elemento.value = toString(elemento.value + ":");
              }
            } else if (tamanhoData == 5) {
              returno = false;
            }
          }
          break;

        case "HH:MM:SS":
        case "H:I:S":
          if (retorno == true) {
            var tamanhoData = toInteger((elemento.value).length);

            if (tamanhoData == 2) {
              if (caractere != ":") {
                elemento.value = toString(elemento.value + ":");
              }
            } else if (tamanhoData == 5) {
              if (caractere != ":") {
                elemento.value = toString(elemento.value + ":");
              }
            } else if (tamanhoData == 5) {
              return false;
            }
          }
          break;

        case "MOEDA":
        case "VALOR":
          // var valor   = toString(elemento.value);
          // var posicao = valor.indexOf(",");

          // if (posicao < 0) {
          //   if ((codigo == 44) || (codigo == 46)) {
          //     if (valor.length == 0) {
          //       elemento.value = valor + "0,";
          //     } else {
          //       elemento.value = valor + ",";
          //     }
          //     retorno = false;
          //   }
          // } else if (((valor.substr(posicao)).length > 2) || (codigo === 44) || (codigo === 46)) {
          //   retorno = false;
          // }
          break;

        case "WEIGHT":
        case "PESO":
          // var valor   = toString(elemento.value);
          // var posicao = valor.indexOf(",");

          // if (posicao < 0) {
          //   if ((codigo == 44) || (codigo == 46)) {
          //     if (valor.length == 0) {
          //       elemento.value = valor + "0,";
          //     } else {
          //       elemento.value = valor + ",";
          //     }
          //     retorno = false;
          //   }
          // } else if (((valor.substr(posicao)).length > 4) || (codigo == 44) || (codigo == 46)) {
          //   retorno = false;
          // }
          break;
      }

    }
  }

  each(extraNegar, function (chave, valor) {
    retorno = (valor.charCodeAt(0) === codigo) ? false : retorno;
  });

  return retorno;
}
// Valida um elemento do tipo Hora no formato passado por parâmetro
// @param {InputHtml} elemento - O elemento que será validado
// @param {String}  mensagem - Indica se deve ou não exibir a mensagem de alerta
function validarHora(elemento, mensagem) {
  var horas = trim(elemento.value, [" ", ":"]);
  var mensagem = (existe(mensagem) === true) ? mensagem : true;

  if (horas.length > 0) {
    if (horas.length === 4) {
      var hora = toInteger(horas.substr(0, 2));
      var minuto = toInteger(horas.substr(2));

      if ((hora >= 0) && (hora <= 23) && (minuto >= 0) && (minuto <= 59)) {
        elemento.value = strPad(hora, 2, "0", "STR_PAD_LEFT") + ":" + strPad(minuto, 2, "0", "STR_PAD_LEFT");
        return true;
      }

    } else if (horas.length === 6) {
      var hora = toInteger(horas.substr(0, 2));
      var minuto = toInteger(horas.substr(2, 2));
      var segundo = toInteger(horas.substr(4));

      if ((hora >= 0) && (hora <= 23) && (minuto >= 0) && (minuto <= 59) && (segundo >= 0) && (segundo <= 59)) {
        elemento.value = strPad(hora, 2, "0", "STR_PAD_LEFT") + ":" + strPad(minuto, 2, "0", "STR_PAD_LEFT") + ":" + strPad(segundo, 2, "0", "STR_PAD_LEFT");
        return true;
      }
    }

    if (mensagem === true) {
      alert("Hora inválida!");
    }

    elemento.value = "";
    return false;
  }

  return true;
}
// Verifica se a Data está válida
// @param {InputHtml} elemento Objeto completo que será testado
// @param {String}  mensagem Indica se é para exibir os alertas de erro (padrão true)
function validarData(elemento, mensagem) {
  var data = toString(elemento.value);
  var mensagem = (existe(mensagem) === true) ? mensagem : true;

  if (data.length > 0) {
    data = trim(data, ["/", "-"]);
    var dia = toInteger(data.substr(0, 2));
    var mes = toInteger(data.substr(2, 2));
    var ano = toInteger(data.substr(4));
    if ((data.length === 8) && (dia > 0) && (dia <= 31) && (mes > 0) && (mes <= 12)) {
      if ((((ano % 4) === 0) && (mes === 2) && (dia <= 29)) || (((ano % 4) !== 0) && (mes === 2) && (dia <= 28)) || (mes !== 2)) {
        elemento.value = strPad(dia, 2, "0", "STR_PAD_LEFT") + "/" + strPad(mes, 2, "0", "STR_PAD_LEFT") + "/" + strPad(ano, 4, "0", "STR_PAD_LEFT");
        return true;
      }
    }

    if (mensagem === true) {
      alert("Data inválida!\r\nEste campo deve seguir o padrão: dd/mm/aaaa.");
    }

    elemento.value = "";
    return false;
  }

  return true;
}
// Valida qual a maior data (1: primeira maior, 2: segunda maior; 0: iguais; -1: inválidas)
// @param {String} primeiraData - A primeira data no padrão "dd/mm/aaaa"
// @param {String} segundaData  - A segunda data no padrão "dd/mm/aaaa"
function validarDataSuperior(primeiraData, segundaData) {
  if ((primeiraData.length === 10) && (segundaData.length === 10)) {
    var intPrimeiraData = toInteger(toString((primeiraData).split("/")[2]) + toString((primeiraData).split("/")[1]) + toString((primeiraData).split("/")[0]));
    var intSegundaData = toInteger(toString((segundaData).split("/")[2]) + toString((segundaData).split("/")[1]) + toString((segundaData).split("/")[0]));

    if (intPrimeiraData > intSegundaData) {
      return 1;
    } else if (intPrimeiraData < intSegundaData) {
      return 2;
    } else if (intPrimeiraData === intSegundaData) {
      return 0;
    }
  }
  return -1;
}
// Valida elementos do tipo CPF ou CNPJ
// @param {InputHtml} elemento - Elemento completo que será validado
// @param {String}  mensagem - Indica se deve ou não exibir a mensagem de alerta
function validarCpfCnpj(elemento, mensagem) {
  var mensagem = (existe(mensagem) === true) ? mensagem : true;
  var cpfCnpj = trim(elemento.value, [" ", ".", "-", "/"]);
  var tamanho = cpfCnpj.length;
  var soma = 0;
  var resto = 0;

  if (tamanho > 0) {
    if ((tamanho === 11) && (cpfCnpj !== "00000000000")) {
      for (var i = 1; i <= 9; i = i + 1) {
        soma = soma + parseInt(cpfCnpj.substring(i - 1, i)) * (11 - i);
      }
      resto = toInteger((soma * 10) % 11);

      if ((resto === 10) || (resto === 11)) {
        resto = 0;
      }

      if (resto === toInteger(cpfCnpj.substr(9, 1))) {
        soma = 0;
        for (var i = 1; i <= 10; i = i + 1) {
          soma = soma + toInteger(cpfCnpj.substring(i - 1, i)) * (12 - i);
        }
        resto = toInteger((soma * 10) % 11);

        if ((resto === 10) || (resto === 11)) {
          resto = 0;
        }

        if (resto === toInteger(cpfCnpj.substr(10, 1))) {
          elemento.value = cpfCnpj.substr(0, 3) + "." + cpfCnpj.substr(3, 3) + "." + cpfCnpj.substr(6, 3) + "-" + cpfCnpj.substr(9);
          return true;
        }
      }
    } else if (tamanho === 14) {
      tamanho = tamanho - 2;
      numeros = cpfCnpj.substring(0, tamanho);
      digitos = cpfCnpj.substring(tamanho);
      pos = tamanho - 7;

      for (var i = tamanho; i >= 1; i = i - 1) {
        soma = soma + (numeros.charAt(tamanho - i) * pos--);
        if (pos < 2) {
          pos = 9;
        }
      }

      resultado = ((soma % 11) < 2) ? 0 : (11 - soma % 11);
      if (resultado == toInteger(digitos.charAt(0))) {
        tamanho = tamanho + 1;
        numeros = cpfCnpj.substring(0, tamanho);
        pos = tamanho - 7;
        soma = 0;

        for (var i = tamanho; i >= 1; i = i - 1) {
          soma = soma + numeros.charAt(tamanho - i) * pos--;
          if (pos < 2) {
            pos = 9;
          }
        }

        resultado = ((soma % 11) < 2) ? 0 : (11 - soma % 11);
        if (resultado == toInteger(digitos.charAt(1))) {
          elemento.value = cpfCnpj.substr(0, 2) + "." + cpfCnpj.substr(2, 3) + "." + cpfCnpj.substr(5, 3) + "/" + cpfCnpj.substr(8, 4) + "-" + cpfCnpj.substr(12);
          return true;
        }
      }
    }

    if (mensagem === true) {
      Swal.fire({ title: "FALHA NA OPERAÇÃO!", text: "CPF/CNPJ inválido!", icon: "error" });
    }

    elemento.value = "";
    return false;
  }

  return true;
}
// Valida elementos do tipo telefone e FAX
// @param {InputHtml} elemento - Elemento a ser validado
// @param {String}  mensagem - Indica se deve ou não exibir a mensagem de alerta
function validarTelefone(elemento, mensagem) {
  var telefone = trim(elemento.value, [" ", "-", "(", ")"]);
  var mensagem = (existe(mensagem) === true) ? mensagem : true;

  if (telefone.length > 0) {
    if (telefone.length === 11) {
      if (telefone !== "00000000000") {
        elemento.value = "(" + telefone.substr(0, 2) + ")" + telefone.substr(2, 5) + "-" + telefone.substr(7, 4);
        return true;
      }
    } else if (telefone.length === 10) {
      elemento.value = "(" + telefone.substr(0, 2) + ")" + telefone.substr(2, 4) + "-" + telefone.substr(6, 4);
      return true;
    }

    if (mensagem === true) {
      alert("Telefone/Fax inválido!\r\nDigite um telefone no formato:\r\n(00)00000-0000");
    }

    elemento.value = "";
    return false;
  }

  return true;
}
// Valida elementos do tipo valor
// @param {InputHtml} elemento    - Elemento a ser validado
// @param {Integer}   quantidadeCasa  - Quantidade de casas decimais a validar
// @param {Boolean}   zerado      - Preenche o valor com zeros caso o número for vazio
function validarCasasDecimais(elemento, quantidadeCasa, zerado) {
  var valor = toFloat(elemento.value);
  var quantidadeCasa = (existe(quantidadeCasa) === true) ? quantidadeCasa : 2;
  var zerado = (existe(zerado) === true) ? zerado : true;

  elemento.value = ((zerado === false) && (valor === 0)) ? "" : numberFormat(valor, quantidadeCasa, ",", "");
}
// Valida elementos do tipo CEP
// @param {InputHtml} elemento - Elemento a ser validado
// @param {String}  mensagem - Indica se deve ou não exibir a mensagem de alerta
function validarCep(elemento, mensagem) {
  var cep = strPad(trim(elemento.value, [".", "-"]), "0", 8, "STR_PAD_LEFT");
  var mensagem = (existe(mensagem) === true) ? mensagem : true;

  if (cep.length > 0) {
    if (cep.length === 8) {
      elemento.value = cep.substr(0, 2) + "." + cep.substr(2, 3) + "-" + cep.substr(5);
      return true;
    }
    if (mensagem === true) {
      alert("CEP inválido!\r\nDigite um CEP no formato:\r\n00.000-000");
    }

    elemento.value = "";
    return false;
  }

  return true;
}
// Validar e-mail
// @param {InputHtml} elemento Elemento a ser validado
// @param {String}  mensagem Indica se deve ou não exibir a mensagem de alerta
function validarEmail(elemento, mensagem) {
  elemento.value = trim(stripAccents(elemento.value, [" ", ",", ";", "/", "ª", "º", "°", "!", "#", "$", "%", "&", "*", "?", "(", ")", "[", "]", "<", ">", "|", "+", "="]));
  var email = elemento.value;
  var mensagem = (existe(mensagem) === true) ? mensagem : true;

  if ((email.length > 0) && (email.indexOf("@") < 1) && (email.indexOf(".") < 1)) {
    if (mensagem === true) {
      alert("Email inválido!");
    }

    elemento.value = "";
    return false;
  }

  return true;
}
// Exibe um alerta para elementos do tipo leitura
// @param {InputHtml} elemento - O elemento a ser verificado
function validarSomenteLeitura(elemento) {
  if (elemento.readOnly === true) {
    alert("Este elemento não pode ser editado!");
  }
}
// Valida elementos do tipo placa de automóvel
// @param {InputHtml} elemento - Elemento a ser validado
// @param {String}  mensagem - Indica se deve ou não exibir a mensagem de alerta
function validarPlaca(elemento, mensagem) {
  var placa = trim(elemento.value.toUpperCase(), ['-']);
  var retorno = false;
  var letras = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
  var numeros = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
  if (mensagem == null) {
    mensagem = true;
  }
  if (placa.length === 7) {

    parteLetra = placa.substr(0, 3);
    parteNumero = placa.substr(3);
    achouLetra = 0;
    achouNumero = 0;

    each(parteLetra, function (letraPlaca) {
      each(letras, function (letra) {
        if (letraPlaca === letra) {
          achouLetra = achouLetra + 1;
        }
      });
    });

    each(parteNumero, function (numeroPlaca) {
      each(numeros, function (numero) {
        if (numeroPlaca === numero) {
          achouNumero = achouNumero + 1;
        }
      });
    });

    if ((achouLetra == 3) && (achouNumero == 4)) {
      elemento.value = [parteLetra, parteNumero].join('-');
      retorno = true;
    }
  }
  if (placa == '') {
    elemento.value = '';
    retorno = true;
  }
  if (retorno == false) {
    if (mensagem === true) {
      elemento.value = '';
      alert("Placa inválida!\r\nDigite uma placa no formato:\r\nAAA-0000");
    }
  }
  return retorno;
}
// :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// ::::::::::::::::::::::::::::::::::::::::::::::::::::: Interface :::::::::::::::::::::::::::::::::::::::::::::::::::::
// :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// Muda a cor de fundo de um elemento
// @param   {InputHtml}   elemento  O elemento a ser destacado
// @param   {String}    cor     A cor a ser inserida
function destacarElemento(elemento, cor) {
  var cor = (existe(cor) === true) ? cor : "#FFFF33";

  try { topo().RenovaSessao(); } catch (e) { }

  if ((elemento.type == "text") || (elemento.type == "password") || (elemento.type == "select-one") || (elemento.type == "textarea")) {
    elemento.style.backgroundColor = cor;

  } else if (elemento.type === "file") {
    try { id("dvFundo_" + elemento.id).style.display = "block"; } catch (e) { }

  } else if ((elemento.type === "button") || (elemento.type === "submit")) {
    elemento.style.backgroundImage = "url('Imagens/botao_foco.png')";
    elemento.onmouseout = function () {
      apagarElemento(elemento);
    }
    var timerBotao = setTimeout(function () {
      apagarElemento(elemento);
    }, 5000);
  }

  return true;
}
// Retorna a cor padrão de um elemento
// @param {InputHtml} elemento  - O elemento a ser apagado
// @param {String}  cor     - A cor a ser inserida
function apagarElemento(elemento, cor) {
  var cor = (existe(cor) === true) ? cor : "#FFFFFF";

  try { topo().RenovaSessao(); } catch (e) { }

  if (existe(elemento) === true) {
    if ((elemento.type === "text") || (elemento.type === "password") || (elemento.type === "select-one") || (elemento.type === "textarea")) {
      elemento.style.backgroundColor = cor;

    } else if (elemento.type === "file") {
      id("dvFundo_" + elemento.id).style.display = "block";

    } else if ((elemento.type === "button") || (elemento.type === "submit")) {
      elemento.style.backgroundImage = "url('Imagens/botao.png')";
    }
  }
  return true;
}
// Carrega o manual de ajuda para o elemento selecionado
// @param {String} codigo - O código do elemento no manual
function exibirManual(codigo) {
  topo().createModal({
    content: "<div style='display:block; padding: 0px; margin:0px; background-color:transparent; width:500px; height:304px; overflow-y: auto'><img src='Imagens/manual/" + codigo + ".png'></div>"
  });
}
// Exibir/ocultar caixa de diálogo
// @param   {Boolean}   exibir  Indica se é para exibir ou ocultar a caixa de diálogo flutuante
function exibirDialogo(exibir) {
  if (exibir === true) {
    id("dvCaixaDialogo").style.display = "block";
    timeOutCentralizar = setInterval(function () {
      id("dvCaixaDialogo").style.top = (50 + toInteger(topo().document.documentElement.scrollTop || topo().document.body.scrollTop)) + "px";
    }, 10);

  } else {
    try { clearInterval(timeOutCentralizar); } catch (e) { }
    timeOutCentralizar = null;

    id("dvConteudo").innerHTML = "";
    id("dvCaixaDialogo").style.display = "none";

    try {
      if (isSet("AO_FECHAR_FLUTUANTE") === true) {
        var aux = AO_FECHAR_FLUTUANTE;
        AO_FECHAR_FLUTUANTE = null;
        eval(aux);
      }
    } catch (e) { }
  }
}
// Faço a barra aumentar de tamanho a cada digito do usuário
// @param {InputHtml} elemento      - Elemento a ser avaliado (text, textarea, etc..)
// @param {String}  idBarra       - Identificador da div que contém a barra de progresso
// @param {String}  idTextoBarra    - Identificador do campo que recebe a porcentagem
// @param {String}  maximoCaractere   - Total de caracteres que representam todo o campo
// @param {String}  tamanhoImgBarra   - Tamanho da imagem da barra
function exibirProgresso(elemento, idBarra, idTextoBarra, maximoCaractere, tamanhoImgBarra) {
  var maximoCaractere = (existe(maximoCaractere) === true) ? toInteger(maximoCaractere) : toInteger(elemento.maxLength);
  var tamanhoImgBarra = (existe(tamanhoImgBarra) === true) ? tamanhoImgBarra : 300;

  if ((elemento.value).length < maximoCaractere) {
    id(idBarra).style.backgroundColor = "#888888";
    id(idBarra).style.backgroundImage = (tamanhoImgBarra === 175) ? "url(Imagens/barra_progresso_175px.png)" : "url(Imagens/barra_progresso.png)";
    id(idBarra).style.color = "#ffffff";

    porcentagem = (maximoCaractere / (elemento.value).length);
    id(idBarra).style.backgroundRepeat = "no-repeat";
    id(idBarra).style.backgroundPosition = "-" + toInteger(tamanhoImgBarra - toInteger(tamanhoImgBarra / porcentagem)) + "px";
    id(idTextoBarra).innerHTML = "[" + (elemento.value).length + " / " + maximoCaractere + "]";

  } else {
    id(idBarra).style.backgroundColor = "#CC0000";
    id(idBarra).style.backgroundImage = "url()";
    id(idBarra).style.color = "#FFFFFF";
    id(idTextoBarra).innerHTML = "[<label class='Alerta'>" + (elemento.value).length + "</label> / " + maximoCaractere + "]";
  }
}
// Carrega um calendário para seleção de data (datePicker)
// @param   {String}  configuracao::idCampo   Identificador do campo a receber o valor
// @param   {String}  configuracao::formato   Define como o campo deverá retornar (Ex.: 'DD/MM/AAAA', 'MM/AAAA')
// @param   {String}  configuracao::callBack  Função a ser chamada após o carregamento da data
function exibirCalendario(configuracao) {

  var configuracaoPadrao = {
    url: exibirUrl() + '/trata-banco.php',
    idCampo: '',
    formato: 'd/m/Y',
    callBack: '',
    mes: gerarData('m'),
    ano: gerarData('Y'),
    idFrame: 'FrameCorpo',
    idSubframe: ''
  };

  if (existe(configuracao.idCampo) == false) {
    configuracao.idCampo = configuracaoPadrao.idCampo;
  }

  if (existe(configuracao.formato) == false) {
    configuracao.formato = configuracaoPadrao.formato;
  }

  if (existe(configuracao.callBack) == false) {
    configuracao.callBack = configuracaoPadrao.callBack;
  }

  if (existe(configuracao.mes) == false) {
    configuracao.mes = configuracaoPadrao.mes;
  }

  if (existe(configuracao.ano) == false) {
    configuracao.ano = configuracaoPadrao.ano;
  }

  if (existe(configuracao.idFrame) == false) {
    configuracao.idFrame = configuracaoPadrao.idFrame;
  }

  if (existe(configuracao.idSubframe) == false) {
    configuracao.idSubframe = configuracaoPadrao.idSubframe;
  }

  configuracao.url = configuracaoPadrao.url
    + '?exibir_calendario=S'
    + '&id_campo=' + configuracao.idCampo
    + '&formato=' + configuracao.formato
    + '&call_back=' + configuracao.callBack
    + '&mes=' + configuracao.mes
    + '&ano=' + configuracao.ano
    + '&id_frame=' + configuracao.idFrame
    + '&id_subframe=' + configuracao.idSubframe
    ;

  topo().createModal({
    url: configuracao.url,
    idModalOnUrl: true,
    width: 600,
    height: 320
  });
}
// Limpa e insere opções há campos do tipo seleção (InputHtml::Select).
// @param {InputHtml::Select} elemento O elemento onde os dados serão inseridos
// @param {Object} dados Os novos dados a serem exibidos no padrão: "{"rotulo" : "valor"}"
// @param {Boolean} opcaoVazia Indica se é para iniciar com a opção vazia "{"---" : ""}"
function inserirOpcoesSelecao(elemento, dados, opcaoVazia) {
  var opcaoVazia = (existe(opcaoVazia) === true) ? opcaoVazia : true;
  while (elemento.options.length > 0) {
    elemento.remove(0);
  }

  if (opcaoVazia == true) {
    var novaOpcao = document.createElement("option");
    novaOpcao.value = "";
    novaOpcao.text = "---";
    try {
      elemento.add(novaOpcao);
    } catch (e) {
      elemento.add(novaOpcao, null);
    }
  }

  if (count(dados) > 0) {
    each(dados, function (rotulo, valor) {
      var novaOpcao = document.createElement("option");

      novaOpcao.text = rotulo;
      novaOpcao.value = valor;

      try {
        elemento.add(novaOpcao);
      } catch (e) {
        elemento.add(novaOpcao, null);
      }
    });
  }
}
// Limpa todos os elementos de uma tabela
// @param {string} idTabela Identificador da tabela
// @param {boolean} cabecalho [false] Indica se o cabeçalho será removido também
// @return {void}
function limparTabela(idTabela, cabecalho) {
  var tabela = id(idTabela);
  var tamanho = tabela.rows.length - 1;
  var primeiraLinha = 0;

  if (cabecalho) {
    primeiraLinha = -1;
  }
  for (var i = tamanho; i > primeiraLinha; i = i - 1) {
    tabela.deleteRow(i);
  }
}

// Insere uma linha na tabela
// @param {string} conf::idTabela Identificador da tabela
// @param {string} conf::idLinha Identificador da linha
// @param {list} conf::classeLinha Lista de classes da linha
// @param {list} colunas Lista com o html das colunas
// @param {list} classes ['TabelaCorpoNormal'] Lista com as classes css das colunas na mesma ordem
// @return {void}
function inserirLinhaTabela(conf, colunas, classes) {
  var tabela = id(conf['idTabela']);
  var idLinha = '';
  var classeLinha = '';
  var quantidade = colunas.length;
  var index = 0;
  var linha = null;
  var coluna = null;
  var conteudo = '';
  var classeCss = '';

  if (existe(conf['idLinha']) === true) {
    idLinha = conf['idLinha'];
  }
  if (existe(conf['classeLinha']) === true) {
    classeLinha = conf['classeLinha'];
  }
  if (existe(classes) === false) {
    classes = new Array(quantidade).join('TabelaCorpoNormal,').split(',');
  }
  linha = tabela.insertRow();
  linha.setAttribute('id', idLinha);
  linha.setAttribute('class', classeLinha);
  for (var index = 0; index < quantidade; index = index + 1) {
    if (classes[index] !== '') {
      classeCss = classes[index];
    }
    coluna = linha.insertCell(index);
    coluna.className = classeCss;
    conteudo = document.createElement('div');
    conteudo.innerHTML = colunas[index];
    coluna.appendChild(conteudo);
  }
}
// :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// ::::::::::::::::::::::::::::::::::::::::::::::::::::::: Modal :::::::::::::::::::::::::::::::::::::::::::::::::::::::
// :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// cria uma div flutuante
function createModal(parameters) {

  var idModal = 'id-' + gerarData('YmdHisu');
  var modalOverlay = document.createElement('div');
  var modalContainer = document.createElement('div');
  var modalHeader = document.createElement('div');
  var modalContent = document.createElement('div');
  var modalClose = document.createElement('div');

  // configurações padrão
  settings = {
    draggable: true,
    openCallback: false,
    closeCallback: false,
    width: 740,
    height: 420
  };

  // configurações passadas por parâmetro
  if (parameters.draggable) {
    settings.draggable = parameters.draggable;
  }

  if (parameters.closeCallback) {
    settings.closeCallback = parameters.closeCallback;
  }

  if (parameters.openCallback) {
    settings.openCallback = parameters.openCallback;
  }

  if (parameters.width) {
    settings.width = parameters.width;
  }

  if (parameters.height) {
    settings.height = parameters.height;
  }

  // conteúdo
  if (parameters.content) {
    modalContent.innerHTML = parameters.content;
  }

  // fechar
  modalClose.onclick = function () {
    closeModal(idModal);
  };

  modalOverlay.onclick = function () {
    closeModal(idModal);
  };


  // arrastar
  if (settings.draggable) {
    modalHeader.style.cursor = 'move';
    modalHeader.onmousedown = function (e) {
      var xPosition = (window.event !== undefined) ? window.event.clientX : e.clientX,
        yPosition = (window.event !== undefined) ? window.event.clientY : e.clientY,
        differenceX = xPosition - modalContainer.offsetLeft,
        differenceY = yPosition - modalContainer.offsetTop;

      document.onmousemove = function (e) {
        xPosition = (window.event !== undefined) ? window.event.clientX : e.clientX;
        yPosition = (window.event !== undefined) ? window.event.clientY : e.clientY;

        modalContainer.style.left = ((xPosition - differenceX) > 0) ? (xPosition - differenceX) + 'px' : 0;
        modalContainer.style.top = ((yPosition - differenceY) > 0) ? (yPosition - differenceY) + 'px' : 0;

        document.onmouseup = function () {
          window.document.onmousemove = null;
        };
      };
    };
  }

  // adicionar
  modalOverlay.setAttribute('id', 'modal-overlay-' + idModal);
  modalContainer.setAttribute('id', 'modal-container-' + idModal);
  modalHeader.setAttribute('id', 'modal-header-' + idModal);
  modalContent.setAttribute('id', 'modal-content-' + idModal);
  modalClose.setAttribute('id', 'modal-close-' + idModal);

  modalOverlay.setAttribute('class', 'modal-overlay');
  modalContainer.setAttribute('class', 'modal-container');
  modalHeader.setAttribute('class', 'modal-header');
  modalContent.setAttribute('class', 'modal-content');
  modalClose.setAttribute('class', 'modal-close');

  modalHeader.appendChild(modalClose);
  modalContainer.appendChild(modalHeader);
  modalContainer.appendChild(modalContent);

  document.body.appendChild(modalOverlay);
  document.body.appendChild(modalContainer);


  // url (não usar ajax...)
  if (parameters.url) {

    var url = parameters.url;

    // usar o id do modal na url
    if (parameters.idModalOnUrl) {
      url = url + '&id_modal=' + idModal;
    }

    modalContent.innerHTML = ''
      + '<iframe '
      + ' name="frame-modal-' + idModal + '"'
      + ' id="frame-modal-' + idModal + '"'
      + ' style="padding:0px; margin:0px; background-color:transparent; width: ' + settings.width + 'px; height: ' + settings.height + 'px; overflow-y:scroll; border-top:0px; border-left:0px; border-style:none"'
      + ' src="' + url + '"'
      + ' border="0"'
      + ' ></iframe>';
  }

  // centralizar
  var documentHeight = Math.max(document.body.scrollHeight, document.documentElement.scrollHeight),

    modalWidth = Math.max(modalContainer.clientWidth, modalContainer.offsetWidth),
    modalHeight = Math.max(modalContainer.clientHeight, modalContainer.offsetHeight),

    browserWidth = 0,
    browserHeight = 0,

    amountScrolledX = 0,
    amountScrolledY = 0;

  if (typeof (window.innerWidth) === 'number') {
    browserWidth = window.innerWidth;
    browserHeight = window.innerHeight;
  } else if (document.documentElement && document.documentElement.clientWidth) {
    browserWidth = document.documentElement.clientWidth;
    browserHeight = document.documentElement.clientHeight;
  }

  if (typeof (window.pageYOffset) === 'number') {
    amountScrolledY = window.pageYOffset;
    amountScrolledX = window.pageXOffset;
  } else if (document.body && document.body.scrollLeft) {
    amountScrolledY = document.body.scrollTop;
    amountScrolledX = document.body.scrollLeft;
  } else if (document.documentElement && document.documentElement.scrollLeft) {
    amountScrolledY = document.documentElement.scrollTop;
    amountScrolledX = document.documentElement.scrollLeft;
  }

  modalContainer.style.top = '100px';
  modalContainer.style.top = amountScrolledY + (browserHeight / 2) - (modalHeight / 2) + 'px';

  modalContainer.style.left = amountScrolledX + (browserWidth / 2) - (modalWidth / 2) + 'px';

  modalOverlay.style.height = documentHeight + 'px';
  modalOverlay.style.width = '100%';

  return idModal;
}
// fecha uma div flutuante
function closeModal(modal) {
  if (modal != '') {
    if ((modal == 'all') || (modal == 'ALL')) {
      var listaOverlay = classe('modal-overlay');
      var listaContainer = classe('modal-container');

      each(listaOverlay, function (chave, idCampo) {
        id(idCampo).style.display = 'none';
      });
      each(listaContainer, function (chave, idCampo) {
        id(idCampo).style.display = 'none';
      });
    } else {
      // id('modal-overlay-' + modal).style.display = 'none';
      // id('modal-container-' + modal).style.display = 'none';

      var node = document.getElementById('modal-overlay-' + modal);
      if (node.parentNode) {
        node.parentNode.removeChild(node);
      }

      var node = document.getElementById('modal-container-' + modal);
      if (node.parentNode) {
        node.parentNode.removeChild(node);
      }
    }
  }
}
// :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// ::::::::::::::::::::::::::::::::::::::::::::::::::::: Pollyfill :::::::::::::::::::::::::::::::::::::::::::::::::::::
// :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
// Substitui as tags que estão dentro de {{ }}
// Obs: usar no máximo 1 espaço entre as tags e o conteúdo
// @param {Object} objeto O objeto com as chaves e valores a serem substituidos
String.prototype.format = function (args) {
  var re = /\{\{ ([^}]+) \}\}/g;
  var s = this.replace(re, function (_, match) { return args[match]; });
  var re = /\{\{([^}]+)\}\}/g;
  return s.replace(re, function (_, match) { return args[match]; });
};


function number_format(numero, decimal, decimal_separador, milhar_separador) {
  numero = (numero + '').replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+numero) ? 0 : +numero,
    prec = !isFinite(+decimal) ? 0 : Math.abs(decimal),
    sep = (typeof milhar_separador === 'undefined') ? ',' : milhar_separador,
    dec = (typeof decimal_separador === 'undefined') ? '.' : decimal_separador,
    s = '',
    toFixedFix = function (n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  // Fix para IE: parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }

  return s.join(dec);
}
