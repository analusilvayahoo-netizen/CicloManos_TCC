<?php
session_start();
require_once 'config.php';

if (!isset($conn) && isset($conexao)) {
    $conn = $conexao;
}

if (!$conn) {
    die("Erro: Conexão com a base de dados não encontrada. Verifique o seu config.php.");
}

$codigo = '';
$manutencao = null;
$servicosManutencao = [];
$mensagem = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo'] ?? '');

    if ($codigo === '' || !ctype_digit($codigo)) {
        $mensagem = 'Informe um código de manutenção válido.';
    } else {
        $sql = 'SELECT
                    m.id_manutencao,
                    m.data_entrada,
                    m.entrega_estimada,
                    m.status,
                    m.valor_total,
                    m.forma_pagamento,
                    m.status_pagamento,
                    dp.nome AS nome_cliente
                FROM manutencao AS m
                INNER JOIN clientes AS c
                    ON c.id_cliente = m.id_cliente
                INNER JOIN dados_pessoais AS dp
                    ON dp.id_dado = c.id_dado
                WHERE m.id_manutencao = ?';

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $mensagem = 'Não foi possível consultar a manutenção.';
        } else {
            $idManutencao = (int) $codigo;
            $stmt->bind_param('i', $idManutencao);
            $stmt->execute();
            $manutencao = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$manutencao) {
                $mensagem = 'Nenhuma manutenção foi encontrada para esse código.';
            } else {
                // Buscar os serviços associados a esta ordem de manutenção
                $sqlServ = 'SELECT s.nome_servico, sm.valor_unitario 
                            FROM servico_manutencao AS sm 
                            INNER JOIN servicos AS s ON sm.id_servico = s.id_servico 
                            WHERE sm.id_manutencao = ?';
                $stmtServ = $conn->prepare($sqlServ);
                if ($stmtServ) {
                    $stmtServ->bind_param('i', $idManutencao);
                    $stmtServ->execute();
                    $resServ = $stmtServ->get_result();
                    while ($rowServ = $resServ->fetch_assoc()) {
                        $servicosManutencao[] = $rowServ;
                    }
                    $stmtServ->close();
                }
            }
        }
    }
}

$etapas = [
    'recebida' => 1,
    'em_analise' => 2,
    'em_manutencao' => 3,
    'pronta' => 4
];

$etapaAtual = $manutencao
    ? ($etapas[$manutencao['status']] ?? 1)
    : 0;

function e($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function formatarData($data) {
    return $data ? date('d/m/Y', strtotime($data)) : '-';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manutenção - CicloManos</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="manutencao.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; color: #222; min-height: 100vh; display: flex; flex-direction: column; }
        .main-content { flex: 1; padding: 30px 0; }
        .manutencao-container { max-width: 750px; margin: 0 auto; }
        .rastreio-card { background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 16px rgba(0,0,0,0.05); }
        .rastreio-form { display: flex; gap: 10px; justify-content: center; margin-top: 15px; flex-wrap: wrap; }
        .rastreio-form input { padding: 10px 14px; border: 1px solid #ccc; border-radius: 8px; width: 260px; font-size: 14px; }
        .rastreio-form button { background: #111; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; }
        .rastreio-form button:hover { background: #333; }
        .etapas-manutencao { display: flex; justify-content: space-between; margin-top: 25px; gap: 10px; flex-wrap: wrap; }
        .etapa-manutencao { flex: 1; background: #e9ecef; color: #6c757d; padding: 10px; border-radius: 6px; font-size: 13px; font-weight: bold; text-align: center; }
        .etapa-manutencao.ativa { background: #28a745; color: #fff; }
        .badge-pago { background: #28a745; color: white; padding: 3px 8px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-pendente { background: #ffc107; color: #333; padding: 3px 8px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        footer { background: #111; color: #aaa; text-align: center; padding: 22px; font-size: 12px; margin-top: auto; width: 100%; }
    </style>
</head>
<body>

<div class="topo-cinza">
    <a href="https://share.google/jYgrtVLyebEBGaqzt" target="_blank">📍 Localização</a>
    <a href="https://wa.me/551239163262?text=Olá!%20Vim%20pelo%20site%20da%20CicloManos%20e%20gostaria%20de%20mais%20informações." target="_blank">💬 Fale conosco</a>
    <a href="https://wa.me/551239163262" target="_blank">📱 WhatsApp: (12) 3916-3262</a>
    <span>📞 Telefone: (12) 3916-3262</span>
</div>

<div class="meio-header">
    <a href="Index.php"><img src="tcc/logo.jpg" class="logo" alt="Logo CicloManos"></a>
    <form class="busca" method="GET" action="Index.php">
        <input type="text" name="busca" value="<?= htmlspecialchars($_GET['busca'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Digite o que você procura">
        <button type="submit">Buscar</button>
    </form>
    <div class="usuario">
        <?php if (($_SESSION['tipo_usuario'] ?? '') === 'cliente'): ?>
            <a href="minha_conta.php">👤 Olá, <?= htmlspecialchars($_SESSION['nome_usuario'] ?? 'Cliente', ENT_QUOTES, 'UTF-8') ?>!</a>
            <a href="meus_pedidos.php">📦 Meus Pedidos</a>
            <a href="favoritos.php">⭐ Favoritos</a>
            <a href="carrinho.php">🛒 Carrinho</a>
            <a href="logout.php">🚪 Sair</a>
        <?php elseif (in_array(($_SESSION['tipo_usuario'] ?? ''), ['funcionario', 'admin'], true)): ?>
            <a href="painel_funcionario.php">👤 <?= htmlspecialchars($_SESSION['nome_usuario'] ?? 'Funcionário', ENT_QUOTES, 'UTF-8') ?></a>
            <a href="meus_pedidos.php">📦 Meus Pedidos</a>
            <a href="carrinho.php">🛒 Carrinho</a>
            <a href="logout.php">🚪 Sair</a>
        <?php else: ?>
            <a href="login.php">👤 Conta</a>
            <a href="favoritos.php">⭐ Favoritos</a>
            <a href="carrinho.php">🛒 Carrinho</a>
        <?php endif; ?>
    </div>
</div>

<div class="menu-bar">
    <div class="produtos-menu">
        <button class="botao-produtos" onclick="abrirProdutos()">☰ Produtos</button>
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

<main class="main-content">
    <div class="container mb-5">
        <div class="mb-4">
            <a href="Index.php" class="btn btn-secondary">← Voltar para a página inicial</a>
        </div>
        <h2 class="text-center fw-bold mb-4">Acompanhe sua manutenção</h2>

        <div class="manutencao-container">
            <div class="rastreio-card text-center">
                <p class="mb-2">Digite o código da sua manutenção (Ordem de Serviço):</p>
                <form method="post" action="manutencao.php" class="rastreio-form">
                    <input type="text" id="codigo" name="codigo" value="<?= e($codigo) ?>" inputmode="numeric" placeholder="Ex: 1, 2, 3..." required>
                    <button type="submit">Acompanhar</button>
                </form>

                <?php if ($mensagem): ?>
                    <div class="alert alert-danger mensagem-manutencao mt-3"><?= e($mensagem) ?></div>
                <?php endif; ?>

                <?php if ($manutencao): ?>
                    <section class="status-manutencao mt-4 text-start bg-light p-4 rounded border">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="fw-bold fs-5 m-0">OS #<?= e($manutencao['id_manutencao']) ?> - Status: <?= e(ucwords(str_replace('_', ' ', $manutencao['status']))) ?></h3>
                            <?php 
                                $stPag = $manutencao['status_pagamento'] ?? 'pendente';
                                $classePag = ($stPag === 'pago') ? 'badge-pago' : 'badge-pendente';
                            ?>
                            <span class="<?= $classePag ?>">Pagamento: <?= ucfirst($stPag) ?></span>
                        </div>

                        <p class="detalhes-manutencao mb-3">
                            Cliente: <strong><?= e($manutencao['nome_cliente']) ?></strong><br>
                            Data de Entrada: <?= formatarData($manutencao['data_entrada']) ?><br>
                            Previsão de Entrega: <?= formatarData($manutencao['entrega_estimada']) ?><br>
                            Forma de Pagamento: <strong><?= e($manutencao['forma_pagamento'] ?? 'Dinheiro / PIX') ?></strong>
                        </p>

                        <!-- Listagem dos Serviços Realizados -->
                        <div class="mb-3">
                            <h6 class="fw-bold text-secondary">Serviços Executados na Bicicleta:</h6>
                            <ul class="list-group list-group-flush bg-white rounded border">
                                <?php if (!empty($servicosManutencao)): ?>
                                    <?php foreach ($servicosManutencao as $serv): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                            <span><?= e($serv['nome_servico']) ?></span>
                                            <span class="fw-semibold">R$ <?= number_format((float)$serv['valor_unitario'], 2, ',', '.') ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="list-group-item text-muted py-2">Nenhum serviço específico vinculado.</li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <div class="text-end mb-3">
                            <span class="text-muted">Valor Total da Manutenção:</span>
                            <span class="fs-5 fw-bold text-danger">R$ <?= number_format((float)($manutencao['valor_total'] ?? 0), 2, ',', '.') ?></span>
                        </div>

                        <div class="etapas-manutencao">
                            <div class="etapa-manutencao <?= $etapaAtual >= 1 ? 'ativa' : '' ?>">Recebida</div>
                            <div class="etapa-manutencao <?= $etapaAtual >= 2 ? 'ativa' : '' ?>">Em análise</div>
                            <div class="etapa-manutencao <?= $etapaAtual >= 3 ? 'ativa' : '' ?>">Em manutenção</div>
                            <div class="etapa-manutencao <?= $etapaAtual >= 4 ? 'ativa' : '' ?>">Pronta</div>
                        </div>
                    </section>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<footer>
    <p class="mb-0">© <?= date('Y') ?> CicloManos - Todos os direitos reservados.</p>
</footer>

<script>
function abrirProdutos() {
    const caixa = document.getElementById("caixa-produtos");
    caixa.classList.toggle("aberto");
}
document.addEventListener("click", function(event) {
    const produtosMenu = document.querySelector(".produtos-menu");
    const caixa = document.getElementById("caixa-produtos");
    if (produtosMenu && !produtosMenu.contains(event.target)) {
        caixa.classList.remove("aberto");
    }
});
</script>
</body>
</html>