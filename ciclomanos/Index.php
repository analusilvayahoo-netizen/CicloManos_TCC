<?php
session_start();
include 'config.php';

if (!isset($conn) && isset($conexao)) {
    $conn = $conexao;
}

if (!isset($conn) || !$conn) {
    die("Erro: conexão com a base de dados não encontrada. Verifique o seu config.php.");
}

$busca = trim($_GET['busca'] ?? '');

// Verifica se o usuário logado é funcionário ou admin para exibir a barra de retorno
$ehFuncionario = (isset($_SESSION['tipo_usuario']) && in_array($_SESSION['tipo_usuario'], ['funcionario', 'admin'], true));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CicloManos</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; color: #222; min-height: 100vh; display: flex; flex-direction: column; }
        
        .banner-central {
            background: #fff;
            padding: 35px 0;
            text-align: center;
        }
        .banner-central img {
            max-height: 200px;
            width: auto;
            object-fit: contain;
        }

        .card-produto {
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 10px;
            padding: 15px;
            transition: 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card-produto:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.08);
        }
        .card-produto img {
            height: 120px !important;
            max-height: 120px !important;
            object-fit: contain;
            width: 100%;
            margin-bottom: 5px;
        }
        .titulo-produto {
            font-weight: 600;
            color: #222;
            margin-top: 10px;
            font-size: 15px;
        }
        .preco-produto {
            color: #e60023;
            font-weight: bold;
            font-size: 16px;
            margin-top: 5px;
        }
        .produtos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .container-produtos {
            flex: 1;
            width: 92%;
            max-width: 1200px;
            margin: 30px auto;
        }
        footer { background: #111; color: #fff; text-align: center; padding: 22px; font-size: 12px; margin-top: auto; }
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
    <a href="Index.php">
        <img src="tcc/logo.jpg" class="logo" alt="Logo CicloManos">
    </a>

    <form class="busca" method="GET" action="Index.php">
        <input type="text" name="busca" value="<?= htmlspecialchars($busca, ENT_QUOTES, 'UTF-8') ?>" placeholder="Digite o que você procura">
        <button type="submit">Buscar</button>
    </form>

    <div class="usuario">
        <?php if (($_SESSION['tipo_usuario'] ?? '') === 'cliente'): ?>
            <a href="minha_conta.php">👤 Olá, <?= htmlspecialchars($_SESSION['nome_usuario'] ?? 'Cliente', ENT_QUOTES, 'UTF-8') ?>!</a>
            <a href="meus_pedidos.php">📦 Meus Pedidos</a>
            <a href="favoritos.php">⭐ Favoritos</a>
            <a href="carrinho.php">🛒 Carrinho</a>
            <a href="logout.php">🚪 Sair</a>
        <?php elseif ($ehFuncionario): ?>
            <a href="painel_funcionario.php">👤 <?= htmlspecialchars($_SESSION['nome_usuario'] ?? 'Funcionário', ENT_QUOTES, 'UTF-8') ?></a>
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

<!-- Barra preta exclusiva para funcionários navegando no site -->
<?php if ($ehFuncionario): ?>
    <div style="background: #111; color: #fff; padding: 10px 20px; text-align: center; font-weight: bold; font-size: 14px; border-bottom: 1px solid #333;">
        ⚙️ Você está navegando como funcionário. 
        <a href="painel_funcionario.php" style="color: #4b86b4; text-decoration: underline; margin-left: 10px;">Voltar ao Painel do Funcionário →</a>
    </div>
<?php endif; ?>

<!-- Banner centralizado e maior -->
<section class="banner-central">
    <img src="tcc/logo.jpg" alt="CicloManos">
</section>

<main class="container-produtos">
    <h2 class="fw-bold mb-4">
        <?php if ($busca !== ''): ?>
            Resultados da busca: <?= htmlspecialchars($busca, ENT_QUOTES, 'UTF-8') ?>
        <?php else: ?>
            Nossa Vitrine de Produtos
        <?php endif; ?>
    </h2>

    <div class="produtos">
        <?php
        if ($busca !== '') {
            $termo = '%' . $busca . '%';
            $stmtBusca = mysqli_prepare($conn, "SELECT * FROM cicloprodutos WHERE produto LIKE ? OR descricao LIKE ? ORDER BY produto LIMIT 100");
            if ($stmtBusca) {
                mysqli_stmt_bind_param($stmtBusca, 'ss', $termo, $termo);
                mysqli_stmt_execute($stmtBusca);
                $result = mysqli_stmt_get_result($stmtBusca);
            } else {
                $result = false;
            }
        } else {
            $sql = "SELECT * FROM cicloprodutos ORDER BY id DESC LIMIT 16";
            $result = mysqli_query($conn, $sql);
        }

        if (!$result) {
            echo '<div class="alert alert-danger w-100">Não foi possível buscar os produtos.</div>';
        } else {
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <div class="card-produto">
                        <a href="produtos.php?id=<?= (int) $row['id'] ?>" class="text-decoration-none text-dark">
                            <img src="<?= htmlspecialchars($row['imagem'] ?? '', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($row['produto'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="img-fluid rounded">
                            <div class="titulo-produto"><?= htmlspecialchars($row['produto'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="preco-produto">R$ <?= number_format((float) ($row['preco_venda'] ?? 0), 2, ',', '.') ?></div>
                        </a>
                        <div class="d-flex gap-2 mt-3">
                            <a href="carrinho.php?acao=add&id=<?= (int) $row['id'] ?>" class="btn btn-success btn-sm flex-grow-1">🛒 Carrinho</a>
                            <a href="favoritos.php?acao=add&id=<?= (int) $row['id'] ?>" class="btn btn-warning btn-sm text-white" title="Favoritar">⭐</a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="alert alert-warning w-100">Nenhum produto encontrado.</div>';
            }
        }
        ?>
    </div>
</main>

<footer>
    <p>© <?= date('Y') ?> CicloManos - Todos os direitos reservados.</p>
</footer>

<script>
function abrirProdutos() {
    const caixa = document.getElementById("caixa-produtos");
    caixa.classList.toggle("aberto");
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
<?php mysqli_close($conn); ?>