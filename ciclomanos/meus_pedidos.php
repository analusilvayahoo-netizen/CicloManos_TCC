<?php
session_start();
include 'config.php';
if (!isset($conn) && isset($conexao)) { $conn = $conexao; }

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'cliente') {
    header('Location: login.php');
    exit;
}

$nomeClienteSessao = $_SESSION['nome_usuario'] ?? 'Cliente';
$ehFuncionario = (isset($_SESSION['tipo_usuario']) && in_array($_SESSION['tipo_usuario'], ['funcionario', 'admin'], true));

// Buscar o id_cliente correspondente ao usuário logado
$idCliente = null;
$sqlCli = "SELECT c.id_cliente FROM clientes c INNER JOIN dados_pessoais dp ON c.id_dado = dp.id_dado WHERE dp.email = ? OR dp.nome = ? LIMIT 1";
$stmtC = mysqli_prepare($conn, $sqlCli);
if ($stmtC) {
    $emailSessao = $_SESSION['email_usuario'] ?? '';
    mysqli_stmt_bind_param($stmtC, 'ss', $emailSessao, $nomeClienteSessao);
    mysqli_stmt_execute($stmtC);
    $resC = mysqli_stmt_get_result($stmtC)->fetch_assoc();
    if ($resC) {
        $idCliente = (int)$resC['id_cliente'];
    }
    mysqli_stmt_close($stmtC);
}

if (!$idCliente && isset($_SESSION['id_cliente'])) {
    $idCliente = (int)$_SESSION['id_cliente'];
}

// 1. Buscar as vendas de produtos do cliente
$vendasCliente = [];
if ($idCliente) {
    $sqlVendas = "SELECT * FROM vendas WHERE id_cliente = ? ORDER BY id_venda DESC";
    $stmtV = mysqli_prepare($conn, $sqlVendas);
    if ($stmtV) {
        mysqli_stmt_bind_param($stmtV, 'i', $idCliente);
        mysqli_stmt_execute($stmtV);
        $vendasCliente = mysqli_stmt_get_result($stmtV);
        mysqli_stmt_close($stmtV);
    }
}

// 2. Buscar as ordens de manutenção do cliente
$manutencoesCliente = [];
if ($idCliente) {
    $sqlManut = "SELECT * FROM manutencao WHERE id_cliente = ? ORDER BY id_manutencao DESC";
    $stmtM = mysqli_prepare($conn, $sqlManut);
    if ($stmtM) {
        mysqli_stmt_bind_param($stmtM, 'i', $idCliente);
        mysqli_stmt_execute($stmtM);
        $manutencoesCliente = mysqli_stmt_get_result($stmtM);
        mysqli_stmt_close($stmtM);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Pedidos e Manutenções - CicloManos</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; color: #222; min-height: 100vh; display: flex; flex-direction: column; }
        .main-container { max-width: 950px; margin: 35px auto; width: 92%; flex: 1; }
        .card-pedido { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.04); }
        .badge-aprovado { background: #28a745; color: white; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-pendente { background: #ffc107; color: #333; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-cancelado { background: #dc3545; color: white; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        
        /* Estilização das abas em azul */
        .nav-pills .nav-link.active { 
            background-color: #4b86b4 !important; 
            color: #fff !important; 
        }
        .nav-pills .nav-link:hover { 
            background-color: #3b6c95; 
            color: #fff; 
        }
        .nav-pills .nav-link { 
            color: #333; 
            font-weight: bold; 
        }

        footer { background: #111; color: #aaa; text-align: center; padding: 22px; font-size: 12px; margin-top: auto; }
    </style>
</head>
<body>

<div class="topo-cinza">
    <a href="https://share.google/jYgrtVLyebEBGaqzt" target="_blank">📍 Localização</a>
    <a href="https://wa.me/551239163262" target="_blank">💬 Fale conosco</a>
    <span>📱 WhatsApp: (12) 3916-3262</span>
    <span>📞 Telefone: (12) 3916-3262</span>
</div>

<header class="meio-header">
    <a href="Index.php"><img src="tcc/logo.jpg" class="logo" alt="Logo CicloManos"></a>
    <div class="usuario">
        <a href="minha_conta.php">👤 Olá, <?= htmlspecialchars($nomeClienteSessao) ?>!</a>
        <a href="meus_pedidos.php">📦 Meus Pedidos</a>
        <a href="favoritos.php">⭐ Favoritos</a>
        <a href="carrinho.php">🛒 Carrinho</a>
        <a href="logout.php">🚪 Sair</a>
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

<?php if ($ehFuncionario): ?>
    <div style="background: #111; color: #fff; padding: 10px 20px; text-align: center; font-weight: bold; font-size: 14px; border-bottom: 1px solid #333;">
        ⚙️ Você está navegando como funcionário. 
        <a href="painel_funcionario.php" style="color: #4b86b4; text-decoration: underline; margin-left: 10px;">Voltar ao Painel do Funcionário →</a>
    </div>
<?php endif; ?>

<div class="main-container">
    <div class="mb-4">
        <a href="Index.php" class="btn btn-secondary">← Voltar para a página inicial</a>
    </div>

    <h2 class="fw-bold mb-4">📦 Minhas Compras & Manutenções</h2>

    <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-compras-tab" data-bs-toggle="pill" data-bs-target="#pills-compras" type="button" role="tab">🛒 Pedidos de Produtos</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-manutencao-tab" data-bs-toggle="pill" data-bs-target="#pills-manutencao" type="button" role="tab">🛠️ Serviços de Oficina</button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        <!-- ABA 1: PEDIDOS DE PRODUTOS -->
        <div class="tab-pane fade show active" id="pills-compras" role="tabpanel">
            <?php if ($vendasCliente && mysqli_num_rows($vendasCliente) > 0): ?>
                <?php while ($v = mysqli_fetch_assoc($vendasCliente)): ?>
                    <?php 
                        $status = $v['status_pagamento'] ?? 'pendente';
                        $classeBadge = 'badge-pendente';
                        if ($status === 'aprovado' || $status === 'pago') $classeBadge = 'badge-aprovado';
                        if ($status === 'cancelado') $classeBadge = 'badge-cancelado';

                        $idVenda = (int)$v['id_venda'];
                        $sqlItens = "SELECT iv.quantidade, iv.valor_unitario, p.produto, p.imagem 
                                     FROM itens_venda iv 
                                     INNER JOIN cicloprodutos p ON iv.id = p.id 
                                     WHERE iv.id_venda = ?";
                        $stmtI = mysqli_prepare($conn, $sqlItens);
                        $itensVenda = null;
                        if ($stmtI) {
                            mysqli_stmt_bind_param($stmtI, 'i', $idVenda);
                            mysqli_stmt_execute($stmtI);
                            $itensVenda = mysqli_stmt_get_result($stmtI);
                        }
                    ?>
                    <div class="card-pedido">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1">Pedido #<?= $v['id_venda'] ?></h5>
                                <small class="text-muted">Data: <?= date('d/m/Y H:i', strtotime($v['data'])) ?></small>
                            </div>
                            <div>
                                <span class="<?= $classeBadge ?>"><?= ucfirst($status) ?></span>
                            </div>
                        </div>

                        <p class="mb-2"><strong>Forma de Pagamento:</strong> <?= htmlspecialchars($v['forma_pagamento'] ?? 'Cartão / PIX') ?></p>

                        <div class="mt-3">
                            <h6 class="fw-bold text-secondary mb-2">Itens do Pedido:</h6>
                            <ul class="list-group list-group-flush">
                                <?php if ($itensVenda && mysqli_num_rows($itensVenda) > 0): ?>
                                    <?php while ($item = mysqli_fetch_assoc($itensVenda)): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <div class="d-flex align-items-center gap-3">
                                                <?php if (!empty($item['imagem'])): ?>
                                                    <img src="<?= htmlspecialchars($item['imagem']) ?>" alt="" style="width: 45px; height: 45px; object-fit: contain;">
                                                <?php endif; ?>
                                                <div>
                                                    <span class="fw-semibold"><?= htmlspecialchars($item['produto']) ?></span><br>
                                                    <small class="text-muted">Qtd: <?= $item['quantidade'] ?> x R$ <?= number_format((float)$item['valor_unitario'], 2, ',', '.') ?></small>
                                                </div>
                                            </div>
                                            <span class="fw-bold">R$ <?= number_format((float)($item['quantidade'] * $item['valor_unitario']), 2, ',', '.') ?></span>
                                        </li>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <li class="list-group-item px-0 text-muted"><em>Detalhes processados na loja.</em></li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="text-end mt-3 pt-2 border-top">
                            <span class="fs-6 text-muted">Valor Total:</span> 
                            <span class="fs-5 fw-bold text-danger">R$ <?= number_format((float)$v['valor_total'], 2, ',', '.') ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-warning text-center p-4">Você ainda não realizou nenhum pedido de produtos.</div>
            <?php endif; ?>
        </div>

        <!-- ABA 2: ORDENS DE MANUTENÇÃO DA OFICINA -->
        <div class="tab-pane fade" id="pills-manutencao" role="tabpanel">
            <?php if ($manutencoesCliente && mysqli_num_rows($manutencoesCliente) > 0): ?>
                <?php while ($m = mysqli_fetch_assoc($manutencoesCliente)): ?>
                    <?php 
                        $stPag = $m['status_pagamento'] ?? 'pendente';
                        $classePag = ($stPag === 'pago') ? 'badge-aprovado' : 'badge-pendente';

                        $idManut = (int)$m['id_manutencao'];
                        $sqlServM = "SELECT s.nome_servico, sm.valor_unitario FROM servico_manutencao sm INNER JOIN servicos s ON sm.id_servico = s.id_servico WHERE sm.id_manutencao = ?";
                        $stmtSM = mysqli_prepare($conn, $sqlServM);
                        $servicosOS = [];
                        if ($stmtSM) {
                            mysqli_stmt_bind_param($stmtSM, 'i', $idManut);
                            mysqli_stmt_execute($stmtSM);
                            $resSM = mysqli_stmt_get_result($stmtSM);
                            while ($sRow = mysqli_fetch_assoc($resSM)) {
                                $servicosOS[] = $sRow;
                            }
                            mysqli_stmt_close($stmtSM);
                        }
                    ?>
                    <div class="card-pedido">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1">Ordem de Serviço #<?= $m['id_manutencao'] ?></h5>
                                <small class="text-muted">Data de Entrada: <?= date('d/m/Y', strtotime($m['data_entrada'])) ?></small>
                            </div>
                            <div class="d-flex gap-2">
                                <span class="badge bg-secondary"><?= ucwords(str_replace('_', ' ', $m['status'])) ?></span>
                                <span class="<?= $classePag ?>">Pagamento: <?= ucfirst($stPag) ?></span>
                            </div>
                        </div>

                        <p class="mb-2"><strong>Previsão de Entrega:</strong> <?= !empty($m['entrega_estimada']) ? date('d/m/Y', strtotime($m['entrega_estimada'])) : 'A definir' ?></p>
                        <p class="mb-2"><strong>Forma de Pagamento:</strong> <?= htmlspecialchars($m['forma_pagamento'] ?? 'Dinheiro / PIX') ?></p>

                        <div class="mt-3">
                            <h6 class="fw-bold text-secondary mb-2">Serviços Executados na Bicicleta:</h6>
                            <ul class="list-group list-group-flush">
                                <?php if (!empty($servicosOS)): ?>
                                    <?php foreach ($servicosOS as $serv): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-1">
                                            <span>• <?= htmlspecialchars($serv['nome_servico']) ?></span>
                                            <span class="fw-semibold">R$ <?= number_format((float)$serv['valor_unitario'], 2, ',', '.') ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="list-group-item px-0 text-muted"><em>Nenhum serviço específico vinculado.</em></li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                            <a href="manutencao.php" class="btn btn-outline-dark btn-sm">🔍 Rastrear no Site</a>
                            <div>
                                <span class="fs-6 text-muted">Valor Total da Manutenção:</span> 
                                <span class="fs-5 fw-bold text-danger">R$ <?= number_format((float)($m['valor_total'] ?? 0), 2, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info text-center p-4">Você não possui nenhuma ordem de manutenção registrada.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer>
    © <?= date('Y') ?> CicloManos · Todos os direitos reservados
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
function abrirProdutos() {
    document.getElementById("caixa-produtos").classList.toggle("aberto");
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