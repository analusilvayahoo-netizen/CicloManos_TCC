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
                    dp.nome AS nome_cliente
                FROM manutencao AS m
                INNER JOIN clientes AS c
                    ON c.id_cliente = m.id_cliente
                INNER JOIN dados_pessoais AS dp
                    ON dp.id_dado = c.id_dado
                WHERE m.id_manutencao = ?';

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $mensagem = 'Não foi possível consultar a manutenção. Verifique se a coluna status foi criada no banco.';
        } else {
            $idManutencao = (int) $codigo;
            $stmt->bind_param('i', $idManutencao);
            $stmt->execute();
            $manutencao = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$manutencao) {
                $mensagem = 'Nenhuma manutenção foi encontrada para esse código.';
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
        .manutencao-container { max-width: 700px; margin: 0 auto; }
        .rastreio-card { background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 16px rgba(0,0,0,0.05); }
        .rastreio-form { display: flex; gap: 10px; justify-content: center; margin-top: 15px; flex-wrap: wrap; }
        .rastreio-form input { padding: 10px 14px; border: 1px solid #ccc; border-radius: 8px; width: 260px; font-size: 14px; }
        .rastreio-form button { background: #111; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; }
        .rastreio-form button:hover { background: #333; }
        .etapas-manutencao { display: flex; justify-content: space-between; margin-top: 25px; gap: 10px; flex-wrap: wrap; }
        .etapa-manutencao { flex: 1; background: #e9ecef; color: #6c757d; padding: 10px; border-radius: 6px; font-size: 13px; font-weight: bold; text-align: center; }
        .etapa-manutencao.ativa { background: #28a745; color: #fff; }
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
            <a href="favoritos.php">⭐ Favoritos</a>
            <a href="carrinho.php">🛒 Carrinho</a>
            <a href="logout.php">🚪 Sair</a>
        <?php elseif (in_array(($_SESSION['tipo_usuario'] ?? ''), ['funcionario', 'admin'], true)): ?>
            <a href="painel_funcionario.php">👤 <?= htmlspecialchars($_SESSION['nome_usuario'] ?? 'Funcionário', ENT_QUOTES, 'UTF-8') ?></a>
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
                <p class="mb-2">Digite o código da sua manutenção:</p>
                <form method="post" action="manutencao.php" class="rastreio-form">
                    <input type="text" id="codigo" name="codigo" value="<?= e($codigo) ?>" inputmode="numeric" placeholder="Código da manutenção" required>
                    <button type="submit">Acompanhar</button>
                </form>

                <?php if ($mensagem): ?>
                    <div class="alert alert-danger mensagem-manutencao mt-3"><?= e($mensagem) ?></div>
                <?php endif; ?>

                <?php if ($manutencao): ?>
                    <section class="status-manutencao mt-4 text-start bg-light p-4 rounded border">
                        <h3 class="fw-bold fs-5 mb-3">Status da bicicleta: <?= e(ucwords(str_replace('_', ' ', $manutencao['status']))) ?></h3>
                        <p class="detalhes-manutencao mb-3">
                            Cliente: <strong><?= e($manutencao['nome_cliente']) ?></strong><br>
                            Entrada: <?= formatarData($manutencao['data_entrada']) ?><br>
                            Entrega estimada: <?= formatarData($manutencao['entrega_estimada']) ?>
                        </p>
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