<?php
$servidor = "localhost";
$usuario = "root";
$senhaBanco = "";
try {

    $conexao = new PDO("mysql:host=$servidor", $usuario, $senhaBanco);
    $conexao->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    $conexao->exec("CREATE DATABASE IF NOT EXISTS gestao_produtos");
    $conexao->exec("USE gestao_produtos");

    $conexao->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(100) NOT NULL UNIQUE,
            senha VARCHAR(255) NOT NULL
        );

        CREATE TABLE IF NOT EXISTS fornecedores (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            cnpj VARCHAR(20) NOT NULL UNIQUE,
            telefone VARCHAR(20)
        );



        CREATE TABLE IF NOT EXISTS produtos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            preco DECIMAL(10,2) NOT NULL,
            fornecedor_id INT NOT NULL,
            FOREIGN KEY (fornecedor_id)
            REFERENCES fornecedores(id)
            ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS cesta_itens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            produto_id INT NOT NULL,
            FOREIGN KEY (usuario_id)
            REFERENCES usuarios(id)
            ON DELETE CASCADE,
            FOREIGN KEY (produto_id)
            REFERENCES produtos(id)
            ON DELETE CASCADE,
            UNIQUE KEY usuario_produto_unico
            (usuario_id, produto_id)
        );
    ");

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

?>