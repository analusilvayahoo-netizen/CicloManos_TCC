<?php
session_start();
include 'config.php';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Bicicletas - CicloManos</title>

    <link rel="stylesheet" href="desing.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous">

    <style>
        /* =========================
           BARRA SUPERIOR (CINZA CLARO)
        ========================= */
        .topo-cinza {
            background-color: #f2f2f2 !important;
            color: #333333 !important;
            padding: 8px 20px !important;
            display: flex !important;
            justify-content: space-around !important;
            align-items: center !important;
            font-size: 14px !important;
            width: 100% !important;
        }

        .topo-cinza a, .topo-cinza span {
            color: #333333 !important;
            text-decoration: none !important;
            font-weight: 500;
        }

        .topo-cinza a:hover {
            opacity: 0.8;
            text-decoration: underline !important;
        }

        /* =========================
           MEIO HEADER (LOGO, BUSCA, CONTA/CARRINHO)
        ========================= */
        .meio-header {
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding: 20px 0;
            background-color: #ffffff;
        }

        .meio-header .logo {
            max-height: 55px;
        }

        .meio-header .busca {
            display: flex;
            align-items: center;
            width: 450px;
        }

        .meio-header .busca input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
            outline: none;
        }

        .meio-header .busca button {
            background-color: #4b86b4;
            color: white;
            border: none;
            padding: 10px 25px;
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }

        /* ESTILO DA ÁREA CONTA E CARRINHO (TEXTO NORMAL E CLICÁVEL) */
        .usuario {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .usuario a {
            color: #333333 !important; /* Cor normal do texto (escuro) */
            text-decoration: none !important;
            font-size: 16px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .usuario a:hover {
            color: #4b86b4 !important; /* Muda levemente a cor apenas ao passar o mouse */
        }

        /* =========================
           MENU PRINCIPAL (FAIXA AZUL)
        ========================= */
        .menu-bar {
            background-color: #4b86b4;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 40px;
            padding: 12px 0;
            color: #ffffff;
            font-weight: bold;
        }

        .produtos-menu {
            position: relative;
        }

        .botao-produtos {
            background: none;
            border: none;
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .caixa-produtos {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            width: 190px;
            background-color: white;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.20);
            z-index: 1000;
            padding: 8px 0;
        }

        .caixa-produtos.aberto {
            display: block;
        }

        .caixa-produtos a {
            display: block;
            padding: 12px 18px;
            color: #333 !important;
            text-decoration: none !important;
            font-size: 15px;
            font-weight: normal;
        }

        .caixa-produtos a:hover {
            background-color: #f5f5f5;
            color: #4b86b4 !important;
        }

        .menu-bar nav {
            display: flex;
            align-items: center;
            gap: 35px;
        }

        .menu-bar nav a {
            text-decoration: none;
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
        }

        /* =========================
           BANNER CENTRAL (LOGO GRANDE)
        ========================= */
        .banner-central {
            text-align: center;
            padding: 50px 0 30px 0;
        }

        .banner-central img {
            width: 100%;
            max-width: 520px;
            height: auto;
        }

        /* =========================
           CARDS DE PRODUTOS
        ========================= */
        .card-produto {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            background-color: #ffffff;
            transition: box-shadow 0.2s ease-in-out;
        }

        .card-produto:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card-produto img {
            height: 180px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .card-produto .titulo-produto {
            font-size: 14px;
            color: #333333;
            min-height: 42px;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .card-produto .preco-produto {
            color: #4b86b4;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .btn-carrinho {
            background-color: #4b86b4 !important;
            color: #ffffff !important;
            border: none !important;
            padding: 8px;
            border-radius: 4px;
            font-weight: 500;
            display: block;
            width: 100%;
            text-align: center;
            text-decoration: none !important;
            font-size: 14px;
        }

        .btn-carrinho:hover {
            background-color: #3b6c95 !important;
        }
    </style>
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
        <a href="ciclomanos.php">
            <img src="tcc/logo.jpg" class="logo" alt="Logo CicloManos">
        </a>

        <!-- PESQUISA -->
        <div class="busca">
            <input type="text" placeholder="Digite o que você procura">
            <button>Buscar</button>
        </div>

        <!-- CONTA E CARRINHO (TEXTO NORMAL E CLICÁVEL) -->
        <div class="usuario">
            <a href="login.php">👤 Conta</a>
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
            <a href="ofertas.html">Ofertas</a>
        </nav>

    </div>

    <!-- =========================
         BANNER CENTRAL (LOGO GRANDE)
    ========================= -->
    <div class="banner-central">
        <img src="tcc/logo.jpg" alt="CicloManos">
    </div>

    <!-- =========================
         VITRINE DE BICICLETAS
    ========================= -->
    <div class="container mb-5">

        <h2 class="text-center fw-bold mb-4">
            Bicicletas
        </h2>

        <?php
        // Consulta filtrando apenas bicicletas
        $sql = "SELECT * FROM cicloprodutos WHERE produto LIKE '%Bicicleta%' OR produto LIKE '%Bike%'";

        $result = mysqli_query($conn, $sql);

        if (!$result) {
            echo "
            <p class='alert alert-danger text-center'>
                Erro ao buscar as bicicletas: " . mysqli_error($conn) . "
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
                    Nenhuma bicicleta encontrada.
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
    <footer class="mt-5 text-center p-3 bg-light border-top">
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