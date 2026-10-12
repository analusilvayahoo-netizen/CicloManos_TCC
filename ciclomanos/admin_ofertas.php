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
$mensagem = '';

// Processar Cadastro de Nova Oferta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'cadastrar_oferta') {
    $id_produto = (int)$_POST['id_produto'];
    $preco_original = (float)$_POST['preco_original'];
    $preco_oferta = (float)$_POST['preco_oferta'];
    $percentual_desconto = !empty($_POST['percentual_desconto']) ? (float)$_POST['percentual_desconto'] : NULL;
    $data_inicio = !empty($_POST['data_inicio']) ? $_POST['data_inicio'] : NULL;
    $data_fim = !empty($_POST['data_fim']) ? $_POST['data_fim'] : NULL;
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    $stmt = mysqli_prepare($conn, "INSERT INTO ofertas (id_produto, preco_original, preco_oferta, percentual_desconto, data_inicio, data_fim, ativo) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'idddssi', $id_produto, $preco_original, $preco_oferta, $percentual_desconto, $data_inicio, $data_fim, $ativo);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $mensagem = "Oferta cadastrada com sucesso!";
    }
}

// Buscar produtos disponíveis para cadastrar nas ofertas
$produtos = mysqli_query($conn, "SELECT id, produto, preco_venda FROM cicloprodutos ORDER BY produto ASC");

// Buscar ofertas cadastradas
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
        h2, h3 { color: #111; font-weight: bold; }
        .card-form { background: #fdfdfd; border: 1px solid #e3e3e3; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; font-size: 14px; }
        th { background: #f2f2f2; }
        button { background: #111; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; }
        button:hover { background: #333; }
        .btn-voltar { display: inline-block; background: #f1f1f1; color: #333; padding: 8px 14px; border-radius: 6px; text-decoration: none; margin-bottom: 20px; border: 1px solid #ddd; font-weight: bold; }
        footer { background: #111; color: #aaa; text-align: center; padding: 22px; font-size: 12px; margin-top: auto; }
    </style>
    <script>
        // Preenche automaticamente o preço original quando o produto é selecionado
        function selecionarProduto(select) {
            let option = select.options[select.selectedIndex];
            let precoVenda = option.getAttribute('data-preco');
            if (precoVenda) {
                document.getElementById('preco_original').value = precoVenda;
                calcularPorPrecoOferta(); // Atualiza caso já haja desconto/oferta calculada
            }
        }

        // Calcula o preço da oferta com base no percentual de desconto inserido
        function calcularPorDesconto() {
            let precoOrig = parseFloat(document.getElementById('preco_original').value) || 0;
            let desconto = parseFloat(document.getElementById('percentual_desconto').value) || 0;

            if (precoOrig > 0 && desconto >= 0) {
                let precoOfe = precoOrig - (precoOrig * (desconto / 100));
                document.getElementById('preco_oferta').value = precoOfe.toFixed(2);
            }
        }

        // Calcula o percentual de desconto com base no preço da oferta inserido
        function calcularPorPrecoOferta() {
            let precoOrig = parseFloat(document.getElementById('preco_original').value) || 0;
            let precoOfe = parseFloat(document.getElementById('preco_oferta').value) || 0;

            if (precoOrig > 0 && precoOfe > 0 && precoOrig >= precoOfe) {
                let desconto = ((precoOrig - precoOfe) / precoOrig) * 100;
                document.getElementById('percentual_desconto').value = desconto.toFixed(2);
            }
        }
    </script>
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

    <?php if(!empty($mensagem)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensagem) ?></div>
    <?php endif; ?>

    <!-- Formulário de cadastro de ofertas com cálculos automáticos -->
    <div class="card-form">
        <h3>Cadastrar Nova Oferta</h3>
        <form method="POST" class="row g-3 mt-2">
            <input type="hidden" name="acao" value="cadastrar_oferta">
            
            <div class="col-md-5">
                <label class="form-label">Produto</label>
                <select name="id_produto" class="form-select" onchange="selecionarProduto(this)" required>
                    <option value="">Selecione o produto...</option>
                    <?php while($p = mysqli_fetch_assoc($produtos)): ?>
                        <option value="<?= $p['id'] ?>" data-preco="<?= $p['preco_venda'] ?>">
                            <?= htmlspecialchars($p['produto']) ?> (R$ <?= number_format($p['preco_venda'], 2, ',', '.') ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Preço Original (R$)</label>
                <input type="number" step="0.01" name="preco_original" id="preco_original" class="form-control" oninput="calcularPorPrecoOferta()" required>
            </div>

            <div class="col-md-2">
                <label class="form-label">Desconto (%)</label>
                <input type="number" step="0.01" name="percentual_desconto" id="percentual_desconto" class="form-control" oninput="calcularPorDesconto()" placeholder="Ex: 15">
            </div>

            <div class="col-md-2">
                <label class="form-label">Preço Oferta (R$)</label>
                <input type="number" step="0.01" name="preco_oferta" id="preco_oferta" class="form-control" oninput="calcularPorPrecoOferta()" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Data Início</label>
                <input type="date" name="data_inicio" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Data Fim</label>
                <input type="date" name="data_fim" class="form-control">
            </div>

            <div class="col-md-3 d-flex align-items-center">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="ativo" id="ativo" value="1" checked>
                    <label class="form-check-label" for="ativo">Oferta Ativa</label>
                </div>
            </div>

            <div class="col-12 mt-3">
                <button type="submit">Salvar Oferta</button>
            </div>
        </form>
    </div>

    <hr class="my-4">

    <h3>Ofertas Cadastradas</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Preço Original</th>
                <th>Preço Oferta</th>
                <th>Desconto</th>
                <th>Início / Fim</th>
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
                    <td><?= number_format((float)$o['percentual_desconto'], 1) ?>%</td>
                    <td><?= $o['data_inicio'] ? date('d/m/Y', strtotime($o['data_inicio'])) : '-' ?> até <?= $o['data_fim'] ? date('d/m/Y', strtotime($o['data_fim'])) : '-' ?></td>
                    <td><?= $o['ativo'] ? '<span style="color:green; font-weight:bold;">Ativa</span>' : '<span style="color:red;">Inativa</span>' ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<footer>© <?= date('Y') ?> CicloManos - Painel do Funcionário</footer>

</body>
</html>