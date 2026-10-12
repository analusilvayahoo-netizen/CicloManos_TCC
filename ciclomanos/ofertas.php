<?php
session_start();
require_once 'config.php';

if (!isset($conn) && isset($conexao)) {
    $conn = $conexao;
}

if (!isset($conn) || !($conn instanceof mysqli)) {
    http_response_code(500);
    exit('Confira a conexão em config.php.');
}

$conn->set_charset('utf8mb4');

function e($v) {
    return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
}

$sql = "
    SELECT
        p.id,
        p.produto,
        p.descricao,
        p.imagem,
        o.preco_original,
        o.preco_oferta,
        o.percentual_desconto
    FROM ofertas o
    INNER JOIN cicloprodutos p
        ON p.id = o.id_produto
    WHERE
        o.ativo = 1
        AND (
            o.data_inicio IS NULL
            OR o.data_inicio <= CURDATE()
        )
        AND (
            o.data_fim IS NULL
            OR o.data_fim >= CURDATE()
        )
    ORDER BY
        o.percentual_desconto DESC,
        p.produto
";

$lista = $conn->query($sql);
$ehFuncionario = (isset($_SESSION['tipo_usuario']) && in_array($_SESSION['tipo_usuario'], ['funcionario', 'admin'], true));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ofertas | CicloManos</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; color: #222; min-height: 100vh; display: flex; flex-direction: column; }
        .ofertas-cm { width: min(1180px, calc(100% - 30px)); margin: 35px auto; flex: 1; }
        .ofertas-cm h1 { text-align: center; color: #315f83; margin-bottom: 8px; font-weight: bold; }
        .ofertas-intro { text-align: center; color: #687b8d; margin-bottom: 28px; }
        .ofertas-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 24px; }
        .oferta-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; padding: 18px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); display: flex; flex-direction: column; transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .oferta-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        .oferta-card-img-link { display: block; text-align: center; position: relative; margin-bottom: 12px; }
        .oferta-card img { width: 100%; height: 190px; object-fit: contain; }
        .desconto { position: absolute; top: 0; left: 0; background: #eaf4ff; color: #315f83; border-radius: 20px; padding: 4px 10px; font-size: 0.75rem; font-weight: bold; }
        .oferta-card h2 { font-size: 1rem; margin: 8px 0 6px; color: #222; font-weight: bold; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .oferta-card p { margin: 0 0 12px; color: #617284; font-size: 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .container-precos { margin-top: auto; margin-bottom: 14px; }
        .preco-antigo { color: #888; text-decoration: line-through; font-size: 12px; }
        .preco-oferta { font-size: 1.25rem; color: #c12b35; font-weight: 800; line-height: 1.2; }
        .oferta-card .btn-carrinho { display: block; text-align: center; background: #238549; color: #fff; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 13px; transition: background 0.2s; }
        .oferta-card .btn-carrinho:hover { background: #1b6b3a; color: #fff; }
        .sem-oferta { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 40px; text-align: center; color: #687b8d; grid-column: 1 / -1; font-size: 15px; box-shadow: 0 4px 16px rgba(0,0,0,0.04); }
        footer { background: #111; color: #aaa; text-align: center; padding: 22px; font-size: 12px; margin-top: auto; width: 100%; }
    </style>
</head>
<body>

<div class="topo-cinza">
    <a href="https://share.google/jYgrtVLyebEBGaqzt" target="_blank" rel="noopener">📍 Localização</a>
    <a href="https://wa.me/551239163262" target="_blank" rel="noopener">💬 Fale conosco</a>
    <span>📱 WhatsApp: (12) 3916-3262</span>
    <span>📞 Telefone: (12) 3916-3262</span>
</div>

<header class="meio-header">
    <a href="Index.php"><img src="tcc/logo.jpg" class="logo" alt="Logo CicloManos"></a>
    <form class="busca" method="GET" action="Index.php">
        <input type="text" name="busca" placeholder="Digite o que você procura">
        <button type="submit">Buscar</button>
    </form>
    <div class="usuario">
        <?php if (($_SESSION['tipo_usuario'] ?? '') === 'cliente'): ?>
            <a href="minha_conta.php">👤 Olá, <?= e($_SESSION['nome_usuario'] ?? 'Cliente') ?>!</a>
            <a href="meus_pedidos.php">📦 Meus Pedidos</a>
            <a href="favoritos.php">⭐ Favoritos</a>
            <a href="carrinho.php">🛒 Carrinho</a>
            <a href="logout.php">🚪 Sair</a>
        <?php elseif ($ehFuncionario): ?>
            <a href="painel_funcionario.php">👤 <?= e($_SESSION['nome_usuario'] ?? 'Funcionário') ?></a>
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
        <button type="button" class="botao-produtos" onclick="abrirProdutos(event)">☰ Produtos</button>
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

<main class="ofertas-cm">
    <div class="mb-4">
        <a href="Index.php" class="btn btn-secondary">← Voltar para a página inicial</a>
    </div>
    <h1>Ofertas da CicloManos</h1>
    <p class="ofertas-intro">Confira os produtos em promoção com descontos imperdíveis.</p>

    <div class="ofertas-grid">
        <?php if ($lista && $lista->num_rows): ?>
            <?php while ($p = $lista->fetch_assoc()): ?>
                <article class="oferta-card">
                    <a href="produtos.php?id=<?= (int)$p['id'] ?>" class="oferta-card-img-link" style="text-decoration:none;">
                        <?php if (!empty($p['imagem'])): ?>
                            <img src="<?= e($p['imagem']) ?>" alt="<?= e($p['produto']) ?>" loading="lazy">
                        <?php endif; ?>
                        <span class="desconto">-<?= number_format((float) $p['percentual_desconto'], 0, ',', '.') ?>%</span>
                    </a>
                    
                    <a href="produtos.php?id=<?= (int)$p['id'] ?>" style="text-decoration:none; color:inherit;">
                        <h2><?= e($p['produto']) ?></h2>
                        <p><?= e($p['descricao']) ?></p>
                    </a>

                    <div class="container-precos">
                        <div class="preco-antigo">R$ <?= number_format((float) $p['preco_original'], 2, ',', '.') ?></div>
                        <div class="preco-oferta">R$ <?= number_format((float) $p['preco_oferta'], 2, ',', '.') ?></div>
                    </div>

                    <a class="btn-carrinho" href="carrinho.php?acao=add&id=<?= (int) $p['id'] ?>">🛒 Colocar no carrinho</a>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="sem-oferta">
                Ainda não há ofertas ativas no momento. Volte em breve!
            </div>
        <?php endif; ?>
    </div>
</main>

<footer>
    © <?= date('Y') ?> CicloManos · Todos os direitos reservados
</footer>

<script>
function abrirProdutos(event) {
    if (event) { event.stopPropagation(); }
    document.getElementById('caixa-produtos')?.classList.toggle('aberto');
}
document.addEventListener('click', function(event) {
    const menu = document.querySelector('.produtos-menu');
    if (menu && !menu.contains(event.target)) {
        document.getElementById('caixa-produtos')?.classList.remove('aberto');
    }
});
</script>
</body>
</html>