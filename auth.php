<?php
// Incluido no inicio de todo PHP protegido: so continua se houver usuario na sessao.
require_once "resposta.php";
require_once "conexao.php";

session_start();

if (!isset($_SESSION['usuario'])) {
    responder(false, "Faca o login para acessar o sistema.", [], 401);
}

// Confere no banco se o usuario da sessao continua cadastrado
$consulta = $conexao->prepare(
    "SELECT id_usuario, nome, perfil FROM usuarios WHERE id_usuario = :id"
);
$consulta->execute(['id' => $_SESSION['usuario']['id_usuario']]);
$usuario_logado = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$usuario_logado) {
    session_destroy();
    responder(false, "Usuario nao encontrado. Faca o login novamente.", [], 401);
}

$_SESSION['usuario'] = $usuario_logado;
