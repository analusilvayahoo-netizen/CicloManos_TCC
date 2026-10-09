<?php
session_start();

// 1. Incluir o ficheiro de configuração da base de dados
require_once 'config.php';

// 2. Garantir que a variável de conexão existe
if (!isset($conn) && isset($conexao)) {
    $conn = $conexao;
}

if (!$conn) {
    die("Erro: Conexão com a base de dados não encontrada. Verifique o seu config.php.");
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Acessórios - CicloManos</title>

    <link rel="stylesheet" href="style.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous">
</head>

<body>

    <!-- =========================
         TOPO (FAIXA CINZA CLARO)
    ========================= -->
    <div class="topo-cinza">
        <a href="https://share.google/jYgrtVLyebEBGaqzt" target="_blank">
            📍 Localização
        </a>

        <a href="https://wa.me/551239163262?text=Ol%C3%A1!%20Vim%20pelo%20site%20da%20CicloManos%20e%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es." target="_blank">
            💬 Fale conosco
        </a>

        <a href="https://wa.me/551239163262" target="_blank">
            📱 WhatsApp: (12) 3916-3262
        </a>

        <span>
            📞 Telefone: (12) 3916-3262
        </span>
    </div>

    <!-- =========================
         CABEÇALHO
    ========================= -->
    <div class="meio-header">

        <!-- LOGO -->
        <a href="index.php">
            <img src="tcc/logo.jpg" class="logo" alt="Logo CicloManos">
        </a>

        <!-- PESQUISA -->
        <div class="busca">
            <input type="text" placeholder="Digite o que você procura">
            <button>Buscar</button>
        </div>

        <!-- CONTA E CARRINHO -->
        <div class="usuario">
<?php if (($_SESSION['tipo_usuario'] ?? '') === 'funcionario'): ?>
    <a href="painel_funcionario.php">👤 <?= htmlspecialchars($_SESSION['nome_usuario'] ?? 'Funcionário', ENT_QUOTES, 'UTF-8') ?></a>
<?php elseif (($_SESSION['tipo_usuario'] ?? '') === 'cliente'): ?>
    <a href="minha_conta.php">👤 Olá, <?= htmlspecialchars($_SESSION['nome_usuario'] ?? 'Cliente', ENT_QUOTES, 'UTF-8') ?></a>
    <a href="logout.php">Sair</a>
<?php else: ?>
    <a href="login.php">👤 Conta</a>
    <a href="login_funcionario.php">Área do funcionário</a>
<?php endif; ?>
<a href="carrinho.php">🛒 Carrinho</a>
</div>

    </div>

    <!-- =========================
         MENU PRINCIPAL (FAIXA AZUL)
    ========================= -->
    <div class="menu-bar">

        <!-- PRODUTOS -->
        <div class="produtos-menu">
            <button class="botao-produtos" onclick="abrirProdutos()">
                ☰ Produtos
            </button>

            <!-- CAIXA DROP-DOWN -->
            <div id="caixa-produtos" class="caixa-produtos">
                <a href="acessorios.php">Acessórios</a>
                <a href="pecas.php">Peças</a>
                <a href="bicicletas.php">Bicicletas</a>
            </div>
        </div>

        <!-- OUTROS ITENS -->
        <nav>
            <a href="manutencao.php">Manutenção</a>
            <a href="ofertas.php">Ofertas</a>
        </nav>

    </div>

    <!-- =========================
         BANNER CENTRAL (LOGO GRANDE)
    ========================= -->
    <div class="banner-central">
        <img src="tcc/logo.jpg" alt="CicloManos">
    </div>

    <!-- =========================
         VITRINE DE ACESSÓRIOS
    ========================= -->
    <div class="container mb-5">

        <h2 class="text-center fw-bold mb-4">
            Acessórios
        </h2>

        <?php
        // Consulta buscando termos de acessórios no nome do produto ou na categoria
        $sql = "SELECT * FROM cicloprodutos WHERE id_categoria = 4 ORDER BY produto";

        $result = mysqli_query($conn, $sql);

        if (!$result) {
            echo "
            <p class='alert alert-danger text-center'>
                Erro ao buscar os acessórios: " . mysqli_error($conn) . "
            </p>
            ";
        } else {
            if (mysqli_num_rows($result) > 0) {
                echo "<div class='row g-4'>";

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "
                    <div class='col-12 col-sm-6 col-md-4 col-lg-3'>
                        <div class='card-produto h-100 d-flex flex-column justify-content-between text-center'>

                            <a href='produtos.php?id=" . $row['id'] . "' style='text-decoration:none;'>
                                <img src='" . htmlspecialchars($row['imagem']) . "' class='img-fluid' alt='" . htmlspecialchars($row['produto']) . "'>

                                <div class='titulo-produto fw-semibold'>
                                    " . htmlspecialchars($row['produto']) . "
                                </div>

                                <div class='preco-produto'>
                                    R$ " . number_format($row['preco_venda'], 2, ',', '.') . "
                                </div>
                            </a>

                            <a href='carrinho.php?acao=add&id=" . $row['id'] . "' class='btn-carrinho mt-auto'>
                                🛒 Colocar no carrinho
                            </a>

                        </div>
                    </div>
                    ";
                }

                echo "</div>";
            } else {
                echo "
                <p class='alert alert-warning text-center'>
                    Nenhum acessório encontrado.
                </p>
                ";
            }
        }

        mysqli_close($conn);
        ?>

    </div>

    <!-- =========================
         RODAPÉ
    ========================= -->
    <footer class="mt-5 text-center p-3 border-top">
        <p class="mb-0">© <?= date('Y') ?> CicloManos - Todos os direitos reservados.</p>
    </footer>

    <!-- =========================
         JAVASCRIPT
    ========================= -->
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