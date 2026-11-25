<?php
require_once "resposta.php";

$dsn=
"mysql:host=localhost;port=3306;dbname=gerenciamento_estoque;charset=utf8mb4";

// Usuario criado pelo proprio script banco/gerenciamento_estoque.sql
$usuario = "estoque_app";
$senha = "Estoque@123";

try{
   $conexao = new PDO($dsn, $usuario, $senha);

  $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  }catch (PDOException $erro){
  error_log($erro->getMessage());
  responder(false, "Erro ao conectar com o banco de dados.", [], 500);
}
