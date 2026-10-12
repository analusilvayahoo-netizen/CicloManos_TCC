<?php
session_start();
include 'config.php';
if (!isset($conn) && isset($conexao)) { $conn =$conexao; }

if (!isset($_SESSION['tipo_usuario'])) {
    header('Location: login_funcionario.php');
    exit;
}

if ($_SESSION['tipo_usuario'] !== 'funcionario') {
    header('Location: login_funcionario.php');
    exit;
}

$nomeFuncionario =$_SESSION['nome_usuario'] ?? 'Funcionário';
$cargoFuncionario =$_SESSION['cargo_funcionario'] ?? 'Funcionário';

$mensagem = '';
$linkWhatsApp = '';$statusEmail = '';

// Processar Cadastro de Nova Manutenção com Cálculo de Valores e Serviços
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acao'])) {
        if ($_POST['acao'] === 'cadastrar') {$id_cliente = (int)$_POST['id_cliente'];$data_entrada = $_POST['data_entrada'];$entrega_estimada = !empty($_POST['entrega_estimada']) ?$_POST['entrega_estimada'] : NULL;
            $status = 'recebida';$status_pagamento = 'pendente';
            $forma_pagamento =$_POST['forma_pagamento'] ?? 'Dinheiro / PIX';
            $servicosSelecionados =$_POST['servicos'] ?? [];

            // Inserir inicialmente com valor total zero para obter o ID da manutenção
            $stmt = mysqli_prepare($conn, "INSERT INTO manutencao (id_cliente, data_entrada, entrega_estimada, status, valor_total, forma_pagamento, status_pagamento) VALUES (?, ?, ?, ?, 0.00, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'isssss', $id_cliente,$data_entrada, $entrega_estimada,$status, $forma_pagamento,$status_pagamento);
                mysqli_stmt_execute($stmt);
                $novoIdManutencao = mysqli_stmt_insert_id($stmt);
                mysqli_stmt_close($stmt);

                $valorTotalManutencao = 0;

                // Inserir os serviços selecionados na tabela servico_manutencao e somar o total
                if (!empty($servicosSelecionados)) {
                    foreach ($servicosSelecionados as$id_servico) {
                        $id_servico = (int)$id_servico;
                        $resServ = mysqli_query($conn, "SELECT valor_base FROM servicos WHERE id_servico = $id_servico LIMIT 1");
                        if ($resServ) {
                            $rowS = mysqli_fetch_assoc($resServ);
                            if ($rowS) {$valorUnitario = (float)$rowS['valor_base'];$quantidade = 1;
                                $valorTotalManutencao +=$valorUnitario;

                                $stmtSM = mysqli_prepare($conn, "INSERT INTO servico_manutencao (id_manutencao, id_servico, quantidade, valor_unitario) VALUES (?, ?, ?, ?)");
                                if ($stmtSM) {
                                    mysqli_stmt_bind_param($stmtSM, 'iiid', $novoIdManutencao,$id_servico, $quantidade,$valorUnitario);
                                    mysqli_stmt_execute($stmtSM);
                                    mysqli_stmt_close($stmtSM);
                                }
                            }
                        }
                    }
                }

                // Atualizar o valor total calculado na tabela manutenção
                $stmtUpdVal = mysqli_prepare($conn, "UPDATE manutencao SET valor_total = ? WHERE id_manutencao = ?");
                if ($stmtUpdVal) {
                    mysqli_stmt_bind_param($stmtUpdVal, 'di', $valorTotalManutencao,$novoIdManutencao);
                    mysqli_stmt_execute($stmtUpdVal);
                    mysqli_stmt_close($stmtUpdVal);
                }

                // Buscar dados do cliente para notificações
                $sqlCliente = "SELECT dp.nome, dp.email, t.numero_telefone 
                               FROM clientes c 
                               INNER JOIN dados_pessoais dp ON c.id_dado = dp.id_dado 
                               LEFT JOIN telefones t ON dp.id_dado = t.id_dado 
                               WHERE c.id_cliente = ? LIMIT 1";
                $stmtC = mysqli_prepare($conn,$sqlCliente);
                if ($stmtC) {
                    mysqli_stmt_bind_param($stmtC, 'i',$id_cliente);
                    mysqli_stmt_execute($stmtC);
                    $resC = mysqli_stmt_get_result($stmtC)->fetch_assoc();
                    mysqli_stmt_close($stmtC);

                    if ($resC) {
                        $nomeCliente =$resC['nome'];
                        $emailCliente =$resC['email'];
                        $telefoneCliente = preg_replace('/[^0-9]/', '',$resC['numero_telefone'] ?? '');

                        $msgWhats = urlencode("Olá, {$nomeCliente}! A sua bicicleta deu entrada na oficina da CicloManos. OS #{$novoIdManutencao}. Valor estimado: R$ " . number_format($valorTotalManutencao, 2, ',', '.') . ". Acompanhe em nosso site!");
                        if (!empty($telefoneCliente)) {$linkWhatsApp = "https://wa.me/55{$telefoneCliente}?text={$msgWhats}";
                        }

                        if (!empty($emailCliente)) {$assunto = "CicloManos - Ordem de Manutenção #{$novoIdManutencao}";
                            $corpoEmail = "Olá, {$nomeCliente}!\n\nA sua bicicleta foi recebida na oficina da CicloManos.\nOrdem de Serviço: #{$novoIdManutencao}\nValor Total: R$ " . number_format($valorTotalManutencao, 2, ',', '.') . "\n\nPode acompanhar o andamento em tempo real em nosso site.\n\nAtenciosamente,\nEquipe CicloManos";
                            $headers = "From: no-reply@ciclomanos.com\r\n" .
                                       "Reply-Box: suporte@ciclomanos.com\r\n" .
                                       "X-Mailer: PHP/" . phpversion();

                            @mail($emailCliente, $assunto,$corpoEmail, $headers);$statusEmail = " e e-mail enviado para {$emailCliente}";
                        }
                    }
                }

                $mensagem = "Ordem de Manutenção #{$novoIdManutencao} cadastrada com sucesso{$statusEmail}!";
            }
        }

        // Processar Atualização de Manutenção Existente (Status, Previsão e Pagamento)
        if ($_POST['acao'] === 'atualizar') {$idM = (int)$_POST['id_manutencao'];$novoStatus = trim($_POST['status']);$novoStatusPagamento = trim($_POST['status_pagamento']);$novaFormaPagamento = trim($_POST['forma_pagamento']);$entrega_estimada = !empty($_POST['entrega_estimada']) ?$_POST['entrega_estimada'] : NULL;

            $stmt = mysqli_prepare($conn, "UPDATE manutencao SET status = ?, status_pagamento = ?, forma_pagamento = ?, entrega_estimada = ? WHERE id_manutencao = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ssssi',$novoStatus, $novoStatusPagamento,$novaFormaPagamento, $entrega_estimada,$idM);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $mensagem = "Manutenção #{$idM} atualizada com sucesso!";
            }
        }
    }
}

// Buscar Clientes, Serviços e Manutenções (Calculando o total dinamicamente caso o campo esteja zero)
$clientes = mysqli_query($conn, "SELECT c.id_cliente, dp.nome FROM clientes c INNER JOIN dados_pessoais dp ON c.id_dado = dp.id_dado ORDER BY dp.nome ASC");
$listaServicos = mysqli_query($conn, "SELECT * FROM servicos ORDER BY nome_servico ASC");
$manutencoes = mysqli_query($conn, "SELECT m.*, dp.nome AS cliente, 
    (SELECT COALESCE(SUM(sm.valor_unitario * sm.quantidade), 0) FROM servico_manutencao sm WHERE sm.id_manutencao = m.id_manutencao) AS calc_total 
    FROM manutencao m 
    INNER JOIN clientes c ON m.id_cliente = c.id_cliente 
    INNER JOIN dados_pessoais dp ON c.id_dado = dp.id_dado 
    ORDER BY m.id_manutencao DESC");
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
        .container-admin { max-width: 1250px; margin: 35px auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.05); width: 94%; flex: 1; }
        h2, h3 { color: #111; font-weight: bold; }
        .card-form { background: #fdfdfd; border: 1px solid #e3e3e3; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; font-size: 13px; vertical-align: middle; }
        th { background: #f2f2f2; }
        select, input { padding: 5px; border-radius: 6px; border: 1px solid #ccc; font-size: 13px; }
        button { background: #111; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; }
        button:hover { background: #333; }
        .btn-voltar { display: inline-block; background: #f1f1f1; color: #333; padding: 8px 14px; border-radius: 6px; text-decoration: none; margin-bottom: 20px; border: 1px solid #ddd; font-weight: bold; }
        .badge-pago { background: #28a745; color: white; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .badge-pendente { background: #ffc107; color: #333; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: bold; }
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
    <h2>🛠️ Controlo de Manutenções e Pagamentos da Oficina</h2>

    <?php if(!empty($mensagem)): ?>
        <div class="alert alert-success d-flex justify-content-between align-items-center">
            <div><?= htmlspecialchars($mensagem) ?></div>
            <?php if(!empty($linkWhatsApp)): ?>
                <a href="<?= $linkWhatsApp ?>" target="_blank" class="btn btn-success btn-sm">💬 Enviar código por WhatsApp</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Formulário para cadastrar nova manutenção com seleção de serviços -->
    <div class="card-form">
        <h3>Nova Ordem de Manutenção</h3>
        <form method="POST" class="row g-3 mt-2">
            <input type="hidden" name="acao" value="cadastrar">
            <div class="col-md-3">
                <label class="form-label">Cliente</label>
                <select name="id_cliente" class="form-select" required>
                    <option value="">Selecione...</option>
                    <?php while($c = mysqli_fetch_assoc($clientes)): ?>
                        <option value="<?= $c['id_cliente'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Data de Entrada</label>
                <input type="date" name="data_entrada" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Previsão de Entrega</label>
                <input type="date" name="entrega_estimada" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Forma de Pagamento</label>
                <select name="forma_pagamento" class="form-select">
                    <option value="PIX">PIX</option>
                    <option value="Cartão de Crédito">Cartão de Crédito</option>
                    <option value="Cartão de Débito">Cartão de Débito</option>
                    <option value="Dinheiro">Dinheiro</option>
                </select>
            </div>

            <!-- Seleção de Serviços da Oficina -->
            <div class="col-12 mt-3">
                <label class="form-label fw-bold">Selecione os Serviços da Oficina:</label>
                <div class="row g-2 bg-white p-3 border rounded">
                    <?php if ($listaServicos && mysqli_num_rows($listaServicos) > 0): ?>
                        <?php while($serv = mysqli_fetch_assoc($listaServicos)): ?>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="servicos[]" value="<?= $serv['id_servico'] ?>" id="serv_<?= $serv['id_servico'] ?>">
                                    <label class="form-check-label" for="serv_<?= $serv['id_servico'] ?>">
                                        <?= htmlspecialchars($serv['nome_servico']) ?> (R$<?= number_format((float)$serv['valor_base'], 2, ',', '.') ?>)
                                    </label>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-muted small">Nenhum serviço cadastrado na tabela `servicos`.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="w-100 py-2">Cadastrar Ordem de Serviço</button>
            </div>
        </form>
    </div>

    <hr class="my-4">

    <h3>Ordens Cadastradas e Pagamentos</h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>OS</th>
                    <th>Cliente</th>
                    <th>Serviços Vinculados</th>
                    <th>Valor Total</th>
                    <th>Pagamento</th>
                    <th>Status OS</th>
                    <th>Previsão</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while($m = mysqli_fetch_assoc($manutencoes)): ?>
                    <?php 
                        // Buscar serviços associados a esta manutenção
                        $idManut = (int)$m['id_manutencao'];$sqlServM = "SELECT s.nome_servico, sm.valor_unitario FROM servico_manutencao sm INNER JOIN servicos s ON sm.id_servico = s.id_servico WHERE sm.id_manutencao = ?";
                        $stmtSM = mysqli_prepare($conn, $sqlServM);$strServicos = "";
                        if ($stmtSM) {
                            mysqli_stmt_bind_param($stmtSM, 'i',$idManut);
                            mysqli_stmt_execute($stmtSM);
                            $resSM = mysqli_stmt_get_result($stmtSM);
                            while($sRow = mysqli_fetch_assoc($resSM)) {
                                $strServicos .= "• " . htmlspecialchars($sRow['nome_servico']) . " (R$ " . number_format((float)$sRow['valor_unitario'], 2, ',', '.') . ")<br>";
                            }
                            mysqli_stmt_close($stmtSM);
                        }
                        if (empty($strServicos)) {$strServicos = "<span class='text-muted'>Nenhum serviço</span>";
                        }

                        // Definir o valor total considerando o campo ou o cálculo dinâmico dos serviços
                        $valorExibido = max((float)($m['valor_total'] ?? 0), (float)($m['calc_total'] ?? 0));
                        $stPag =$m['status_pagamento'] ?? 'pendente';
                    ?>
                    <tr>
                        <td class="fw-bold">#<?= $m['id_manutencao'] ?></td>
                        <td><?= htmlspecialchars($m['cliente']) ?></td>
                        <td style="font-size: 12px; line-height: 1.3;"><?= $strServicos ?></td>
                        <td class="fw-bold text-danger">R$<?= number_format($valorExibido, 2, ',', '.') ?></td>
                        <td>
                            <form method="POST" class="d-flex flex-column gap-1">
                                <input type="hidden" name="acao" value="atualizar">
                                <input type="hidden" name="id_manutencao" value="<?= $m['id_manutencao'] ?>">
                                <input type="hidden" name="status" value="<?= $m['status'] ?>">
                                <input type="hidden" name="entrega_estimada" value="<?= $m['entrega_estimada'] ?>">
                                
                                <select name="status_pagamento" class="form-select form-select-sm" style="width: 110px;">
                                    <option value="pendente" <?= $stPag=='pendente'?'selected':'' ?>>Pendente</option>
                                    <option value="pago" <?= $stPag=='pago'?'selected':'' ?>>Pago</option>
                                </select>
                                <select name="forma_pagamento" class="form-select form-select-sm" style="width: 110px;">
                                    <option value="PIX" <?= ($m['forma_pagamento']??'')=='PIX'?'selected':'' ?>>PIX</option>
                                    <option value="Cartão de Crédito" <?= ($m['forma_pagamento']??'')=='Cartão de Crédito'?'selected':'' ?>>Crédito</option>
                                    <option value="Cartão de Débito" <?= ($m['forma_pagamento']??'')=='Cartão de Débito'?'selected':'' ?>>Débito</option>
                                    <option value="Dinheiro" <?= ($m['forma_pagamento']??'')=='Dinheiro'?'selected':'' ?>>Dinheiro</option>
                                </select>
                        </td>
                        <td>
                                <select name="status" class="form-select form-select-sm" style="width: 115px;">
                                    <option value="recebida" <?= $m['status']=='recebida'?'selected':'' ?>>Recebida</option>
                                    <option value="em_analise" <?= $m['status']=='em_analise'?'selected':'' ?>>Em análise</option>
                                    <option value="em_manutencao" <?= $m['status']=='em_manutencao'?'selected':'' ?>>Em manutenção</option>
                                    <option value="pronta" <?= $m['status']=='pronta'?'selected':'' ?>>Pronta</option>
                                </select>
                        </td>
                        <td>
                                <input type="date" name="entrega_estimada" value="<?= $m['entrega_estimada'] ?>" style="width: 125px;">
                        </td>
                        <td>
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