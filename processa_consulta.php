<?php
require_once "resposta.php";

$id_produto = $_GET['id_produto'] ?? "";
$id_produto = trim($id_produto);

$descricao = $_GET['descricao'] ?? "";
$descricao = trim($descricao);

$nome = $_GET['nome'] ?? "";
$nome = trim($nome);

$id_fornecedor = $_GET['id_fornecedor'] ?? "";
$id_fornecedor = trim($id_fornecedor);

$preco = $_GET['preco'] ?? "";
$preco = trim($preco);

$quantidade_estoque = $_GET['quantidade_estoque'] ?? "";
$quantidade_estoque = trim($quantidade_estoque);

if(
    $id_produto == "" && 
    $descricao == "" &&
    $nome == "" &&
    $id_fornecedor == "" &&
    $preco == "" &&
    $quantidade_estoque == ""
){
  responder(false, "Informe pelo menos um criterio para consulta.");
}else{
  // Logica adicional: informa quantos criterios foram preenchidos
  $criterios = array_filter(
    [$id_produto, $descricao, $nome, $id_fornecedor, $preco, $quantidade_estoque],
    function ($valor) { return $valor != ""; }
  );

  responder(true, "Consulta validada com " . count($criterios) . " criterio(s).");
}
