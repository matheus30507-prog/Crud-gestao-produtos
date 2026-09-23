<?php
session_start();

require "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    $senhaHash = hash("sha256", $senha);

    $sql = "SELECT * FROM usuarios WHERE email = ? AND senha = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->execute([$email, $senhaHash]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $_SESSION["usuario_id"] = $usuario["id"];
        $_SESSION["usuario_email"] = $usuario["email"];

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