<?php

session_start();

include 'config.php';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <title> CicloManos </title>

    <link rel="stylesheet" href="style.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
          crossorigin="anonymous">

</head>


<body>

<!-- =========================
     TOPO
========================= -->

<div class="topo">

    <a href="https://share.google/jYgrtVLyebEBGaqzt">
        📍 Localização
    </a>

    <a href="https://wa.me/551239163262?text=Ol%C3%A1!%20Vim%20pelo%20site%20da%20CicloManos%20e%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es." target="_blank">
        💬 Fale conosco
    </a>

    <a href="#">
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

        <img
            src="tcc/logo.jpg"
            class="logo"
            alt="Logo CicloManos"
        >

    </a>


    <!-- PESQUISA -->

    <div class="busca">

        <input
            type="text"
            placeholder="Digite o que você procura"
        >

        <button>
            Buscar
        </button>

    </div>


    <!-- CONTA E CARRINHO -->

    <div class="usuario">

        <?php if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'funcionario'): ?>

            <a href="painel_funcionario.php">
                👤 <?= htmlspecialchars($_SESSION['nome_usuario'] ?? 'Funcionário') ?>
            </a>

        <?php elseif (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'cliente'): ?>

            <a href="index.php">
                👤 Olá, <?= htmlspecialchars($_SESSION['nome_usuario'] ?? 'Cliente') ?>
            </a>

        <?php else: ?>

            <a href="login.php">
                👤 Conta
            </a>

        <?php endif; ?>

        <a href="carrinho.php">
            🛒 Carrinho
        </a>

    </div>

</div>


<!-- =========================
     MENU
========================= -->

<div class="menu">

    <div class="produtos-menu">

        <button
            type="button"
            class="botao-produtos"
            onclick="abrirProdutos()"
        >
            ☰ Produtos
        </button>

        <div
            id="caixa-produtos"
            class="caixa-produtos"
        >

            <a href="acessorios.php">
                Acessórios
            </a>

            <a href="pecas.php">
                Peças
            </a>

            <a href="bicicletas.php">
                Bicicletas
            </a>

        </div>

    </div>

    <nav>

        <a href="manutencao.php">
            Manutenção
        </a>

        <a href="ofertas.php">
            Ofertas
        </a>

    </nav>

</div>



<!-- =========================
     BANNER
========================= -->

<div class="banner">

    <img
        src="tcc/logo.jpg"
        alt="CicloManos"
    >

</div>


<!-- =========================
     TÍTULO
========================= -->

<div class="container mt-5">

    <h1 class="text-center mb-4">
        Acessórios
    </h1>


<!-- =========================
     PRODUTOS
========================= -->




<!-- =========================
     RODAPÉ
========================= -->

<footer>

    <p>

        © 2026 CicloManos - Todos os direitos reservados.

    </p>

</footer>


<!-- =========================
     JAVASCRIPT
========================= -->

<script>

function abrirProdutos() {

    const caixa =
        document.getElementById("caixa-produtos");

    caixa.classList.toggle("aberto");

}


/* Fecha a caixa quando clicar fora */

document.addEventListener("click", function(event) {

    const produtosMenu =
        document.querySelector(".produtos-menu");

    const caixa =
        document.getElementById("caixa-produtos");

    if (!produtosMenu.contains(event.target)) {

        caixa.classList.remove("aberto");

    }

});

</script>


</body>

</html>