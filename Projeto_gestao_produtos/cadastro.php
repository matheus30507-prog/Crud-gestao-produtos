<?php session_start();
	require "conexao.php";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$usuario = new Usuario($_POST["email"]);

		try {
			$usuario->cadastrar($conexao, $_POST["senha"]);

			// já deixa o usuário logado após o cadastro
			$_SESSION["usuario_id"] = $usuario->getId();
			$_SESSION["usuario_email"] = $usuario->getEmail();

			header("Location: gestao.php");
			exit;
		} catch (PDOException $e) {
			if ($e->getCode() == 23000) {
        		$erro = "E-mail já cadastrado.";
    		}
    		else {
        		$erro = "Erro ao cadastrar. Tente novamente.";
    		}
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