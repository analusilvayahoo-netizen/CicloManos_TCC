<?php

session_start();

include 'config.php';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<title>Acessórios - CicloManos</title>

<link rel="stylesheet" href="desing.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
rel="stylesheet"
integrity="sha384-sRIlkx6YILFv47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
crossorigin="anonymous">


<style>

/* =========================
   MENU
========================= */

.menu {

    display: flex;
    align-items: center;
    justify-content: center;
    gap: 30px;
    position: relative;

}


/* =========================
   PRODUTOS
========================= */

.produtos-menu {

    position: relative;

}

.botao-produtos {

    background: none;
    border: none;
    color: inherit;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    padding: 10px 15px;

}

.botao-produtos:hover {

    opacity: 0.8;

}


/* =========================
   CAIXA DE PRODUTOS
========================= */

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
    transition: 0.2s;

}

.caixa-produtos a:hover {

    background-color: #f5f5f5;
    color: #4A86B8 !important;

}


/* =========================
   LINKS DO MENU
========================= */

.menu nav {

    display: flex;
    align-items: center;
    gap: 25px;

}

.menu nav a {

    text-decoration: none;
    color: inherit;
    font-size: 16px;

}

.menu nav a:visited {

    color: inherit;

}

.menu nav a:hover {

    opacity: 0.8;

}


/* =========================
   CONTA E CARRINHO
========================= */

.topo a {

    color: inherit !important;
    text-decoration: none !important;

}

.topo a:visited {

    color: inherit !important;
    text-decoration: none !important;

}

.topo a:hover {

    color: inherit !important;
    text-decoration: none !important;

}

.usuario {

    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;

}

.usuario a {

    color: inherit !important;
    text-decoration: none !important;
    font-size: 16px;

}

.usuario a:visited {

    color: inherit !important;
    text-decoration: none !important;

}

.usuario a:hover {

    color: inherit !important;
    text-decoration: none !important;

}


/* =========================
   BOTÃO BUSCAR
========================= */

.busca button {

    cursor: pointer;

}


/* =========================
   TÍTULO
========================= */

.titulo-acessorios {

    text-align: center;
    margin: 40px 0 30px 0;
    font-size: 30px;
    font-weight: bold;

}


/* =========================
   CARDS
========================= */

.card-acessorio {

    background: white;
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.10);
    transition: 0.3s;
    height: 320px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;

}

.card-acessorio:hover {

    transform: translateY(-5px);

}

.card-acessorio img {

    width: 100%;
    height: 160px;
    object-fit: contain;

}

.preco-acessorio {

    color: #e60023;
    font-weight: bold;
    font-size: 20px;
    margin-top: 10px;

}

.link-acessorio {

    text-decoration: none !important;
    color: inherit !important;

}


/* =========================
   RODAPÉ
========================= */

footer {

    background: #111;
    color: white;
    text-align: center;
    padding: 20px;
    margin-top: 40px;

}

</style>

</head>


<body>


<!-- =========================
     TOPO
========================= -->

<div class="topo">

    <a href="#">
        📍 Localização
    </a>

    <a href="https://wa.me/551239163262?text=Ol%C3%A1!%20Vim%20pelo%20site%20da%20CicloManos%20e%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es"
       target="_blank">

        💬 Fale conosco

    </a>

    <a href="https://wa.me/551239163262?text=Ol%C3%A1!%20Vim%20pelo%20site%20da%20CicloManos%20e%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es"
       target="_blank">

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

        <a href="login.php">

            👤 Conta

        </a>

        <a href="carrinho.php">

            🛒 Carrinho

        </a>

    </div>


</div>


<!-- =========================
     MENU
========================= -->

<div class="menu">


    <!-- PRODUTOS -->

    <div class="produtos-menu">

        <button
            class="botao-produtos"
            onclick="abrirProdutos()"
        >

            ☰ Produtos

        </button>


        <!-- CAIXA DE PRODUTOS -->

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


    <!-- OUTROS ITENS -->

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

<h2 class="titulo-acessorios">

    ACESSÓRIOS

</h2>


<!-- =========================
     PRODUTOS
========================= -->

<div class="container">

    <div class="row g-4">


        <!-- CAPACETE -->

        <div class="col-12 col-md-6 col-lg-4">

            <a href="#" class="link-acessorio">

                <div class="card-acessorio">

                    <img
                        src="https://cdn-icons-png.flaticon.com/512/2972/2972185.png"
                        alt="Capacete Bike"
                    >

                    <h3>

                        Capacete Bike

                    </h3>

                    <p class="preco-acessorio">

                        R$ 89,90

                    </p>

                </div>

            </a>

        </div>


        <!-- LUVA -->

        <div class="col-12 col-md-6 col-lg-4">

            <a href="#" class="link-acessorio">

                <div class="card-acessorio">

                    <img
                        src="https://cdn-icons-png.flaticon.com/512/854/854894.png"
                        alt="Luva Ciclismo"
                    >

                    <h3>

                        Luva Ciclismo

                    </h3>

                    <p class="preco-acessorio">

                        R$ 39,90

                    </p>

                </div>

            </a>

        </div>


        <!-- GARRAFA -->

        <div class="col-12 col-md-6 col-lg-4">

            <a href="#" class="link-acessorio">

                <div class="card-acessorio">

                    <img
                        src="https://cdn-icons-png.flaticon.com/512/2972/2972223.png"
                        alt="Garrafa Squeeze"
                    >

                    <h3>

                        Garrafa Squeeze

                    </h3>

                    <p class="preco-acessorio">

                        R$ 25,00

                    </p>

                </div>

            </a>

        </div>


        <!-- LUZ LED -->

        <div class="col-12 col-md-6 col-lg-4">

            <a href="#" class="link-acessorio">

                <div class="card-acessorio">

                    <img
                        src="https://cdn-icons-png.flaticon.com/512/1046/1046874.png"
                        alt="Luz LED Bike"
                    >

                    <h3>

                        Luz LED Bike

                    </h3>

                    <p class="preco-acessorio">

                        R$ 35,00

                    </p>

                </div>

            </a>

        </div>


        <!-- BOLSA SELIM -->

        <div class="col-12 col-md-6 col-lg-4">

            <a href="#" class="link-acessorio">

                <div class="card-acessorio">

                    <img
                        src="https://cdn-icons-png.flaticon.com/512/2972/2972215.png"
                        alt="Bolsa Selim"
                    >

                    <h3>

                        Bolsa Selim

                    </h3>

                    <p class="preco-acessorio">

                        R$ 49,90

                    </p>

                </div>

            </a>

        </div>


        <!-- CADEADO -->

        <div class="col-12 col-md-6 col-lg-4">

            <a href="#" class="link-acessorio">

                <div class="card-acessorio">

                    <img
                        src="https://cdn-icons-png.flaticon.com/512/1046/1046857.png"
                        alt="Cadeado Bike"
                    >

                    <h3>

                        Cadeado Bike

                    </h3>

                    <p class="preco-acessorio">

                        R$ 59,90

                    </p>

                </div>

            </a>

        </div>


    </div>

</div>


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