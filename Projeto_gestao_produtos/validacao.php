<?php 
$servidor = "localhost"; 
$usuario = "root"; 
$senhaBanco = "";

try {
    $conexao = new PDO("mysql:host=$servidor", $usuario, $senhaBanco);

    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conexao->exec("CREATE DATABASE IF NOT EXISTS gestao_produtos");

    $conexao->exec("USE gestao_produtos");

    $conexao->exec(
        " CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(100) NOT NULL UNIQUE, 
            senha VARCHAR(255) NOT NULL 
        ) "
    );

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

 ?>