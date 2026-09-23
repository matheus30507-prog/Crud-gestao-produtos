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
        ) "
    );

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
            header("Location: gestao.php");
            exit;
            echo "Usuário ou senha inválidos!";
        }
    }

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

 ?>