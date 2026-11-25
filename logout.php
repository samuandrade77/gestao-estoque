<?php
require_once "resposta.php";

session_start();
$_SESSION = [];
session_destroy();

responder(true, "Sessao encerrada.");
