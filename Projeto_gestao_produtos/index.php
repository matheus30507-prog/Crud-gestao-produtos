<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <title>Gestão de Produtos - Login</title>
</head>
<body>

    <div class="container" style="border: 1px solid #e0e0e0;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
        <form action="validacao.php" method="POST">
            <h1>Gestão de Produtos</h1>
            
            <?php if (isset($_SESSION["erro"])): ?>
                <p style="color: red; font-weight: bold; text-align: center; margin-bottom: 15px;">
                    <?php 
                        echo $_SESSION["erro"]; 
                        unset($_SESSION["erro"]); 
                    ?>
                </p>
            <?php endif; ?>

            <div class="input-group">
                <input type="email" id="email" name="email" placeholder="Digite seu Email..." required>
            </div>

            <div class="input-group">
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha..." required>        
            </div>

            <button type="submit">Entrar</button>

            <a href="cadastro.php">Criar conta</a>
        </form>
    </div>

</body>
</html>