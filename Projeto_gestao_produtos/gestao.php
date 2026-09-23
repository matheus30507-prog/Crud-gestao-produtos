<?php
session_start();

if (!isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestão produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Gestão de Produtos</a>
            <div class="d-flex align-items-center text-white">
                <span class="me-3">Olá, <strong><?php echo htmlspecialchars($_SESSION["usuario_email"]); ?></strong></span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Sair</a>
            </div>
        </div>
    </nav>
    <div class="container mt-5">

    <h2 class="text-center mb-4">
        Sistema de Gestão de Produtos
    </h2>

    <div class="row g-3">

        <div class="col-md-6">
            <a href="fornecedor.php" class="btn btn-primary w-100 p-3">
                Cadastros
            </a>
        </div>

        <div class="col-md-6">
            <a href="ajax.php" class="btn btn-warning w-100 p-3">
                Atualizar dados
            </a>
        </div>

        <div class="col-md-6">
            <a href="produto.php" class="btn btn-success w-100 p-3">
                Selecionar produtos
            </a>
        </div>

        <div class="col-md-6">
            <a href="cesta.php" class="btn btn-info w-100 p-3">
                Minha cesta
            </a>
        </div>

    </div>

</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>