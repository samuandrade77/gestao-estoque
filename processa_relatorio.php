<?php
require_once "resposta.php";

$data_inicio = $_GET['data_inicio'] ?? "";
$data_inicio = trim($data_inicio);

$data_final = $_GET['data_final'] ?? "";
$data_final = trim($data_final);

$codigo_produto = $_GET['codigo_produto'] ?? "";
$codigo_produto = trim($codigo_produto);

$codigo_fornecedor = $_GET['codigo_fornecedor'] ?? "";
$codigo_fornecedor = trim($codigo_fornecedor);

$tipo_movimentacao = $_GET['tipo_movimentacao'] ?? "";
$tipo_movimentacao = trim($tipo_movimentacao);


if ( $data_inicio =="" || $data_final =="") {
   responder(false, "Informe as datas.");
}elseif($data_inicio > $data_final) {
  responder(false, "Data inicial nao pode ser posterior a data final");
}elseif(
    ($codigo_produto != "" &&(!is_numeric($codigo_produto) || $codigo_produto <= 0 )) ||
    ($codigo_fornecedor != "" &&(!is_numeric($codigo_fornecedor) || $codigo_fornecedor <= 0))
){
    responder(false, "Codigo do produto ou fornecedor invalido!");
}elseif(
    $tipo_movimentacao != "" &&
    $tipo_movimentacao != "Entrada" &&
    $tipo_movimentacao != "Saida"
){
 responder(false, "Tipo de Movimentacao invalido.");
}else{
    // Logica adicional: calcula quantos dias o relatorio cobre
    $dias = (new DateTime($data_inicio))->diff(new DateTime($data_final))->days + 1;

    responder(true, "Relatorio validado. Periodo de " . $dias . " dia(s).");
}
