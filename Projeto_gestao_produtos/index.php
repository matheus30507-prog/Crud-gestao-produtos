<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title></title>
</head>
<body>
	<div id="cont">
		<form action="validacao.php" method="POST">
			<h1>Gestão de Produtos</h1>
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
			<a href="cadastro.php" id="botao"><em>Criar conta</em></a>
			<br>
			<button type="submit">Entrar</button>
		</form>
	</div>
</body>
</html>