<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit;
}
require "conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acao = $_POST["acao"] ?? "";

    try {
        if ($acao == "novo_fornecedor") {
            $fornecedor = new Fornecedor(
                $_POST["nome"] ?? "",
                $_POST["cnpj"] ?? "",
                $_POST["telefone"] ?? ""
            );
            $fornecedor->salvar($conexao);
            $_SESSION["msg"] = ["success", "Fornecedor cadastrado com sucesso!"];

        } elseif ($acao == "novo_produto") {
            $fornecedor = Fornecedor::buscar($conexao, (int) ($_POST["fornecedor_id"] ?? 0));
            $produto = new Produto(
                $_POST["nome"] ?? "",
                (float) ($_POST["preco"] ?? 0),
                $fornecedor
            );
            $produto->salvar($conexao);
            $_SESSION["msg"] = ["success", "Produto cadastrado com sucesso!"];

        } elseif ($acao == "excluir_fornecedor") {
            Fornecedor::excluir($conexao, (int) ($_POST["id"] ?? 0));
            $_SESSION["msg"] = ["warning", "Fornecedor excluído (os produtos dele também)."];

        } elseif ($acao == "excluir_produto") {
            Produto::excluir($conexao, (int) ($_POST["id"] ?? 0));
            $_SESSION["msg"] = ["warning", "Produto excluído."];
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION["msg"] = ["danger", "CNPJ já cadastrado."];
        } else {
            $_SESSION["msg"] = ["danger", "Erro ao salvar no banco de dados."];
        }
    } catch (Exception $e) {
        $_SESSION["msg"] = ["danger", $e->getMessage()];
    }

    header("Location: fornecedor.php");
    exit;
}

$fornecedores = Fornecedor::listar($conexao);
$produtos = Produto::listar($conexao);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastros</title>
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

    <h2 class="mb-4">Cadastros</h2>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Novo fornecedor</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="acao" value="novo_fornecedor">
                        <div class="mb-2">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control" maxlength="100" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">CNPJ</label>
                            <input type="text" name="cnpj" class="form-control" placeholder="00.000.000/0000-00" maxlength="18" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="telefone" class="form-control" maxlength="20">
                        </div>
                        <button class="btn btn-primary">Cadastrar fornecedor</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Novo produto</div>
                <div class="card-body">
                    <?php if (empty($fornecedores)): ?>
                        <div class="alert alert-info mb-0">Cadastre um fornecedor antes de cadastrar produtos.</div>
                    <?php else: ?>
                        <form method="POST">
                            <input type="hidden" name="acao" value="novo_produto">
                            <div class="mb-2">
                                <label class="form-label">Nome</label>
                                <input type="text" name="nome" class="form-control" maxlength="100" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Preço (R$)</label>
                                <input type="number" name="preco" class="form-control" step="0.01" min="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Fornecedor</label>
                                <select name="fornecedor_id" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($fornecedores as $f): ?>
                                        <option value="<?php echo $f->getId(); ?>"><?php echo htmlspecialchars($f->getNome()); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button class="btn btn-success">Cadastrar produto</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-secondary mt-4">
        A <strong>cesta</strong> é montada em <a href="produto.php">Selecionar produtos</a>.
    </div>

    <h4 class="mt-4">Fornecedores</h4>
    <div class="table-responsive">
        <table class="table table-striped bg-white align-middle">
            <thead>
                <tr><th>Nome</th><th>CNPJ</th><th>Telefone</th><th style="width:1%"></th></tr>
            </thead>
            <tbody>
            <?php foreach ($fornecedores as $f): ?>
                <tr>
                    <td><?php echo htmlspecialchars($f->getNome()); ?></td>
                    <td><?php echo htmlspecialchars($f->getCnpj()); ?></td>
                    <td><?php echo htmlspecialchars($f->getTelefone()); ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Excluir este fornecedor e todos os seus produtos?');">
                            <input type="hidden" name="acao" value="excluir_fornecedor">
                            <input type="hidden" name="id" value="<?php echo $f->getId(); ?>">
                            <button class="btn btn-sm btn-outline-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($fornecedores)): ?>
                <tr><td colspan="4" class="text-center text-muted">Nenhum fornecedor cadastrado.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <h4 class="mt-4">Produtos</h4>
    <div class="table-responsive">
        <table class="table table-striped bg-white align-middle">
            <thead>
                <tr><th>Nome</th><th>Preço</th><th>Fornecedor</th><th style="width:1%"></th></tr>
            </thead>
            <tbody>
            <?php foreach ($produtos as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p->getNome()); ?></td>
                    <td>R$ <?php echo number_format($p->getPreco(), 2, ",", "."); ?></td>
                    <td><?php echo htmlspecialchars($p->getFornecedor()->getNome()); ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Excluir este produto?');">
                            <input type="hidden" name="acao" value="excluir_produto">
                            <input type="hidden" name="id" value="<?php echo $p->getId(); ?>">
                            <button class="btn btn-sm btn-outline-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($produtos)): ?>
                <tr><td colspan="4" class="text-center text-muted">Nenhum produto cadastrado.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>