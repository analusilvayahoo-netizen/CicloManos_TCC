<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'funcionario') {
    header('Location: login_funcionario.php');
    exit;
}

$nomeFuncionario = $_SESSION['nome_usuario'] ?? 'Funcionário';
$cargoFuncionario = $_SESSION['cargo_funcionario'] ?? 'Funcionário';

function e($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Funcionário | CicloManos</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body.admin-cm { min-height: 100vh; background: #f7f7f7; color: #222; display: flex; flex-direction: column; font-family: Arial, Helvetica, sans-serif; }
        
        /* Topo azul com o nome/título e dados do funcionário */
        .cm-admin-topo { background: #4b86b4; color: #fff; padding: 18px 6%; min-height: 75px; display: flex; align-items: center; justify-content: space-between; }
        .cm-admin-logo { color: #fff; text-decoration: none; font-size: 24px; font-weight: 700; }
        .cm-admin-logo:hover { color: #fff; text-decoration: none; }
        .cm-admin-user { display: flex; align-items: center; gap: 15px; }
        .cm-admin-user span { text-align: right; font-size: 14px; color: #f2f2f2; }
        .cm-admin-user span strong { display: block; margin-bottom: 2px; color: #fff; }
        
        .cm-admin-main { width: 92%; max-width: 1180px; margin: 45px auto 60px; flex: 1; }
        .cm-admin-titulo { margin-bottom: 28px; }
        .cm-admin-titulo h1 { font-size: 32px; font-weight: 700; color: #171717; margin-bottom: 8px; }
        .cm-admin-titulo p { color: #777; font-size: 15px; line-height: 1.5; }
        .cm-admin-botoes { margin-bottom: 28px; display: flex; gap: 12px; }
        .cm-admin-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .cm-admin-card { background: #fff; border: 1px solid #e8e8e8; border-radius: 14px; padding: 25px; min-height: 190px; text-decoration: none; color: #222; box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05); transition: transform 0.2s ease, box-shadow 0.2s ease; display: flex; flex-direction: column; }
        .cm-admin-card:hover { transform: translateY(-5px); box-shadow: 0 8px 22px rgba(0, 0, 0, 0.10); }
        .cm-admin-card .emoji { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background: #f2f2f2; border-radius: 12px; font-size: 24px; margin-bottom: 20px; }
        .cm-admin-card h2 { font-size: 18px; margin-bottom: 8px; color: #181818; font-weight: bold; }
        .cm-admin-card p { color: #777; font-size: 13px; line-height: 1.5; }
        .cm-rodape { background: #111; color: #aaa; text-align: center; padding: 22px; font-size: 12px; margin-top: auto; width: 100%; }
        
        .cm-btn-sair { padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; background: #fff; color: #4b86b4; border: none; transition: 0.2s; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; }
        .cm-btn-sair:hover { background: #f2f2f2; color: #3b6c95; }
        
        .cm-btn-cadastrar { padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; background: #f1f1f1; color: #333; border: 1px solid #ddd; display: inline-flex; align-items: center; justify-content: center; transition: 0.2s; }
        .cm-btn-cadastrar:hover { background: #e5e5e5; color: #333; }

        @media(max-width: 900px) { .cm-admin-grid { grid-template-columns: repeat(2, 1fr); } }
        @media(max-width: 650px) { .cm-admin-grid { grid-template-columns: 1fr; } .cm-admin-topo { flex-direction: column; gap: 15px; text-align: center; } .cm-admin-user { flex-direction: column; } .cm-admin-user span { text-align: center; } }
    </style>
</head>
<body class="admin-cm">

    <header class="cm-admin-topo">
        <a class="cm-admin-logo" href="painel_funcionario.php">🚲 CicloManos · Funcionário</a>
        <div class="cm-admin-user">
            <span>
                <strong><?= e($nomeFuncionario) ?></strong>
                <?= e($cargoFuncionario) ?>
            </span>
            <a class="cm-btn-sair" href="logout.php">Sair</a>
        </div>
    </header>

    <main class="cm-admin-main">
        <div class="cm-admin-titulo">
            <h1>Painel do Funcionário</h1>
            <p>Gerencie produtos, clientes, vendas, ofertas e serviços de manutenção da CicloManos.</p>
        </div>

        <div class="cm-admin-botoes">
            <a class="cm-btn-cadastrar" href="cadastro_funcionario.php">＋ Cadastrar funcionário</a>
        </div>

        <div class="cm-admin-grid">
            <a class="cm-admin-card" href="cadastro.php">
                <div class="emoji">📦</div>
                <h2>Produtos</h2>
                <p>Cadastrar e administrar bicicletas, peças e acessórios.</p>
            </a>

            <a class="cm-admin-card" href="admin_manutencao.php">
                <div class="emoji">🛠️</div>
                <h2>Manutenções</h2>
                <p>Criar ordens de serviço e atualizar o status das manutenções.</p>
            </a>

            <a class="cm-admin-card" href="clientes.php">
                <div class="emoji">👥</div>
                <h2>Clientes</h2>
                <p>Consultar clientes, contatos e endereços cadastrados.</p>
            </a>

            <a class="cm-admin-card" href="vendas.php">
                <div class="emoji">🧾</div>
                <h2>Vendas</h2>
                <p>Consultar vendas, pagamentos e itens de cada venda.</p>
            </a>

            <a class="cm-admin-card" href="admin_ofertas.php">
                <div class="emoji">🏷️</div>
                <h2>Ofertas</h2>
                <p>Criar, editar, ativar e desativar promoções da loja.</p>
            </a>

            <a class="cm-admin-card" href="Index.php" target="_blank" rel="noopener">
                <div class="emoji">🛍️</div>
                <h2>Visualizar produtos na loja</h2>
                <p>Abrir a loja e navegar pelo catálogo.</p>
            </a>
        </div>
    </main>

    <footer class="cm-rodape">
        © <?= date('Y') ?> CicloManos · Área administrativa
    </footer>

</body>
</html>