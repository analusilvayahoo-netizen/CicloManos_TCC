<?php
session_start();
include 'config.php';
if (!isset($conn) && isset($conexao)) { $conn = $conexao; }
if (!$conn) { die("Erro: Conexão com a base de dados não encontrada."); }
$ehFuncionario = (isset($_SESSION['tipo_usuario']) && in_array($_SESSION['tipo_usuario'], ['funcionario', 'admin'], true));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bicicletas - CicloManos</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; color: #222; min-height: 100vh; display: flex; flex-direction: column; }
        .main-content { flex: 1; padding: 30px 0; }
        .card-produto { background: #fff; border: 1px solid #e8e8e8; border-radius: 10px; transition: 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .card-produto:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,0.08); }
        .preco-produto { color: #e60023; font-weight: bold; font-size: 16px; margin-top: 5px; }
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
    <a href="Index.php"><img src="tcc/logo.jpg" class="logo" alt="Logo"></a>
    <form class="busca" method="GET" action="Index.php">
        <input type="text" name="busca" placeholder="Digite o que você procura">
        <button type="submit">Buscar</button>
    </form>
    <div class="usuario">
        <?php if (($_SESSION['tipo_usuario'] ?? '') === 'cliente'): ?>
            <a href="minha_conta.php">👤 Olá, <?= htmlspecialchars($_SESSION['nome_usuario'] ?? 'Cliente') ?>!</a>
            <a href="meus_pedidos.php">📦 Meus Pedidos</a>
            <a href="favoritos.php">⭐ Favoritos</a>
            <a href="carrinho.php">🛒 Carrinho</a>
            <a href="logout.php">🚪 Sair</a>
        <?php elseif ($ehFuncionario): ?>
            <a href="painel_funcionario.php">👤 <?= htmlspecialchars($_SESSION['nome_usuario']) ?></a>
            <a href="carrinho.php">🛒 Carrinho</a>
            <a href="logout.php">🚪 Sair</a>
        <?php else: ?>
            <a href="login.php">👤 Conta</a>
            <a href="favoritos.php">⭐ Favoritos</a>
            <a href="carrinho.php">🛒 Carrinho</a>
        <?php endif; ?>
    </div>
</header>
<div class="menu-bar">
    <div class="produtos-menu">
        <button class="botao-produtos" type="button" onclick="abrirProdutos()">☰ Produtos</button>
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
<?php if ($ehFuncionario): ?>
    <div style="background: #111; color: #fff; padding: 10px 20px; text-align: center; font-weight: bold; font-size: 14px; border-bottom: 1px solid #333;">
        ⚙️ Você está navegando como funcionário. 
        <a href="painel_funcionario.php" style="color: #4b86b4; text-decoration: underline; margin-left: 10px;">Voltar ao Painel do Funcionário →</a>
    </div>
<?php endif; ?>
<main class="main-content">
    <div class="container mb-5">
        <div class="mb-4">
            <a href="Index.php" class="btn btn-secondary">← Voltar para a página inicial</a>
        </div>
        <h2 class="text-center fw-bold mb-4">Bicicletas</h2>
        <?php
        $sql = "SELECT p.* FROM cicloprodutos p INNER JOIN categorias c ON p.id_categoria = c.id_categoria WHERE c.nome_categoria LIKE '%bicicleta%' OR p.id_categoria IN (1, 2)";
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            echo "<div class='row g-4'>";
            while ($row = mysqli_fetch_assoc($result)) {
                $id = (int) $row['id'];
                echo "
                <div class='col-12 col-sm-6 col-md-4 col-lg-3'>
                    <div class='card-produto h-100 d-flex flex-column justify-content-between text-center p-3'>
                        <a href='produtos.php?id={$id}' style='text-decoration:none; color:inherit;'>
                            <img src='".htmlspecialchars($row['imagem'])."' class='img-fluid rounded' style='height:160px; object-fit:contain;'>
                            <div class='titulo-produto fw-semibold mt-2 text-dark'>".htmlspecialchars($row['produto'])."</div>
                            <div class='preco-produto'>R$ ".number_format((float)$row['preco_venda'], 2, ',', '.')."</div>
                        </a>
                        <div class='d-flex gap-2 mt-3'>
                            <a href='carrinho.php?acao=add&id={$id}' class='btn btn-success btn-sm flex-grow-1'>🛒 Carrinho</a>
                            <a href='favoritos.php?acao=add&id={$id}' class='btn btn-warning btn-sm text-white' title='Favoritar'>⭐</a>
                        </div>
                    </div>
                </div>";
            }
            echo "</div>";
        } else {
            echo "<p class='alert alert-warning text-center'>Nenhuma bicicleta encontrada.</p>";
        }
        mysqli_close($conn);
        ?>
    </div>
</main>
<footer><p>© <?= date('Y') ?> CicloManos - Todos os direitos reservados.</p></footer>
<script>
function abrirProdutos() { document.getElementById("caixa-produtos").classList.toggle("aberto"); }
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