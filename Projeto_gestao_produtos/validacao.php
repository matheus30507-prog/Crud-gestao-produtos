<?php
session_start();

require "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = Usuario::autenticar($conexao, $_POST["email"], $_POST["senha"]);

    if ($usuario) {
        $_SESSION["usuario_id"] = $usuario->getId();
        $_SESSION["usuario_email"] = $usuario->getEmail();

        header("Location: gestao.php");
        exit;
    }
    else {
        $_SESSION["erro"] = "E-mail ou senha incorretos.";
        header("Location: index.php");
        exit;
    }
}

?>