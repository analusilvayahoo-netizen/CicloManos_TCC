<?php
session_start();
require_once 'config.php';
if (!isset($conn) && isset($conexao)) { $conn = $conexao; }
if (!isset($conn) || !$conn) { http_response_code(500); exit('Não foi possível conectar ao banco de dados.'); }
if (($_SESSION['tipo_usuario'] ?? '') !== 'cliente' || empty($_SESSION['id_cliente'])) {
    header('Location: login.php'); exit;
}
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$idCliente = (int)$_SESSION['id_cliente'];
$stmt = $conn->prepare('SELECT dp.nome, dp.email, dp.cpf, en.rua, en.cep, b.nome_bairro
    FROM clientes c
    INNER JOIN dados_pessoais dp ON dp.id_dado = c.id_dado
    LEFT JOIN enderecos en ON en.id_endereco = dp.id_endereco
    LEFT JOIN bairros b ON b.id_bairro = en.id_bairro
    WHERE c.id_cliente = ? LIMIT 1');
$stmt->bind_param('i', $idCliente); $stmt->execute(); $perfil = $stmt->get_result()->fetch_assoc(); $stmt->close();
$stmt = $conn->prepare('SELECT id_manutencao, data_entrada, entrega_estimada, status FROM manutencao WHERE id_cliente = ? ORDER BY id_manutencao DESC');
$stmt->bind_param('i', $idCliente); $stmt->execute(); $manutencoes = $stmt->get_result();
$stmtV = $conn->prepare('SELECT id_venda, data, valor_total, status_pagamento FROM vendas WHERE id_cliente = ? ORDER BY id_venda DESC');
$stmtV->bind_param('i', $idCliente); $stmtV->execute(); $vendas = $stmtV->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Minha conta - CicloManos</title>
        <link rel="stylesheet" href="style.css">
<style>
.conta-cliente{max-width:1050px;margin:36px auto;padding:0 18px}
.painel-conta{background:#fff;border-radius:12px;padding:24px;margin:18px 0;box-shadow:0 4px 14px rgba(0,0,0,.08)}
.painel-conta h2{color:#244f70;margin-top:0}
.dados-conta{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:12px}
.dado-conta{background:#f5f8fa;padding:12px;border-radius:8px;overflow-wrap:anywhere}
.tabela-conta{width:100%;border-collapse:collapse}
.tabela-conta th,.tabela-conta td{text-align:left;padding:10px;border-bottom:1px solid #e5e5e5}
.tabela-wrap{overflow-x:auto}
.sair-conta{display:inline-block;background:#4A86B8;color:#fff;padding:10px 16px;border-radius:7px}@media(max-width:600px){.painel-conta{padding:16px}.tabela-conta th,.tabela-conta td{padding:8px;font-size:.9rem}}</style></head><body>

<div class="topo-cinza">
    <a href="https://share.google/jYgrtVLyebEBGaqzt" target="_blank" rel="noopener">📍 Localização</a>
    <a href="https://wa.me/551239163262" target="_blank" rel="noopener">💬 Fale conosco</a>
    <span>📱 WhatsApp: (12) 3916-3262</span>
    <span>📞 Telefone: (12) 3916-3262</span>
</div>

<header class="meio-header">
    <a href="index.php">
        <img src="tcc/logo.jpg" class="logo" alt="Logo CicloManos"></a>
        <div class="usuario">
            <a href="minha_conta.php">👤 Olá, <?= e($_SESSION['nome_usuario'] ?? 'Cliente') ?></a>
            <a href="logout.php">Sair da conta</a>
            <a href="carrinho.php">🛒 Carrinho</a>
        </div>
    </header>

<div class="menu-bar">
    <div class="produtos-menu">
        <button type="button" class="botao-produtos" onclick="abrirProdutos(event)">☰ Produtos</button><div id="caixa-produtos" class="caixa-produtos"><a href="acessorios.php">Acessórios</a><a href="pecas.php">Peças</a><a href="bicicletas.php">Bicicletas</a></div></div><nav><a href="manutencao.php">Manutenção</a><a href="ofertas.php">Ofertas</a></nav></div>

        <main class="conta-cliente">
            <h1>Minha conta</h1>
            <p>Seus dados e registros salvos no CicloManos.</p>
            
<section class="painel-conta"><h2>Dados cadastrados</h2><?php if (!$perfil): ?><p>Não foi possível encontrar os dados desta conta.</p><?php else: ?><div class="dados-conta"><div class="dado-conta"><strong>Nome</strong><br><?= e($perfil['nome']) ?></div><div class="dado-conta"><strong>E-mail</strong><br><?= e($perfil['email']) ?></div><div class="dado-conta"><strong>CPF</strong><br><?= e($perfil['cpf']) ?></div><div class="dado-conta"><strong>Rua</strong><br><?= e($perfil['rua'] ?? 'Não informado') ?></div><div class="dado-conta"><strong>CEP</strong><br><?= e($perfil['cep'] ?? 'Não informado') ?></div><div class="dado-conta"><strong>Bairro</strong><br><?= e($perfil['nome_bairro'] ?? 'Não informado') ?></div></div><?php endif; ?></section>
<section class="painel-conta"><h2>Minhas manutenções</h2><?php if ($manutencoes->num_rows === 0): ?><p>Você ainda não tem manutenções registradas nesta conta.</p><?php else: ?><div class="tabela-wrap"><table class="tabela-conta"><thead><tr><th>Código</th><th>Entrada</th><th>Entrega estimada</th><th>Status</th></tr></thead><tbody><?php while($m=$manutencoes->fetch_assoc()): ?><tr><td><?= (int)$m['id_manutencao'] ?></td><td><?= e($m['data_entrada'] ? date('d/m/Y',strtotime($m['data_entrada'])) : '-') ?></td><td><?= e($m['entrega_estimada'] ? date('d/m/Y',strtotime($m['entrega_estimada'])) : '-') ?></td><td><?= e(ucwords(str_replace('_',' ',$m['status']))) ?></td></tr><?php endwhile; ?></tbody></table></div><?php endif; ?></section>
<section class="painel-conta"><h2>Meus pedidos</h2><?php if ($vendas->num_rows === 0): ?><p>Você ainda não tem pedidos registrados. Os pedidos aparecerão aqui quando o fluxo de compra for integrado ao banco.</p><?php else: ?><div class="tabela-wrap"><table class="tabela-conta"><thead><tr><th>Pedido</th><th>Data</th><th>Total</th><th>Pagamento</th></tr></thead><tbody><?php while($v=$vendas->fetch_assoc()): ?><tr><td>#<?= (int)$v['id_venda'] ?></td><td><?= e(date('d/m/Y',strtotime($v['data']))) ?></td><td>R$ <?= number_format((float)$v['valor_total'],2,',','.') ?></td><td><?= e(ucfirst($v['status_pagamento'] ?? 'pendente')) ?></td></tr><?php endwhile; ?></tbody></table></div><?php endif; ?></section>
<a class="sair-conta" href="logout.php">Sair da conta</a></main><footer><p>© <?= date('Y') ?> CicloManos - Todos os direitos reservados.</p></footer>
<script>function abrirProdutos(event){if(event)event.stopPropagation();document.getElementById('caixa-produtos')?.classList.toggle('aberto');}document.addEventListener('click',function(event){const menu=document.querySelector('.produtos-menu');if(menu&&!menu.contains(event.target))document.getElementById('caixa-produtos')?.classList.remove('aberto');});</script></body></html>
