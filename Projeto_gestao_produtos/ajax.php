<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atualizar dados</title>
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
    <h2 class="mb-3">Atualizar dados</h2>
    <div id="aviso"></div>

    <ul class="nav nav-tabs">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#abaFornecedores">Fornecedores</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#abaProdutos">Produtos</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#abaCesta">Cesta</button></li>
    </ul>

    <div class="tab-content bg-white border border-top-0 p-3">
        <div class="tab-pane fade show active" id="abaFornecedores">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Nome</th><th>CNPJ</th><th>Telefone</th><th></th></tr></thead>
                    <tbody id="tbFornecedores"></tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade" id="abaProdutos">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Nome</th><th>Preço</th><th>Fornecedor</th><th></th></tr></thead>
                    <tbody id="tbProdutos"></tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade" id="abaCesta">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Produto</th><th>Fornecedor</th><th>Preço</th><th></th></tr></thead>
                    <tbody id="tbCesta"></tbody>
                </table>
            </div>
            <p id="resumoCesta" class="fw-bold mb-0"></p>
        </div>
    </div>
</div>

<script>
    function esc(s) {
        return String(s ?? "").replace(/[&<>"']/g, c => ({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c]));
    }
    function moeda(v) {
        return "R$ " + Number(v).toLocaleString("pt-BR", {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    let listaFornecedores = [];

    async function chamar(acao, dados = null) {
        const opcoes = dados ? {method: "POST", body: new URLSearchParams(dados)} : {};
        const resp = await fetch("ajax_dados.php?acao=" + acao, opcoes);
        const json = await resp.json();
        if (!resp.ok) {
            if (resp.status === 401) window.location = "index.php";
            throw new Error(json.erro || "Erro inesperado.");
        }
        return json;
    }

    function aviso(msg, tipo = "success") {
        const div = document.getElementById("aviso");
        div.innerHTML = `<div class="alert alert-${tipo} py-2">${esc(msg)}</div>`;
        setTimeout(() => div.innerHTML = "", 4000);
    }

    function lerLinha(linha) {
        const dados = {id: linha.dataset.id};
        linha.querySelectorAll("[data-campo]").forEach(i => dados[i.dataset.campo] = i.value);
        return dados;
    }

    async function carregarFornecedores() {
        listaFornecedores = await chamar("listar_fornecedores");
        const tb = document.getElementById("tbFornecedores");
        tb.innerHTML = listaFornecedores.length ? "" : `<tr><td colspan="4" class="text-muted text-center">Nenhum fornecedor.</td></tr>`;
        listaFornecedores.forEach(f => {
            tb.insertAdjacentHTML("beforeend", `
                <tr data-id="${f.id}">
                    <td><input class="form-control form-control-sm" data-campo="nome" value="${esc(f.nome)}"></td>
                    <td><input class="form-control form-control-sm" data-campo="cnpj" value="${esc(f.cnpj)}" maxlength="18"></td>
                    <td><input class="form-control form-control-sm" data-campo="telefone" value="${esc(f.telefone)}"></td>
                    <td><button class="btn btn-sm btn-success" onclick="salvarFornecedor(${f.id})">Salvar</button></td>
                </tr>`);
        });
    }

    async function salvarFornecedor(id) {
        const linha = document.querySelector(`#tbFornecedores tr[data-id="${id}"]`);
        try {
            const r = await chamar("atualizar_fornecedor", lerLinha(linha));
            aviso(r.mensagem);
            carregarTudo();
        } catch (e) { aviso(e.message, "danger"); }
    }

    async function carregarProdutos() {
        const produtos = await chamar("listar_produtos");
        const tb = document.getElementById("tbProdutos");
        tb.innerHTML = produtos.length ? "" : `<tr><td colspan="4" class="text-muted text-center">Nenhum produto.</td></tr>`;
        produtos.forEach(p => {
            const opcoes = listaFornecedores.map(f =>
                `<option value="${f.id}" ${f.id == p.fornecedor_id ? "selected" : ""}>${esc(f.nome)}</option>`).join("");
            tb.insertAdjacentHTML("beforeend", `
                <tr data-id="${p.id}">
                    <td><input class="form-control form-control-sm" data-campo="nome" value="${esc(p.nome)}"></td>
                    <td><input type="number" step="0.01" min="0.01" class="form-control form-control-sm" data-campo="preco" value="${p.preco}"></td>
                    <td><select class="form-select form-select-sm" data-campo="fornecedor_id">${opcoes}</select></td>
                    <td><button class="btn btn-sm btn-success" onclick="salvarProduto(${p.id})">Salvar</button></td>
                </tr>`);
        });
    }

    async function salvarProduto(id) {
        const linha = document.querySelector(`#tbProdutos tr[data-id="${id}"]`);
        try {
            const r = await chamar("atualizar_produto", lerLinha(linha));
            aviso(r.mensagem);
            carregarTudo();
        } catch (e) { aviso(e.message, "danger"); }
    }

    async function carregarCesta() {
        const c = await chamar("listar_cesta");
        const tb = document.getElementById("tbCesta");
        tb.innerHTML = c.itens.length ? "" : `<tr><td colspan="4" class="text-muted text-center">Cesta vazia.</td></tr>`;
        c.itens.forEach(p => {
            tb.insertAdjacentHTML("beforeend", `
                <tr>
                    <td>${esc(p.nome)}</td>
                    <td>${esc(p.fornecedor)}</td>
                    <td>${moeda(p.preco)}</td>
                    <td><button class="btn btn-sm btn-outline-danger" onclick="removerDaCesta(${p.id})">Remover</button></td>
                </tr>`);
        });
        document.getElementById("resumoCesta").textContent =
            c.quantidade + " produto(s) — Total: " + moeda(c.total);
    }

    async function removerDaCesta(produtoId) {
        try {
            const r = await chamar("remover_cesta", {produto_id: produtoId});
            aviso(r.mensagem, "warning");
            carregarCesta();
        } catch (e) { aviso(e.message, "danger"); }
    }

    async function carregarTudo() {
        try {
            await carregarFornecedores();
            await carregarProdutos();
            await carregarCesta();
        } catch (e) { aviso(e.message, "danger"); }
    }

    carregarTudo();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>