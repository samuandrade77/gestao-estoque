<?php
date_default_timezone_set('America/Sao_Paulo');

// Todas as respostas do servidor saem em JSON. O HTML fica somente nos arquivos .html.
function responder($sucesso, $mensagem, $dados = [], $codigo = 200)
{
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(
        ['sucesso' => $sucesso, 'mensagem' => $mensagem, 'dados' => $dados],
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

// Qualquer erro inesperado tambem volta em JSON
set_exception_handler(function ($erro) {
    error_log($erro->getMessage());
    responder(false, "Erro interno no servidor.", [], 500);
});

function data_valida($data)
{
    $objeto = DateTime::createFromFormat('Y-m-d', $data);
    return $objeto && $objeto->format('Y-m-d') === $data;
}
