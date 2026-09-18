<?php

session_start();

include 'config.php';

?>

<html lang="pt-br">

<head>

<meta charset="UTF-8">

<title>CicloManos</title>

<link rel="stylesheet" href="desing.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
rel="stylesheet"
integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
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


/* Caixa aberta */

.caixa-produtos.aberto {
    display: block;
}


/* Links dentro da caixa */

.caixa-produtos a {

    display: block;

    padding: 12px 18px;

    color: #333 !important;

    text-decoration: none !important;

    font-size: 15px;

    transition: 0.2s;
}


/* Quando passar o mouse */

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


</style>

</head>


<body>


<!-- =========================
     TOPO
========================= -->

<div class="topo">

    <a href="https://share.google/jYgrtVLyebEBGaqzt">
        📍 Localização
    </a>

    <a href=https://wa.me/551239163262?text=Ol%C3%A1!%20Vim%20pelo%20site%20da%20CicloManos%20e%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es." target="_blank">
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


        <!-- CAIXA -->

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

        <a href="ofertas.html">
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
     PRODUTOS
========================= -->

<div class="produtos">

</div>


<!-- =========================
     VITRINE DE PRODUTOS
========================= -->

<div class="container mt-5">


    <h1 class="text-center mb-4">
        Nossa Vitrine de Produtos
    </h1>


<?php


$sql = "SELECT * FROM cicloprodutos";


$result = mysqli_query($conn, $sql);


if (!$result) {


    echo "

    <p class='alert alert-danger'>

        Erro ao buscar os produtos:
        " . mysqli_error($conn) . "

    </p>

    ";


} else {


    if (mysqli_num_rows($result) > 0) {


        echo "<div class='row'>";


        while ($row = mysqli_fetch_assoc($result)) {


            echo "

            <div class='col-12 col-md-6 col-lg-3 mb-4'>


                <div class='card h-100 shadow-sm'>


                    <a

                        href='produtos.php?id=".$row['id']."'

                        style='text-decoration:none;color:inherit;'

                    >


                        <img

                            src='".$row['imagem']."'

                            class='card-img-top'

                            alt='Imagem do produto'

                        >


                        <div class='card-body'>


                            <h5 class='card-title'>

                                ".$row['produto']."

                            </h5>


                            <p class='card-text'>

                                ".$row['descricao']."

                            </p>


                        </div>


                    </a>


                    <div class='card-body pt-0 mt-auto'>


                        <p class='fw-bold text-primary fs-5'>

                            R$ "

                            . number_format(

                                $row['preco_venda'],

                                2,

                                ',',

                                '.'

                            )

                            . "

                        </p>


                        <form

                            method='POST'

                            action='carrinho.php'

                        >


                            <input

                                type='hidden'

                                name='id'

                                value='".$row['id']."'

                            >


                            <button

                                type='submit'

                                name='adicionar'

                                class='btn btn-primary w-100'

                            >

                                🛒 Adicionar ao carrinho

                            </button>


                        </form>


                    </div>


                </div>


            </div>

            ";

        }


        echo "</div>";


    } else {


        echo "

        <p class='alert alert-warning'>

            Nenhum produto encontrado.

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