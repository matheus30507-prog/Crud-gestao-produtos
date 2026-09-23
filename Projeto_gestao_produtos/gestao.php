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
    <div class="container my-4">
        
        <ul class="nav nav-tabs fw-bold" id="sistemaTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="cadastros-tab" data-bs-toggle="tab" data-bs-target="#cadastros" type="button" role="tab">
                    1. Cadastros
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ajax-tab" data-bs-toggle="tab" data-bs-target="#ajax" type="button" role="tab">
                    2. Área AJAX
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="selecao-tab" data-bs-toggle="tab" data-bs-target="#selecao" type="button" role="tab">
                    3. Seleção
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cesta-tab" data-bs-toggle="tab" data-bs-target="#cesta" type="button" role="tab">
                    4. Cesta de Compras
                </button>
            </li>
        </ul>

        <div class="tab-content bg-white p-4 border border-top-0 rounded-bottom shadow-sm">
            
            <div class="tab-pane fade show active" id="cadastros" role="tabpanel">
                <h4 class="mb-3">Área de Cadastros</h4>
            </div>

            <div class="tab-pane fade" id="ajax" role="tabpanel">
                <h4 class="mb-3">Área AJAX</h4>
            </div>

            <div class="tab-pane fade" id="selecao" role="tabpanel">
                <h4 class="mb-3">Seleção de Produtos</h4>
            </div>

            <div class="tab-pane fade" id="cesta" role="tabpanel">
                <h4 class="mb-3">Resumo da Cesta</h4>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>