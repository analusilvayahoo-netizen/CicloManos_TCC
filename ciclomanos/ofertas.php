<?php
session_start();
require_once 'config.php';
if (!isset($conn) && isset($conexao)) { $conn = $conexao; }
if (!isset($conn) || !$conn) { http_response_code(500); exit('Não foi possível conectar ao banco de dados.'); }

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$sql = "SELECT o.id_oferta, o.preco_original, o.preco_oferta, o.percentual_desconto,
               p.id, p.produto, p.imagem, p.descricao
        FROM ofertas o
        INNER JOIN cicloprodutos p ON p.id = o.id_produto
        WHERE o.ativo = 1
          AND (o.data_inicio IS NULL OR o.data_inicio <= CURDATE())
          AND (o.data_fim IS NULL OR o.data_fim >= CURDATE())
        ORDER BY o.id_oferta DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ofertas - CicloManos</title>
<link rel="stylesheet" href="style.css">
<style>
.conteudo-ofertas{max-width:1200px;margin:36px auto;padding:0 20px;}
.conteudo-ofertas h1{text-align:center;margin-bottom:28px;color:#244f70;}
.grade-ofertas{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:24px;}
.card-oferta{background:#fff;border-radius:12px;padding:18px;box-shadow:0 4px 14px rgba(0,0,0,.09);display:flex;flex-direction:column;gap:12px;min-height:340px;}
.card-oferta img{width:100%;height:170px;object-fit:contain;}
.card-oferta h2{font-size:1.05rem;margin:0;}
.preco-original{text-decoration:line-through;color:#777;}
.preco-oferta{font-size:1.25rem;font-weight:700;color:#c62828;}
.tag-oferta{align-self:flex-start;background:#c62828;color:white;padding:5px 9px;border-radius:6px;font-size:.85rem;}
.link-produto{margin-top:auto;background:#4A86B8;color:#fff;padding:10px;text-align:center;border-radius:7px;}
.mensagem-ofertas{text-align:center;background:#fff;padding:24px;border-radius:10px;}
@media(max-width:600px){.conteudo-ofertas{padding:0 12px}.card-oferta{min-height:unset}}
</style>
</head>

<body>
<div class="topo-cinza">
  <a href="https://share.google/jYgrtVLyebEBGaqzt" target="_blank" rel="noopener">📍 Localização</a>
  <a href="https://wa.me/551239163262" target="_blank" rel="noopener">💬 Fale conosco</a>
  <span>📱 WhatsApp: (12) 3916-3262</span><span>📞 Telefone: (12) 3916-3262</span>
</div>

<header class="meio-header">
  <a href="index.php"><img src="tcc/logo.jpg" class="logo" alt="Logo CicloManos"></a>
  <div class="usuario">
  <?php if (($_SESSION['tipo_usuario'] ?? '') === 'funcionario'): ?>
    <a href="painel_funcionario.php">👤 <?= e($_SESSION['nome_usuario'] ?? 'Funcionário') ?></a>
  <?php elseif (($_SESSION['tipo_usuario'] ?? '') === 'cliente'): ?>
    <a href="minha_conta.php">👤 Olá, <?= e($_SESSION['nome_usuario'] ?? 'Cliente') ?></a><a href="logout.php">Sair</a>
  <?php else: ?><a href="login.php">👤 Conta</a><a href="login_funcionario.php">Área do funcionário</a><?php endif; ?>
    <a href="carrinho.php">🛒 Carrinho</a>
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

<section class="banner-central">
  <img src="tcc/logo.jpg" alt="CicloManos">
</section>

<main class="conteudo-ofertas">
  <h1>Ofertas do CicloManos</h1>

<?php if (!$result): ?>
  <p class="mensagem-ofertas">Não foi possível carregar as ofertas. Confira a tabela ofertas no banco.</p>
<?php elseif (mysqli_num_rows($result) === 0): ?>
  <p class="mensagem-ofertas">Não há ofertas ativas no momento.</p>
<?php else: ?><div class="grade-ofertas">
  <?php while ($oferta = mysqli_fetch_assoc($result)): ?>
  <article class="card-oferta">
    <?php if ($oferta['percentual_desconto'] !== null): ?><span class="tag-oferta">-<?= e(number_format((float)$oferta['percentual_desconto'], 0, ',', '.')) ?>%</span><?php endif; ?>
    <a href="produtos.php?id=<?= (int)$oferta['id'] ?>"><img src="<?= e($oferta['imagem']) ?>" alt="<?= e($oferta['produto']) ?>"></a>
    <h2><?= e($oferta['produto']) ?></h2>
    <p class="preco-original">De R$ <?= number_format((float)$oferta['preco_original'], 2, ',', '.') ?></p>
    <p class="preco-oferta">Por R$ <?= number_format((float)$oferta['preco_oferta'], 2, ',', '.') ?></p>
    <a class="link-produto" href="produtos.php?id=<?= (int)$oferta['id'] ?>">Ver produto</a>
  </article>
  <?php endwhile; ?></div><?php endif; ?>
</main>

<footer>
  <p>© <?= date('Y') ?> CicloManos - Todos os direitos reservados.</p>
</footer>

<script>function abrirProdutos(event){if(event)event.stopPropagation();document.getElementById('caixa-produtos')?.classList.toggle('aberto');}document.addEventListener('click',function(event){const menu=document.querySelector('.produtos-menu');if(menu&&!menu.contains(event.target))document.getElementById('caixa-produtos')?.classList.remove('aberto');});</script>
</body>

</html>
