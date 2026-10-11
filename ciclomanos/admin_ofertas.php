<?php
session_start();
include 'config.php';
if (!isset($conn) && isset($conexao)) { $conn = $conexao; }

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'funcionario') {
    header('Location: login_funcionario.php');
    exit;
}

$nomeFuncionario = $_SESSION['nome_usuario'] ?? 'Funcionário';
$cargoFuncionario = $_SESSION['cargo_funcionario'] ?? 'Funcionário';

$ofertas = mysqli_query($conn, "SELECT o.*, p.produto FROM ofertas o INNER JOIN cicloprodutos p ON o.id_produto = p.id ORDER BY o.id_oferta DESC");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Ofertas - CicloManos</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; color: #222; min-height: 100vh; display: flex; flex-direction: column; }
        .cm-admin-topo { background: #4b86b4; color: #fff; padding: 18px 6%; min-height: 75px; display: flex; align-items: center; justify-content: space-between; }
        .cm-admin-logo { color: #fff; text-decoration: none; font-size: 24px; font-weight: 700; }
        .cm-admin-logo:hover { color: #fff; text-decoration: none; }
        .cm-admin-user { display: flex; align-items: center; gap: 15px; }
        .cm-admin-user span { text-align: right; font-size: 14px; color: #f2f2f2; }
        .cm-admin-user span strong { display: block; margin-bottom: 2px; color: #fff; }
        .cm-btn-sair { padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; background: #fff; color: #4b86b4; border: none; transition: 0.2s; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; }
        .cm-btn-sair:hover { background: #f2f2f2; color: #3b6c95; }
        .container-admin { max-width: 1100px; margin: 35px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.05); width: 92%; flex: 1; }
        h2 { margin-bottom: 20px; color: #111; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; font-size: 14px; }
        th { background: #f2f2f2; }
        .btn-voltar { display: inline-block; background: #f1f1f1; color: #333; padding: 8px 14px; border-radius: 6px; text-decoration: none; margin-bottom: 20px; border: 1px solid #ddd; font-weight: bold; }
        footer { background: #111; color: #aaa; text-align: center; padding: 22px; font-size: 12px; margin-top: auto; }
    </style>
</head>
<body>

<header class="cm-admin-topo">
    <a class="cm-admin-logo" href="painel_funcionario.php">🚲 CicloManos · Funcionário</a>
    <div class="cm-admin-user">
        <span>
            <strong><?= htmlspecialchars($nomeFuncionario) ?></strong>
            <?= htmlspecialchars($cargoFuncionario) ?>
        </span>
        <a class="cm-btn-sair" href="logout.php">Sair</a>
    </div>
</header>

<div class="container-admin">
    <a href="painel_funcionario.php" class="btn-voltar">← Voltar ao painel</a>
    <h2>🏷️ Gerenciamento de Ofertas</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Preço Original</th>
                <th>Preço Oferta</th>
                <th>Desconto</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($o = mysqli_fetch_assoc($ofertas)): ?>
                <tr>
                    <td>#<?= $o['id_oferta'] ?></td>
                    <td><?= htmlspecialchars($o['produto']) ?></td>
                    <td>R$ <?= number_format((float)$o['preco_original'], 2, ',', '.') ?></td>
                    <td>R$ <?= number_format((float)$o['preco_oferta'], 2, ',', '.') ?></td>
                    <td><?= number_format((float)$o['percentual_desconto'], 0) ?>%</td>
                    <td><?= $o['ativo'] ? '<span style="color:green; font-weight:bold;">Ativa</span>' : '<span style="color:red;">Inativa</span>' ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<footer>© <?= date('Y') ?> CicloManos - Painel do Funcionário</footer>

</body>
</html>