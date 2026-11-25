<?php
require_once "resposta.php";

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
    // Logica adicional: mostra o CNPJ no formato oficial
    $cnpj_formatado = substr($cnpj, 0, 2) . "." . substr($cnpj, 2, 3) . "." .
        substr($cnpj, 5, 3) . "/" . substr($cnpj, 8, 4) . "-" . substr($cnpj, 12, 2);

    responder(true, "Fornecedor aprovado para cadastro. CNPJ: " . $cnpj_formatado);
}
