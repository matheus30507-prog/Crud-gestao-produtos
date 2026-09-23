<?php session_start();
	require "conexao.php"; 
	if ($_SERVER["REQUEST_METHOD"] == "POST") { 
		$email = $_POST["email"]; 
		$senha = $_POST["senha"]; 
		$senhaHash = hash("sha256", $senha); 

		try { 
			$sql = "INSERT INTO usuarios (email, senha) VALUES (?, ?)"; 
			$stmt = $conexao->prepare($sql); 
			$stmt->execute([ $email, $senhaHash ]); 
			header("Location: gestao.php"); 
			exit; 
		} catch (PDOException $e) { 
			$erro = "E-mail já cadastrado."; 
		} 
	} 
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Cadastrar usuário</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<div class="container">
		<form method="POST">
			<h1>Cadastro Usuário</h1>
			
			<?php if (isset($erro)): ?>
				<p style="color: red; font-weight: bold; text-align: center; margin-bottom: 15px;">
					<?php echo $erro; ?>
				</p>
			<?php endif; ?>

			<div class="input-group">
				<input type="email" id="email" name="email" placeholder="Digite seu Email..." required>
			</div>

			<div class="input-group">
				<input type="password" id="senha" name="senha" placeholder="Digite sua senha..." required>		
			</div>

			<button type="submit">Cadastrar</button>
			<a href="index.php">Já tem uma conta? Voltar ao login</a>
		</form>
	</div>
</body>
</html>