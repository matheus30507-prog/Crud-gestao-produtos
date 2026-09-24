<?php
session_start();
header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION["usuario_id"])) {
    http_response_code(401);
    echo json_encode(["erro" => "Sessão expirada. Faça login novamente."]);
    exit;
}

require "conexao.php";

$usuarioId = $_SESSION["usuario_id"];
$acao = $_GET["acao"] ?? "";

try {
    if ($acao == "listar_fornecedores") {
        $dados = $conexao->query("SELECT * FROM fornecedores ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($dados);

    } elseif ($acao == "listar_produtos") {
        $dados = $conexao->query(
            "SELECT p.id, p.nome, p.preco, p.fornecedor_id
             FROM produtos p ORDER BY p.nome"
        )->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($dados);

    } elseif ($acao == "listar_cesta") {
        $stmt = $conexao->prepare(
            "SELECT p.id, p.nome, p.preco, f.nome AS fornecedor
             FROM cesta_itens c
             JOIN produtos p ON p.id = c.produto_id
             JOIN fornecedores f ON f.id = p.fornecedor_id
             WHERE c.usuario_id = ?
             ORDER BY p.nome"
        );
        $stmt->execute([$usuarioId]);
        $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode([
            "itens"      => $itens,
            "quantidade" => count($itens),
            "total"      => array_sum(array_column($itens, "preco"))
        ]);

    } elseif ($_SERVER["REQUEST_METHOD"] != "POST") {
        throw new Exception("Método inválido.");

    } elseif ($acao == "atualizar_fornecedor") {
        $id       = (int) ($_POST["id"] ?? 0);
        $nome     = trim($_POST["nome"] ?? "");
        $cnpj     = preg_replace('/\D/', '', $_POST["cnpj"] ?? "");
        $telefone = trim($_POST["telefone"] ?? "");

        if ($nome == "") throw new Exception("Informe o nome do fornecedor.");
        if (strlen($cnpj) != 14) throw new Exception("O CNPJ deve ter 14 dígitos.");

        $stmt = $conexao->prepare("UPDATE fornecedores SET nome = ?, cnpj = ?, telefone = ? WHERE id = ?");
        $stmt->execute([$nome, $cnpj, $telefone, $id]);
        echo json_encode(["mensagem" => "Fornecedor atualizado com sucesso."]);

    } elseif ($acao == "atualizar_produto") {
        $id            = (int) ($_POST["id"] ?? 0);
        $nome          = trim($_POST["nome"] ?? "");
        $preco         = (float) ($_POST["preco"] ?? 0);
        $fornecedor_id = (int) ($_POST["fornecedor_id"] ?? 0);

        if ($nome == "") throw new Exception("Informe o nome do produto.");
        if ($preco <= 0) throw new Exception("O preço deve ser maior que zero.");

        $stmt = $conexao->prepare("UPDATE produtos SET nome = ?, preco = ?, fornecedor_id = ? WHERE id = ?");
        $stmt->execute([$nome, $preco, $fornecedor_id, $id]);
        echo json_encode(["mensagem" => "Produto atualizado com sucesso."]);

    } elseif ($acao == "remover_cesta") {
        $stmt = $conexao->prepare("DELETE FROM cesta_itens WHERE usuario_id = ? AND produto_id = ?");
        $stmt->execute([$usuarioId, (int) ($_POST["produto_id"] ?? 0)]);
        echo json_encode(["mensagem" => "Produto removido da cesta."]);

    } else {
        throw new Exception("Ação inválida.");
    }

} catch (PDOException $e) {
    http_response_code(422);
    if ($e->getCode() == 23000) {
        echo json_encode(["erro" => "CNPJ já cadastrado ou fornecedor inválido."]);
    } else {
        echo json_encode(["erro" => "Erro no banco de dados."]);
    }
} catch (Exception $e) {
    http_response_code(422);
    echo json_encode(["erro" => $e->getMessage()]);
}