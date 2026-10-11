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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_manutencao'], $_POST['status'])) {
    $idM = (int)$_POST['id_manutencao'];
    $novoStatus = trim($_POST['status']);
    $stmt = mysqli_prepare($conn, "UPDATE manutencao SET status = ? WHERE id_manutencao = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'si', $novoStatus, $idM);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header('Location: admin_manutencao.php');
    exit;
}

$manutencoes = mysqli_query($conn, "SELECT m.*, dp.nome AS cliente FROM manutencao m INNER JOIN clientes c ON m.id_cliente = c.id_cliente INNER JOIN dados_pessoais dp ON c.id_dado = dp.id_dado ORDER BY m.id_manutencao DESC");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Manutenções - CicloManos</title>
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
        select { padding: 6px; border-radius: 6px; border: 1px solid #ccc; }
        button { background: #111; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; }
        button:hover { background: #333; }
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
    <h2>🛠️ Ordens de Serviço de Manutenção</h2>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Cliente</th>
                <th>Data Entrada</th>
                <th>Previsão</th>
                <th>Status Atual</th>
                <th>Ação / Alterar</th>
            </tr>
        </thead>
        <tbody>
            <?php while($m = mysqli_fetch_assoc($manutencoes)): ?>
                <tr>
                    <td>#<?= $m['id_manutencao'] ?></td>
                    <td><?= htmlspecialchars($m['cliente']) ?></td>
                    <td><?= date('d/m/Y', strtotime($m['data_entrada'])) ?></td>
                    <td><?= $m['entrega_estimada'] ? date('d/m/Y', strtotime($m['entrega_estimada'])) : '-' ?></td>
                    <td><b><?= ucwords(str_replace('_', ' ', $m['status'])) ?></b></td>
                    <td>
                        <form method="POST" style="display:flex; gap:8px; align-items:center;">
                            <input type="hidden" name="id_manutencao" value="<?= $m['id_manutencao'] ?>">
                            <select name="status">
                                <option value="recebida" <?= $m['status']=='recebida'?'selected':'' ?>>Recebida</option>
                                <option value="em_analise" <?= $m['status']=='em_analise'?'selected':'' ?>>Em análise</option>
                                <option value="em_manutencao" <?= $m['status']=='em_manutencao'?'selected':'' ?>>Em manutenção</option>
                                <option value="pronta" <?= $m['status']=='pronta'?'selected':'' ?>>Pronta</option>
                            </select>
                            <button type="submit">Salvar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<footer>© <?= date('Y') ?> CicloManos - Painel do Funcionário</footer>

</body>
</html>