<?php
require_once "auth.php";

$codigo_pedido = $_POST['codigo_pedido'] ?? "";
$codigo_pedido = trim($codigo_pedido);

$codigo_produto = $_POST['codigo_produto'] ?? "";
$codigo_produto = trim($codigo_produto);

$data = $_POST['data'] ?? "";
$data = trim($data);

$quantidade_separada = $_POST['quantidade_separada'] ?? "";
$quantidade_separada = trim($quantidade_separada);

$observacao = $_POST['observacao'] ?? "";
$observacao = trim($observacao);

if(
    $codigo_pedido == "" ||
    $codigo_produto == "" ||
    !data_valida($data) ||
    $observacao == "" ||
    !ctype_digit($quantidade_separada) ||
    $quantidade_separada <= 0
){
  responder(false, "Dados invalidos para separacao");
}elseif($data > date('Y-m-d')){
  responder(false, "A data da separacao nao pode ser futura.");
}else{
  // Transacao: ou grava tudo ou nao grava nada
  $conexao->beginTransaction();

  $sql = "SELECT
            ip.quantidade_solicitada,
            ip.quantidade_separada,
            p.quantidade_estoque,
            ip.id_item,
            pe.situacao
        FROM itens_pedido ip
        INNER JOIN produtos p
        ON ip.id_produto = p.id_produto
        INNER JOIN pedidos pe
        ON ip.id_pedido = pe.id_pedido
        WHERE ip.id_pedido = :codigo_pedido
        AND ip.id_produto = :codigo_produto
        FOR UPDATE";

$comando = $conexao->prepare($sql);

$comando->execute([
    'codigo_pedido' => $codigo_pedido,
    'codigo_produto' => $codigo_produto
]);

$item = $comando->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    responder(false, "Produto nao encontrado nesse pedido.");
} else {
    $quantidade_restante =
        $item['quantidade_solicitada'] -
        $item['quantidade_separada'];

    if ($item['situacao'] == "Cancelado") {
        responder(false, "Esse pedido esta cancelado.");
    } elseif ($quantidade_separada > $quantidade_restante) {
        responder(false, "Quantidade superior ao restante do pedido.");
    } elseif ($quantidade_separada > $item['quantidade_estoque']) {
        responder(false, "Estoque insuficiente.");
    } else {
        // 1) Atualiza a quantidade separada do item
        $comando = $conexao->prepare(
            "UPDATE itens_pedido
             SET quantidade_separada = quantidade_separada + :quantidade
             WHERE id_item = :id_item"
        );
        $comando->execute([
            'quantidade' => $quantidade_separada,
            'id_item' => $item['id_item']
        ]);

        // 2) Baixa o estoque do produto
        $comando = $conexao->prepare(
            "UPDATE produtos
             SET quantidade_estoque = quantidade_estoque - :quantidade
             WHERE id_produto = :id_produto"
        );
        $comando->execute([
            'quantidade' => $quantidade_separada,
            'id_produto' => $codigo_produto
        ]);

        // 3) Registra a saida, com o usuario guardado na sessao
        $comando = $conexao->prepare(
            "INSERT INTO movimentacoes_estoque
             (id_produto, id_usuario, id_pedido, tipo, quantidade, data_movimentacao, observacao)
             VALUES
             (:id_produto, :id_usuario, :id_pedido, 'Saída', :quantidade, :data_movimentacao, :observacao)"
        );
        $comando->execute([
            'id_produto' => $codigo_produto,
            'id_usuario' => $usuario_logado['id_usuario'],
            'id_pedido' => $codigo_pedido,
            'quantidade' => $quantidade_separada,
            'data_movimentacao' => $data . " " . date('H:i:s'),
            'observacao' => $observacao
        ]);

        // 4) Atualiza a situacao do pedido
        $comando = $conexao->prepare(
            "SELECT COUNT(*) FROM itens_pedido
             WHERE id_pedido = :id_pedido
             AND quantidade_separada < quantidade_solicitada"
        );
        $comando->execute(['id_pedido' => $codigo_pedido]);
        $situacao = $comando->fetchColumn() == 0 ? "Concluído" : "Em separação";

        $comando = $conexao->prepare(
            "UPDATE pedidos SET situacao = :situacao WHERE id_pedido = :id_pedido"
        );
        $comando->execute(['situacao' => $situacao, 'id_pedido' => $codigo_pedido]);

        $conexao->commit();

        responder(true, "Separacao registrada com sucesso! Situacao do pedido: " . $situacao);
    }
}

}
