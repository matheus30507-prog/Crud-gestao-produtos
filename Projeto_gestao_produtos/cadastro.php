<?php
session_start();
require "conexao.php";

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $email = trim($_POST["email"] ?? "");
        $senha = $_POST["senha"] ?? "";

        if (empty($email) || empty($senha)) {
            throw new Exception("Preencha todos os campos.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Digite um e-mail válido.");
        }

        if (strlen($senha) < 6) {
            throw new Exception("A senha deve ter pelo menos 6 caracteres.");
        }

        $usuario = new Usuario($email);

        $usuario->cadastrar($conexao, $senha);

        $stmt = $conexao->prepare(
            "SELECT id, email FROM usuarios WHERE email = ?"
        );
        $stmt->execute([$email]);

        $usuarioCadastrado = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuarioCadastrado) {
            throw new Exception("Usuário cadastrado, mas não foi possível iniciar a sessão.");
        }

        $_SESSION["usuario_id"] = $usuarioCadastrado["id"];
        $_SESSION["usuario_email"] = $usuarioCadastrado["email"];

        header("Location: gestao.php");
        exit;

    } catch (PDOException $e) {

        if ($e->getCode() == "23000") {
            $erro = "Este e-mail já está cadastrado.";
        } else {
            $erro = "Erro no banco de dados: " . $e->getMessage();
        }

    } catch (Exception $e) {
        $erro = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h1>Cadastro</h1>

    <?php if (!empty($erro)): ?>
        <p class="erro">
            <?= htmlspecialchars($erro) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="">

        <label for="email">E-mail:</label>

        <input
            type="email"
            id="email"
            name="email"
            required
            value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
        >

        <label for="senha">Senha:</label>

        <input
            type="password"
            id="senha"
            name="senha"
            required
            minlength="6"
        >

        <button type="submit">Cadastrar</button>

    </form>

        <a href="index.php">Voltar para o login</a>

</div>

</body>
</html>
