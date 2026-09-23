<?php 
session_start();

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
            FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS cesta_itens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            produto_id INT NOT NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
            UNIQUE KEY usuario_produto_unico (usuario_id, produto_id)
        );
    ");

    if ($_SERVER["REQUEST_METHOD"] == "POST") { 
        $email = $_POST["email"]; 
        $senha = $_POST["senha"];

        $senhaHash = hash("sha256", $senha);

        $sql = "SELECT id FROM usuarios WHERE email = ? AND senha = ?"; 
        $stmt = $conexao->prepare($sql); 
        $stmt->execute([$email, $senhaHash]);

        if($stmt->rowCount() > 0){
            $usuarioLogado = $stmt->fetch(PDO::FETCH_ASSOC);

            $_SESSION["usuario_id"] = $usuarioLogado["id"];
            $_SESSION["usuario_email"] = $usuarioLogado["email"];

            header("Location: gestao.php");
            exit;
        }
        else{
            $_SESSION["erro"] = "E-mail ou senha incorretos!";
            header("Location: index.php");
            exit;
        }
    }

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

 ?>