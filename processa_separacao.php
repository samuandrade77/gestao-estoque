<?php
require_once "resposta.php";

$codigo_pedido = $_POST['codigo_pedido'] ?? "";
$codigo_pedido = trim($codigo_pedido);

$codigo_produto = $_POST['codigo_produto'] ?? "";
$codigo_produto = trim($codigo_produto);

$data = $_POST['data'] ?? "";
$data = trim($data);

$quantidade_separada = $_POST['quantidade_separada'] ?? "";
$quantidade_separada = trim($quantidade_separada);

$observacao = $_POST['observacao'] ?? "";
$observacao = trim($observacao);

if(
    $codigo_pedido == "" ||
    $codigo_produto == "" ||
    !data_valida($data) ||
    $observacao == "" ||
    !ctype_digit($quantidade_separada) ||
    $quantidade_separada <= 0
){
  responder(false, "Dados invalidos para separacao");
}elseif($data > date('Y-m-d')){
  responder(false, "A data da separacao nao pode ser futura.");
}else{
  // Logica adicional: classifica o volume da separacao
  $volume = $quantidade_separada > 50 ? "grande" : "pequeno";

  responder(true, "Separacao validada: " . $quantidade_separada .
      " unidade(s), volume " . $volume . ".");
}
