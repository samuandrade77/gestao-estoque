<?php
require_once "resposta.php";

$nome = $_POST['nome'] ?? "";
$nome = trim($nome);

$descricao = $_POST['descricao'] ?? "";
$descricao = trim($descricao);

$preco = $_POST['preco'] ?? "";
$preco = trim($preco);

$quantidade_estoque = $_POST['quantidade_estoque'] ?? "";
$quantidade_estoque = trim($quantidade_estoque);

$id_fornecedor = $_POST['id_fornecedor'] ?? "";
$id_fornecedor = trim($id_fornecedor);

if (
    $nome == "" || 
    $descricao == "" ||
    !is_numeric($preco) ||
    $preco <= 0 ||
    !is_numeric($quantidade_estoque) ||
    $quantidade_estoque < 0 ||
    !is_numeric($id_fornecedor) ||
    $id_fornecedor <= 0
) {
    responder(false, "Dados invalidos");

} else {

    // Logica adicional: valor total do produto em estoque
    $valor_total = number_format($preco * $quantidade_estoque, 2, ",", ".");

    responder(true, "Dados do produto validados! Valor total em estoque: R$ " . $valor_total);
}
