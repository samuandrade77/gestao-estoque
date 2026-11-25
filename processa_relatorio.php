<?php
require_once "auth.php";

$data_inicio = $_GET['data_inicio'] ?? "";
$data_inicio = trim($data_inicio);

$data_final = $_GET['data_final'] ?? "";
$data_final = trim($data_final);

$codigo_produto = $_GET['codigo_produto'] ?? "";
$codigo_produto = trim($codigo_produto);

$codigo_fornecedor = $_GET['codigo_fornecedor'] ?? "";
$codigo_fornecedor = trim($codigo_fornecedor);

$tipo_movimentacao = $_GET['tipo_movimentacao'] ?? "";
$tipo_movimentacao = trim($tipo_movimentacao);


if ( $data_inicio =="" || $data_final =="") {
   responder(false, "Informe as datas.");
}elseif($data_inicio > $data_final) {
  responder(false, "Data inicial nao pode ser posterior a data final");
}elseif(
    ($codigo_produto != "" &&(!is_numeric($codigo_produto) || $codigo_produto <= 0 )) ||
    ($codigo_fornecedor != "" &&(!is_numeric($codigo_fornecedor) || $codigo_fornecedor <= 0))
){
    responder(false, "Codigo do produto ou fornecedor invalido!");
}elseif(
    $tipo_movimentacao != "" &&
    $tipo_movimentacao != "Entrada" &&
    $tipo_movimentacao != "Saida"
){
 responder(false, "Tipo de Movimentacao invalido.");
}else{
    $filtros = [];
    $parametros = [];

    $filtros[] = "m.data_movimentacao >= :data_inicio";
    $filtros[] = "m.data_movimentacao <= :data_final";

    $parametros['data_inicio'] = $data_inicio . " 00:00:00";
    $parametros['data_final'] = $data_final . " 23:59:59";

    if ($codigo_produto != "") {
        $filtros[] = "m.id_produto = :codigo_produto";
        $parametros['codigo_produto'] = $codigo_produto;
    }

    if ($codigo_fornecedor != "") {
        $filtros[] = "p.id_fornecedor = :codigo_fornecedor";
        $parametros['codigo_fornecedor'] = $codigo_fornecedor;
    }

    if ($tipo_movimentacao != "") {
        $filtros[] = "m.tipo = :tipo_movimentacao";
        // No banco o valor e gravado com acento
        $parametros['tipo_movimentacao'] =
            $tipo_movimentacao == "Saida" ? "Saída" : $tipo_movimentacao;
    }

    $sql = "SELECT
                m.id_movimentacao,
                m.id_produto,
                p.nome AS nome_produto,
                p.id_fornecedor,
                m.tipo,
                m.quantidade,
                m.data_movimentacao,
                m.observacao
            FROM movimentacoes_estoque m
            INNER JOIN produtos p
            ON m.id_produto = p.id_produto
            WHERE " . implode(" AND ", $filtros);

    $comando = $conexao->prepare($sql);

    $comando->execute($parametros);

    $movimentacoes = $comando->fetchAll(PDO::FETCH_ASSOC);

    if (count($movimentacoes) > 0) {

        $resultado = [];

        foreach ($movimentacoes as $movimentacao) {

            $resultado[] = [
                "Codigo da movimentacao" => $movimentacao['id_movimentacao'],
                "Produto" => $movimentacao['nome_produto'],
                "Codigo do fornecedor" => $movimentacao['id_fornecedor'],
                "Tipo" => $movimentacao['tipo'],
                "Quantidade" => $movimentacao['quantidade'],
                "Data" => $movimentacao['data_movimentacao'],
                "Observacao" => $movimentacao['observacao']
            ];
        }

        responder(true, "Movimentacoes encontradas: " . count($resultado), $resultado);

    } else {

        responder(true, "Nenhuma movimentacao encontrada para os criterios informados.");
    }
}
