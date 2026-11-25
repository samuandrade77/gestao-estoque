<?php
require_once "auth.php";

$comando = $conexao->query(
    "SELECT id_usuario, nome, email, perfil FROM usuarios ORDER BY nome"
);

responder(true, "", $comando->fetchAll(PDO::FETCH_ASSOC));
