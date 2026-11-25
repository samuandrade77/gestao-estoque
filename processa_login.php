<?php
require_once "resposta.php";
require_once "conexao.php";

session_start();

$email = trim($_POST['email'] ?? "");
$senha = $_POST['senha'] ?? "";

if ($email == "" || $senha == "") {
    responder(false, "Informe o email e a senha.");
}

$comando = $conexao->prepare("SELECT * FROM usuarios WHERE email = :email");
$comando->execute(['email' => $email]);
$usuario = $comando->fetch(PDO::FETCH_ASSOC);

if (!$usuario || !password_verify($senha, $usuario['senha'])) {
    responder(false, "Email ou senha incorretos.");
}

// Gera um novo identificador de sessao apos o login
session_regenerate_id(true);

// A senha nunca e guardada na sessao
$_SESSION['usuario'] = [
    'id_usuario' => $usuario['id_usuario'],
    'nome' => $usuario['nome'],
    'perfil' => $usuario['perfil']
];

responder(true, "Login realizado com sucesso!");
