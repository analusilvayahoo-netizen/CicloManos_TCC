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
        .ofertas-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 22px; }
        .oferta-card { background: #fff; border: 1px solid #dbe3ea; border-radius: 13px; overflow: hidden; padding: 16px; box-shadow: 0 5px 18px rgba(26, 54, 79, .06); display: flex; flex-direction: column; gap: 10px; transition: 0.2s; }
        .oferta-card:hover { transform: translateY(-3px); box-shadow: 0 8px 22px rgba(0,0,0,0.08); }
        .oferta-card img { width: 100%; height: 180px; object-fit: contain; }
        .oferta-card h2 { font-size: 1.05rem; margin: 0; color: #315f83; font-weight: bold; }
        .oferta-card p { margin: 0; color: #617284; font-size: 13px; }
        .preco-antigo { color: #7a8793; text-decoration: line-through; font-size: 13px; }
        .preco-oferta { font-size: 1.3rem; color: #c12b35; font-weight: 800; }
        .desconto { align-self: flex-start; background: #eaf4ff; color: #315f83; border-radius: 20px; padding: 4px 9px; font-size: .8rem; font-weight: bold; }
        .oferta-card .btn-carrinho { display: block; text-align: center; background: #4A86B8; color: #fff; padding: 11px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: auto; }
        .oferta-card .btn-carrinho:hover { background: #345f85; color: #fff; }
        .sem-oferta { background: #f4f7fa; border-radius: 12px; padding: 30px; text-align: center; color: #687b8d; grid-column: 1 / -1; }
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
            <a href="favoritos.php">⭐ Favoritos</a>
            <a href="carrinho.php">🛒 Carrinho</a>
            <a href="logout.php">🚪 Sair</a>
        <?php elseif (in_array(($_SESSION['tipo_usuario'] ?? ''), ['funcionario', 'admin'], true)): ?>
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

<main class="ofertas-cm">
    <div class="mb-4">
        <a href="Index.php" class="btn btn-secondary">← Voltar para a página inicial</a>
    </div>
    <h1>Ofertas da CicloManos</h1>
    <p class="ofertas-intro">Confira os produtos em promoção.</p>

    <div class="ofertas-grid">
        <?php if ($lista && $lista->num_rows): ?>
            <?php while ($p = $lista->fetch_assoc()): ?>
                <article class="oferta-card">
                    <a href="produtos.php?id=<?= (int)$p['id'] ?>" style="text-decoration:none; color:inherit;">
                        <?php if (!empty($p['imagem'])): ?>
                            <img src="<?= e($p['imagem']) ?>" alt="<?= e($p['produto']) ?>" loading="lazy">
                        <?php endif; ?>
                        <span class="desconto mt-2">-<?= number_format((float) $p['percentual_desconto'], 0, ',', '.') ?>%</span>
                        <h2 class="mt-2"><?= e($p['produto']) ?></h2>
                        <p class="mt-1"><?= e($p['descricao']) ?></p>
                        <div class="preco-antigo mt-2">R$ <?= number_format((float) $p['preco_original'], 2, ',', '.') ?></div>
                        <div class="preco-oferta">R$ <?= number_format((float) $p['preco_oferta'], 2, ',', '.') ?></div>
                    </a>
                    <a class="btn-carrinho" href="carrinho.php?acao=add&id=<?= (int) $p['id'] ?>">🛒 Colocar no carrinho</a>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="sem-oferta">
                Ainda não há ofertas ativas. Volte em breve!
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