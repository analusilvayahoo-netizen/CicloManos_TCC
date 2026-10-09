<?php
session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'funcionario') {
    header('Location: login_funcionario.php');
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

    <link rel="stylesheet" href="funcionario.css?v=4">
</head>

<body>

<header>

    <a href="painel_funcionario.php" class="logo">
        🚲 CicloManos
    </a>

    <div class="funcionario">

        <div class="icone-funcionario">
            👤
        </div>

        <div class="dados-funcionario">

            <strong>
                <?= htmlspecialchars($nomeFuncionario, ENT_QUOTES, 'UTF-8') ?>
            </strong>

            <span>
                <?= htmlspecialchars($cargoFuncionario, ENT_QUOTES, 'UTF-8') ?>
            </span>

        </div>

    </div>

</header>


<main class="container">

    <div class="titulo">

        <h1>Painel do Funcionário</h1>

        <p>
            Gerencie os produtos, clientes, vendas e serviços da CicloManos.
        </p>

    </div>


    <div class="cards">

        <a href="cadastro.php" class="card">

            <div class="icone">
                📦
            </div>

            <h2>Produtos</h2>

            <p>
                Cadastrar e gerenciar os produtos disponíveis na loja.
            </p>

            <span class="seta">
                →
            </span>

        </a>


        <a href="cadastro.php" class="card">

            <div class="icone">
                🔧
            </div>

            <h2>Peças</h2>

            <p>
                Cadastrar e administrar peças disponíveis para venda.
            </p>

            <span class="seta">
                →
            </span>

        </a>


        <a href="manutencao.php" class="card">

            <div class="icone">
                🛠️
            </div>

            <h2>Manutenções</h2>

            <p>
                Consultar e atualizar as solicitações de manutenção.
            </p>

            <span class="seta">
                →
            </span>

        </a>


        <a href="clientes.php" class="card">

            <div class="icone">
                👥
            </div>

            <h2>Clientes</h2>

            <p>
                Consultar informações dos clientes cadastrados.
            </p>

            <span class="seta">
                →
            </span>

        </a>


        <a href="vendas.php" class="card">

            <div class="icone">
                🧾
            </div>

            <h2>Vendas</h2>

            <p>
                Consultar pedidos e vendas realizadas pela loja.
            </p>

            <span class="seta">
                →
            </span>

        </a>


        <a href="ofertas.php" class="card">

            <div class="icone">
                🏷️
            </div>

            <h2>Ofertas</h2>

            <p>
                Gerenciar produtos que estão em oferta.
            </p>

            <span class="seta">
                →
            </span>

        </a>


        <a href="cadastro_funcionario.php" class="card card-funcionario">

            <div class="icone">
                👤+
            </div>

            <h2>Cadastrar Funcionário</h2>

            <p>
                Cadastrar novos funcionários para acessar o painel.
            </p>

            <span class="seta">
                →
            </span>

        </a>

    </div>


    <div class="area-sair">

        <a href="logout.php" class="sair">
            🚪 Sair da conta
        </a>

    </div>

</main>


<footer>

    © <?= date('Y') ?> CicloManos - Painel do Funcionário

</footer>

</body>
</html>