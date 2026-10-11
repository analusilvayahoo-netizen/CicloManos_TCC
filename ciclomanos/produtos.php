<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sql = "SELECT * FROM cicloprodutos WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    http_response_code(404);
    $produto = null;
} else {
    $produto = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CicloManos - Produto</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; min-height: 100vh; display: flex; flex-direction: column; }
        .conteudo-principal { flex: 1; width: 100%; padding: 40px 0; display: flex; align-items: center; }
        .botao-voltar { display: inline-block; background: #4A86B8; color: white; padding: 10px 18px; border-radius: 6px; font-size: 15px; font-weight: bold; text-decoration:none; }
        .botao-voltar:hover { background: #345f85; color: white; }
        .produto-detalhe { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); }
        .imagem-produto { width: 100%; max-height: 420px; object-fit: contain; }
        .nome-produto { font-size: 30px; font-weight: bold; color: #222; }
        .preco-produto { color: #e60023; font-weight: bold; }
        .descricao-produto { color: #555; font-size: 16px; line-height: 1.7; }
        footer { background: #111; color: white; text-align: center; padding: 20px; margin-top: auto; width: 100%; }
    </style>
</head>
<body>

<main class="conteudo-principal">
    <div class="container">
        <div class="mb-4">
            <a href="Index.php" class="botao-voltar">← Voltar para a página inicial</a>
        </div>
        <?php if ($produto === null): ?>
            <div class="text-center py-5">
                <h2>Produto não encontrado</h2>
                <a href="Index.php" class="botao-voltar mt-3">Voltar para a página inicial</a>
            </div>
        <?php else: ?>
            <div class="produto-detalhe">
                <div class="row align-items-center g-4">
                    <div class="col-md-6 text-center">
                        <img src="<?= htmlspecialchars($produto['imagem'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="imagem-produto img-fluid rounded" alt="Produto">
                    </div>
                    <div class="col-md-6">
                        <h1 class="nome-produto mb-4"><?= htmlspecialchars($produto['produto'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
                        <h3 class="preco-produto mb-4">R$ <?= number_format((float)$produto['preco_venda'], 2, ',', '.') ?></h3>
                        <p class="descricao-produto"><?= nl2br(htmlspecialchars($produto['descricao'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
                        
                        <div class="d-flex gap-2 mt-4">
                            <a href="carrinho.php?acao=add&id=<?= (int)$produto['id'] ?>" class="btn btn-success btn-lg flex-grow-1">🛒 Adicionar ao Carrinho</a>
                            <a href="favoritos.php?acao=add&id=<?= (int)$produto['id'] ?>" class="btn btn-warning btn-lg text-white">⭐ Favoritar</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<footer><p>&copy; 2026 CicloManos. Todos os direitos reservados.</p></footer>
</body>
</html>