<?php
// Valida os dados do usuario
require_once "resposta.php";

$nome = trim($_POST['nome'] ?? "");
$email = trim($_POST['email'] ?? "");
$senha = $_POST['senha'] ?? "";
$confirmar_senha = $_POST['confirmar_senha'] ?? "";
$perfil = trim($_POST['perfil'] ?? "");

if ($nome == "" || $email == "" || $perfil == "") {
    responder(false, "Preencha nome, email e perfil.");
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responder(false, "Email invalido.");
}
if ($perfil != "Administrador" && $perfil != "Estoquista") {
    responder(false, "Perfil invalido.");
}
if ($senha == "" || $confirmar_senha == "") {
    responder(false, "Informe a senha e a confirmacao.");
}
if (strlen($senha) < 6) {
    responder(false, "A senha deve ter pelo menos 6 caracteres.");
}
if ($senha !== $confirmar_senha) {
    responder(false, "As senhas nao conferem.");
}

// Logica adicional: informa o nivel de acesso do perfil escolhido
$acesso = $perfil == "Administrador" ? "acesso total" : "acesso ao estoque";

responder(true, "Dados do usuario validados! Perfil com " . $acesso . ".");
