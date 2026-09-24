<?php
$servidor = "localhost";
$usuario = "root";
$senhaBanco = "";
try {

    $conexao = new PDO("mysql:host=$servidor", $usuario, $senhaBanco);
    $conexao->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    $conexao->exec("CREATE DATABASE IF NOT EXISTS gestao_produtos");
    $conexao->exec("USE gestao_produtos");

    $conexao->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(100) NOT NULL UNIQUE,
            senha VARCHAR(255) NOT NULL
        );

        CREATE TABLE IF NOT EXISTS fornecedores (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            cnpj VARCHAR(20) NOT NULL UNIQUE,
            telefone VARCHAR(20)
        );

        CREATE TABLE IF NOT EXISTS produtos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            preco DECIMAL(10,2) NOT NULL,
            fornecedor_id INT NOT NULL,
            FOREIGN KEY (fornecedor_id)
            REFERENCES fornecedores(id)
            ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS cesta_itens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            produto_id INT NOT NULL,
            FOREIGN KEY (usuario_id)
            REFERENCES usuarios(id)
            ON DELETE CASCADE,
            FOREIGN KEY (produto_id)
            REFERENCES produtos(id)
            ON DELETE CASCADE,
            UNIQUE KEY usuario_produto_unico
            (usuario_id, produto_id)
        );
    ");

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

class Usuario
{
    private ?int $id;
    private string $email;

    public function __construct(string $email, ?int $id = null)
    {
        $this->email = $email;
        $this->id = $id;
    }

    public function getId(): ?int    { return $this->id; }
    public function getEmail(): string { return $this->email; }

    public static function gerarHash(string $senha): string
    {
        return hash("sha256", $senha);
    }

    public function cadastrar(PDO $conexao, string $senha): void
    {
        $stmt = $conexao->prepare("INSERT INTO usuarios (email, senha) VALUES (?, ?)");
        $stmt->execute([$this->email, self::gerarHash($senha)]);
        $this->id = (int) $conexao->lastInsertId();
    }

    public static function autenticar(PDO $conexao, string $email, string $senha): ?Usuario
    {
        $stmt = $conexao->prepare("SELECT id, email FROM usuarios WHERE email = ? AND senha = ?");
        $stmt->execute([$email, self::gerarHash($senha)]);
        $linha = $stmt->fetch(PDO::FETCH_ASSOC);

        return $linha ? new Usuario($linha["email"], (int) $linha["id"]) : null;
    }
}


class Fornecedor
{
    private ?int $id;
    private string $nome;
    private string $cnpj;
    private string $telefone;

    public function __construct(string $nome, string $cnpj, ?string $telefone = "", ?int $id = null)
    {
        $this->nome     = trim($nome);
        $this->cnpj     = preg_replace('/\D/', '', $cnpj);
        $this->telefone = trim($telefone ?? "");
        $this->id       = $id;
    }

    public function getId(): ?int         { return $this->id; }
    public function getNome(): string     { return $this->nome; }
    public function getCnpj(): string     { return $this->cnpj; }
    public function getTelefone(): string { return $this->telefone; }

    public function validar(): void
    {
        if ($this->nome == "") {
            throw new Exception("Informe o nome do fornecedor.");
        }
        if (strlen($this->cnpj) != 14) {
            throw new Exception("O CNPJ deve ter 14 dígitos.");
        }
    }

    public function salvar(PDO $conexao): void
    {
        $this->validar();

        if ($this->id === null) {
            $stmt = $conexao->prepare("INSERT INTO fornecedores (nome, cnpj, telefone) VALUES (?, ?, ?)");
            $stmt->execute([$this->nome, $this->cnpj, $this->telefone]);
            $this->id = (int) $conexao->lastInsertId();
        } else {
            $stmt = $conexao->prepare("UPDATE fornecedores SET nome = ?, cnpj = ?, telefone = ? WHERE id = ?");
            $stmt->execute([$this->nome, $this->cnpj, $this->telefone, $this->id]);
        }
    }

    public static function excluir(PDO $conexao, int $id): void
    {
        $stmt = $conexao->prepare("DELETE FROM fornecedores WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function buscar(PDO $conexao, int $id): ?Fornecedor
    {
        $stmt = $conexao->prepare("SELECT * FROM fornecedores WHERE id = ?");
        $stmt->execute([$id]);
        $l = $stmt->fetch(PDO::FETCH_ASSOC);

        return $l ? new Fornecedor($l["nome"], $l["cnpj"], $l["telefone"], (int) $l["id"]) : null;
    }

    // Retorna um array de objetos Fornecedor
    public static function listar(PDO $conexao): array
    {
        $linhas = $conexao->query("SELECT * FROM fornecedores ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn($l) => new Fornecedor($l["nome"], $l["cnpj"], $l["telefone"], (int) $l["id"]),
            $linhas
        );
    }

    public function toArray(): array
    {
        return [
            "id"       => $this->id,
            "nome"     => $this->nome,
            "cnpj"     => $this->cnpj,
            "telefone" => $this->telefone
        ];
    }
}


class Produto
{
    private ?int $id;
    private string $nome;
    private float $preco;
    private ?Fornecedor $fornecedor; // relacionamento: Produto pertence a um Fornecedor

    public function __construct(string $nome, float $preco, ?Fornecedor $fornecedor, ?int $id = null)
    {
        $this->nome       = trim($nome);
        $this->preco      = $preco;
        $this->fornecedor = $fornecedor;
        $this->id         = $id;
    }

    public function getId(): ?int                { return $this->id; }
    public function getNome(): string            { return $this->nome; }
    public function getPreco(): float            { return $this->preco; }
    public function getFornecedor(): ?Fornecedor { return $this->fornecedor; }

    public function validar(): void
    {
        if ($this->nome == "") {
            throw new Exception("Informe o nome do produto.");
        }
        if ($this->preco <= 0) {
            throw new Exception("O preço deve ser maior que zero.");
        }
        if ($this->fornecedor === null || $this->fornecedor->getId() === null) {
            throw new Exception("Selecione um fornecedor.");
        }
    }

    public function salvar(PDO $conexao): void
    {
        $this->validar();
        $fornecedorId = $this->fornecedor->getId();

        if ($this->id === null) {
            $stmt = $conexao->prepare("INSERT INTO produtos (nome, preco, fornecedor_id) VALUES (?, ?, ?)");
            $stmt->execute([$this->nome, $this->preco, $fornecedorId]);
            $this->id = (int) $conexao->lastInsertId();
        } else {
            $stmt = $conexao->prepare("UPDATE produtos SET nome = ?, preco = ?, fornecedor_id = ? WHERE id = ?");
            $stmt->execute([$this->nome, $this->preco, $fornecedorId, $this->id]);
        }
    }

    public static function excluir(PDO $conexao, int $id): void
    {
        $stmt = $conexao->prepare("DELETE FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function deLinha(array $l): Produto
    {
        $fornecedor = new Fornecedor($l["f_nome"], $l["cnpj"], $l["telefone"], (int) $l["f_id"]);
        return new Produto($l["nome"], (float) $l["preco"], $fornecedor, (int) $l["id"]);
    }

    public static function listar(PDO $conexao): array
    {
        $linhas = $conexao->query(
            "SELECT p.id, p.nome, p.preco,
                    f.id AS f_id, f.nome AS f_nome, f.cnpj, f.telefone
             FROM produtos p
             JOIN fornecedores f ON f.id = p.fornecedor_id
             ORDER BY p.nome"
        )->fetchAll(PDO::FETCH_ASSOC);

        return array_map([Produto::class, "deLinha"], $linhas);
    }

    public function toArray(): array
    {
        return [
            "id"            => $this->id,
            "nome"          => $this->nome,
            "preco"         => $this->preco,
            "fornecedor_id" => $this->fornecedor ? $this->fornecedor->getId() : null
        ];
    }
}


class Cesta
{
    private Usuario $usuario;
    private array $itens = [];

    public function __construct(Usuario $usuario)
    {
        $this->usuario = $usuario;
    }

    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    public function carregar(PDO $conexao): void
    {
        $stmt = $conexao->prepare(
            "SELECT p.id, p.nome, p.preco,
                    f.id AS f_id, f.nome AS f_nome, f.cnpj, f.telefone
             FROM cesta_itens c
             JOIN produtos p ON p.id = c.produto_id
             JOIN fornecedores f ON f.id = p.fornecedor_id
             WHERE c.usuario_id = ?
             ORDER BY p.nome"
        );
        $stmt->execute([$this->usuario->getId()]);

        $this->itens = array_map([Produto::class, "deLinha"], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function adicionar(PDO $conexao, array $produtoIds): int
    {
        $stmt = $conexao->prepare(
            "INSERT IGNORE INTO cesta_itens (usuario_id, produto_id)
             SELECT ?, id FROM produtos WHERE id = ?"
        );

        $adicionados = 0;
        foreach ($produtoIds as $produtoId) {
            $stmt->execute([$this->usuario->getId(), (int) $produtoId]);
            $adicionados += $stmt->rowCount();
        }
        return $adicionados;
    }

    public function remover(PDO $conexao, int $produtoId): void
    {
        $stmt = $conexao->prepare("DELETE FROM cesta_itens WHERE usuario_id = ? AND produto_id = ?");
        $stmt->execute([$this->usuario->getId(), $produtoId]);
    }

    public function getItens(): array
    {
        return $this->itens;
    }

    public function idsProdutos(): array
    {
        return array_map(fn($p) => $p->getId(), $this->itens);
    }

    public function quantidade(): int
    {
        return count($this->itens);
    }

    public function total(): float
    {
        return array_sum(array_map(fn($p) => $p->getPreco(), $this->itens));
    }
}
?>