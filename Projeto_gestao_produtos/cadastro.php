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

	if ($_SERVER["REQUEST_METHOD"] == "POST") { 
		$email = $_POST["email"]; 
		$senha = $_POST["senha"];

		$senhaHash = hash("sha256", $senha);

		$sql = "SELECT id FROM usuarios WHERE email = ?"; 
		$stmt = $conexao->prepare($sql); 
		$stmt->execute([$email]);

		if ($stmt->rowCount() > 0) {
			echo "Usuário já cadastrado.";
		}
		else {
			$sql = "INSERT INTO usuarios (email, senha) VALUES (?, ?)";
			$stmt = $conexao->prepare($sql);
			$stmt->execute([$email, $senhaHash]);

			echo "Usuário cadastrado com sucesso!";

			$usuarioLogado = $stmt->fetch(PDO::FETCH_ASSOC);

			$_SESSION["usuario_id"] = $usuarioLogado["id"];
            $_SESSION["usuario_email"] = $usuarioLogado["email"];

            header("Location: gestao.php");
            exit;
		}
	}

}

catch (PDOException $e) { 
	echo "Erro: " . $e->getMessage(); 
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Cadastrar usuário</title>
</head>
<body>
	<div class="cont">
		<form method="POST">
			<h1>Cadastro Usuário</h1>
			<br>
			<div>
			<label for="email"></label>
			<input type="email" id="email" name="email" placeholder="Digite seu Email...">
			</div>
			<br>
			<div>
			<label for="senha"></label>
			<input type="password" id="senha" name="senha" placeholder="Digite sua senha...">		
			<br>
			</div>
			<br>
			<button type="submit">Cadastrar</button>
		</form>
	</div>
</body>
</html>