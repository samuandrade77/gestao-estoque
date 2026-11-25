<?php
require_once "auth.php";

$nome = $_POST['nome_fornecedor'] ?? "";
$nome = trim($nome);

$cnpj = $_POST['cnpj_fornecedor'] ?? "";
$cnpj = trim($cnpj);

$telefone = $_POST['telefone_fornecedor'] ?? "";
$telefone = trim($telefone);

$email = $_POST['email_fornecedor'] ?? "";
$email = trim($email);

$endereco = $_POST['endereco_fornecedor'] ?? "";
$endereco = trim($endereco);

if ($nome == "" || 
    $cnpj == "" ||
    $telefone == "" ||
    $email == "" ||
    $endereco == ""
    )
{
responder(false, "Dados invalidos");
} elseif (!ctype_digit($cnpj) || strlen($cnpj) != 14) {
    responder(false, "O CNPJ deve ter 14 numeros.");
} elseif (!ctype_digit($telefone) || strlen($telefone) < 10 || strlen($telefone) > 11) {
    responder(false, "O telefone deve ter 10 ou 11 numeros, com DDD.");
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responder(false, "Email invalido.");
} else {
$comando = $conexao->prepare("SELECT id_fornecedor FROM fornecedores WHERE cnpj = :cnpj");
$comando->execute(['cnpj' => $cnpj]);

if ($comando->fetch()) {
    responder(false, "Ja existe um fornecedor com esse CNPJ.");
}

$sql = "INSERT INTO fornecedores
(nome, cnpj, telefone, email, endereco)
VALUES
(:nome, :cnpj, :telefone, :email, :endereco)";

$comando = $conexao->prepare($sql);

$comando->bindParam(':nome', $nome);
$comando->bindParam(':cnpj', $cnpj);
$comando->bindParam(':telefone', $telefone);
$comando->bindParam(':email', $email);
$comando->bindParam(':endereco', $endereco);

$comando->execute();
responder(true, "Fornecedor aprovado para cadastro. Codigo: " . $conexao->lastInsertId());
}
