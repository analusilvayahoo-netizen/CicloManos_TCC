<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'funcionario') {
    header('Location: login.php');
    exit;
}

$nomeFuncionario = $_SESSION['nome_usuario'] ?? 'Funcionário';
$cargoFuncionario = $_SESSION['cargo_funcionario'] ?? 'Funcionário';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Painel do Funcionário - CicloManos</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
        }

        header {
            background-color: #111;
            color: white;
            padding: 20px 50px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            font-size: 25px;
            font-weight: bold;
        }

        .funcionario {
            text-align: right;
        }

        .funcionario strong {
            display: block;
            font-size: 16px;
        }

        .funcionario span {
            font-size: 13px;
            color: #ccc;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
        }

        .titulo {
            margin-bottom: 30px;
        }

        .titulo h1 {
            font-size: 30px;
            margin-bottom: 8px;
        }

        .titulo p {
            color: #666;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            text-decoration: none;
            color: #333;
            transition: 0.2s;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.12);
        }

        .icone {
            font-size: 35px;
            margin-bottom: 15px;
        }

        .card h2 {
            font-size: 20px;
            margin-bottom: 8px;
        }

        .card p {
            color: #777;
            font-size: 14px;
            line-height: 1.5;
        }

        .sair {
            display: inline-block;
            margin-top: 35px;
            padding: 12px 25px;
            background-color: #c62828;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.2s;
        }

        .sair:hover {
            background-color: #a51f1f;
        }

        footer {
            margin-top: 60px;
            background-color: #111;
            color: #aaa;
            text-align: center;
            padding: 20px;
            font-size: 13px;
        }

        @media (max-width: 600px) {
            header {
                padding: 20px;
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .funcionario {
                text-align: center;
            }

            .container {
                width: 92%;
                margin-top: 30px;
            }
        }
    </style>
</head>

<body>

<header>
    <div class="logo">
        🚲 CicloManos
    </div>

    <div class="funcionario">
        <strong><?= htmlspecialchars($nomeFuncionario) ?></strong>
        <span><?= htmlspecialchars($cargoFuncionario) ?></span>
    </div>
</header>

<main class="container">

    <div class="titulo">
        <h1>Painel do Funcionário</h1>
        <p>Bem-vindo ao painel administrativo da CicloManos.</p>
    </div>

    <div class="cards">

        <a href="cadastro_produtos.php" class="card">
            <div class="icone">📦</div>
            <h2>Produtos</h2>
            <p>Cadastrar e gerenciar produtos da loja.</p>
        </a>

        <a href="cadastro_pecas.php" class="card">
            <div class="icone">🔧</div>
            <h2>Peças</h2>
            <p>Cadastrar e administrar peças disponíveis.</p>
        </a>

        <a href="manutencao.php" class="card">
            <div class="icone">🛠️</div>
            <h2>Manutenções</h2>
            <p>Consultar e atualizar solicitações de manutenção.</p>
        </a>

        <a href="clientes.php" class="card">
            <div class="icone">👥</div>
            <h2>Clientes</h2>
            <p>Consultar informações dos clientes cadastrados.</p>
        </a>

        <a href="vendas.php" class="card">
            <div class="icone">🧾</div>
            <h2>Vendas</h2>
            <p>Consultar pedidos e vendas realizadas.</p>
        </a>

        <a href="ofertas.php" class="card">
            <div class="icone">🏷️</div>
            <h2>Ofertas</h2>
            <p>Gerenciar produtos e ofertas da loja.</p>
        </a>
        
        <a href="cadastro_funcionario.php">
               Cadastrar Funcionário
        </a>


    </div>

    <a href="logout.php" class="sair">
        🚪 Sair da conta
    </a>

</main>

<footer>
    © <?= date('Y') ?> CicloManos - Painel do Funcionário
</footer>

</body>
</html>