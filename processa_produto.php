<?php
require_once "auth.php";

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

    // O produto so pode ser ligado a um fornecedor que existe
    $comando = $conexao->prepare(
        "SELECT id_fornecedor FROM fornecedores WHERE id_fornecedor = :id"
    );
    $comando->execute(['id' => $id_fornecedor]);

    if (!$comando->fetch()) {
        responder(false, "Fornecedor nao encontrado.");
    }

    $sql = "INSERT INTO produtos
    (nome, descricao, preco, quantidade_estoque, id_fornecedor)
    VALUES
    (:nome, :descricao, :preco, :quantidade_estoque, :id_fornecedor)";

    $comando = $conexao->prepare($sql);

    $comando->bindParam(':nome', $nome);
    $comando->bindParam(':descricao', $descricao);
    $comando->bindParam(':preco', $preco);
    $comando->bindParam(':quantidade_estoque', $quantidade_estoque);
    $comando->bindParam(':id_fornecedor', $id_fornecedor);

    $comando->execute();

   // Logica adicional: valor total do produto em estoque
   $valor_total = number_format($preco * $quantidade_estoque, 2, ",", ".");

   responder(true, "Produto cadastrado com sucesso! Codigo: " . $conexao->lastInsertId()
       . ". Valor total em estoque: R$ " . $valor_total);
}
