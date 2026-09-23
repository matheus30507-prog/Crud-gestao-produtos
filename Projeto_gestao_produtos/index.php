<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Gestão de Produtos - Login</title>
</head>
<body>

    <div class="cont">
        <form action="validacao.php" method="POST">
            <h1>Gestão de Produtos</h1>
            
            <?php if (isset($_SESSION["erro"])): ?>
                <p style="color: red; font-weight: bold;">
                    <?php 
                        echo $_SESSION["erro"]; 
                        unset($_SESSION["erro"]); 
                    ?>
                </p>
            <?php endif; ?>

            <br>
            <div>
                <label for="email"></label>
                <input type="email" id="email" name="email" placeholder="Digite seu Email..." required>
            </div>
            <br>
            <div>
                <label for="senha"></label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha..." required>        
                <br>
            </div>
            <br>
            <a href="cadastro.php" id="botao"><em>Criar conta</em></a>
            <br><br>
            <button type="submit">Entrar</button>
        </form>
    </div>

</body>
</html>