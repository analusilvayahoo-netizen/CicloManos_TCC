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

// Adicionar Aos Favoritos (Salva no Banco)
if ($acao === 'add' && $idProd > 0) {
    $stmtCheck = mysqli_prepare($conn, "SELECT id FROM favoritos WHERE id_cliente = ? AND id_produto = ?");
    mysqli_stmt_bind_param($stmtCheck, 'ii', $idCliente, $idProd);
    mysqli_stmt_execute($stmtCheck);
    $resCheck = mysqli_stmt_get_result($stmtCheck);

    if (mysqli_num_rows($resCheck) === 0) {
        $stmtIns = mysqli_prepare($conn, "INSERT INTO favoritos (id_cliente, id_produto) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmtIns, 'ii', $idCliente, $idProd);
        mysqli_stmt_execute($stmtIns);
    }
    header('Location: favoritos.php');
    exit;
}

// Remover dos Favoritos
if ($acao === 'remover' && $idProd > 0) {
    $stmtRem = mysqli_prepare($conn, "DELETE FROM favoritos WHERE id_cliente = ? AND id_produto = ?");
    mysqli_stmt_bind_param($stmtRem, 'ii', $idCliente, $idProd);
    mysqli_stmt_execute($stmtRem);
    header('Location: favoritos.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Favoritos - CicloManos</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; color: #222; min-height: 100vh; display: flex; flex-direction: column; }
        .main-content { flex: 1; padding: 40px 0; }
        .card-produto { background: #fff; border: 1px solid #e8e8e8; border-radius: 10px; transition: 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .card-produto:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,0.08); }
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
        <h2 class="mb-4 fw-bold">⭐ Seus Produtos Favoritos</h2>
        
        <?php
        $resFavoritos = mysqli_query($conn, "SELECT p.* FROM favoritos f INNER JOIN cicloprodutos p ON f.id_produto = p.id WHERE f.id_cliente = $idCliente");
        if (!$resFavoritos || mysqli_num_rows($resFavoritos) == 0):
        ?>
            <p class="alert alert-info">Você ainda não favoritou nenhum produto.</p>
        <?php else: ?>
            <div class="row g-4">
                <?php while ($prod = mysqli_fetch_assoc($resFavoritos)): ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card-produto h-100 p-3 text-center d-flex flex-column justify-content-between">
                            <a href="produtos.php?id=<?= (int)$prod['id'] ?>" style="text-decoration:none; color:inherit;">
                                <img src="<?= htmlspecialchars($prod['imagem']) ?>" class="img-fluid rounded" style="height:150px; object-fit:contain;">
                                <div class="fw-semibold mt-2 text-dark"><?= htmlspecialchars($prod['produto']) ?></div>
                                <div class="text-danger fw-bold mt-1">R$ <?= number_format((float)$prod['preco_venda'], 2, ',', '.') ?></div>
                            </a>
                            <div class="d-flex gap-2 mt-3">
                                <a href="carrinho.php?acao=add&id=<?= $prod['id'] ?>" class="btn btn-success btn-sm flex-grow-1">🛒 Carrinho</a>
                                <a href="favoritos.php?acao=remover&id=<?= $prod['id'] ?>" class="btn btn-outline-danger btn-sm" title="Remover">❌</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
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