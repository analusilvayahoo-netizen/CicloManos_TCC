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

// Atualizar status do pedido se enviado pelo formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'atualizar_status') {
    $idVendaAlt = (int)$_POST['id_venda'];
    $novoStatus = $_POST['status_pagamento'];
    
    $stmtAlt = mysqli_prepare($conn, "UPDATE vendas SET status_pagamento = ? WHERE id_venda = ?");
    if ($stmtAlt) {
        mysqli_stmt_bind_param($stmtAlt, 'si', $novoStatus, $idVendaAlt);
        mysqli_stmt_execute($stmtAlt);
        mysqli_stmt_close($stmtAlt);
    }
}

$vendas = mysqli_query($conn, "SELECT v.*, dp.nome AS cliente, dp.email FROM vendas v INNER JOIN clientes c ON v.id_cliente = c.id_cliente INNER JOIN dados_pessoais dp ON c.id_dado = dp.id_dado ORDER BY v.id_venda DESC");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Controle de Pedidos - CicloManos</title>
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
        .container-admin { max-width: 1250px; margin: 30px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.05); width: 92%; flex: 1; }
        h2 { margin-bottom: 20px; color: #111; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; font-size: 14px; vertical-align: middle; }
        th { background: #f2f2f2; }
        .badge-aprovado { background: #28a745; color: white; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-pendente { background: #ffc107; color: #333; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-cancelado { background: #dc3545; color: white; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
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
    <h2>📦 Controlo Detalhado de Pedidos</h2>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Pedido</th>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Produtos Comprados</th>
                    <th>Valor Total</th>
                    <th>Pagamento</th>
                    <th>Status</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php while($v = mysqli_fetch_assoc($vendas)): ?>
                    <?php 
                        // Mapeamento correto do status da venda
                        $status = $v['status_pagamento'] ?? 'pendente';
                        $classeBadge = 'badge-pendente';
                        if ($status === 'aprovado' || $status === 'pago') $classeBadge = 'badge-aprovado';
                        if ($status === 'cancelado') $classeBadge = 'badge-cancelado';

                        // Busca detalhada dos itens de cada venda na tabela itens_venda e cicloprodutos
                        $idVendaAtual = (int)$v['id_venda'];
                        $sqlItensV = "SELECT iv.quantidade, iv.valor_unitario, p.produto FROM itens_venda iv INNER JOIN cicloprodutos p ON iv.id = p.id WHERE iv.id_venda = ?";
                        $stmtIV = mysqli_prepare($conn, $sqlItensV);
                        $listaItensStr = "";
                        if ($stmtIV) {
                            mysqli_stmt_bind_param($stmtIV, 'i', $idVendaAtual);
                            mysqli_stmt_execute($stmtIV);
                            $resIV = mysqli_stmt_get_result($stmtIV);
                            while ($it = mysqli_fetch_assoc($resIV)) {
                                $subtotalItem = (float)$it['quantidade'] * (float)$it['valor_unitario'];
                                $listaItensStr .= "• <strong>{$it['quantidade']}x</strong> " . htmlspecialchars($it['produto']) . " (R$ " . number_format($subtotalItem, 2, ',', '.') . ")<br>";
                            }
                            mysqli_stmt_close($stmtIV);
                        }
                        if (empty($listaItensStr)) {
                            $listaItensStr = "<span class='text-muted'>Nenhum item registrado</span>";
                        }
                    ?>
                    <tr>
                        <td class="fw-bold">#<?= $v['id_venda'] ?></td>
                        <td>
                            <?= htmlspecialchars($v['cliente']) ?><br>
                            <small class="text-muted"><?= htmlspecialchars($v['email']) ?></small>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($v['data'])) ?></td>
                        <td style="font-size: 13px; line-height: 1.5;"><?= $listaItensStr ?></td>
                        <td class="fw-bold text-danger">R$ <?= number_format((float)$v['valor_total'], 2, ',', '.') ?></td>
                        <td><?= htmlspecialchars($v['forma_pagamento'] ?? 'PIX / Cartão') ?></td>
                        <td><span class="<?= $classeBadge ?>"><?= ucfirst($status) ?></span></td>
                        <td>
                            <form method="POST" class="d-flex gap-1 align-items-center">
                                <input type="hidden" name="acao" value="atualizar_status">
                                <input type="hidden" name="id_venda" value="<?= $v['id_venda'] ?>">
                                <select name="status_pagamento" class="form-select form-select-sm" style="width: 115px;">
                                    <option value="pendente" <?= $status=='pendente'?'selected':'' ?>>Pendente</option>
                                    <option value="aprovado" <?= ($status=='aprovado'||$status=='pago')?'selected':'' ?>>Aprovado</option>
                                    <option value="cancelado" <?= $status=='cancelado'?'selected':'' ?>>Cancelado</option>
                                </select>
                                <button type="submit" class="btn btn-dark btn-sm">Salvar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<footer>© <?= date('Y') ?> CicloManos - Painel do Funcionário</footer>

</body>
</html>