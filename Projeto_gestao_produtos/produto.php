<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit;
}
require "conexao.php";

$usuario = new Usuario($_SESSION["usuario_email"], (int) $_SESSION["usuario_id"]);
$cesta = new Cesta($usuario);
$minimo = 2;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selecionados = $_POST["produtos"] ?? [];
    $selecionados = array_unique(array_filter(array_map("intval", (array) $selecionados)));

    if (count($selecionados) < $minimo) {
        $_SESSION["msg"] = ["danger", "Selecione pelo menos $minimo produtos."];
        header("Location: produto.php");
        exit;
    }

    $adicionados = $cesta->adicionar($conexao, $selecionados);

    $_SESSION["msg"] = ["success", "$adicionados produto(s) adicionado(s) à cesta!"];
    header("Location: cesta.php");
    exit;
}

$produtos = Produto::listar($conexao);

$cesta->carregar($conexao);
$naCesta = $cesta->idsProdutos();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Selecionar produtos</title>
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

    <h2 class="mb-1">Selecionar produtos</h2>
    <p class="text-muted">Marque pelo menos <?php echo $minimo; ?> produtos para adicionar à cesta (1 unidade de cada).</p>

    <?php if (empty($produtos)): ?>
        <div class="alert alert-info">Nenhum produto cadastrado. <a href="fornecedor.php">Cadastrar agora</a>.</div>
    <?php else: ?>
        <form method="POST" id="formSelecao">
            <div class="table-responsive">
                <table class="table table-hover bg-white align-middle">
                    <thead>
                        <tr><th style="width:1%"></th><th>Produto</th><th>Fornecedor</th><th>Preço</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($produtos as $p):
                        $jaNaCesta = in_array($p->getId(), $naCesta); ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input chk" name="produtos[]"
                                       value="<?php echo $p->getId(); ?>"
                                       <?php echo $jaNaCesta ? "checked disabled" : ""; ?>>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($p->getNome()); ?>
                                <?php if ($jaNaCesta): ?><span class="badge bg-secondary ms-1">Já na cesta</span><?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($p->getFornecedor()->getNome()); ?></td>
                            <td>R$ <?php echo number_format($p->getPreco(), 2, ",", "."); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div id="alertaValidacao" class="alert alert-danger d-none"></div>

            <div class="d-flex align-items-center gap-3">
                <button type="submit" class="btn btn-success" id="btnAdicionar" disabled>Adicionar à cesta</button>
                <span class="text-muted">Selecionados: <strong id="contador">0</strong></span>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
    const MINIMO = <?php echo $minimo; ?>;
    const btn = document.getElementById("btnAdicionar");
    const contador = document.getElementById("contador");
    const alerta = document.getElementById("alertaValidacao");

    function selecionados() {
        return document.querySelectorAll(".chk:not(:disabled):checked").length;
    }

    document.querySelectorAll(".chk:not(:disabled)").forEach(c => {
        c.addEventListener("change", () => {
            const n = selecionados();
            contador.textContent = n;
            btn.disabled = n < MINIMO;
            if (n >= MINIMO) alerta.classList.add("d-none");
        });
    });

    const form = document.getElementById("formSelecao");
    if (form) {
        form.addEventListener("submit", e => {
            if (selecionados() < MINIMO) {
                e.preventDefault();
                alerta.textContent = "Selecione pelo menos " + MINIMO + " produtos.";
                alerta.classList.remove("d-none");
            }
        });
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>