<?php
// Cadastra (sem codigo) ou altera (com codigo) um usuario
require_once "auth.php";

$id_usuario = trim($_POST['id_usuario'] ?? "");
$nome = trim($_POST['nome'] ?? "");
$email = trim($_POST['email'] ?? "");
$senha = $_POST['senha'] ?? "";
$confirmar_senha = $_POST['confirmar_senha'] ?? "";
$perfil = trim($_POST['perfil'] ?? "");

$novo = $id_usuario == "";

if ($nome == "" || $email == "" || $perfil == "") {
    responder(false, "Preencha nome, email e perfil.");
}
if (!$novo && !ctype_digit($id_usuario)) {
    responder(false, "Codigo de usuario invalido.");
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    responder(false, "Email invalido.");
}
if ($perfil != "Administrador" && $perfil != "Estoquista") {
    responder(false, "Perfil invalido.");
}
if ($novo && ($senha == "" || $confirmar_senha == "")) {
    responder(false, "Informe a senha e a confirmacao.");
}
if ($senha != "" && strlen($senha) < 6) {
    responder(false, "A senha deve ter pelo menos 6 caracteres.");
}
if ($senha !== $confirmar_senha) {
    responder(false, "As senhas nao conferem.");
}

// Nao permite dois usuarios com o mesmo email
$comando = $conexao->prepare(
    "SELECT id_usuario FROM usuarios WHERE email = :email AND id_usuario <> :id"
);
$comando->execute(['email' => $email, 'id' => $novo ? 0 : $id_usuario]);

if ($comando->fetch()) {
    responder(false, "Ja existe um usuario com esse email.");
}

$parametros = ['nome' => $nome, 'email' => $email, 'perfil' => $perfil];

if ($novo) {
    $sql = "INSERT INTO usuarios (nome, email, senha, perfil)
            VALUES (:nome, :email, :senha, :perfil)";
    $parametros['senha'] = password_hash($senha, PASSWORD_DEFAULT);
    $mensagem = "Usuario cadastrado com sucesso!";
} else {
    $comando = $conexao->prepare("SELECT id_usuario FROM usuarios WHERE id_usuario = :id");
    $comando->execute(['id' => $id_usuario]);

    if (!$comando->fetch()) {
        responder(false, "Usuario nao encontrado.");
    }

    // Na alteracao, senha em branco mantem a senha atual
    $troca_senha = "";
    if ($senha != "") {
        $troca_senha = ", senha = :senha";
        $parametros['senha'] = password_hash($senha, PASSWORD_DEFAULT);
    }
    $sql = "UPDATE usuarios SET nome = :nome, email = :email, perfil = :perfil
            $troca_senha WHERE id_usuario = :id";
    $parametros['id'] = $id_usuario;
    $mensagem = "Usuario alterado com sucesso!";
}

$comando = $conexao->prepare($sql);
$comando->execute($parametros);

responder(true, $mensagem);
