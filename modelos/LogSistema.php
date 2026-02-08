<?php
require_once 'Classes/bancoDeDados.php';
require_once 'Classes/PHPSpreadsheet/PHPSpreadsheet.php';
require_once 'Classes/tFPDF/tfpdf.php';

require_once 'Usuario.php';
require_once 'TipoArquivo.php';
require_once 'Documentos.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LogSistema{

    private $id_log;
    private $id_empresa;
    private $usuario;
    private $tabela_acao;
    private $modulo;
    private $descricao;
    private $data_log;

    private function tabela(){
        return (string) 'log_sistema';
    }

    private function modelo(){
        return (array) ['_id' => convert_id(''), 'empresa' => convert_id(''),'usuario' => (string) '', 'tabela_acao' => (string) '', 'modulo' => (string) '', 'descricao' => (string) '', 'data_log' => 'date'];
    }

    private function colocar_dados($dados){
        date_default_timezone_set('America/Sao_Paulo');

        if(array_key_exists('id_log', $dados) == true){
            $this->id_log = convert_id($dados['id_log']);
        }
        
        if(array_key_exists('id_empresa', $dados) == true){
            $this->id_empresa = convert_id($dados['id_empresa']);
        }

        if(array_key_exists('usuario', $dados) == true){
            $this->usuario = convert_id($dados['usuario']);
        }

        if(array_key_exists('tabela_acao', $dados) == true){
            $this->tabela_acao = (string) $dados['tabela_acao'];
        }else{
            $this->tabela_acao = (string) '';
        }

        if(array_key_exists('modulo', $dados) == true){
            $this->modulo = (string) $dados['modulo'];
        }else{
            $this->modulo = (string) '';
        }

        if(array_key_exists('descricao', $dados) == true){
            $this->descricao = (string) $dados['descricao'];
        }else{
            $this->descricao = (string) '';
        }

        if(array_key_exists('data_log', $dados) == true){
            $this->data_log = model_date($dados['data_log']);
        }else{
            $this->data_log = model_date();
        }
    }

    public function salvar_dados($dados){
        $objeto_usuario = new Usuario();
        $this->colocar_dados($dados);

        $retorno_usuario = (array) $objeto_usuario->pesquisar((array) ['filtro' => (array) ['_id', '===', $dados['usuario']]]);

        if(empty($retorno_usuario) == false){
            if(array_key_exists('login_usuario', $retorno_usuario) == true){
                $this->usuario = (string) $retorno_usuario['login_usuario'];
            }
        }

        return (bool) model_insert((string) $this->tabela(), (array) ['empresa' => $this->id_empresa, 'usuario' => (string) $this->usuario, 'tabela_acao' => (string) $this->tabela_acao, 'modulo' => (string) $this->modulo, 'descricao' => (string) $this->descricao, 'data_log' => $this->data_log]);
    }

    public function deletar($dados){
        return (bool) model_delete((string) $this->tabela(), (array) $dados['filtro']);
    }

    public function pesquisar($dados){
        return (array) model_one((string) $this->tabela(), (array) $dados['filtro']);
    }

    public function pesquisar_todos($dados){
        return (array) model_all((string) $this->tabela(), (array) $dados['filtro'], (array) $dados['ordenacao'], (int) $dados['limite']);
    }

    /** 
     * @param array $dados
     * Função responsável por validar e montar os filtros de pesquisas do log, com as informações que vem do usuário.
    */
    private function filtro_log_sistema($dados){
        $id_empresa = (int) (isset($dados['codigo_empresa']) ? (int) intval($dados['codigo_empresa'], 10):0);
        $id_usuario = (int) (isset($dados['codigo_usuario']) ? (int) intval($dados['codigo_usuario'], 10):0);
        $usuario = (string) (isset($dados['usuario']) ? (string) $dados['usuario']:'TODOS');
        $codigo_barras = (string) (isset($dados['codigo_barras']) ? (string) $dados['codigo_barras']:'');
        $modulo = (string) (isset($dados['modulo']) ? (string) $dados['modulo']:'TODOS');
        $data_inical = (String) (isset($dados['data_inicial']) ? (string) $dados['data_inicial']:'');
        $data_final = (string) (isset($dados['data_final']) ? (string) $dados['data_final']:'');

        $filtro = (array) [];
        $filtro_pesquisa = (array) ['filtro' => (array) [], 'ordenacao' => (array) ['data_log' => (bool) false], 'limite' => (int) 1000];

        if($id_empresa != 0){
            array_push($filtro, (array) ['id_empresa', '===', (int) $id_empresa]);
        }

        if($usuario != 'TODOS'){
            array_push($filtro, (array) ['usuario', '===', (string) $usuario]);
        }

        if($codigo_barras != ''){
            array_push($filtro, (array) ['codigo_barras', '===', (string) $codigo_barras]);
        }

        if($modulo != 'TODOS'){
            array_push($filtro, (array) ['modulo', '===', (string) $modulo]);
        }

        if($data_inical != ''){
            array_push($filtro, (array) ['data_log', '>=', model_date($data_inical, '00:00:00')]);
        }

        if($data_final != ''){
            array_push($filtro, (array) ['data_log', '<=', model_date($data_final, '23:59:59')]);
        }
        
        $filtro_pesquisa['filtro'] = (array) ['and' => (array) $filtro];

        return (array) $this->pesquisar_todos($filtro_pesquisa);
    }

    /**
     * função responsável por montar o relatório do tipo arquivo .xlsx, com os filtros que o usuário escolheu no formulário de pesquisa do sistema.
     * @param mixed $dados
     * @return array
     */
    public function gerar_arquivo_xlsx($dados){
      $objeto_tipo_arquivo = new TipoArquivo();
      $filtro_pesquisa_tipo_arquivo = (array) ['filtro' => (array) ['tipo_arquivo', '===', (string) '.XLSX']];
      $retorno_tipo_arquivo = (array) $objeto_tipo_arquivo->pesquisar($filtro_pesquisa_tipo_arquivo);

      $codigo_barras_arquivo = (string) codigo_barras();
      $endereco_documento = (string) $retorno_tipo_arquivo['endereco_documento'].$codigo_barras_arquivo.'.xlsx';

      
      if(empty($retorno_tipo_arquivo) == true){

        return (array) ['status' => (bool) false, 'tipo_erro' => (string) 'ERRO_CONFIGURACAO', 'titulo' => (string) 'ERRO DURANTE O PROCESSO', 'descricao' => (string) 'Não é possível gerar o arquivo, sem antes configurar um endereço de arquivo', 'icone' => (string) 'error'];
      }

      $retorno_dados = (array) $this->filtro_log_sistema($dados);

      if(empty($retorno_dados) == true){
        return (array) ['status' => (bool) false, 'tipo_erro' => (string) 'ERRO_PESQUISA', 'titulo' => (string) 'ERRO DURANTE O PROCESSO', 'descricao' => (string) 'Não foi encontrado logs com os filtros passados!'];
      }

      //AQUI COMEÇA A GERAR O ARQUIVO DO EXCELL COM AS INFORMAÇÕES VINDAS POR MEIO DA PESQUISA DO LOG

      $spreadsheet = new Spreadsheet();
        $planilha = $spreadsheet->getActiveSheet();
    
        // Largura automática das colunas
        foreach(array('A', 'B', 'C', 'D', 'E', 'F') as $coluna) {
          $planilha->getColumnDimension($coluna)->setAutoSize(true);
        }
    
        //Propriedades do objeto.
        $spreadsheet->getProperties()->setCreator($dados['usuario'])->setLastModifiedBy(strval($dados['usuario']))->setTitle($codigo_barras_arquivo);
    
        // Estilos
        $negrito = (array) ['font' => ['bold' => true]];
        $borda = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['argb' => '00000000']]]];
    
        $linha = (int) 1;

        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        //Cabeçalho da planilha
        $sheet->getStyle('A' . $linha)->getFont()->setBold(true);
        $sheet->setCellValue('A' . $linha, 'ID');
        $sheet->getStyle('B' . $linha)->getFont()->setBold(true);
        $sheet->setCellValue('B' . $linha, 'USUARIO');
        $sheet->getStyle('C' . $linha)->getFont()->setBold(true);
        $sheet->setCellValue('C' . $linha, 'CODIGO BARRAS');
        $sheet->getStyle('D' . $linha)->getFont()->setBold(true);
        $sheet->setCellValue('D' . $linha, 'MODULO');
        $sheet->getStyle('E' . $linha)->getFont()->setBold(true);
        $sheet->setCellValue('E' . $linha, 'DATA');
        $sheet->getStyle('F' . $linha)->getFont()->setBold(true);
        $sheet->setCellValue('F' . $linha, 'DESCRICAO');

        foreach($retorno_dados as $log_sistema){
          $linha = $linha + 1;
          $sheet->setCellValue('A'.$linha, $log_sistema['id_log']);
          $sheet->setCellValue('B'.$linha, $log_sistema['usuario']);
          $sheet->setCellValue('C'.$linha, $log_sistema['codigo_barras']);
          $sheet->setCellValue('D'.$linha, $log_sistema['modulo']);
          $sheet->setCellValue('E'.$linha, convert_date($log_sistema['data_log']));
          $sheet->setCellValue('F'.$linha, $log_sistema['descricao']);
        }

        $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($endereco_documento);

        $array_dados_documento = (array) ['codigo_empresa' => (int) intval($dados['codigo_empresa'], 10), 'codigo_tipo_arquivo' => (int) intval($retorno_tipo_arquivo['id_tipo_arquivo'], 10), 'codigo_usuario' => (int) intval($dados['codigo_usuario'], 10), 'nome_documento' => (string) 'RELATORIO LOG DO SISTEMA', 'descricao' => (string) 'Documento de relatório de log gerado pelo sistema', 'endereco' => (string) $endereco_documento, 'codigo_barras' => (string) $codigo_barras_arquivo, 'tipo_alteracao' => (string) 'INFORMACOES', 'forma_visualizacao' => (string) 'PUBLICO', 'tipo_arquivo' => (string) 'RELATORIO_SISTEMA'];

        $objeto_documento = new Documentos();
        $retorno = (bool) $objeto_documento->salvar_dados($array_dados_documento);

        if($retorno == true){
            $retorno_pesquisa_documento = (array) $objeto_documento->pesquisar_documento(['filtro' => (array) ['codigo_barras', '===', (string) $codigo_barras_arquivo]]);

            if(empty($retorno_pesquisa_documento) == false){
                return (array) ['status' => (bool) true, 'tipo_erro' => (string) 'SUCESSO_REGISTRO_LOG', 'titulo' => (string) 'SUCESSO AO SALVAR LOG', 'mensagem' => (string) 'Arquivo do log salvo com sucesso!', 'icone' => (string) 'success', 'endereco_documento' => (string) $endereco_documento, 'codigo_documento' => (int) intval($retorno_pesquisa_documento['id_documento'], 10)];
            }else{
                return (array) ['status' => (bool) false, 'tipo_erro' => (string) 'ERRO_GERAR_DOCUMENTO', 'titulo' => (string) 'ERRO DURANTE O PROCESSO', 'mensagem' => (string) 'Não foi possível salvar o arquivo!', 'icone' => (string) 'error'];
            }
        }else{
            return (array) ['status' => (bool) false, 'tipo_erro' => (string) 'ERRO_GERAR_DOCUMENTO', 'titulo' => (string) 'ERRO DURANTE O PROCESSO', 'mensagem' => (string) 'Não foi possível salvar o arquivo!', 'icone' => (string) 'error'];
        }
    }

    public function gerar_arquivo_pdf($dados){
        $objeto_tipo_arquivo = new TipoArquivo();
        $filtro_pesquisa_tipo_arquivo = (array) ['filtro' => (array) ['tipo_arquivo', '===', (String) '.PDF']];
        $retorno_tipo_arquivo = (array) $objeto_tipo_arquivo->pesquisar($filtro_pesquisa_tipo_arquivo);
        
        $codigo_barras_arquivo = (string) codigo_barras();
        $endereco_documento = (string) $retorno_tipo_arquivo['endereco_documento'].$codigo_barras_arquivo.'.pdf';

        if(empty($retorno_tipo_arquivo) == true){
            return (array) ['status' => (bool) false, 'tipo_erro' => (string) 'ERRO_CONFIGURACAO', 'titulo' => (string) 'ERRO DURANTE O PROCESSO', 'descricao' => (string) 'Não é possível gerar o arquivo, sem antes configurar um endereço de arquivo', 'icone' => (string) 'error'];
        }

        $retorno_dados = (array) $this->filtro_log_sistema($dados);

        if(empty($retorno_dados) == true){
            return (array) ['status' => (bool) false, 'tipo_erro' => (string) 'ERRO_PESQUISA', 'titulo' => (string) 'ERRO DURANTE O PROCESSO', 'descricao' => (string) 'Não foi encontrado logs com os filtros passados!'];
        }

        define("_SYSTEM_TTFONTS", "C:/Windows/Fonts/");
        
        $pdf = new tFPDF();
        $pdf->AddPage();
        
        $pdf->AddFont('DejaVu','B','arialBd.ttf',true);
        $pdf->SetFont('DejaVu','B',30);
        
        $pdf->Image('imagens/logo_empresa_preto_pequeno.jpg',10,6,30);
        $pdf->Cell(200, 20, 'Relatório de Logs', 0, 0, 'C');
        
        $pdf->Ln(20);
        
        $pdf->SetFont('DejaVu','B',12);
        $pdf->SetFillColor(255, 0, 0);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(128, 0, 0);
        $pdf->SetLineWidth(.3);

        $width = array(20, 25, 40, 45, 25, 35);
        
        $cabecalho = array('ID', 'USUARIO', 'CODIGO BARRAS', 'MODULO', 'DATA', 'DESCRICAO');
        for($cont = 0; $cont < count($cabecalho); $cont++){
            $pdf->Cell($width[$cont], 7, $cabecalho[$cont], 1, 0, 'C', true);
        }
        $pdf->Ln();

        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        
        $fill = (bool) false;
        foreach($retorno_dados as $dados_log){
            $pdf->Cell($width[0], 6, str_pad($dados_log['id_log'], 4, "0", STR_PAD_LEFT), 'LR', 0, 'C', $fill);
            $pdf->Cell($width[1], 6, $dados_log['usuario'], 'LR', 0, 'C', $fill);
            $pdf->Cell($width[2], 6, $dados_log['codigo_barras'], 'LR', 0, 'C', $fill);
            $pdf->Cell($width[3], 6, $dados_log['modulo'], 'LR', 0, 'C', $fill);
            $pdf->Cell($width[4], 6, convert_date($dados_log['data_log']), 'LR', 0, 'L', $fill);
            $pdf->MultiCell($width[5], 6, $dados_log['descricao'], 'LR', 'L',$fill);
            $pdf->Ln();
            $fill = !$fill;
        }

        $pdf->Cell(array_sum($width), 0, '', 'T');

        $pdf->Output('F', $endereco_documento);

        $array_dados_documento = (array) ['codigo_empresa' => (int) intval($dados['codigo_empresa'], 10), 'codigo_tipo_arquivo' => (int) intval($retorno_tipo_arquivo['id_tipo_arquivo'], 10), 'codigo_usuario' => (int) intval($dados['codigo_usuario'], 10), 'nome_documento' => (string) 'RELATORIO LOG DO SISTEMA', 'descricao' => (string) 'Documento de relatório de log gerado pelo sistema', 'endereco' => (string) $endereco_documento, 'codigo_barras' => (string) $codigo_barras_arquivo, 'tipo_alteracao' => (string) 'INFORMACOES', 'forma_visualizacao' => (string) 'PUBLICO', 'tipo_arquivo' => (string) 'RELATORIO_SISTEMA'];

        $objeto_documento = new Documentos();
        $retorno = (bool) $objeto_documento->salvar_dados($array_dados_documento);

        if($retorno == true){
            $retorno_pesquisa_documento = (array) $objeto_documento->pesquisar_documento(['filtro' => (array) ['codigo_barras', '===', (string) $codigo_barras_arquivo]]);

            if(empty($retorno_pesquisa_documento) == false){
                return (array) ['status' => (bool) true, 'tipo_erro' => (string) 'SUCESSO_REGISTRO_LOG', 'titulo' => (string) 'SUCESSO AO SALVAR LOG', 'mensagem' => (string) 'Arquivo do log salvo com sucesso!', 'icone' => (string) 'success', 'endereco_documento' => (string) $endereco_documento, 'codigo_documento' => (int) intval($retorno_pesquisa_documento['id_documento'], 10)];
            }else{
                return (array) ['status' => (bool) false, 'tipo_erro' => (string) 'ERRO_GERAR_DOCUMENTO', 'titulo' => (string) 'ERRO DURANTE O PROCESSO', 'mensagem' => (string) 'Não foi possível salvar o arquivo!', 'icone' => (string) 'error'];
            }
        }else{
            return (array) ['status' => (bool) false, 'tipo_erro' => (string) 'ERRO_GERAR_DOCUMENTO', 'titulo' => (string) 'ERRO DURANTE O PROCESSO', 'mensagem' => (string) 'Não foi possível salvar o arquivo!', 'icone' => (string) 'error'];
        }
    }
}
?>