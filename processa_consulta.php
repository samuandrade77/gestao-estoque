<?php
require_once "auth.php";

$id_produto = $_GET['id_produto'] ?? "";
$id_produto = trim($id_produto);

$descricao = $_GET['descricao'] ?? "";
$descricao = trim($descricao);

$nome = $_GET['nome'] ?? "";
$nome = trim($nome);

$id_fornecedor = $_GET['id_fornecedor'] ?? "";
$id_fornecedor = trim($id_fornecedor);

$preco = $_GET['preco'] ?? "";
$preco = trim($preco);

$quantidade_estoque = $_GET['quantidade_estoque'] ?? "";
$quantidade_estoque = trim($quantidade_estoque);

if(
    $id_produto == "" && 
    $descricao == "" &&
    $nome == "" &&
    $id_fornecedor == "" &&
    $preco == "" &&
    $quantidade_estoque == ""
){
  responder(false, "Informe pelo menos um criterio para consulta.");
}else{
  $filtros = [];
  $parametros =[];

  if($id_produto != ""){
    $filtros[] = "id_produto LIKE :id_produto";
    $parametros['id_produto'] = "%$id_produto%";
  }
  if($nome != ""){
    $filtros[] = "nome LIKE :nome";
    $parametros['nome'] = "%$nome%";
  }
  if($id_fornecedor != ""){
    $filtros[] = "id_fornecedor LIKE :id_fornecedor";
    $parametros['id_fornecedor'] = "%$id_fornecedor%";
  }
  if($descricao != ""){
    $filtros[] = "descricao LIKE :descricao";
    $parametros['descricao'] = "%$descricao%";
  }
  if($quantidade_estoque != ""){
    $filtros[] = "quantidade_estoque LIKE :quantidade_estoque";
    $parametros['quantidade_estoque'] = "%$quantidade_estoque%";
  }

  if($preco != ""){
    $filtros[] = "preco = :preco";
    $parametros['preco'] = $preco;
  }

$sql = "SELECT * FROM produtos WHERE " . implode(" AND ", $filtros);
$comando = $conexao->prepare($sql);
$comando->execute($parametros);
$produtos = $comando->fetchAll(PDO::FETCH_ASSOC);

if(count($produtos) > 0){
  $resultado = [];
  foreach($produtos as $produto){
    $resultado[] = [
      "Produto" => $produto['nome'],
      "descricao" => $produto['descricao'],
      "preco" => $produto['preco'],
      "estoque" => $produto['quantidade_estoque'],
      "fornecedor" => $produto['id_fornecedor']
    ];
  }
  responder(true, "Produtos encontrados: " . count($resultado), $resultado);
}else{
  responder(true, "Nenhum produto encontrado.");
}

}
