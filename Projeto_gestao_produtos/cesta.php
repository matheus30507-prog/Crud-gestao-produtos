<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit;
}
require "conexao.php";

$usuario = new Usuario($_SESSION["usuario_email"], (int) $_SESSION["usuario_id"]);
$cesta = new Cesta($usuario);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cesta->remover($conexao, (int) ($_POST["produto_id"] ?? 0));
    $_SESSION["msg"] = ["warning", "Produto removido da cesta."];
    header("Location: cesta.php");
    exit;
}

$cesta->carregar($conexao);
$itens = $cesta->getItens();
$quantidade = $cesta->quantidade();
$total = $cesta->total();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Minha cesta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="gestao.php">← Voltar ao menu</a>
        <div class="d-flex align-items-center text-white">
            <span class="me-3">Olá, <strong><?php echo htmlspecialchars($_SESSION["usuario_email"]); ?></strong></span>
            <a href="saida.php" class="btn btn-outline-danger btn-sm">Sair</a>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">

    <?php if (isset($_SESSION["msg"])): ?>
        <div class="alert alert-<?php echo $_SESSION["msg"][0]; ?> alert-dismissible fade show">
            <?php echo htmlspecialchars($_SESSION["msg"][1]); unset($_SESSION["msg"]); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <h2 class="mb-4">Minha cesta</h2>

    <?php if (empty($itens)): ?>
        <div class="alert alert-info">
            Sua cesta está vazia. <a href="produto.php">Selecionar produtos</a>.
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="table-responsive">
                    <table class="table table-striped bg-white align-middle">
                        <thead>
                            <tr><th>Produto</th><th>Fornecedor</th><th>Preço</th><th style="width:1%"></th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($itens as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p->getNome()); ?></td>
                                <td><?php echo htmlspecialchars($p->getFornecedor()->getNome()); ?></td>
                                <td>R$ <?php echo number_format($p->getPreco(), 2, ",", "."); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="produto_id" value="<?php echo $p->getId(); ?>">
                                        <button class="btn btn-sm btn-outline-danger">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">Resumo</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Produtos selecionados</span>
                            <strong><?php echo $quantidade; ?></strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Valor total</span>
                            <strong>R$ <?php echo number_format($total, 2, ",", "."); ?></strong>
                        </li>
                    </ul>
                    <div class="card-body">
                        <a href="produto.php" class="btn btn-outline-primary w-100">Adicionar mais produtos</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>