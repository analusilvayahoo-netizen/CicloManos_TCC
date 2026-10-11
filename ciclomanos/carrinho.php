<?php
session_start();
include 'config.php';
if (!isset($conn) && isset($conexao)) { $conn = $conexao; }
if (!$conn) { die("Erro: Conexão com a base de dados não encontrada."); }

if (($_SESSION['tipo_usuario'] ?? '') !== 'cliente' || empty($_SESSION['id_cliente'])) {
    header('Location: login.php');
    exit;
}

$idCliente = (int) $_SESSION['id_cliente'];
$acao = $_GET['acao'] ?? '';
$idProd = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Adicionar ao Carrinho compatível com a restrição uk_carrinho_cliente_produto do SQL
if ($acao === 'add' && $idProd > 0) {
    $stmt = mysqli_prepare($conn, "INSERT INTO carrinho (id_cliente, id_produto, quantidade) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE quantidade = quantidade + 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ii', $idCliente, $idProd);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header('Location: carrinho.php');
    exit;
}

// Remover do Carrinho
if ($acao === 'remover' && $idProd > 0) {
    $stmtRem = mysqli_prepare($conn, "DELETE FROM carrinho WHERE id_cliente = ? AND id_produto = ?");
    if ($stmtRem) {
        mysqli_stmt_bind_param($stmtRem, 'ii', $idCliente, $idProd);
        mysqli_stmt_execute($stmtRem);
        mysqli_stmt_close($stmtRem);
    }
    header('Location: carrinho.php');
    exit;
}

// Finalizar Compra
if ($acao === 'finalizar') {
    $resItens = mysqli_query($conn, "SELECT c.quantidade, p.preco_venda FROM carrinho c INNER JOIN cicloprodutos p ON c.id_produto = p.id WHERE c.id_cliente = $idCliente");
    $totalVenda = 0;
    while ($item = mysqli_fetch_assoc($resItens)) {
        $totalVenda += ((float)$item['preco_venda'] * (int)$item['quantidade']);
    }

    if ($totalVenda > 0) {
        $stmtVenda = mysqli_prepare($conn, "INSERT INTO vendas (id_cliente, data, valor_total, status_pagamento) VALUES (?, NOW(), ?, 'pago')");
        if ($stmtVenda) {
            mysqli_stmt_bind_param($stmtVenda, 'id', $idCliente, $totalVenda);
            mysqli_stmt_execute($stmtVenda);
            mysqli_stmt_close($stmtVenda);
        }
        mysqli_query($conn, "DELETE FROM carrinho WHERE id_cliente = $idCliente");
        $mensagemSucesso = "Compra efetuada com sucesso!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Carrinho - CicloManos</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; color: #222; min-height: 100vh; display: flex; flex-direction: column; }
        .main-content { flex: 1; padding: 40px 0; }
        .cart-box { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 16px rgba(0,0,0,0.05); }
        footer { background: #111; color: #aaa; text-align: center; padding: 22px; font-size: 12px; margin-top: auto; }
    </style>
</head>
<body>

<div class="topo-cinza">
    <a href="https://share.google/jYgrtVLyebEBGaqzt" target="_blank">📍 Localização</a>
    <a href="https://wa.me/551239163262?text=Olá!%20Vim%20pelo%20site%20da%20CicloManos" target="_blank">💬 Fale conosco</a>
    <a href="https://wa.me/551239163262" target="_blank">📱 WhatsApp: (12) 3916-3262</a>
    <span>📞 Telefone: (12) 3916-3262</span>
</div>

<header class="meio-header">
    <a href="Index.php"><img src="tcc/logo.jpg" class="logo" alt="Logo CicloManos"></a>
    <form class="busca" method="GET" action="Index.php">
        <input type="text" name="busca" placeholder="Digite o que você procura">
        <button type="submit">Buscar</button>
    </form>
    <div class="usuario">
        <a href="minha_conta.php">👤 Olá, <?= htmlspecialchars($_SESSION['nome_usuario'] ?? 'Cliente', ENT_QUOTES, 'UTF-8') ?>!</a>
        <a href="favoritos.php">⭐ Favoritos</a>
        <a href="carrinho.php">🛒 Carrinho</a>
        <a href="logout.php">🚪 Sair</a>
    </div>
</header>

<div class="menu-bar">
    <div class="produtos-menu">
        <button type="button" class="botao-produtos" onclick="abrirProdutos()">☰ Produtos</button>
        <div id="caixa-produtos" class="caixa-produtos">
            <a href="acessorios.php">Acessórios</a>
            <a href="pecas.php">Peças</a>
            <a href="bicicletas.php">Bicicletas</a>
        </div>
    </div>
    <nav>
        <a href="manutencao.php">Manutenção</a>
        <a href="ofertas.php">Ofertas</a>
    </nav>
</div>

<main class="main-content">
    <div class="container">
        <div class="mb-4">
            <a href="Index.php" class="btn btn-secondary">← Voltar para a página inicial</a>
        </div>
        <div class="cart-box">
            <h2 class="mb-4 fw-bold">🛒 Seu Carrinho de Compras</h2>
            
            <?php if (isset($mensagemSucesso)): ?>
                <div class="alert alert-success"><h3><?= $mensagemSucesso ?></h3></div>
                <a href="Index.php" class="btn btn-primary mt-2">Continuar comprando</a>
            <?php else: 
                $resCarrinho = mysqli_query($conn, "SELECT c.id_produto, c.quantidade, p.produto, p.preco_venda, p.imagem FROM carrinho c INNER JOIN cicloprodutos p ON c.id_produto = p.id WHERE c.id_cliente = $idCliente");
                if (!$resCarrinho || mysqli_num_rows($resCarrinho) == 0):
            ?>
                <p class="alert alert-warning">O seu carrinho está vazio.</p>
                <a href="Index.php" class="btn btn-primary mt-2">Voltar às compras</a>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Preço</th>
                                <th>Quantidade</th>
                                <th>Subtotal</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalGeral = 0;
                            while ($item = mysqli_fetch_assoc($resCarrinho)) {
                                $subtotal = (float)$item['preco_venda'] * (int)$item['quantidade'];
                                $totalGeral += $subtotal;
                                echo "<tr>
                                    <td>".htmlspecialchars($item['produto'])."</td>
                                    <td>R$ ".number_format((float)$item['preco_venda'], 2, ',', '.')."</td>
                                    <td>{$item['quantidade']}</td>
                                    <td>R$ ".number_format($subtotal, 2, ',', '.')."</td>
                                    <td><a href='carrinho.php?acao=remover&id={$item['id_produto']}' class='btn btn-danger btn-sm'>Remover</a></td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-3">
                    <h4 class="fw-bold m-0">Total: R$ <?= number_format($totalGeral, 2, ',', '.') ?></h4>
                    <a href="carrinho.php?acao=finalizar" class="btn btn-success btn-lg px-4">Finalizar Compra</a>
                </div>
            <?php endif; endif; ?>
        </div>
    </div>
</main>

<footer>© <?= date('Y') ?> CicloManos - Todos os direitos reservados.</footer>

<script>
function abrirProdutos() {
    document.getElementById("caixa-produtos").classList.toggle("aberto");
}
document.addEventListener("click", function(event) {
    const produtosMenu = document.querySelector(".produtos-menu");
    const caixa = document.getElementById("caixa-produtos");
    if (produtosMenu && caixa && !produtosMenu.contains(event.target)) {
        caixa.classList.remove("aberto");
    }
});
</script>
</body>
</html>