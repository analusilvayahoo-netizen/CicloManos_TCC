<?php
require_once __DIR__ . '/config.php';

$codigo = '';
$manutencao = null;
$mensagem = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo'] ?? '');
    if ($codigo === '' || !ctype_digit($codigo)) {
        $mensagem = 'Informe um código de manutenção válido.';
    } else {
        $sql = 'SELECT m.id_manutencao, m.data_entrada, m.entrega_estimada, m.status, dp.nome AS nome_cliente
                FROM manutencao AS m
                INNER JOIN clientes AS c ON c.id_cliente = m.id_cliente
                INNER JOIN dados_pessoais AS dp ON dp.id_dado = c.id_dado
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
            if (!$manutencao) $mensagem = 'Nenhuma manutenção foi encontrada para esse código.';
        }
    }
}

$etapas = ['recebida' => 1, 'em_analise' => 2, 'em_manutencao' => 3, 'pronta' => 4];
$etapaAtual = $manutencao ? ($etapas[$manutencao['status']] ?? 1) : 0;
function e($valor) { return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8'); }
function formatarData($data) { return $data ? date('d/m/Y', strtotime($data)) : '-'; }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title> Manutenção - CicloManos </title>

  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; background: #f5f5f5; color: #222; }
    a { text-decoration: none; color: inherit; }
    .topo { background: #f5f5f5; display: flex; justify-content: space-around; padding: 8px; font-size: 12px; }
    .topo a:hover, .usuario a:hover { color: #e60000; }
    .meio-header { display: flex; align-items: center; justify-content: space-between; padding: 15px 40px; background: white; }
    .logo { height: 120px; } .busca { display: flex; width: 40%; }
    .busca input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px 0 0 5px; }
    .busca button, .rastreio button { padding: 10px 20px; border: none; background: #4A86B8; color: white; cursor: pointer; }
    .busca button { border-radius: 0 5px 5px 0; } .usuario a { display: block; margin: 3px 0; }
    .menu { background: #4A86B8; color: white; display: flex; align-items: center; padding: 10px 40px; gap: 20px; }
    .menu nav a { margin: 0 10px; } .menu nav a:hover { text-decoration: underline; }
    .departamentos { background: #3f719b; padding: 10px 15px; border-radius: 5px; }
    .titulo { text-align: center; margin: 30px; }
    .rastreio { background: white; width: min(700px, 90%); margin: 0 auto 40px; padding: 30px; border-radius: 12px; box-shadow: 0 6px 15px rgba(0,0,0,.1); text-align: center; }
    .rastreio input { width: min(60%, 360px); padding: 12px; margin: 10px 0; border-radius: 5px; border: 1px solid #ccc; }
    .rastreio button { padding: 12px 25px; border-radius: 5px; } .rastreio button:hover { background: #3f719b; }
    .mensagem { margin: 15px 0 0; padding: 12px; border-radius: 5px; background: #fde8e8; color: #9b1c1c; }
    .status { margin-top: 30px; } .detalhes { margin: 14px 0; line-height: 1.7; }
    .etapas { display: flex; justify-content: space-between; gap: 8px; margin-top: 20px; }
    .etapa { flex: 1; padding: 10px 5px; border-radius: 6px; background: #ddd; font-size: 14px; }
    .ativa { background: #4A86B8; color: white; }
    footer { background: #111; color: white; text-align: center; padding: 20px; margin-top: 40px; }
    @media (max-width:650px) { .meio-header, .topo, .menu { padding-left: 15px; padding-right: 15px; } .busca { display:none; } .etapas { flex-direction:column; } .rastreio input { width:100%; } }
  </style>
</head>

<body>

  <div class="topo"><a href="manutencao.php">📍 Rastreie sua manutenção</a><a href="#">💬 Fale conosco</a><span>📱 WhatsApp: (12) 99999-0000</span><span>📞 Telefone: (12) 3721-0000</span></div>
  <header class="meio-header"><a href="ciclomanos.html"><img src="https://i.pinimg.com/736x/88/99/99/889999c134977d6379c48cea6a4ff373.jpg" class="logo" alt="CicloManos"></a><div class="busca"><input type="text" placeholder="Digite o que você procura"><button type="button">Buscar</button></div><div class="usuario"><a href="#">👤 Conta</a><a href="#">🛒 Carrinho</a></div></header>
  <div class="menu"><a href="departamentos.html" class="departamentos">☰ Departamentos</a><nav><a href="acessorios.html">Acessórios</a><a href="bicicletas.html">Bicicletas</a><a href="pecas.html">Peças</a><a href="manutencao.php">Manutenção</a><a href="ofertas.html">Ofertas</a></nav></div>
  <h2 class="titulo">ACOMPANHE SUA MANUTENÇÃO</h2>
  <main class="rastreio">
    <p>Digite o código da sua manutenção:</p>
    <form method="post" action="manutencao.php"><input type="text" id="codigo" name="codigo" value="<?= e($codigo) ?>" inputmode="numeric" required><br><button type="submit">Acompanhar</button></form>
    <?php if ($mensagem): ?><p class="mensagem"><?= e($mensagem) ?></p><?php endif; ?>
    <?php if ($manutencao): ?>
      <section class="status"><h3>Status da bicicleta: <?= e(ucwords(str_replace('_', ' ', $manutencao['status']))) ?></h3>
        <p class="detalhes">Cliente: <strong><?= e($manutencao['nome_cliente']) ?></strong><br>Entrada: <?= formatarData($manutencao['data_entrada']) ?><br>Entrega estimada: <?= formatarData($manutencao['entrega_estimada']) ?></p>
        <div class="etapas"><div class="etapa <?= $etapaAtual >= 1 ? 'ativa' : '' ?>">Recebida</div><div class="etapa <?= $etapaAtual >= 2 ? 'ativa' : '' ?>">Em análise</div><div class="etapa <?= $etapaAtual >= 3 ? 'ativa' : '' ?>">Em manutenção</div><div class="etapa <?= $etapaAtual >= 4 ? 'ativa' : '' ?>">Pronta</div></div>
      </section>
    <?php endif; ?>
  </main>

  <footer><p>© 2026 CicloManos - Todos os direitos reservados</p></footer>

</body>
</html>
