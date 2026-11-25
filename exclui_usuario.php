<?php
require_once "auth.php";

$id_usuario = trim($_POST['id_usuario'] ?? "");

if (!ctype_digit($id_usuario)) {
    responder(false, "Codigo de usuario invalido.");
}
if ($id_usuario == $usuario_logado['id_usuario']) {
    responder(false, "Voce nao pode excluir o proprio usuario logado.");
}

try {
    $comando = $conexao->prepare("DELETE FROM usuarios WHERE id_usuario = :id");
    $comando->execute(['id' => $id_usuario]);
} catch (PDOException $erro) {
    // 23000 = o registro esta ligado a outra tabela (chave estrangeira)
    if ($erro->getCode() == "23000") {
        responder(false, "Esse usuario possui pedidos ou movimentacoes e nao pode ser excluido.");
    }
    throw $erro;
}

if ($comando->rowCount() == 0) {
    responder(false, "Usuario nao encontrado.");
}

responder(true, "Usuario excluido com sucesso!");
